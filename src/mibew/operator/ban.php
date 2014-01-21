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
require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(MIBEW_FS_ROOT.'/libs/chat.php');
require_once(MIBEW_FS_ROOT.'/libs/operator.php');
require_once(MIBEW_FS_ROOT.'/libs/pagination.php');

$operator = check_login();
csrfchecktoken();
$page = array('banId' => '');
$page['saved'] = false;
$page['thread'] = '';
$page['threadid'] = '';
$page['errors'] = array();

if (isset($_POST['address'])) {
	$banId = verifyparam("banId", "/^(\d{1,9})?$/", "");
	$address = getparam("address");
	$days = getparam("days");
	$comment = getparam('comment');
	$threadid = isset($_POST['threadid']) ? getparam('threadid') : "";

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

	if ((!$banId && $existing_ban) ||
		($banId && $existing_ban && $banId != $existing_ban['banid'])) {
		$page['errors'][] = getlocal2("ban.error.duplicate", array($address, $existing_ban['banid']));
	}

	if (count($page['errors']) == 0) {
		$db = Database::getInstance();
		$now = time();
		$till_time = $now + $days * 24 * 60 * 60;
		if (!$banId) {
			$db->query(
				"insert into {chatban} (dtmcreated,dtmtill,address,comment) " .
				"values (:now,:till,:address,:comment)",
				array(
					':now' => $now,
					':till' => $till_time,
					':address' => $address,
					':comment' => $comment
				)
			);
		} else {
			$db->query(
				"update {chatban} set dtmtill = :till,address = :address, " .
				"comment = :comment where banid = :banid",
				array(
					':till' => $till_time,
					':address' => $address,
					':comment' => $comment,
					':banid' => $banId
				)
			);
		}

		$page['saved'] = true;
		$page['address'] = $address;

	} else {
		$page['banId'] = topage($banId);
		$page['formaddress'] = topage($address);
		$page['formdays'] = topage($days);
		$page['formcomment'] = topage($comment);
		$page['threadid'] = $threadid;
	}
} else if (isset($_GET['id'])) {
	$banId = verifyparam('id', "/^\d{1,9}$/");
	$db = Database::getInstance();
	$ban = $db->query(
		"select banid,(dtmtill - :now)" .
		" as days,address,comment from {chatban} where banid = :banid",
		array(
			':banid' => $banId,
			':now' => time()
		),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);

	if ($ban) {
		$page['banId'] = topage($ban['banid']);
		$page['formaddress'] = topage($ban['address']);
		$page['formdays'] = topage(round($ban['days'] / 86400));
		$page['formcomment'] = topage($ban['comment']);
	} else {
		$page['errors'][] = "Wrong id";
	}
} else if (isset($_GET['thread'])) {
	$threadid = verifyparam('thread', "/^\d{1,9}$/");
	$thread = Thread::load($threadid);
	if ($thread) {
		$page['thread'] = topage($thread->userName);
		$page['threadid'] = $threadid;
		$page['formaddress'] = topage($thread->remote);
		$page['formdays'] = 15;
	}
}

$page['title'] = getlocal("page_ban.title");

$page = array_merge(
	$page,
	prepare_menu($operator, false)
);

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('ban', $page);

?>