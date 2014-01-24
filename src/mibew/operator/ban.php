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

require_once('../libs/common.php');
require_once('../libs/chat.php');
require_once('../libs/operator.php');
require_once('../libs/pagination.php');

$operator = check_login();
csrfchecktoken();

$page = array('banId' => '');
$page['saved'] = false;
$page['thread'] = '';
$page['threadid'] = '';
$errors = array();

if (isset($_POST['address'])) {
	$banId = verifyparam("banId", "/^(\d{1,10})?$/", "");
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

	$link = connect();
	$existing_ban = ban_for_addr_($address, $link);
	mysql_close($link);

	if ((!$banId && $existing_ban) ||
		($banId && $existing_ban && $banId != $existing_ban['banid'])) {
		$errors[] = getlocal2("ban.error.duplicate", array(safe_htmlspecialchars($address), safe_htmlspecialchars($existing_ban['banid'])));
	}

	if (count($errors) == 0) {
		$link = connect();
		$utime = time() + $days * 24 * 60 * 60;
		if (!$banId) {
			$query = sprintf(
				"insert into ${mysqlprefix}chatban (dtmcreated,dtmtill,address,comment) values (CURRENT_TIMESTAMP,%s,'%s','%s')",
				"FROM_UNIXTIME(" . intval($utime) . ")",
				mysql_real_escape_string($address, $link),
				mysql_real_escape_string($comment, $link));
			perform_query($query, $link);
		} else {
			$query = sprintf(
				"update ${mysqlprefix}chatban set dtmtill = %s,address = '%s',comment = '%s' where banid = %s",
				"FROM_UNIXTIME(" . intval($utime) . ")",
				mysql_real_escape_string($address, $link),
				mysql_real_escape_string($comment, $link),
				intval($banId));
			perform_query($query, $link);
		}
		mysql_close($link);

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
	$banId = verifyparam('id', "/^\d{1,10}$/");
	$link = connect();
	$ban = select_one_row("select banid,(unix_timestamp(dtmtill)-unix_timestamp(CURRENT_TIMESTAMP)) as days,address,comment from ${mysqlprefix}chatban where banid = " . intval($banId), $link);
	mysql_close($link);

	if ($ban) {
		$page['banId'] = topage($ban['banid']);
		$page['formaddress'] = topage($ban['address']);
		$page['formdays'] = topage(round($ban['days'] / 86400));
		$page['formcomment'] = topage($ban['comment']);
	} else {
		$errors[] = "Wrong id";
	}
} else if (isset($_GET['thread'])) {
	$threadid = verifyparam('thread', "/^\d{1,10}$/");
	$thread = thread_by_id($threadid);
	if ($thread) {
		$page['thread'] = topage($thread['userName']);
		$page['threadid'] = $threadid;
		$page['formaddress'] = topage($thread['remote']);
		$page['formdays'] = 15;
	}
}

prepare_menu($operator, false);
start_html_output();
require('../view/ban.php');
exit;
?>