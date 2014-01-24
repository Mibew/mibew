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

$page = array();
$query = isset($_GET['q']) ? myiconv(getoutputenc(), $mibew_encoding, $_GET['q']) : false;

if ($query !== false) {
	$link = connect();

	$result = mysql_query("select ${mysqlprefix}chatgroup.groupid as groupid, vclocalname " .
						  "from ${mysqlprefix}chatgroup order by vclocalname", $link);
	$groupName = array();
	while ($group = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$groupName[$group['groupid']] = $group['vclocalname'];
	}
	mysql_free_result($result);
	$page['groupName'] = $groupName;

	$escapedQuery = mysql_real_escape_string($query, $link);
	select_with_pagintation("DISTINCT unix_timestamp(${mysqlprefix}chatthread.dtmcreated) as created, " .
							"unix_timestamp(${mysqlprefix}chatthread.dtmmodified) as modified, ${mysqlprefix}chatthread.threadid, " .
							"${mysqlprefix}chatthread.remote, ${mysqlprefix}chatthread.agentName, ${mysqlprefix}chatthread.userName, groupid, " .
							"messageCount as size",
							"${mysqlprefix}chatthread, ${mysqlprefix}chatmessage",
							array(
								 "${mysqlprefix}chatmessage.threadid = ${mysqlprefix}chatthread.threadid",
								 "((${mysqlprefix}chatthread.userName LIKE '%%$escapedQuery%%') or (${mysqlprefix}chatmessage.tmessage LIKE '%%$escapedQuery%%'))"
							),
							"order by created DESC",
							"DISTINCT ${mysqlprefix}chatthread.dtmcreated", $link);

	mysql_close($link);

	$page['formq'] = topage($query);
} else {
	setup_empty_pagination();
}

prepare_menu($operator);
start_html_output();
require('../view/thread_search.php');
?>