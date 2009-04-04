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
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once('libs/common.php');
require_once('libs/chat.php');
require_once('libs/operator.php');
require_once('libs/groups.php');
require_once('libs/expand.php');

loadsettings();
if($settings['enablessl'] == "1" && $settings['forcessl'] == "1") {
	if(!is_secure_request()) {
		$requested = $_SERVER['PHP_SELF'];
		if($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['QUERY_STRING']) {
			header("Location: ".get_app_location(true,true)."/client.php?".$_SERVER['QUERY_STRING']);
		} else {
			die("only https connections are processed");
		} 		
		exit;
	}
}

if( !isset($_GET['token']) || !isset($_GET['thread']) ) {

	$chatstyle = verifyparam( "style", "/^\w+$/", "");
	$info = getgetparam('info');
	$email = getgetparam('email');
	$thread = NULL;
	$firstmessage = NULL;
	if( isset($_SESSION['threadid']) ) {
		$thread = reopen_thread($_SESSION['threadid']);
	}

	if( !$thread ) {
		if(!has_online_operators()) {
			$page = array();
			setup_logo();
			$page['formname'] = topage(getgetparam('name'));
			$page['formemail'] = topage($email);
			$page['info'] = topage($info);
			expand("styles", getchatstyle(), "leavemessage.tpl");
			exit;
		}

		$groupid = "";
		if($settings['enablegroups'] == '1') {
			$groupid = verifyparam( "group", "/^\d{1,8}$/", "");
			if($groupid) {
				$group = group_by_id($groupid);
				if(!$group) {
					$groupid = "";
				}
			}
		}

		$visitor = visitor_from_request();
		$referer = isset($_GET['url']) ? $_GET['url'] :
			(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "");
		if(isset($_GET['referrer']) && $_GET['referrer']) {
			$referer .= "\n".$_GET['referrer'];
		}
		
		if($settings['enablepresurvey'] == '1') {
			if(isset($_POST['survey']) && $_POST['survey'] == 'on') {
				$firstmessage = getparam("message");
				$info = getparam("info");
				$email = getparam("email");
				if($settings['usercanchangename'] == "1" && isset($_POST['name'])) {
					$newname = getparam("name");
					if($newname != $visitor['name']) {
						$data = strtr(base64_encode(myiconv($webim_encoding,"utf-8",$newname)), '+/=', '-_,');
						setcookie($namecookie, $data, time()+60*60*24*365);
						$visitor['name'] = $newname;
					}
				}
				$referer = urldecode(getparam("referrer"));
			} else {
				$page = array();
				setup_logo();
				setup_survey($visitor['name'], $email, $groupid, $info, $referer);
				expand("styles", getchatstyle(), "survey.tpl");
				exit;
			}
		}

		$extAddr = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
		          $_SERVER['HTTP_X_FORWARDED_FOR'] != $_SERVER['REMOTE_ADDR']) {
			$extAddr = $_SERVER['REMOTE_ADDR'].' ('.$_SERVER['HTTP_X_FORWARDED_FOR'].')';
		}
		$userbrowser = $_SERVER['HTTP_USER_AGENT'];
		$remoteHost = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : $extAddr;
		$thread = create_thread($groupid,$visitor['name'], $remoteHost, $referer,$current_locale,$visitor['id'], $userbrowser);
		$_SESSION['threadid'] = $thread['threadid'];
		if( $referer ) {
			post_message($thread['threadid'],$kind_for_agent,getstring2('chat.came.from',array($referer)));
		}
		post_message($thread['threadid'],$kind_info,getstring('chat.wait'));
		if($email) {
			post_message($thread['threadid'],$kind_for_agent,getstring2('chat.visitor.email',array($email)));
		}
		if($info) {
			post_message($thread['threadid'],$kind_for_agent,getstring2('chat.visitor.info',array($info)));
		}
		if($firstmessage) {
			$postedid = post_message($thread['threadid'],$kind_user,$firstmessage,$visitor['name']);
			$link = connect();
			commit_thread( $thread['threadid'], array('shownmessageid' => $postedid), $link);
			mysql_close($link);
		}
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