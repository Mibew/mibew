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
use Mibew\Thread;
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');
require_once(MIBEW_FS_ROOT . '/libs/operator.php');
require_once(MIBEW_FS_ROOT . '/libs/chat.php');
require_once(MIBEW_FS_ROOT . '/libs/groups.php');
require_once(MIBEW_FS_ROOT . '/libs/userinfo.php');
require_once(MIBEW_FS_ROOT . '/libs/track.php');

$operator = check_login();

$page = array();

setlocale(LC_TIME, getstring("time.locale"));

if (isset($_GET['threadid'])) {
    // Load thread info
    $thread_id = verify_param("threadid", "/^(\d{1,9})?$/", "");
    $thread = Thread::load($thread_id);
    $group = group_by_id($thread->groupId);

    $thread_info = array(
        'userName' => to_page($thread->userName),
        'userAddress' => get_user_addr(to_page($thread->remote)),
        'userAgentVersion' => get_user_agent_version(to_page($thread->userAgent)),
        'agentName' => to_page($thread->agentName),
        'chatTime' => ($thread->modified - $thread->created),
        'chatStarted' => $thread->created,
        'groupName' => to_page(get_group_name($group)),
    );
    $page['threadInfo'] = $thread_info;

    // Build messages list
    $last_id = -1;
    $messages = $thread->getMessages(false, $last_id);
    $page['threadMessages'] = json_encode($messages);
}

$page['title'] = getlocal("thread.chat_log");

$page = array_merge($page, prepare_menu($operator, false));

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('thread_log', $page);
