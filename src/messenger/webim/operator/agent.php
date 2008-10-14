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
require_once('../libs/chat.php');
require_once('../libs/operator.php');
require_once('../libs/pagination.php');
require_once('../libs/expand.php');

$operator = check_login();

$threadid = verifyparam( "thread", "/^\d{1,8}$/");

if( !isset($_GET['token']) ) {

	$remote_level = get_remote_level($_SERVER['HTTP_USER_AGENT']);
	if( $remote_level != "ajaxed" ) {
		die("old browser is used, please update it");
	}

	$thread = thread_by_id($threadid);
	if( !$thread || !isset($thread['ltoken']) ) {
		die("wrong thread");
	}

	$viewonly = verifyparam( "viewonly", "/^true$/", false);

	$forcetake = verifyparam("force", "/^true$/", false);
	if( !$viewonly && $thread['istate'] == $state_chatting && $operator['operatorid'] != $thread['agentId'] && $forcetake == false ) {
		$page = array(
			'user' => topage($thread['userName']), 'agent' => topage($thread['agentName']), 'link' => $_SERVER['PHP_SELF']."?thread=$threadid&amp;force=true"
		);
		start_html_output();
		require('../view/confirm.php');
		exit;
	}

	if (!$viewonly)
		take_thread($thread,$operator);

	$token = $thread['ltoken'];
	header("Location: $webimroot/operator/agent.php?thread=$threadid&token=$token&level=$remote_level");
	exit;
}

$token = verifyparam( "token", "/^\d{1,8}$/");

$thread = thread_by_id($threadid);
if( !$thread || !isset($thread['ltoken']) || $token != $thread['ltoken'] ) {
	die("wrong thread");
}

setup_chatview_for_operator($thread, $operator);

start_html_output();

$pparam = verifyparam( "act", "/^(redirect)$/", "default");
if( $pparam == "redirect" ) {
	$page['pagination_list'] = get_redirect_links($threadid,$token);
	expand("../styles", getchatstyle(), "redirect.tpl");
} else {
	expand("../styles", getchatstyle(), "chat.tpl");
}

?>