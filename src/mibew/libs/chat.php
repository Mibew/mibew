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

use Mibew\Asset\Generator\UrlGeneratorInterface as AssetUrlGeneratorInterface;
use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Mibew\Settings;
use Mibew\Thread;
use Mibew\Style\ChatStyle;
use Mibew\Style\PageStyle;
use Mibew\Routing\Generator\SecureUrlGeneratorInterface as UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Convert messages to formatted text
 *
 * @param array $msg message object which most be formatted
 *
 * @return string formatted message
 */

function message_to_text($msg)
{
    $message_time = date("H:i:s ", $msg['created']);
    if ($msg['kind'] == Thread::KIND_USER || $msg['kind'] == Thread::KIND_AGENT) {
        if ($msg['name']) {
            return $message_time . $msg['name'] . ": " . $msg['message'] . "\n";
        } else {
            return $message_time . $msg['message'] . "\n";
        }
    } elseif ($msg['kind'] == Thread::KIND_INFO) {
        return $message_time . $msg['message'] . "\n";
    } else {
        return $message_time . "[" . $msg['message'] . "]\n";
    }
}

/**
 * Sanitize message body and make it a safe HTML string.
 *
 * @param array $msg Message object
 * @return array Message object with sanitized body.
 */
function sanitize_message($msg)
{
    $message_body = $msg['message'];

    // Messages entered by user or operator cannot contain any markup
    if ($msg['kind'] == Thread::KIND_USER || $msg['kind'] == Thread::KIND_AGENT) {
        $message_body = safe_htmlspecialchars($message_body);
    }

    $msg['message'] = sanitize_string($message_body, 'low', 'moderate');

    return $msg;
}

/**
 * Format username
 *
 * @param string $user_name client username
 * @param string $addr ip address of client
 * @param string $id id of client
 *
 * @return string formatted username with "usernamepattern" pattern
 */
function get_user_name($user_name, $addr, $id)
{
    return str_replace(
        "{addr}",
        $addr,
        str_replace(
            "{id}",
            $id,
            str_replace("{name}", $user_name, Settings::get('usernamepattern'))
        )
    );
}

/**
 * Prepare logo data
 *
 * @param array $group Group info
 * @return array Array of logo data
 */
function setup_logo($group = null)
{
    $data = array();
    $top_level_group = (!$group) ? array() : get_top_level_group($group);

    $group_name = empty($top_level_group['vctitle'])
        ? Settings::get('title')
        : $top_level_group['vctitle'];

    $logo = empty($top_level_group['vclogo'])
        ? Settings::get('logo')
        : $top_level_group['vclogo'];

    $mibew_host = empty($top_level_group['vchosturl'])
        ? Settings::get('hosturl')
        : $top_level_group['vchosturl'];

    $data['company'] = array(
        'name' => $group_name,
        'chatLogoURL' => $logo,
    );
    $data['mibewHost'] = $mibew_host;

    return $data;
}

/**
 * Prepare values common for chat, prechat survey form and leave message form.
 * @return array
 */
function prepare_chat_app_data()
{
    $data = array();

    // Set refresh frequency
    $data['frequency'] = Settings::get('updatefrequency_chat');

    return $data;
}

/**
 * Prepare data to display leave message form
 *
 * @param string $name User name
 * @param string $email User email
 * @param int $group_id Id of selected group
 * @param string $info User info
 * @param string $referrer URL of referrer page
 * @return array Array of leave message form data
 */
function setup_leavemessage($name, $email, $group_id, $info, $referrer)
{
    $data = prepare_chat_app_data();

    // Create some empty arrays
    $data['leaveMessage'] = array();

    $group = group_by_id($group_id);
    $group_name = '';
    if ($group) {
        $group_name = get_group_name($group);
    }

    $data['leaveMessage']['leaveMessageForm'] = array(
        'name' => $name,
        'email' => $email,
        'groupId' => $group_id,
        'groupName' => $group_name,
        'info' => $info,
        'referrer' => $referrer,
        'showCaptcha' => (bool) (Settings::get("enablecaptcha") == "1" && can_show_captcha()),
    );

    $data['page.title'] = (empty($group_name) ? '' : $group_name . ': ')
        . getlocal('Leave your message');
    $data['leaveMessage']['page'] = array(
        'title' => $data['page.title']
    );

    if (Settings::get('enablegroups') == '1') {
        $data['leaveMessage']['leaveMessageForm']['groups']
            = prepare_groups_select($group_id);
    }

    $data['startFrom'] = 'leaveMessage';

    return $data;
}

/**
 * Prepare data to display pre-chat survey
 *
 * @param string $name User name
 * @param string $email User email
 * @param int $group_id Id of selected group
 * @param string $info User info
 * @param string $referrer URL of referrer page
 * @return array Array of survey data
 */
function setup_survey($name, $email, $group_id, $info, $referrer)
{
    $data = prepare_chat_app_data();

    // Create some empty arrays
    $data['survey'] = array();

    $data['survey']['surveyForm'] = array(
        'name' => $name,
        'groupId' => $group_id,
        'email' => $email,
        'info' => $info,
        'referrer' => $referrer,
        'showEmail' => (bool) (Settings::get("surveyaskmail") == "1"),
        'showMessage' => (bool) (Settings::get("surveyaskmessage") == "1"),
        'canChangeName' => (bool) (Settings::get('usercanchangename') == "1"),
    );

    $data['page.title'] = getlocal('Live support');
    $data['survey']['page'] = array(
        'title' => $data['page.title']
    );

    // Set privacy policy link (if needed)
    if (Settings::get('enableprivacypolicy')
         && strcmp('', Settings::get('privacypolicy'))) {
        $data['survey']['surveyForm']['privacyPolicyUrl'] = Settings::get('privacypolicy');
    }

    if (Settings::get('enablegroups') == '1' && Settings::get('surveyaskgroup') == '1') {
        $data['survey']['surveyForm']['groups']
            = prepare_groups_select($group_id);
    }

    $data['startFrom'] = 'survey';

    return $data;
}

/**
 * Prepare groups list to build group select box.
 *
 * If $group_id specified groups list will consist of group with id equals to
 * $group_id and its children.
 *
 * @param int $group_id Id of selected group
 * @return array|boolean Array of groups info arrays or boolean false if there
 *   are no suitable groups.
 *   Group info array contain following keys:
 *    - 'id': int, group id;
 *    - 'name': string, group name;
 *    - 'description': string, group description;
 *    - 'online': boolean, indicates if group online;
 *    - 'selected': boolean, indicates if group selected by default.
 */
function prepare_groups_select($group_id)
{
    $show_groups = ($group_id == '') ? true : group_has_children($group_id);

    if (!$show_groups) {
        return false;
    }

    $all_groups = get_groups(false);

    if (empty($all_groups)) {
        return false;
    }

    $groups_list = array();
    $selected_group_id = $group_id;

    foreach ($all_groups as $group) {
        $group_is_empty = (bool) ($group['inumofagents'] == 0);
        $group_related_with_specified = empty($group_id)
            || $group['parent'] == $group_id
            || $group['groupid'] == $group_id;

        if ($group_is_empty || !$group_related_with_specified) {
            continue;
        }

        if (group_is_online($group) && !$selected_group_id) {
            $selected_group_id = $group['groupid'];
        }

        $groups_list[] = array(
            'id' => $group['groupid'],
            'name' => get_group_name($group),
            'description' => get_group_description($group),
            'online' => group_is_online($group),
            'selected' => (bool) ($group['groupid'] == $selected_group_id),
        );
    }

    // One group must be selected by default
    if (!empty($groups_list)) {
        // Check if there is selected group
        $selected_group_present = false;
        foreach ($groups_list as $group) {
            if ($group['selected']) {
                $selected_group_present = true;
                break;
            }
        }

        // If there is no selected group select the first one
        if (!$selected_group_present) {
            $groups_list[0]['selected'] = true;
        }
    }

    return $groups_list;
}

/**
 * Prepare some data for chat for both user and operator
 *
 * @param Thread $thread thread object
 * @return array Array of chat view data
 */
function setup_chatview(Thread $thread)
{
    $data = prepare_chat_app_data();

    // Get group info
    if (!empty($thread->groupId)) {
        $group = group_by_id($thread->groupId);
        $group = get_top_level_group($group);
    } else {
        $group = array();
    }

    // Create some empty arrays
    $data['chat'] = array(
        'messageForm' => array(),
        'links' => array(),
        'windowsParams' => array(),
    );

    // Set thread params
    $data['chat']['thread'] = array(
        'id' => $thread->id,
        'token' => $thread->lastToken,
        'agentId' => $thread->agentId,
        'userId' => $thread->userId,
    );

    $data['page.title'] = empty($group['vcchattitle'])
        ? Settings::get('chattitle')
        : $group['vcchattitle'];
    $data['chat']['page'] = array(
        'title' => $data['page.title']
    );

    // Setup logo
    $data = array_merge_recursive($data, setup_logo($group));

    // Set enter key shortcut
    if (Settings::get('sendmessagekey') == 'enter') {
        $data['chat']['messageForm']['ignoreCtrl'] = true;
    } else {
        $data['chat']['messageForm']['ignoreCtrl'] = false;
    }

    // Load dialogs style options
    $chat_style = new ChatStyle(ChatStyle::getCurrentStyle());
    $style_config = $chat_style->getConfigurations();
    $data['chat']['windowsParams']['mail']
        = $style_config['mail']['window'];

    // Load core style options
    $page_style = new PageStyle(PageStyle::getCurrentStyle());
    $style_config = $page_style->getConfigurations();
    $data['chat']['windowsParams']['history']
        = $style_config['history']['window'];

    $data['startFrom'] = 'chat';

    return $data;
}

/**
 * Prepare some data for chat for user
 *
 * @param UrlGeneratorInterface $url_generator A URL generator object.
 * @param AssetUrlGeneratorInterface $asset_url_generator An asset URL generator
 * object.
 * @param Request $request The current request.
 * @param Thread $thread thread object that will be used
 * @return array Array of chat view data
 */
function setup_chatview_for_user(
    UrlGeneratorInterface $url_generator,
    AssetUrlGeneratorInterface $asset_url_generator,
    Request $request,
    Thread $thread
) {
    $data = setup_chatview($thread);

    // Set user info
    $data['chat']['user'] = array(
        'name' => htmlspecialchars($thread->userName),
        'canChangeName' => (bool) (Settings::get('usercanchangename') == "1"),
        'defaultName' => (bool) (getlocal("Guest") != $thread->userName),
        'canPost' => true,
        'isAgent' => false,
    );

    // Set link to send mail page
    $data['chat']['links']['mail'] = $url_generator->generate(
        'chat_user_mail',
        array(
            'thread_id' => $thread->id,
            'token' => $thread->lastToken,
        )
    );

    // Set SSL link
    if (Settings::get('enablessl') == "1" && $request->isSecure()) {
        $data['chat']['links']['ssl'] = $url_generator->generateSecure(
            'chat_user',
            array(
                'thread_id' => $thread->id,
                'token' => $thread->lastToken,
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    // Set chat link
    $data['chat']['links']['chat'] = $url_generator->generate(
        'chat_user',
        array(
            'thread_id' => $thread->id,
            'token' => $thread->lastToken,
        ),
        UrlGeneratorInterface::ABSOLUTE_URL
    );

    // Set default operator's avatar
    $operator = operator_by_id($thread->agentId);
    $data['chat']['avatar'] = $operator['vcavatar']
        ? $asset_url_generator->generate($operator['vcavatar'])
        : '';


    return $data;
}

/**
 * Prepare some data for chat for operator
 *
 * @param UrlGeneratorInterface $url_generator A URL generator object.
 * @param Request $request The current request.
 * @param Thread $thread thread object.
 * @param array $operator Operator's data.
 * @return array Array of chat view data
 */
function setup_chatview_for_operator(
    UrlGeneratorInterface $url_generator,
    Request $request,
    Thread $thread,
    $operator
) {
    $data = setup_chatview($thread);

    // Set operator info
    $data['chat']['user'] = array(
        'name' => htmlspecialchars(
            get_user_name(
                $thread->userName,
                $thread->remote,
                $thread->userId
            )
        ),
        'canPost' => (bool) ($thread->agentId == $operator['operatorid']),
        'isAgent' => true,
    );

    // Set SSL link
    if (Settings::get('enablessl') == "1" && !$request->isSecure()) {
        $data['chat']['links']['ssl'] = $url_generator->generateSecure(
            'chat_operator',
            array(
                'thread_id' => $thread->id,
                'token' => $thread->lastToken,
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    // Set chat link
    $data['chat']['links']['chat'] = $url_generator->generate(
        'chat_operator',
        array(
            'thread_id' => $thread->id,
            'token' => $thread->lastToken,
        ),
        UrlGeneratorInterface::ABSOLUTE_URL
    );

    // Set history window params
    $data['chat']['links']['history'] = $url_generator->generate(
        'history_user',
        array('user_id' => $thread->userId)
    );

    // Set tracking params
    if (Settings::get('enabletracking')) {
        $visitor = track_get_visitor_by_thread_id($thread->id);
        $data['chat']['links']['tracked'] = $url_generator->generate(
            'history_user_track',
            array('visitor' => $visitor['visitorid'])
        );
    }

    // Check if agent can post messages
    if ($thread->agentId == $operator['operatorid']) {
        // Get predefined answers
        $canned_messages = load_canned_messages($thread->locale, 0);
        if ($thread->groupId) {
            $canned_messages = array_merge(
                load_canned_messages($thread->locale, $thread->groupId),
                $canned_messages
            );
        };

        $predefined_answers = array();
        foreach ($canned_messages as $answer) {
            $predefined_answers[] = array(
                'short' => htmlspecialchars(
                    $answer['vctitle'] ? $answer['vctitle'] : cut_string($answer['vcvalue'], 97, '...')
                ),
                'full' => $answer['vcvalue'],
            );
        }
        $data['chat']['messageForm']['predefinedAnswers'] = $predefined_answers;
    }
    // Set link to user redirection page
    $data['chat']['links']['redirect'] = $url_generator->generate(
        'chat_operator_redirection_links',
        array(
            'thread_id' => $thread->id,
            'token' => $thread->lastToken,
        )
    );

    return $data;
}

/**
 * @return array Return visitor info from active request. contains
 * (user_id string, user_name string)
 */
function visitor_from_request()
{
    $default_name = getlocal("Guest");
    $user_name = $default_name;
    if (isset($_COOKIE[USERNAME_COOKIE_NAME])) {
        $data = base64_decode(strtr($_COOKIE[USERNAME_COOKIE_NAME], '-_,', '+/='));
        if (strlen($data) > 0) {
            $user_name = $data;
        }
    }

    if ($user_name == $default_name) {
        $user_name = get_get_param('name', $user_name);
    }

    if (isset($_COOKIE[USERID_COOKIE_NAME])) {
        $user_id = $_COOKIE[USERID_COOKIE_NAME];
    } else {
        $user_id = uniqid('', true);
        setcookie(USERID_COOKIE_NAME, $user_id, time() + 60 * 60 * 24 * 365);
    }

    return array('id' => $user_id, 'name' => $user_name);
}

/**
 * @return string Return remote host from active request
 */
function get_remote_host()
{
    $ext_addr = $_SERVER['REMOTE_ADDR'];
    $has_proxy = isset($_SERVER['HTTP_X_FORWARDED_FOR'])
        && $_SERVER['HTTP_X_FORWARDED_FOR'] != $_SERVER['REMOTE_ADDR'];
    if ($has_proxy) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'], 2);
        $ext_addr = (count($ips) > 1)
            ? $ips[0] . ' (' . $_SERVER['HTTP_X_FORWARDED_FOR'] . ')'
            : $ips[0];
    }

    return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : $ext_addr;
}

/**
 * Start chat thread for user
 *
 * @param int $group_id Id of group related to thread
 * @param array $requested_operator Array of requested operator info
 * @param string $visitor_id Id of the visitor
 * @param string $visitor_name Name of the visitor
 * @param string $referrer Page user came from
 * @param string $info User info
 *
 * @return Thread thread object
 */
function chat_start_for_user(
    $group_id,
    $requested_operator,
    $visitor_id,
    $visitor_name,
    $referrer,
    $info
) {
    // Get user info
    $remote_host = get_remote_host();
    $user_browser = $_SERVER['HTTP_USER_AGENT'];

    // Check connection limit
    if (Thread::connectionLimitReached($remote_host)) {
        die("number of connections from your IP is exceeded, try again later");
    }

    // Check if visitor was invited to chat
    $is_invited = false;
    if (Settings::get('enabletracking')) {
        $invitation_state = invitation_state($_SESSION[SESSION_PREFIX . 'visitorid']);
        if ($invitation_state['invited']) {
            $is_invited = true;
        }
    }

    // Get info about requested operator
    $requested_operator_online = false;
    if ($requested_operator) {
        $requested_operator_online = is_operator_online(
            $requested_operator['operatorid']
        );
    }

    // Get thread object
    if ($is_invited) {
        // Get thread from invitation
        $thread = invitation_accept($_SESSION[SESSION_PREFIX . 'visitorid']);
        if (!$thread) {
            die("Cannot start thread");
        }
    } else {
        // Create thread
        $thread = new Thread();
        $thread->state = Thread::STATE_LOADING;
        $thread->agentId = 0;
        if ($requested_operator && $requested_operator_online) {
            $thread->nextAgent = $requested_operator['operatorid'];
        }
    }

    // Update thread fields
    $thread->groupId = $group_id;
    $thread->userName = $visitor_name;
    $thread->remote = $remote_host;
    $thread->referer = $referrer;
    $thread->locale = get_current_locale();
    $thread->userId = $visitor_id;
    $thread->userAgent = $user_browser;
    $thread->save();

    $_SESSION[SESSION_PREFIX . 'threadid'] = $thread->id;

    // Store own thread ids to restrict access for other people
    if (!isset($_SESSION[SESSION_PREFIX . 'own_threads'])) {
        $_SESSION[SESSION_PREFIX . 'own_threads'] = array();
    }
    $_SESSION[SESSION_PREFIX . 'own_threads'][] = $thread->id;

    // Bind thread to the visitor
    if (Settings::get('enabletracking')) {
        track_visitor_bind_thread($visitor_id, $thread);
    }

    // Send several messages
    if ($is_invited) {
        $operator = operator_by_id($thread->agentId);
        $operator_name = get_operator_name($operator);
        $thread->postMessage(
            Thread::KIND_FOR_AGENT,
            getlocal(
                'Visitor accepted invitation from operator {0}',
                array($operator_name),
                get_current_locale(),
                true
            )
        );
    } else {
        if ($referrer) {
            $thread->postMessage(
                Thread::KIND_FOR_AGENT,
                getlocal('Visitor came from page {0}', array($referrer), get_current_locale(), true)
            );
        }
        if ($requested_operator && !$requested_operator_online) {
            $thread->postMessage(
                Thread::KIND_INFO,
                getlocal(
                    'Thank you for contacting us. We are sorry, but requested operator <strong>{0}</strong> is offline. Another operator will be with you shortly.',
                    array(get_operator_name($requested_operator)),
                    get_current_locale(),
                    true
                )
            );
        } else {
            $thread->postMessage(
                Thread::KIND_INFO,
                getlocal('Thank you for contacting us. An operator will be with you shortly.', null, get_current_locale(), true)
            );
        }
    }

    // TODO: May be move sending this message somewhere else?
    if ($info) {
        $thread->postMessage(
            Thread::KIND_FOR_AGENT,
            getlocal('Info: {0}', array($info), get_current_locale(), true)
        );
    }

    // Let plugins know that user is ready to chat.
    $dispatcher = EventDispatcher::getInstance();
    $event_args = array('thread' => $thread);
    $dispatcher->triggerEvent(Events::THREAD_USER_IS_READY, $event_args);

    return $thread;
}
