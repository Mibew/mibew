<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2007 Internet Services Ltd.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require('../libs/common.php');
require('../libs/chat.php');
require('../libs/operator.php');

$operator = check_login();

$threadid = verifyparam( "thread", "/^\d{1,8}$/");

if( !isset($_GET['token']) ) {

	if( get_remote_level($_SERVER['HTTP_USER_AGENT']) != "ajaxed" ) {
		die("old browser is used, please update it");
	}

	$thread = thread_by_id($threadid);
	if( !$thread || !isset($thread['ltoken']) ) {
		die("wrong thread");
	}

	 take_thread($thread,$operator);

	$token = $thread['ltoken'];
	header("Location: ".$_SERVER['PHP_SELF']."?thread=$threadid&token=$token");
	exit;
}

$token = verifyparam( "token", "/^\d{1,8}$/");

$thread = thread_by_id($threadid);
if( !$thread || !isset($thread['ltoken']) || $token != $thread['ltoken'] ) {
	die("wrong thread");
}

setup_chatview_for_operator($thread, $operator);

start_html_output();


	require('../view/chat_ajaxed.php');

?>