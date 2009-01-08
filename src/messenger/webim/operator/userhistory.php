<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Web Messenger Community
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
require_once('../libs/pagination.php');

$operator = check_login();

$page = array( 'operator' => topage(get_operator_name($operator)) );

$userid = "";
if( isset($_GET['userid'])) {
	$userid = verifyparam( "userid", "/^.{0,63}$/", "");
}

function threads_by_userid($userid) {
	if ($userid == "") {
	    return null;
	}
	$link = connect();

	$query = sprintf("select unix_timestamp(dtmcreated) as created, unix_timestamp(dtmmodified) as modified, ".
			 " threadid, remote, agentName, userName, userAgent ".
			 "from chatthread ".
			 "where userid=\"$userid\" order by created DESC", $userid);

	$result = mysql_query($query, $link) or die(' Query failed: ' .mysql_error().": ".$query);

	$foundThreads = array();
	while ($thread = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$foundThreads[] = $thread;
	}

	mysql_free_result($result);
	mysql_close($link);
	return $foundThreads;
}

$found = threads_by_userid($userid);

setup_pagination($found,6);
start_html_output();
require('../view/userhistory.php');
?>