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

require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(dirname(dirname(__FILE__)).'/libs/chat.php');
require_once(dirname(dirname(__FILE__)).'/libs/operator.php');
require_once(dirname(dirname(__FILE__)).'/libs/pagination.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/thread.php');

$operator = check_login();
csrfchecktoken();
$page = array('banId' => '');
$page['saved'] = false;
$page['thread'] = '';
$page['threadid'] = '';
$errors = array();

if (isset($_POST['address'])) {
	$banId = verifyparam("banId", "/^(\d{1,9})?$/", "");
	$address = getparam("address");
	$days = getparam("days");
	$comment = getparam('comment');
	$threadid = isset($_POST['threadid']) ? getparam('threadid') : "";

	if (!$address) {
		$errors[] = no_field("form.field.address");
	}

	if (!preg_match("/^\d+$/", $days)) {
		$errors[] = wrong_field("form.field.ban_days");
	}

	if (!$comment) {
		$errors[] = no_field("form.field.ban_comment");
	}

	$existing_ban = ban_for_addr($address);

	if ((!$banId && $existing_ban) ||
		($banId && $existing_ban && $banId != $existing_ban['banid'])) {
		$errors[] = getlocal2("ban.error.duplicate", array($address, $existing_ban['banid']));
	}

	if (count($errors) == 0) {
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

		if (!$threadid) {
			header("Location: $mibewroot/operator/blocked.php");
			exit;
		} else {
			$page['saved'] = true;
			$page['address'] = $address;
		}
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
		$errors[] = "Wrong id";
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

prepare_menu($operator, false);
start_html_output();
require(dirname(dirname(__FILE__)).'/view/ban.php');
exit;
?>