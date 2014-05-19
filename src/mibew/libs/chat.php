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

// Import namespaces and classes of the core
use Mibew\Database;
use Mibew\Settings;
use Mibew\Thread;
use Mibew\Style\ChatStyle;
use Mibew\Style\PageStyle;

/**
 * Convert messages to formated text
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
 * Check if browser support ajax requests
 *
 * @param array $browser_id name of browser
 * @param array $ver browser version
 * @param array $user_agent user agent of browser
 *
 * @return bool true on ajax support and false if ajax is not supported
 */
function is_ajax_browser($browser_id, $ver, $user_agent)
{
    if ($browser_id == "opera") {
        return $ver >= 8.02;
    }
    if ($browser_id == "safari") {
        return $ver >= 125;
    }
    if ($browser_id == "msie") {
        return $ver >= 5.5 && !strstr($user_agent, "powerpc");
    }
    if ($browser_id == "netscape") {
        return $ver >= 7.1;
    }
    if ($browser_id == "mozilla") {
        return $ver >= 1.4;
    }
    if ($browser_id == "firefox") {
        return $ver >= 1.0;
    }
    if ($browser_id == "chrome") {
        return true;
    }

    return false;
}

/**
 * Check if browser support ajax requests
 *
 * @param array $browser_id Code name of browser
 * @param array $ver Browser version
 * @param array $user_agent User Agent of browser
 *
 * @return bool true on ajax support and false if ajax is not supported
 */
function get_remote_level($user_agent)
{
    $known_agents = get_known_user_agents();
    $user_agent = strtolower($user_agent);
    foreach ($known_agents as $agent) {
        if (strstr($user_agent, $agent)) {
            if (preg_match("/" . $agent . "[\\s\/]?(\\d+(\\.\\d+)?)/", $user_agent, $matches)) {
                $ver = $matches[1];

                if (is_ajax_browser($agent, $ver, $user_agent)) {
                    return "ajaxed";
                } else {
                    return "old";
                }
            }
        }
    }

    return "ajaxed";
}

/**
 * Returns a list of known user agents code names.
 *
 * @return array List of known user agents
 */
function get_known_user_agents()
{
    return array(
        "opera",
        "msie",
        "chrome",
        "safari",
        "firefox",
        "netscape",
        "mozilla",
    );
}

/**
 * Check if browser is opera 9.5 or greater
 *
 * @return bool Result of comparison of visitor browser and Opera 9.5
 */
function is_agent_opera95()
{
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strstr($user_agent, "opera")) {
        if (preg_match("/opera[\\s\/]?(\\d+(\\.\\d+)?)/", $user_agent, $matches)) {
            $ver = $matches[1];

            if ($ver >= "9.5") {
                return true;
            }
        }
    }

    return false;
}

/**
 * Check if browser is opera with mac os
 *
 * @return bool Result of comparison of visitor browser and Opera_on_mac
 */
function is_mac_opera()
{
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

    return strstr($user_agent, "opera") && strstr($user_agent, "mac");
}

/**
 * Check if frame src needed
 *
 * @return bool True if frame is necessary otherwise return False
 */
function needs_frame_src()
{
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

    return strstr($user_agent, "safari/");
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

    // Set enter key shortcut
    if (Settings::get('sendmessagekey') == 'enter') {
        $data['send_shortcut'] = "Enter";
    } else {
        $data['send_shortcut'] = is_mac_opera() ? "&#8984;-Enter" : "Ctrl-Enter";
    }

    // Set refresh frequency
    $data['frequency'] = Settings::get('updatefrequency_chat');

    // Set some localized strings
    $data['localized'] = array(
        'email.required' => no_field("form.field.email"),
        'name.required' => no_field("form.field.name"),
        'message.required' => no_field("form.field.message"),
        'wrong.email' => wrong_field("form.field.email"),
    );

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

    // Load JavaScript plugins and JavaScripts, CSS files required by them
    $data = array_merge_recursive($data, get_plugins_data('client_chat_window'));

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
        . getlocal('leavemessage.title');
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
 * Prepare data to dispaly pre-chat survey
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

    // Load JavaScript plugins and JavaScripts, CSS files required by them
    $data = array_merge_recursive($data, get_plugins_data('client_chat_window'));

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

    $data['page.title'] = getlocal('presurvey.title');
    $data['survey']['page'] = array(
        'title' => $data['page.title']
    );

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
        'token' => $thread->lastToken
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

    // Set some browser info
    $data['isOpera95'] = is_agent_opera95();
    $data['neediframesrc'] = needs_frame_src();

    // Load dialogs style options
    $chat_style = new ChatStyle(ChatStyle::getCurrentStyle());
    $style_config = $chat_style->getConfigurations();
    $data['chat']['windowsParams']['mail']
        = $style_config['mail']['window_params'];

    // Load core style options
    $page_style = new PageStyle(PageStyle::getCurrentStyle());
    $style_config = $page_style->getConfigurations();
    $data['chat']['windowsParams']['history']
        = $style_config['history']['window_params'];

    $data['startFrom'] = 'chat';

    return $data;
}

/**
 * Prepare some data for chat for user
 *
 * @param Thread $thread thread object that will be used
 * @return array Array of chat view data
 */
function setup_chatview_for_user(Thread $thread)
{
    $data = setup_chatview($thread);

    // Load JavaScript plugins and JavaScripts, CSS files required by them
    $data = array_merge_recursive($data, get_plugins_data('client_chat_window'));

    // Set user info
    $data['chat']['user'] = array(
        'name' => htmlspecialchars($thread->userName),
        'canChangeName' => (bool) (Settings::get('usercanchangename') == "1"),
        'defaultName' => (bool) (getstring("chat.default.username") != $thread->userName),
        'canPost' => true,
        'isAgent' => false,
    );

    $params = "thread=" . $thread->id . "&amp;token=" . $thread->lastToken;

    // Set link to send mail page
    $data['chat']['links']['mail'] = MIBEW_WEB_ROOT . "/client.php?"
        . $params
        . "&amp;act=mailthread";

    // Set SSL link
    if (Settings::get('enablessl') == "1" && !is_secure_request()) {
        $data['chat']['links']['ssl'] = get_app_location(true, true)
            . "/client.php?"
            . $params;
    }

    return $data;
}

/**
 * Prepare some data for chat for operator
 *
 * @param Thread $thread thread object
 * @return array Array of chat view data
 */
function setup_chatview_for_operator(Thread $thread, $operator)
{
    $data = setup_chatview($thread);

    // Load JavaScript plugins and JavaScripts, CSS files required by them
    $data = array_merge_recursive($data, get_plugins_data('agent_chat_window'));

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
    if (Settings::get('enablessl') == "1" && !is_secure_request()) {
        $data['chat']['links']['ssl'] = get_app_location(true, true)
            . "/operator/agent.php?thread="
            . $thread->id
            . "&amp;token="
            . $thread->lastToken;
    }

    // Set history window params
    $data['chat']['links']['history'] = MIBEW_WEB_ROOT
        . '/operator/history/user/'
        . ((string) $thread->userId);

    // Set tracking params
    if (Settings::get('enabletracking')) {
        $visitor = track_get_visitor_by_thread_id($thread->id);
        $tracked_link_params = array("visitor" => "" . $visitor['visitorid']);
        $data['chat']['links']['tracked'] = add_params(
            MIBEW_WEB_ROOT . "/operator/history/user-track",
            $tracked_link_params
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
    $params = "thread=" . $thread->id . "&amp;token=" . $thread->lastToken;
    $data['chat']['links']['redirect'] = MIBEW_WEB_ROOT . "/operator/agent.php?"
        . $params
        . "&amp;act=redirect";

    $data['namePostfix'] = "";

    return $data;
}

/**
 * Check if the address is banned
 *
 * @param string $addr IP address which most be checked
 *
 * @return null|array It is banned address structure. contains (banid string,
 * comment string)
 */
function ban_for_addr($addr)
{
    $db = Database::getInstance();
    return $db->query(
        "SELECT banid,comment FROM {chatban} WHERE dtmtill > :now AND address = :addr",
        array(
            ':addr' => $addr,
            ':now' => time(),
        ),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );
}

/**
 * @return array Return visitor info from active request. contains
 * (user_id string, user_name string)
 */
function visitor_from_request()
{
    $default_name = getstring("chat.default.username");
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
 * @return array Return remote host from active request. contains
 * (user_id string, user_name string)
 */
function get_remote_host()
{
    $ext_addr = $_SERVER['REMOTE_ADDR'];
    $has_proxy = isset($_SERVER['HTTP_X_FORWARDED_FOR'])
        && $_SERVER['HTTP_X_FORWARDED_FOR'] != $_SERVER['REMOTE_ADDR'];
    if ($has_proxy) {
        $ext_addr = $_SERVER['REMOTE_ADDR'] . ' (' . $_SERVER['HTTP_X_FORWARDED_FOR'] . ')';
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
        $invitation_state = invitation_state($_SESSION['visitorid']);
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
        $thread = invitation_accept($_SESSION['visitorid']);
        if (!$thread) {
            die("Cannot start thread");
        }
        $thread->state = Thread::STATE_CHATTING;
    } else {
        // Create thread
        $thread = Thread::create();
        $thread->state = Thread::STATE_LOADING;
        if ($requested_operator && $requested_operator_online) {
            $thread->nextAgent = $requested_operator['operatorid'];
        }
    }

    // Update thread fields
    $thread->groupId = $group_id;
    $thread->userName = $visitor_name;
    $thread->remote = $remote_host;
    $thread->referer = $referrer;
    $thread->locale = CURRENT_LOCALE;
    $thread->userId = $visitor_id;
    $thread->userAgent = $user_browser;
    $thread->save();

    $_SESSION['threadid'] = $thread->id;

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
            getstring2(
                'chat.visitor.invitation.accepted',
                array($operator_name),
                true
            )
        );
    } else {
        if ($referrer) {
            $thread->postMessage(
                Thread::KIND_FOR_AGENT,
                getstring2('chat.came.from', array($referrer), true)
            );
        }
        if ($requested_operator && !$requested_operator_online) {
            $thread->postMessage(
                Thread::KIND_INFO,
                getstring2(
                    'chat.requested_operator.offline',
                    array(get_operator_name($requested_operator)),
                    true
                )
            );
        } else {
            $thread->postMessage(Thread::KIND_INFO, getstring('chat.wait', true));
        }
    }

    // TODO: May be move sending this message somewhere else?
    if ($info) {
        $thread->postMessage(
            Thread::KIND_FOR_AGENT,
            getstring2('chat.visitor.info', array($info), true)
        );
    }

    return $thread;
}
