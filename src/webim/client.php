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

require('libs/common.php');
require('libs/chat.php');

if( !isset($_GET['token']) || !isset($_GET['thread']) ) {

	$thread = NULL;
	if( isset($_SESSION['threadid']) ) {
		$thread = reopen_thread($_SESSION['threadid']);
	}

	if( !$thread ) {
		$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
		$remote = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : $_SERVER['REMOTE_ADDR'];
		$userName = isset($_COOKIE[$namecookie]) ? $_COOKIE[$namecookie] : getstring("chat.default.username");

		$thread = create_thread($userName, $remote, $referer,$current_locale);
		$_SESSION['threadid'] = $thread['threadid'];
		post_message($thread['threadid'],$kind_info,getstring('chat.wait'));
	}	
	$threadid = $thread['threadid'];
	$token = $thread['ltoken'];
	$level = get_remote_level($_SERVER['HTTP_USER_AGENT']);
	header("Location: ".dirname($_SERVER['PHP_SELF'])."/client.php?thread=$threadid&token=$token&level=$level");
	exit;
}

$token = verifyparam( "token", "/^\d{1,8}$/");
$threadid = verifyparam( "thread", "/^\d{1,8}$/");
$level = verifyparam( "level", "/^(ajaxed|simple|old)$/");

$thread = thread_by_id($threadid);
if( !$thread || !isset($thread['ltoken']) || $token != $thread['ltoken'] ) {
	die("wrong thread");
}

//$level = "simple";

setup_chatview_for_user($thread, $level);
start_html_output();

$pparam = verifyparam( "page", "/^(mailthread)$/", "default");
if( $pparam == "mailthread" ) {
	require('view/chat_mailthread.php');
} else if( $level == "ajaxed" ) {
	require('view/chat_ajaxed.php');
} else if( $level == "simple" ) {
	require('view/chat_simple.php');
} else if( $level == "old" ) {
	require('view/chat_oldbrowser.php');
}

?>