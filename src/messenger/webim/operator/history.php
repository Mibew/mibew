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
loadsettings();

setlocale(LC_TIME, getstring("time.locale"));

$page = array();
$query = isset($_GET['q']) ? myiconv(getoutputenc(), $webim_encoding, $_GET['q']) : false;

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