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
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('../libs/chat.php');

$operator = check_login();

$threadid = verifyparam( "thread", "/^\d{1,8}$/");
$token = verifyparam( "token", "/^\d{1,8}$/");

$thread = thread_by_id($threadid);
if( !$thread || !isset($thread['ltoken']) || $token != $thread['ltoken'] ) {
	die("wrong thread");
}

$nextid = verifyparam( "nextAgent", "/^\d{1,8}$/");
$nextOperator = operator_by_id($nextid);

$page = array();
$errors = array();

if( $nextOperator ) {
	$page['nextAgent'] = topage(get_operator_name($nextOperator));
	if( $thread['istate'] == $state_chatting ) {
		$link = connect();
		commit_thread( $threadid,
			array("istate" => $state_waiting, "nextagent" => $nextid, "agentId" => 0), $link);
		post_message_($thread['threadid'], $kind_events,
			getstring2_("chat.status.operator.redirect",
				array(get_operator_name($operator)),$thread['locale']), $link);
		mysql_close($link);
	} else {
		$errors[] = "You are not chatting with visitor";  // FIXME
	}
} else {
	$errors[] = "Unknown operator";	// FIXME
}

start_html_output();
if( count($errors) > 0 ) {
	require('../view/chat_error.php');
} else {
	require('../view/redirected.php');
}

?>