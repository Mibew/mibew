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

require_once('../libs/init.php');
require_once('../libs/operator.php');
require_once('../libs/chat.php');
require_once('../libs/userinfo.php');
require_once('../libs/pagination.php');

$operator = check_login();
force_password($operator);

setlocale(LC_TIME, getstring("time.locale"));

$page = array();
$query = isset($_GET['q']) ? myiconv(getoutputenc(), $webim_encoding, $_GET['q']) : false;

$searchType = verifyparam('type', '/^(all|message|operator|visitor)$/', 'all');
$searchInSystemMessages = (verifyparam('insystemmessages', '/^on$/', 'off') == 'on') || !$query;

if ($query !== false) {
	$db = Database::getInstance();
	$groups = $db->query(
		"select {chatgroup}.groupid as groupid, vclocalname " .
		"from {chatgroup} order by vclocalname",
		NULL,
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);

	$groupName = array();
	foreach ($groups as $group) {
		$groupName[$group['groupid']] = $group['vclocalname'];
	}
	$page['groupName'] = $groupName;

	$escapedQuery = $escapedQuery?$escapedQuery:'';

	$values = array(
		':query' => "%{$escapedQuery}%"
	);

	$searchConditions = array();
	if ($searchType == 'message' || $searchType == 'all') {
		$searchConditions[] = "({chatmessage}.tmessage LIKE :query" .
					($searchInSystemMessages?'':" AND ({chatmessage}.ikind = :kind_user OR {chatmessage}.ikind = :kind_agent)") .
					")";
		if (! $searchInSystemMessages) {
			$values[':kind_user'] = Thread::KIND_USER;
			$values[':kind_agent'] = Thread::KIND_AGENT;
		}
	}
	if ($searchType == 'operator' || $searchType == 'all') {
		$searchConditions[] = "({chatthread}.agentName LIKE :query)";
	}
	if ($searchType == 'visitor' || $searchType == 'all') {
		$searchConditions[] = "({chatthread}.userName LIKE :query)";
		$searchConditions[] = "({chatthread}.remote LIKE :query)";
	}

	// Load threads
	select_with_pagintation("DISTINCT {chatthread}.*",
		"{chatthread}, {chatmessage}",
		array(
			"{chatmessage}.threadid = {chatthread}.threadid",
			"(" . implode(' or ', $searchConditions)  .  ")"
		),
		"order by {chatthread}.dtmcreated DESC",
		"DISTINCT {chatthread}.dtmcreated", $values);

	// Build Thread object
	foreach ($page['pagination.items'] as $key => $item) {
		$page['pagination.items'][$key] = Thread::createFromDbInfo($item);
	}

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