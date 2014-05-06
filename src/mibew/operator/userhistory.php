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
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');

$operator = check_login();

$page = array();

setlocale(LC_TIME, getstring("time.locale"));

$user_id = "";
if (isset($_GET['userid'])) {
    $user_id = verify_param("userid", "/^.{0,63}$/", "");
}

if (!empty($user_id)) {
    $db = Database::getInstance();

    $query = "SELECT {chatthread}.* "
        . "FROM {chatthread} "
        . "WHERE userid=:user_id "
            . "AND (invitationstate = :invitation_accepted "
                . "OR invitationstate = :invitation_not_invited) "
        . "ORDER BY dtmcreated DESC";

    $found = $db->query(
        $query,
        array(
            ':user_id' => $user_id,
            ':invitation_accepted' => Thread::INVITATION_ACCEPTED,
            ':invitation_not_invited' => Thread::INVITATION_NOT_INVITED,
        ),
        array('return_rows' => Database::RETURN_ALL_ROWS)
    );
} else {
    $found = null;
}

$page = array_merge($page, prepare_menu($operator));

// Setup pagination
$pagination = setup_pagination($found, 6);
$page['pagination'] = $pagination['info'];
$page['pagination.items'] = $pagination['items'];

foreach ($page['pagination.items'] as $key => $item) {
    $thread = Thread::createFromDbInfo($item);
    $page['pagination.items'][$key] = array(
        'threadId' => to_page($thread->id),
        'userName' => to_page($thread->userName),
        'userAddress' => get_user_addr(to_page($thread->remote)),
        'agentName' => to_page($thread->agentName),
        'chatTime' => ($thread->modified - $thread->created),
        'chatCreated' => $thread->created,
    );
}

$page['title'] = getlocal("page.analysis.userhistory.title");
$page['menuid'] = "history";

$page_style = new PageStyle(PageStyle::getCurrentStyle());
$page_style->render('user_history', $page);
