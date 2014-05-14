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
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');

$operator = check_login();

if (Settings::get('enablessl') == "1" && Settings::get('forcessl') == "1") {
    if (!is_secure_request()) {
        $requested = $_SERVER['PHP_SELF'];
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['QUERY_STRING']) {
            header("Location: " . get_app_location(true, true) . "/operator/agent.php?" . $_SERVER['QUERY_STRING']);
        } else {
            die("only https connections are handled");
        }
        exit;
    }
}

$thread_id = verify_param("thread", "/^\d{1,8}$/");
$page = array(
    'errors' => array(),
);

// Initialize chat style which is currently used in system
$chat_style = new ChatStyle(ChatStyle::getCurrentStyle());

$page_style = new PageStyle(PageStyle::getCurrentStyle());

if (!isset($_GET['token'])) {

    $remote_level = get_remote_level($_SERVER['HTTP_USER_AGENT']);
    if ($remote_level != "ajaxed") {
        $page['errors'][] = getlocal("thread.error.old_browser");
        $chat_style->render('error', $page);
        exit;
    }

    $thread = Thread::load($thread_id);
    if (!$thread || !isset($thread->lastToken)) {
        $page['errors'][] = getlocal("thread.error.wrong_thread");
        $chat_style->render('error', $page);
        exit;
    }

    $view_only = verify_param("viewonly", "/^true$/", false);

    $force_take = verify_param("force", "/^true$/", false);
    if (!$view_only && $thread->state == Thread::STATE_CHATTING && $operator['operatorid'] != $thread->agentId) {

        if (!is_capable(CAN_TAKEOVER, $operator)) {
            $page['errors'][] = getlocal("thread.error.cannot_take_over");
            $chat_style->render('error', $page);
            exit;
        }

        if ($force_take == false) {
            $page = array(
                'user' => $thread->userName,
                'agent' => $thread->agentName,
                'link' => $_SERVER['PHP_SELF'] . "?thread=$thread_id&force=true",
                'title' => getlocal("confirm.take.head"),
            );
            $page_style->render('confirm', $page);
            exit;
        }
    }

    if (!$view_only) {
        if (!$thread->take($operator)) {
            $page['errors'][] = getlocal("thread.error.cannot_take");
            $chat_style->render('error', $page);
            exit;
        }
    } elseif (!is_capable(CAN_VIEWTHREADS, $operator)) {
        $page['errors'][] = getlocal("thread.error.cannot_view");
        $chat_style->render('error', $page);
        exit;
    }

    $token = $thread->lastToken;
    $redirect_to = MIBEW_WEB_ROOT . "/operator/agent.php?thread="
        . intval($thread_id) . "&token=" . urlencode($token);
    header("Location: " . $redirect_to);
    exit;
}

$token = verify_param("token", "/^\d{1,8}$/");

$thread = Thread::load($thread_id, $token);
if (!$thread) {
    die("wrong thread");
}

if ($thread->agentId != $operator['operatorid'] && !is_capable(CAN_VIEWTHREADS, $operator)) {
    $page['errors'][] = "Cannot view threads";
    $chat_style->render('error', $page);
    exit;
}

$page = array_merge_recursive(
    $page,
    setup_chatview_for_operator($thread, $operator)
);

start_html_output();

$pparam = verify_param("act", "/^(redirect)$/", "default");
if ($pparam == "redirect") {
    $page = array_merge_recursive(
        $page,
        setup_redirect_links($thread_id, $operator, $token)
    );
    $chat_style->render('redirect', $page);
} else {
    // Build js application options
    $page['chatOptions'] = json_encode($page['chat']);
    // Render the page
    $chat_style->render('chat', $page);
}
