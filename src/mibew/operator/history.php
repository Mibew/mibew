<?php
/*
 * Copyright 2005-2013 the original author or authors.
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
require_once(MIBEW_FS_ROOT . '/libs/operator.php');
require_once(MIBEW_FS_ROOT . '/libs/chat.php');
require_once(MIBEW_FS_ROOT . '/libs/userinfo.php');
require_once(MIBEW_FS_ROOT . '/libs/pagination.php');
require_once(MIBEW_FS_ROOT . '/libs/cron.php');
require_once(MIBEW_FS_ROOT . '/libs/track.php');

$operator = check_login();
force_password($operator);

setlocale(LC_TIME, getstring("time.locale"));

$page = array();
$query = isset($_GET['q']) ? myiconv(getoutputenc(), MIBEW_ENCODING, $_GET['q']) : false;

$search_type = verify_param('type', '/^(all|message|operator|visitor)$/', 'all');
$search_in_system_messages = (verify_param('insystemmessages', '/^on$/', 'off') == 'on') || !$query;

if ($query !== false) {
    $db = Database::getInstance();
    $groups = $db->query(
        ("SELECT {chatgroup}.groupid AS groupid, vclocalname " .
            "FROM {chatgroup} " .
            "ORDER BY vclocalname"),
        null,
        array('return_rows' => Database::RETURN_ALL_ROWS)
    );

    $group_name = array();
    foreach ($groups as $group) {
        $group_name[$group['groupid']] = $group['vclocalname'];
    }
    $page['groupName'] = $group_name;

    $values = array(
        ':query' => "%{$query}%",
        ':invitation_accepted' => Thread::INVITATION_ACCEPTED,
        ':invitation_not_invited' => Thread::INVITATION_NOT_INVITED,
    );

    $search_conditions = array();
    if ($search_type == 'message' || $search_type == 'all') {
        $search_conditions[] = "({chatmessage}.tmessage LIKE :query"
            . ($search_in_system_messages
                ? ''
                : " AND ({chatmessage}.ikind = :kind_user OR {chatmessage}.ikind = :kind_agent)")
            . ")";
        if (!$search_in_system_messages) {
            $values[':kind_user'] = Thread::KIND_USER;
            $values[':kind_agent'] = Thread::KIND_AGENT;
        }
    }
    if ($search_type == 'operator' || $search_type == 'all') {
        $search_conditions[] = "({chatthread}.agentName LIKE :query)";
    }
    if ($search_type == 'visitor' || $search_type == 'all') {
        $search_conditions[] = "({chatthread}.userName LIKE :query)";
        $search_conditions[] = "({chatthread}.remote LIKE :query)";
    }

    // Load threads
    list($threads_count) = $db->query(
        ("SELECT COUNT(DISTINCT {chatthread}.dtmcreated) "
        . "FROM {chatthread}, {chatmessage} "
        . "WHERE {chatmessage}.threadid = {chatthread}.threadid "
            . "AND ({chatthread}.invitationstate = :invitation_accepted "
                . "OR {chatthread}.invitationstate = :invitation_not_invited) "
            . "AND (" . implode(' OR ', $search_conditions) . ")"),
        $values,
        array(
            'return_rows' => Database::RETURN_ONE_ROW,
            'fetch_type' => Database::FETCH_NUM,
        )
    );

    $pagination_info = pagination_info($threads_count);

    if ($threads_count && $pagination_info) {
        $page['pagination'] = $pagination_info;

        $limit_start = intval($pagination_info['start']);
        $limit_end = intval($pagination_info['end'] - $pagination_info['start']);

        $threads_list = $db->query(
            ("SELECT DISTINCT {chatthread}.* "
            . "FROM {chatthread}, {chatmessage} "
            . "WHERE {chatmessage}.threadid = {chatthread}.threadid "
                . "AND ({chatthread}.invitationstate = :invitation_accepted "
                    . "OR {chatthread}.invitationstate = :invitation_not_invited) "
                . "AND (" . implode(' OR ', $search_conditions) . ") "
            . "ORDER BY {chatthread}.dtmcreated DESC "
            . "LIMIT " . $limit_start . ", " . $limit_end),
            $values,
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        foreach ($threads_list as $item) {
            $page['pagination.items'][] = Thread::createFromDbInfo($item);
        }
    } else {
        $page['pagination'] = false;
        $page['pagination.items'] = false;
    }

    $page['formq'] = to_page($query);
} else {
    $page['pagination'] = false;
    $page['pagination.items'] = false;
}

$page['formtype'] = $search_type;
$page['forminsystemmessages'] = $search_in_system_messages;
$page['title'] = getlocal("page_analysis.search.title");
$page['menuid'] = "history";

$page = array_merge($page, prepare_menu($operator));

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('thread_search', $page);
