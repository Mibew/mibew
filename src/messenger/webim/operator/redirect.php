<?php
/*
 * This file is part of Mibew Messenger project.
 *
 * Copyright (c) 2005-2009 Mibew Messenger Community
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
require_once('../libs/expand.php');
require_once('../libs/groups.php');

$operator = check_login();

$threadid = verifyparam( "thread", "/^\d{1,8}$/");
$token = verifyparam( "token", "/^\d{1,8}$/");

$thread = thread_by_id($threadid);
if( !$thread || !isset($thread['ltoken']) || $token != $thread['ltoken'] ) {
	die("wrong thread");
}

$page = array();
$errors = array();

if(isset($_GET['nextGroup'])) {
	$nextid = verifyparam( "nextGroup", "/^\d{1,8}$/");
	$nextGroup = group_by_id($nextid);
	
	if( $nextGroup ) {
		$page['message'] = getlocal2("chat.redirected.group.content",array(topage(get_group_name($nextGroup))));
		if( $thread['istate'] == $state_chatting ) {
			$link = connect();
			commit_thread( $threadid,
				array("istate" => $state_waiting, "nextagent" => 0, "groupid" => $nextid, "agentId" => 0, "agentName" => "''"), $link);
			post_message_($thread['threadid'], $kind_events,
				getstring2_("chat.status.operator.redirect",
					array(get_operator_name($operator)),$thread['locale']), $link);
			mysql_close($link);
		} else {
			$errors[] = getlocal("chat.redirect.cannot");
		}
	} else {
		$errors[] = "Unknown group";
	}

} else {
	$nextid = verifyparam( "nextAgent", "/^\d{1,8}$/");
	$nextOperator = operator_by_id($nextid);

	if( $nextOperator ) {
		$page['message'] = getlocal2("chat.redirected.content",array(topage(get_operator_name($nextOperator))));
		if( $thread['istate'] == $state_chatting ) {
			$link = connect();
			commit_thread( $threadid,
				array("istate" => $state_waiting, "nextagent" => $nextid, "agentId" => 0), $link);
			post_message_($thread['threadid'], $kind_events,
				getstring2_("chat.status.operator.redirect",
					array(get_operator_name($operator)),$thread['locale']), $link);
			mysql_close($link);
		} else {
			$errors[] = getlocal("chat.redirect.cannot");
		}
	} else {
		$errors[] = "Unknown operator";
	}
}

setup_logo();
if( count($errors) > 0 ) {
	expand("../styles", getchatstyle(), "error.tpl");
} else {
	expand("../styles", getchatstyle(), "redirected.tpl");
}

?>