<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2018 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Mibew\RequestProcessor;

// Import namespaces and classes of the core
use Mibew\Asset\AssetManagerAwareInterface;
use Mibew\Asset\AssetManagerInterface;
use Mibew\Authentication\AuthenticationManagerAwareInterface;
use Mibew\Authentication\AuthenticationManagerInterface;
use Mibew\Http\Exception\AccessDeniedException;
use Mibew\Mail\MailerFactoryAwareInterface;
use Mibew\Mail\MailerFactoryInterface;
use Mibew\Mail\Template as MailTemplate;
use Mibew\Mail\Utils as MailUtils;
use Mibew\Settings;
use Mibew\Thread;
use Mibew\API\API as MibewAPI;
use Mibew\RequestProcessor\Exception\ThreadProcessorException;
use Mibew\Routing\RouterAwareInterface;
use Mibew\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Incapsulates thread api and thread processing functions.
 *
 * Event triggered by the class (see description of the RequestProcessor class
 * for details):
 *  - threadRequestReceived
 *  - threadReceiveRequestError
 *  - threadCallError
 *  - threadFunctionCall
 *
 * WARNING:
 *  threadResponseReceived registered but never called because of asynchronous
 *  nature of Core-to-Window interaction
 *
 * @todo Move all API functions to another place.
 */
class ThreadProcessor extends ClientSideProcessor implements
    RouterAwareInterface,
    AssetManagerAwareInterface,
    AuthenticationManagerAwareInterface,
    MailerFactoryAwareInterface
{
    /**
     * @var AuthenticationManagerInterface|null
     */
    protected $authenticationManager = null;

    /**
     * The request which is hadled now.
     *
     * @var Request|null
     */
    protected $currentRequest = null;

    /**
     * A Router instance.
     *
     * @var RouterInterface|null
     */
    protected $router = null;

    /**
     * @var MailerFactoryInterface|null
     */
    protected $mailerFactory = null;

    /**
     * An instance of asset manager.
     *
     * @var AssetManagerInterface|null
     */
    protected $assetManager = null;

    /**
     * Loads thread by id and token and checks if thread loaded
     *
     * @param int $thread_id Id of the thread
     * @param int $last_token Last token of the thread
     * @return \Mibew\Thread
     * @throws \Mibew\RequestProcessor\ThreadProcessorException
     */
    public static function getThread($thread_id, $last_token)
    {
        // Load thread
        $thread = Thread::load($thread_id, $last_token);
        // Check thread
        if (!$thread) {
            throw new ThreadProcessorException(
                'Wrong thread',
                ThreadProcessorException::ERROR_WRONG_THREAD
            );
        }

        // Return thread
        return $thread;
    }

    /**
     * Check if arguments exists in $args array
     *
     * @param array $args Arguments array
     * @param array $vars Array of arguments names that must be checked
     * @throws \Mibew\RequestProcessor\ThreadProcessorException
     */
    public static function checkParams($args, $vars)
    {
        if (empty($vars)) {
            return;
        }
        // Check variables exists
        foreach ($vars as $var) {
            if (!array_key_exists($var, $args)) {
                throw new ThreadProcessorException(
                    "There is no '{$var}' variable in arguments list",
                    ThreadProcessorException::ERROR_WRONG_ARGUMENTS
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest($request)
    {
        $this->currentRequest = $request;
        $response = parent::handleRequest($request);
        $this->currentRequest = null;

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * {@inheritdoc}
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthenticationManager(AuthenticationManagerInterface $manager)
    {
        $this->authenticationManager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthenticationManager()
    {
        return $this->authenticationManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setMailerFactory(MailerFactoryInterface $factory)
    {
        $this->mailerFactory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getMailerFactory()
    {
        return $this->mailerFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetManager()
    {
        return $this->assetManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setAssetManager(AssetManagerInterface $manager)
    {
        $this->assetManager = $manager;
    }

    /**
     * Class constructor
     */
    protected function __construct()
    {
        parent::__construct(array(
            'signature' => '',
            'trusted_signatures' => array(''),
            'event_prefix' => 'thread',
        ));
    }

    /**
     * Creates and returns an instance of the \Mibew\API\API class.
     *
     * @return \Mibew\API\API
     */
    protected function getMibewAPIInstance()
    {
        return MibewAPI::getAPI('\\Mibew\\API\\Interaction\\ChatInteraction');
    }

    /**
     * Check if operator logged in
     *
     * @return array Operators info array
     * @throws \Mibew\RequestProcessor\ThreadProcessorException If operator is
     *   not logged in.
     */
    protected function checkOperator()
    {
        $operator = $this->getAuthenticationManager()->getOperator();
        if (!$operator) {
            throw new ThreadProcessorException(
                "Operator is not logged in!",
                ThreadProcessorException::ERROR_AGENT_NOT_LOGGED_IN
            );
        }

        return $operator;
    }

    /**
     * Sends asynchronous request
     *
     * @param array $request The 'request' array. See Mibew API for details
     * @return boolean true on success or false on failure
     */
    protected function sendAsyncRequest($request)
    {
        // Define empty thread id and thread token
        $thread_id = null;
        $token = null;
        $recipient = null;
        foreach ($request['functions'] as $function) {
            // Save thread id and thread token from first function in package
            if (is_null($thread_id)) {
                $thread_id = $function['arguments']['threadId'];
                $token = $function['arguments']['token'];
                $recipient = $function['arguments']['recipient'];
                continue;
            }
            // Check thread id and thread token for the remaining functions
            $wrong_thread_id = $thread_id != $function['arguments']['threadId'];
            $wrong_token = $token != $function['arguments']['token'];
            if ($wrong_thread_id || $wrong_token) {
                throw new ThreadProcessorException(
                    'Various thread id or thread token in different functions in one package!',
                    ThreadProcessorException::VARIOUS_THREAD_ID
                );
            }
            // Check request recipient
            if ($recipient !== $function['arguments']['recipient']) {
                throw new ThreadProcessorException(
                    'Various recipient in different functions in one package!',
                    ThreadProcessorException::VARIOUS_RECIPIENT
                );
            }
        }
        // Store request in buffer
        if ($recipient == 'agent' || $recipient == 'both') {
            $this->addRequestToBuffer('thread_agent_' . $thread_id, $request);
        }
        if ($recipient == 'user' || $recipient == 'both') {
            $this->addRequestToBuffer('thread_user_' . $thread_id, $request);
        }

        return true;
    }

    /**
     * Additional validation for functions that called via call method
     *
     * @param Array $function A Function array
     */
    protected function checkFunction($function)
    {
        // Check recipient argument existence
        if (!array_key_exists('recipient', $function['arguments'])) {
            throw new ThreadProcessorException(
                "'recipient' argument is not set in function '{$function['function']}'!",
                ThreadProcessorException::EMPTY_RECIPIENT
            );
        }
        $recipient = $function['arguments']['recipient'];
        // Check recipient value
        if ($recipient != 'agent' && $recipient != 'both' && $recipient != 'user') {
            throw new ThreadProcessorException(
                "Wrong recipient value '{$recipient}'! It should be one of 'agent', 'user', 'both'",
                ThreadProcessorException::WRONG_RECIPIENT_VALUE
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function processFunction($function, \Mibew\API\ExecutionContext &$context)
    {
        // Check if a function can be called. Operators can call anythig, thus
        // we should continue validation only for users.
        if (!$this->getAuthenticationManager()->getOperator()) {
            // A function is called by a user. We need to check that the thread
            // is related with the user.
            $arguments = $context->getArgumentsList($function);
            $thread_id = $arguments['threadId'];
            // As defined in Mibew\API\Interaction\ChatInteraction "threadid"
            // argument is mandatory, but some function allows it to be null. In
            // such cases there is no thread and there is nothing to check.
            if (!is_null($thread_id)) {
                $is_own_thread = isset($_SESSION[SESSION_PREFIX . 'own_threads'])
                    && in_array($thread_id, $_SESSION[SESSION_PREFIX . 'own_threads']);
                if (!$is_own_thread) {
                    throw new AccessDeniedException();
                }
            }
        }

        // The function can be called. Process it.
        parent::processFunction($function, $context);
    }

    /**
     * Update chat window state. API function
     *
     * Call periodically by chat window
     * @param array $args Associative array of arguments. It must contains
     *   following keys:
     *    - 'threadId': Id of the thread related to chat window
     *    - 'token': last thread token
     *    - 'user': TRUE if window used by user and FALSE otherwise
     *    - 'typed': indicates if user(or agent) typed
     *    - 'lastId': id of the last sent to message
     * @return array Array of results. It contains following keys:
     *    - 'typing': indicates if another side of the conversation is typing
     *      message
     *    - 'canPost': indicates if agent(user can post message all the time)
     *      can post the message
     *    - 'threadState': current state of the thread. See Thread::STATE_*
     *      constants for details.
     *    - 'threadAgentId': ID of the agent that is currently related with the
     *      thread.
     */
    protected function apiUpdate($args)
    {
        // Load thread
        $thread = self::getThread($args['threadId'], $args['token']);

        // Check variables
        self::checkParams($args, array('user', 'typed'));

        if (!$args['user']) {
            $operator = $this->checkOperator();
            $thread->checkForReassign($operator);
        }

        $thread->ping($args['user'], $args['typed']);

        // Create requests key
        $requests_key = false;
        if ($args['user']) {
            $requests_key = 'thread_user_' . $thread->id;
        } else {
            if ($operator['operatorid'] == $thread->agentId) {
                $requests_key = 'thread_agent_' . $thread->id;
            }
        }

        // Load stored requests
        if ($requests_key !== false) {
            $stored_requests = $this->getRequestsFromBuffer($requests_key);
            if ($stored_requests !== false) {
                $this->responses = array_merge($this->responses, $stored_requests);
            }
        }

        // Get status values
        if ($args['user']) {
            $is_typing = abs($thread->lastPingAgent - time()) < Settings::get('connection_timeout')
                && $thread->agentTyping;
            // Users can post messages only when thread is open.
            $can_post = $thread->state != Thread::STATE_CLOSED;
        } else {
            $is_typing = abs($thread->lastPingUser - time()) < Settings::get('connection_timeout')
                && $thread->userTyping;
            // Operators can post messages only to own threads.
            $can_post = ($operator['operatorid'] == $thread->agentId);
        }

        return array(
            'threadState' => $thread->state,
            'threadAgentId' => $thread->agentId,
            'typing' => $is_typing,
            'canPost' => $can_post,
        );
    }

    /**
     * Send new messages to window. API function
     *
     * @param array $args Associative array of arguments. It must contains the
     *   following keys:
     *    - 'threadId': Id of the thread related to chat window
     *    - 'token': last thread token
     *    - 'user': TRUE if window used by user and FALSE otherwise
     *    - 'lastId': last sent message id
     */
    protected function apiUpdateMessages($args)
    {
        // Load thread
        $thread = self::getThread($args['threadId'], $args['token']);

        // Check variables
        self::checkParams($args, array('user', 'lastId'));

        // Check access
        if (!$args['user']) {
            $this->checkOperator();
        }

        // Send new messages
        $last_message_id = $args['lastId'];
        $messages = array_map(
            'sanitize_message',
            $thread->getMessages($args['user'], $last_message_id)
        );

        return array(
            'messages' => $messages,
            'lastId' => $last_message_id,
        );
    }

    /**
     * Post message to thread. API function
     *
     * @param array $args Associative array of arguments. It must contains the
     *   following keys:
     *    - 'threadId': Id of the thread related to chat window
     *    - 'token': last thread token
     *    - 'user': TRUE if window used by user and FALSE otherwise
     *    - 'message': posted message
     * @throws ThreadProcessorException
     */
    protected function apiPost($args)
    {
        // Load thread
        $thread = self::getThread($args['threadId'], $args['token']);

        // Check variables
        self::checkParams($args, array('user', 'message'));

        // Get operator's array
        if (!$args['user']) {
            $operator = $this->checkOperator();
            // Operators can post messages only to own threads.
            $can_post = ($operator['operatorid'] == $thread->agentId);
        } else {
            // Users can post messages only when a thread is open.
            $can_post = $thread->state != Thread::STATE_CLOSED;
        }

        if (!$can_post) {
            throw new ThreadProcessorException(
                "Cannot send",
                ThreadProcessorException::ERROR_CANNOT_SEND
            );
        }

        // Set fields
        $kind = $args['user'] ? Thread::KIND_USER : Thread::KIND_AGENT;
        if ($args['user']) {
            $msg_options = array('name' => $thread->userName);
        } else {
            $msg_options = array(
                'name' => $thread->agentName,
                'operator_id' => $operator['operatorid'],
            );
        }

        // Post message
        $posted_id = $thread->postMessage($kind, $args['message'], $msg_options);

        // Update shownMessageId
        if ($args['user'] && $thread->shownMessageId == 0) {
            $thread->shownMessageId = $posted_id;
            $thread->save();
        }
    }

    /**
     * Rename user in the chat. API function
     *
     * @param array $args Associative array of arguments. It must contains the
     *   following keys:
     *    - 'threadId': Id of the thread related to chat window
     *    - 'token': last thread token
     *    - 'name': new user name
     * @throws \Mibew\RequestProcessor\ThreadProcessorException
     */
    protected function apiRename($args)
    {
        // Check rename possibility
        if (Settings::get('usercanchangename') != "1") {
            throw new ThreadProcessorException(
                'server: forbidden to change name',
                ThreadProcessorException::ERROR_FORBIDDEN_RENAME
            );
        }

        // Load thread
        $thread = self::getThread($args['threadId'], $args['token']);

        // Check if new name exists
        self::checkParams($args, array('name'));

        //Rename user
        $thread->renameUser($args['name']);
        // Update user name in cookies
        $data = strtr(base64_encode($args['name']), '+/=', '-_,');
        setcookie(USERNAME_COOKIE_NAME, $data, time() + 60 * 60 * 24 * 365);
    }

    /**
     * Close chat thread. API function
     *
     * @param array $args Associative array of arguments. It must contains the
     *   following keys:
     *    - 'threadId': Id of the thread related to chat window
     *    - 'token': last thread token
     *    - 'user': TRUE if window used by user and FALSE otherwise
     * @return array Array of results. It contains following keys:
     *    - 'closed': indicates if thread can be closed
     */
    protected function apiClose($args)
    {
        // Load thread and check thread's last token
        $thread = self::getThread($args['threadId'], $args['token']);

        // Check if new user variable exists
        self::checkParams($args, array('user'));

        // Load operator
        if (!$args['user']) {
            $operator = $this->checkOperator();
        }

        // Close thread
        if ($args['user'] || $thread->agentId == $operator['operatorid']) {
            $thread->close($args['user']);
        }

        return array(
            'closed' => true,
        );
    }

    /**
     * Process submitted prechat survey.
     *
     * @param array $args Associative array of arguments. It must contains the
     *   following keys:
     *    - 'threadId': for this function this param equals to null;
     *    - 'token': for this function this param equals to null;
     *    - 'name': string, user name;
     *    - 'email': string, user email;
     *    - 'message': string, first user message;
     *    - 'info': string, some info about user;
     *    - 'referrer': page user came from;
     *    - 'groupId': selected group id.
     * @return array Array of results. It contains following keys:
     *    - 'next': string, indicates what module run next;
     *    - 'options': options array for next module.
     */
    protected function apiProcessSurvey($args)
    {
        $visitor = visitor_from_request();

        // Get form values
        $first_message = $args['message'];
        $info = $args['info'];
        $email = $args['email'];
        $referrer = $args['referrer'];

        // Verify group id
        $group_id = 0;
        $group = null;
        if (Settings::get('enablegroups') == '1') {
            if (preg_match("/^\d{1,8}$/", $args['groupId']) != 0) {
                $group = group_by_id($args['groupId']);
                if ($group) {
                    $group_id = (int)$args['groupId'];
                }
            }
        }

        if (Settings::get('usercanchangename') == "1" && !empty($args['name'])) {
            $newname = $args['name'];
            if ($newname != $visitor['name']) {
                $data = strtr(base64_encode($newname), '+/=', '-_,');
                setcookie(USERNAME_COOKIE_NAME, $data, time() + 60 * 60 * 24 * 365);
                $visitor['name'] = $newname;
            }
        }

        // Check if there are online operators
        if (!has_online_operators($group_id)) {
            // Display leave message page
            $client_data = setup_leavemessage(
                $visitor['name'],
                $email,
                $group_id,
                $info,
                $referrer
            );
            $options = $client_data['leaveMessage'];
            $options['page'] += setup_logo($group);

            return array(
                'next' => 'leaveMessage',
                'options' => $options,
            );
        }

        // Initialize dialog
        $thread = chat_start_for_user(
            $group_id,
            false,
            $visitor['id'],
            $visitor['name'],
            $referrer,
            $info
        );

        // Send some messages
        if ($email) {
            $thread->postMessage(
                Thread::KIND_FOR_AGENT,
                getlocal('E-Mail: {0}', array($email), get_current_locale(), true)
            );
        }

        if ($first_message) {
            $posted_id = $thread->postMessage(
                Thread::KIND_USER,
                $first_message,
                array('name' => $visitor['name'])
            );
            $thread->shownMessageId = $posted_id;
            $thread->save();
        }

        // Prepare chat options
        $client_data = setup_chatview_for_user(
            $this->getRouter(),
            $this->getAssetManager()->getUrlGenerator(),
            $this->currentRequest,
            $thread
        );
        $options = $client_data['chat'];
        $options['page'] += setup_logo($group);

        return array(
            'next' => 'chat',
            'options' => $options,
        );
    }

    /**
     * Process submitted leave message form.
     *
     * Send message to operator email and create special mail thread.
     * @param array $args Associative array of arguments. It must contains the
     *   following keys:
     *    - 'threadId': for this function this param equals to null;
     *    - 'token': for this function this param equals to null;
     *    - 'name': string, user name;
     *    - 'email': string, user email;
     *    - 'message': string, user message;
     *    - 'info': string, some info about user;
     *    - 'referrer': string, page user came from;
     *    - 'captcha': string, captcha value;
     *    - 'groupId': selected group id.
     *
     * @throws \Mibew\RequestProcessor\ThreadProcessorException Can throw an
     *   exception if captcha or email is wrong.
     */
    protected function apiProcessLeaveMessage($args)
    {
        // Check captcha
        if (Settings::get('enablecaptcha') == '1' && can_show_captcha()) {
            $captcha = $args['captcha'];
            $original = isset($_SESSION[SESSION_PREFIX . 'mibew_captcha'])
                ? $_SESSION[SESSION_PREFIX . 'mibew_captcha']
                : '';
            unset($_SESSION[SESSION_PREFIX . 'mibew_captcha']);
            if (empty($original) || empty($captcha) || $captcha != $original) {
                throw new ThreadProcessorException(
                    getlocal('The letters you typed don\'t match the letters that were shown in the picture.'),
                    ThreadProcessorException::ERROR_WRONG_CAPTCHA
                );
            }
        }

        // Get form fields
        $email = $args['email'];
        $name = $args['name'];
        $message = $args['message'];
        $info = $args['info'];
        $referrer = $args['referrer'];

        if (!MailUtils::isValidAddress($email)) {
            throw new ThreadProcessorException(
                wrong_field("Your email"),
                ThreadProcessorException::ERROR_WRONG_EMAIL
            );
        }

        // Verify group id
        $group_id = 0;
        if (Settings::get('enablegroups') == '1') {
            if (preg_match("/^\d{1,8}$/", $args['groupId']) != 0) {
                $group = group_by_id($args['groupId']);
                if ($group) {
                    $group_id = (int)$args['groupId'];
                }
            }
        }

        // Create thread for left message
        $remote_host = get_remote_host();
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
        $visitor = visitor_from_request();

        // Get message locale
        $message_locale = Settings::get('left_messages_locale');
        if (!locale_is_available($message_locale)) {
            $message_locale = get_home_locale();
        }

        // Create thread
        $thread = new Thread();
        $thread->groupId = $group_id;
        $thread->userName = $name;
        $thread->remote = $remote_host;
        $thread->referer = $referrer;
        $thread->locale = get_current_locale();
        $thread->userId = $visitor['id'];
        $thread->userAgent = $user_browser;
        $thread->state = Thread::STATE_LEFT;
        $thread->closed = time();
        $thread->save();

        // Send some messages
        if ($referrer) {
            $thread->postMessage(
                Thread::KIND_FOR_AGENT,
                getlocal('Visitor came from page {0}', array($referrer), get_current_locale(), true)
            );
        }
        if ($email) {
            $thread->postMessage(
                Thread::KIND_FOR_AGENT,
                getlocal('E-Mail: {0}', array($email), get_current_locale(), true)
            );
        }
        if ($info) {
            $thread->postMessage(
                Thread::KIND_FOR_AGENT,
                getlocal('Info: {0}', array($info), get_current_locale(), true)
            );
        }
        $thread->postMessage(Thread::KIND_USER, $message, array('name' => $name));

        // Get email for message
        $inbox_mail = get_group_email($group_id);

        if (empty($inbox_mail)) {
            $inbox_mail = Settings::get('email');
        }

        // Send email
        if ($inbox_mail) {
            // Prepare message to send by email
            $mail_template = MailTemplate::loadByName('leave_message', $message_locale);
            if (!$mail_template) {
                trigger_error(
                    'Cannot send e-mail because "leave_message" mail template cannot be loaded.',
                    E_USER_WARNING
                );

                return;
            }

            $subject = $mail_template->buildSubject(array($args['name']));
            $body = $mail_template->buildBody(array(
                $args['name'],
                $email,
                $message,
                ($info ? $info . "\n" : ""),
            ));

            // Send
            $this->getMailerFactory()->getMailer()->send(
                MailUtils::buildMessage($inbox_mail, $email, $subject, $body)
            );
        }
    }

    /**
     * Returns relative path of the avatar for operator related with the thread.
     *
     * @param array $args Associative array of arguments. It must contains the
     *   following keys:
     *    - 'threadId': ID of the thread the avatar should be retrieved for.
     *    - 'token': Token of the thread.
     * @return array Array of results. It contains following keys:
     *    - 'imageLink': string, relative path to operator's avatar.
     */
    protected function apiGetAvatar($args)
    {
        // Load thread and check thread's last token
        $thread = self::getThread($args['threadId'], $args['token']);

        $image_link = false;
        if ($thread->agentId) {
            $operator = operator_by_id($thread->agentId);
            if ($operator['vcavatar']) {
                $url_generator = $this->getAssetManager()->getUrlGenerator();
                $image_link = $url_generator->generate($operator['vcavatar']);
            }
        }

        return array('imageLink' => $image_link);
    }
}
