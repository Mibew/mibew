<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
 * 
 * Contributors:
 *    Pavel Petroshenko - initial API and implementation
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
