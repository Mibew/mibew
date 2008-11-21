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

require_once('libs/common.php');
require_once('libs/chat.php');
require_once('libs/operator.php');
require_once('libs/expand.php');

if( !isset($_GET['token']) || !isset($_GET['thread']) ) {

	$chatstyle = verifyparam( "style", "/^\w+$/", "");
	$thread = NULL;
	if( isset($_SESSION['threadid']) ) {
		$thread = reopen_thread($_SESSION['threadid']);
	}

	if( !$thread ) {
		if(!has_online_operators()) {
			setup_logo();
			expand("styles", getchatstyle(), "leavemessage.tpl");
			exit;
		}

		$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
		$extAddr = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
		          $_SERVER['HTTP_X_FORWARDED_FOR'] != $_SERVER['REMOTE_ADDR']) {
			$extAddr = $_SERVER['REMOTE_ADDR'].' ('.$_SERVER['HTTP_X_FORWARDED_FOR'].')';
		}
		$userbrowser = $_SERVER['HTTP_USER_AGENT'];
		$remoteHost = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : $extAddr;
		$visitor = $remote_visitor();
		$thread = create_thread($visitor['name'], $remoteHost, $referer,$current_locale,$visitor['id'], $userbrowser);
		$_SESSION['threadid'] = $thread['threadid'];
		if( $referer ) {
			post_message($thread['threadid'],$kind_for_agent,getstring2('chat.came.from',array($referer)));
		}
		post_message($thread['threadid'],$kind_info,getstring('chat.wait'));
	}
	$threadid = $thread['threadid'];
	$token = $thread['ltoken'];
	$level = get_remote_level($_SERVER['HTTP_USER_AGENT']);
	header("Location: $webimroot/client.php?thread=$threadid&token=$token&level=$level".($chatstyle ? "&style=$chatstyle" : ""));
	exit;
}

$token = verifyparam( "token", "/^\d{1,8}$/");
$threadid = verifyparam( "thread", "/^\d{1,8}$/");
$level = verifyparam( "level", "/^(ajaxed|simple|old)$/");

$thread = thread_by_id($threadid);
if( !$thread || !isset($thread['ltoken']) || $token != $thread['ltoken'] ) {
	die("wrong thread");
}

setup_chatview_for_user($thread, $level);

$pparam = verifyparam( "act", "/^(mailthread)$/", "default");
if( $pparam == "mailthread" ) {
	expand("styles", getchatstyle(), "mail.tpl");
} else if( $level == "ajaxed" ) {
	expand("styles", getchatstyle(), "chat.tpl");
} else if( $level == "simple" ) {
	expand("styles", getchatstyle(), "chatsimple.tpl");
} else if( $level == "old" ) {
	expand("styles", getchatstyle(), "nochat.tpl");
}

?>