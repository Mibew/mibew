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
use Mibew\Thread;
use Mibew\Style\ChatStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');

$operator = check_login();

$thread_id = verify_param("thread", "/^\d{1,8}$/");
$token = verify_param("token", "/^\d{1,8}$/");

$thread = Thread::load($thread_id, $token);
if (!$thread) {
    die("wrong thread");
}

$page = array(
    'errors' => array(),
);

// Initialize chat style which is currently used in system
$chat_style = new ChatStyle(ChatStyle::getCurrentStyle());

if (isset($_GET['nextGroup'])) {
    $next_id = verify_param("nextGroup", "/^\d{1,8}$/");
    $next_group = group_by_id($next_id);

    if ($next_group) {
        $page['message'] = getlocal2(
            "chat.redirected.group.content",
            array(get_group_name($next_group))
        );
        if ($thread->state == Thread::STATE_CHATTING) {
            $thread->state = Thread::STATE_WAITING;
            $thread->nextAgent = 0;
            $thread->groupId = $next_id;
            $thread->agentId = 0;
            $thread->agentName = '';
            $thread->save();

            $thread->postMessage(
                Thread::KIND_EVENTS,
                getstring2_(
                    "chat.status.operator.redirect",
                    array(get_operator_name($operator)),
                    $thread->locale,
                    true
                )
            );
        } else {
            $page['errors'][] = getlocal("chat.redirect.cannot");
        }
    } else {
        $page['errors'][] = "Unknown group";
    }
} else {
    $next_id = verify_param("nextAgent", "/^\d{1,8}$/");
    $next_operator = operator_by_id($next_id);

    if ($next_operator) {
        $page['message'] = getlocal2(
            "chat.redirected.content",
            array(get_operator_name($next_operator))
        );
        if ($thread->state == Thread::STATE_CHATTING) {
            $thread->state = Thread::STATE_WAITING;
            $thread->nextAgent = $next_id;
            $thread->agentId = 0;
            if ($thread->groupId != 0) {
                $db = Database::getInstance();
                list($groups_count) = $db->query(
                    ("SELECT count(*) AS count "
                        . "FROM {chatgroupoperator} "
                        . "WHERE operatorid = ? AND groupid = ?"),
                    array($next_id, $thread->groupId),
                    array(
                        'return_rows' => Database::RETURN_ONE_ROW,
                        'fetch_type' => Database::FETCH_NUM,
                    )
                );
                if ($groups_count === 0) {
                    $thread->groupId = 0;
                }
            }
            $thread->save();
            $thread->postMessage(
                Thread::KIND_EVENTS,
                getstring2_(
                    "chat.status.operator.redirect",
                    array(get_operator_name($operator)),
                    $thread->locale,
                    true
                )
            );
        } else {
            $page['errors'][] = getlocal("chat.redirect.cannot");
        }
    } else {
        $page['errors'][] = "Unknown operator";
    }
}

$page = array_merge_recursive($page, setup_logo());

if (count($page['errors']) > 0) {
    $chat_style->render('error', $page);
} else {
    $chat_style->render('redirected', $page);
}
