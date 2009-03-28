<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2009 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
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

$page = array();
$query = isset($_GET['q']) ? myiconv(getoutputenc(), $webim_encoding, $_GET['q']) : false;

if($query !== false) {
	$link = connect();
	
	$result = mysql_query("select chatgroup.groupid as groupid, vclocalname ".
			 "from chatgroup order by vclocalname", $link);
	$groupName = array();
	while ($group = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$groupName[$group['groupid']] = $group['vclocalname'];
	}
	$page['groupName'] = $groupName;
	mysql_free_result($result);

	$result = mysql_query(
		 "select DISTINCT unix_timestamp(chatthread.dtmcreated) as created, ".
    	 "unix_timestamp(chatthread.dtmmodified) as modified, chatthread.threadid, ".
		 "chatthread.remote, chatthread.agentName, chatthread.userName, groupid, ".
		 "messageCount as size ".
		 "from chatthread, chatmessage ".
		 "where chatmessage.threadid = chatthread.threadid and ".
			"((chatthread.userName LIKE '%%$query%%') or ".
			" (chatmessage.tmessage LIKE '%%$query%%'))".
		 "order by created DESC", $link)
							or die(' Query failed: ' .mysql_error().": ".$query);

	$foundThreads = array();
	while ($thread = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$foundThreads[] = $thread;
	}

	mysql_free_result($result);
	mysql_close($link);

	$page['formq'] = topage($query);
	setup_pagination($foundThreads);
} else {
	setup_empty_pagination();
}

prepare_menu($operator);
start_html_output();
require('../view/thread_search.php');
?>