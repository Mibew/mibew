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

require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('../libs/chat.php');
require_once('../libs/userinfo.php');
require_once('../libs/pagination.php');

$operator = check_login();
force_password($operator);

loadsettings();

setlocale(LC_TIME, getstring("time.locale"));

$page = array();
$query = isset($_GET['q']) ? myiconv(getoutputenc(), $webim_encoding, $_GET['q']) : false;

$searchType = verifyparam('type', '/^(all|message|operator|visitor)$/', 'all');
$searchInSystemMessages = (verifyparam('insystemmessages', '/^on$/', 'off') == 'on') || !$query;

if ($query !== false) {
	$link = connect();

	$result = perform_query("select ${mysqlprefix}chatgroup.groupid as groupid, vclocalname " .
						  "from ${mysqlprefix}chatgroup order by vclocalname", $link);
	$groupName = array();
	while ($group = db_fetch_assoc($result)) {
		$groupName[$group['groupid']] = $group['vclocalname'];
	}
	db_free_result($result);
	$page['groupName'] = $groupName;

	$escapedQuery = db_escape_string($query, $link);
	$searchConditions = array();
	if ($searchType == 'message' || $searchType == 'all') {
		$searchConditions[] = "(${mysqlprefix}chatmessage.tmessage LIKE '%%$escapedQuery%%'" .
					($searchInSystemMessages?'':" AND (${mysqlprefix}chatmessage.ikind = $kind_user OR ${mysqlprefix}chatmessage.ikind = $kind_agent)") .
					")";
	}
	if ($searchType == 'operator' || $searchType == 'all') {
		$searchConditions[] = "(${mysqlprefix}chatthread.agentName LIKE '%%$escapedQuery%%')";
	}
	if ($searchType == 'visitor' || $searchType == 'all') {
		$searchConditions[] = "(${mysqlprefix}chatthread.userName LIKE '%%$escapedQuery%%')";
		$searchConditions[] = "(${mysqlprefix}chatthread.remote LIKE '%%$escapedQuery%%')";
	}
	select_with_pagintation("DISTINCT unix_timestamp(${mysqlprefix}chatthread.dtmcreated) as created, " .
							"unix_timestamp(${mysqlprefix}chatthread.dtmmodified) as modified, ${mysqlprefix}chatthread.threadid, " .
							"${mysqlprefix}chatthread.remote, ${mysqlprefix}chatthread.agentName, ${mysqlprefix}chatthread.userName, groupid, " .
							"messageCount as size",
							"${mysqlprefix}chatthread, ${mysqlprefix}chatmessage",
							array(
								 "${mysqlprefix}chatmessage.threadid = ${mysqlprefix}chatthread.threadid",
								 "(" . implode(' or ', $searchConditions)  .  ")"
							),
							"order by created DESC",
							"DISTINCT ${mysqlprefix}chatthread.dtmcreated", $link);

	close_connection($link);

	$page['formq'] = topage($query);
} else {
	setup_empty_pagination();
}

$page['formtype'] = $searchType;
$page['forminsystemmessages'] = $searchInSystemMessages;

prepare_menu($operator);
start_html_output();
require('../view/thread_search.php');
?>
