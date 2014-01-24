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

$page = array();
$errors = array();

setlocale(LC_TIME, getstring("time.locale"));

$link = connect();

if (isset($_GET['act']) && $_GET['act'] == 'del') {
	$banId = isset($_GET['id']) ? $_GET['id'] : "";

	if (!preg_match("/^\d+$/", $banId)) {
		$errors[] = "Cannot delete: wrong argument";
	}

	if (count($errors) == 0) {
		perform_query("delete from ${mysqlprefix}chatban where banid = " . intval($banId), $link);
		header("Location: $mibewroot/operator/blocked.php");
		exit;
	}
}

$result = mysql_query("select banid,unix_timestamp(dtmtill) as till,address,comment from ${mysqlprefix}chatban", $link)
		or die(' Query failed: ' . mysql_error($link));

$blockedList = array();
while ($ban = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$blockedList[] = $ban;
}

mysql_free_result($result);
mysql_close($link);

setup_pagination($blockedList);

prepare_menu($operator);
start_html_output();

require('../view/blocked_visitors.php');
exit;
?>