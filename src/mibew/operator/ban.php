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
csrf_check_token();
$page = array('banId' => '');
$page['saved'] = false;
$page['thread'] = '';
$page['threadid'] = '';
$page['errors'] = array();

if (isset($_POST['address'])) {
    $ban_id = verify_param("banId", "/^(\d{1,9})?$/", "");
    $address = get_param("address");
    $days = get_param("days");
    $comment = get_param('comment');
    $thread_id = isset($_POST['threadid']) ? get_param('threadid') : "";

    if (!$address) {
        $page['errors'][] = no_field("form.field.address");
    }

    if (!preg_match("/^\d+$/", $days)) {
        $page['errors'][] = wrong_field("form.field.ban_days");
    }

    if (!$comment) {
        $page['errors'][] = no_field("form.field.ban_comment");
    }

    $existing_ban = ban_for_addr($address);

    if ((!$ban_id && $existing_ban) ||
        ($ban_id && $existing_ban && $ban_id != $existing_ban['banid'])) {
        $page['errors'][] = getlocal2("ban.error.duplicate", array($address, $existing_ban['banid']));
    }

    if (count($page['errors']) == 0) {
        $db = Database::getInstance();
        $now = time();
        $till_time = $now + $days * 24 * 60 * 60;
        if (!$ban_id) {
            $db->query(
                ("INSERT INTO {chatban} (dtmcreated, dtmtill, address, comment) "
                    . "VALUES (:now,:till,:address,:comment)"),
                array(
                    ':now' => $now,
                    ':till' => $till_time,
                    ':address' => $address,
                    ':comment' => $comment,
                )
            );
        } else {
            $db->query(
                ("UPDATE {chatban} SET dtmtill = :till, address = :address, "
                    . "comment = :comment WHERE banid = :banid"),
                array(
                    ':till' => $till_time,
                    ':address' => $address,
                    ':comment' => $comment,
                    ':banid' => $ban_id,
                )
            );
        }

        $page['saved'] = true;
        $page['address'] = $address;
    } else {
        $page['banId'] = $ban_id;
        $page['formaddress'] = $address;
        $page['formdays'] = $days;
        $page['formcomment'] = $comment;
        $page['threadid'] = $thread_id;
    }
} elseif (isset($_GET['id'])) {
    $ban_id = verify_param('id', "/^\d{1,9}$/");
    $db = Database::getInstance();
    $ban = $db->query(
        ("SELECT banid, (dtmtill - :now) AS days, address, comment "
            . "FROM {chatban} WHERE banid = :banid"),
        array(
            ':banid' => $ban_id,
            ':now' => time(),
        ),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );

    if ($ban) {
        $page['banId'] = $ban['banid'];
        $page['formaddress'] = $ban['address'];
        $page['formdays'] = round($ban['days'] / 86400);
        $page['formcomment'] = $ban['comment'];
    } else {
        $page['errors'][] = "Wrong id";
    }
} elseif (isset($_GET['thread'])) {
    $thread_id = verify_param('thread', "/^\d{1,9}$/");
    $thread = Thread::load($thread_id);
    if ($thread) {
        $page['thread'] = htmlspecialchars($thread->userName);
        $page['threadid'] = $thread_id;
        $page['formaddress'] = $thread->remote;
        $page['formdays'] = 15;
    }
}

$page['title'] = getlocal("page_ban.title");

$page = array_merge($page, prepare_menu($operator, false));

$page_style = new PageStyle(PageStyle::getCurrentStyle());
$page_style->render('ban', $page);
