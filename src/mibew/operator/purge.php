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
require_once('../libs/operator.php');
require_once('../libs/chat.php');
require_once('../libs/userinfo.php');
require_once('../libs/pagination.php');

$operator = check_login();
loadsettings();

setlocale(LC_TIME, getstring("time.locale"));

if (isset($_POST['thread'])) {
	foreach ($_POST['thread'] as $t) {
		if (! is_numeric($t)) {
			die("Non-numeric thread id???");
		}
	}
	
	$link = connect();
	$threads = implode(",", $_POST['thread']);
	mysql_query("delete from ${mysqlprefix}chatmessage where threadid in ($threads);");
	mysql_query("delete from ${mysqlprefix}chatthread where threadid in ($threads);");
}

$page = array();
$page['formstart'] = $_GET['start'];
$page['formend']   = $_GET['end'];

$start = strlen($_GET['start']) ? strtotime(myiconv(getoutputenc(), $mibew_encoding, $_GET['start'])) : false;
$end   = strlen($_GET['end']) ? strtotime(myiconv(getoutputenc(), $mibew_encoding, $_GET['end'])) : false;

if ($start !== false || $end !== false) {
	$link = connect();

	$result = mysql_query("select ${mysqlprefix}chatgroup.groupid as groupid, vclocalname " .
						  "from ${mysqlprefix}chatgroup order by vclocalname", $link);
	$groupName = array();
	while ($group = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$groupName[$group['groupid']] = $group['vclocalname'];
	}
	mysql_free_result($result);
	$page['groupName'] = $groupName;

	$queryArray = array("${mysqlprefix}chatmessage.threadid = ${mysqlprefix}chatthread.threadid");
	if ($start !== false) {
		$queryArray[]= sprintf("unix_timestamp(${mysqlprefix}chatthread.dtmcreated) >= %d", $start);
	}
	if ($end !== false) {
		$queryArray[]= sprintf("unix_timestamp(${mysqlprefix}chatthread.dtmcreated) < %d", $end);
	}

	select_with_pagintation("DISTINCT unix_timestamp(${mysqlprefix}chatthread.dtmcreated) as created, " .
							"unix_timestamp(${mysqlprefix}chatthread.dtmmodified) as modified, ${mysqlprefix}chatthread.threadid, " .
							"${mysqlprefix}chatthread.remote, ${mysqlprefix}chatthread.agentName, ${mysqlprefix}chatthread.userName, groupid, " .
							"messageCount as size",
							"${mysqlprefix}chatthread, ${mysqlprefix}chatmessage",
							$queryArray,
							"order by created DESC",
							"DISTINCT ${mysqlprefix}chatthread.dtmcreated", $link);

	mysql_close($link);
} else {
	setup_empty_pagination();
}

prepare_menu($operator);
start_html_output();
require('../view/thread_purge_search.php');
?>