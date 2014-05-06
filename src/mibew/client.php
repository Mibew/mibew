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
use Mibew\Settings;
use Mibew\Thread;
use Mibew\Style\ChatStyle;

// Initialize libraries
require_once(dirname(__FILE__) . '/libs/init.php');

if (Settings::get('enablessl') == "1" && Settings::get('forcessl') == "1") {
    if (!is_secure_request()) {
        $requested = $_SERVER['PHP_SELF'];
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['QUERY_STRING']) {
            header("Location: " . get_app_location(true, true) . "/client.php?" . $_SERVER['QUERY_STRING']);
        } else {
            die("only https connections are handled");
        }
        exit;
    }
}

// Initialize chat style which is currently used in system
$chat_style = new ChatStyle(ChatStyle::getCurrentStyle());

// Do not support old browsers at all
if (get_remote_level($_SERVER['HTTP_USER_AGENT']) == 'old') {
    // Create page array
    $page = array_merge_recursive(
        setup_logo()
    );
    $chat_style->render('nochat', $page);
    exit;
}

$action = verify_param("act", "/^(invitation|mailthread)$/", "default");

if ($action == 'invitation' && Settings::get('enabletracking')) {
    // Check if user invited to chat
    $invitation_state = invitation_state($_SESSION['visitorid']);

    if ($invitation_state['invited'] && $invitation_state['threadid']) {
        $thread = Thread::load($invitation_state['threadid']);

        // Prepare page
        $page = setup_invitation_view($thread);

        // Build js application options
        $page['invitationOptions'] = json_encode($page['invitation']);
        // Expand page
        $chat_style->render('chat', $page);
        exit;
    }
}

if (!isset($_GET['token']) || !isset($_GET['thread'])) {

    $thread = null;
    if (isset($_SESSION['threadid'])) {
        $thread = Thread::reopen($_SESSION['threadid']);
    }

    if (!$thread) {

        // Load group info
        $group_id = "";
        $group_name = "";
        $group = null;
        if (Settings::get('enablegroups') == '1') {
            $group_id = verify_param("group", "/^\d{1,8}$/", "");
            if ($group_id) {
                $group = group_by_id($group_id);
                if (!$group) {
                    $group_id = "";
                } else {
                    $group_name = get_group_name($group);
                }
            }
        }

        // Get operator code
        $operator_code = empty($_GET['operator_code']) ? '' : $_GET['operator_code'];
        if (!preg_match("/^[A-z0-9_]+$/", $operator_code)) {
            $operator_code = false;
        }

        // Get visitor info
        $visitor = visitor_from_request();
        $info = get_get_param('info');
        $email = get_get_param('email');

        // Get referrer
        $referrer = isset($_GET['url'])
            ? $_GET['url']
            : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "");

        if (isset($_GET['referrer']) && $_GET['referrer']) {
            $referrer .= "\n" . $_GET['referrer'];
        }

        // Check if there are online operators
        if (!has_online_operators($group_id)) {
            // Display leave message page
            $page = array_merge_recursive(
                setup_logo($group),
                setup_leavemessage(
                    $visitor['name'],
                    $email,
                    $group_id,
                    $info,
                    $referrer
                )
            );
            $page['leaveMessageOptions'] = json_encode($page['leaveMessage']);
            $chat_style->render('chat', $page);
            exit;
        }

        // Get invitation info
        if (Settings::get('enabletracking')) {
            $invitation_state = invitation_state($_SESSION['visitorid']);
            $visitor_is_invited = $invitation_state['invited'];
        } else {
            $visitor_is_invited = false;
        }

        // Get operator info
        $requested_operator = false;
        if ($operator_code) {
            $requested_operator = operator_by_code($operator_code);
        }

        // Check if survey should be displayed
        if (Settings::get('enablepresurvey') == '1' && !$visitor_is_invited && !$requested_operator) {
            // Display prechat survey
            $page = array_merge_recursive(
                setup_logo($group),
                setup_survey(
                    $visitor['name'],
                    $email,
                    $group_id,
                    $info,
                    $referrer
                )
            );
            $page['surveyOptions'] = json_encode($page['survey']);
            $chat_style->render('chat', $page);
            exit;
        }

        // Start chat thread
        $thread = chat_start_for_user(
            $group_id,
            $requested_operator,
            $visitor['id'],
            $visitor['name'],
            $referrer,
            $info
        );
    }
    $thread_id = $thread->id;
    $token = $thread->lastToken;
    $chat_style_name = verify_param("style", "/^\w+$/", "");
    $redirect_to = MIBEW_WEB_ROOT . "/client.php?thread=" . intval($thread_id)
        . "&token=" . urlencode($token)
        . ($chat_style_name ? "&style=" . urlencode($chat_style_name) : "");
    header("Location: " . $redirect_to);
    exit;
}

$token = verify_param("token", "/^\d{1,8}$/");
$thread_id = verify_param("thread", "/^\d{1,8}$/");

$thread = Thread::load($thread_id, $token);
if (!$thread) {
    die("wrong thread");
}

$page = setup_chatview_for_user($thread);

if ($action == "mailthread") {
    $chat_style->render('mail', $page);
} else {
    // Build js application options
    $page['chatOptions'] = json_encode($page['chat']);
    // Expand page
    $chat_style->render('chat', $page);
}
