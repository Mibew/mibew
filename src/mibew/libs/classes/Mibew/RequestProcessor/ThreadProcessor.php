<?php
/*
 * Copyright 2005-2014 the original author or authors.
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
use Mibew\Settings;
use Mibew\Thread;
use Mibew\API\API as MibewAPI;
use Mibew\RequestProcessor\Exception\ThreadProcessorException;

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
 */
class ThreadProcessor extends ClientSideProcessor
{
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
     * Check if operator logged in
     *
     * @return array Operators info array
     * @throws \Mibew\RequestProcessor\ThreadProcessorException If operator is
     *   not logged in.
     */
    public static function checkOperator()
    {
        $operator = get_logged_in();
        if (!$operator) {
            throw new ThreadProcessorException(
                "Operator is not logged in!",
                ThreadProcessorException::ERROR_AGENT_NOT_LOGGED_IN
            );
        }

        return $operator;
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
        // Check recipient argument existance
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
     */
    protected function apiUpdate($args)
    {
        // Load thread
        $thread = self::getThread($args['threadId'], $args['token']);

        // Check variables
        self::checkParams($args, array('user', 'typed'));

        if (!$args['user']) {
            $operator = self::checkOperator();
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
            $is_typing = abs($thread->lastPingAgent - time()) < Thread::CONNECTION_TIMEOUT
                && $thread->agentTyping;
        } else {
            $is_typing = abs($thread->lastPingUser - time()) < Thread::CONNECTION_TIMEOUT
                && $thread->userTyping;
        }
        $can_post = $args['user'] || $operator['operatorid'] == $thread->agentId;

        return array(
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
            self::checkOperator();
        }

        // Send new messages
        $last_message_id = $args['lastId'];
        $messages = $thread->getMessages($args['user'], $last_message_id);
        if (empty($messages)) {
            $messages = array();
        }

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
            $operator = self::checkOperator();
        }

        // Check message can be sent
        if (!$args['user'] && $operator['operatorid'] != $thread->agentId) {
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
            $operator = self::checkOperator();
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
        $group_id = '';
        $group = null;
        if (Settings::get('enablegroups') == '1') {
            if (preg_match("/^\d{1,8}$/", $args['groupId']) != 0) {
                $group = group_by_id($args['groupId']);
                if ($group) {
                    $group_id = $args['groupId'];
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
                getstring2('chat.visitor.email', array($email), true)
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
        $client_data = setup_chatview_for_user($thread);
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
     * Send message to operator email and create special meil thread.
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
            $original = isset($_SESSION["mibew_captcha"])
                ? $_SESSION["mibew_captcha"]
                : '';
            unset($_SESSION['mibew_captcha']);
            if (empty($original) || empty($captcha) || $captcha != $original) {
                throw new ThreadProcessorException(
                    getlocal('errors.captcha'),
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

        if (!is_valid_email($email)) {
            throw new ThreadProcessorException(
                wrong_field("form.field.email"),
                ThreadProcessorException::ERROR_WRONG_EMAIL
            );
        }

        // Verify group id
        $group_id = '';
        if (Settings::get('enablegroups') == '1') {
            if (preg_match("/^\d{1,8}$/", $args['groupId']) != 0) {
                $group = group_by_id($args['groupId']);
                if ($group) {
                    $group_id = $args['groupId'];
                }
            }
        }

        // Create thread for left message
        $remote_host = get_remote_host();
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
        $visitor = visitor_from_request();

        // Get message locale
        $message_locale = Settings::get('left_messages_locale');
        if (!locale_exists($message_locale)) {
            $message_locale = HOME_LOCALE;
        }

        // Create thread
        $thread = Thread::create();
        $thread->groupId = $group_id;
        $thread->userName = $name;
        $thread->remote = $remote_host;
        $thread->referer = $referrer;
        $thread->locale = CURRENT_LOCALE;
        $thread->userId = $visitor['id'];
        $thread->userAgent = $user_browser;
        $thread->state = Thread::STATE_LEFT;
        $thread->closed = time();
        $thread->save();

        // Send some messages
        if ($referrer) {
            $thread->postMessage(
                Thread::KIND_FOR_AGENT,
                getstring2('chat.came.from', array($referrer), true)
            );
        }
        if ($email) {
            $thread->postMessage(
                Thread::KIND_FOR_AGENT,
                getstring2('chat.visitor.email', array($email), true)
            );
        }
        if ($info) {
            $thread->postMessage(
                Thread::KIND_FOR_AGENT,
                getstring2('chat.visitor.info', array($info), true)
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
            $subject = getstring2_(
                "leavemail.subject",
                array($args['name']),
                $message_locale
            );
            $body = getstring2_(
                "leavemail.body",
                array(
                    $args['name'],
                    $email,
                    $message,
                    $info ? $info . "\n" : ""
                ),
                $message_locale
            );

            // Send
            mibew_mail($inbox_mail, $email, $subject, $body);
        }
    }
}
