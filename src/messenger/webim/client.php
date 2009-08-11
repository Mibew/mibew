<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
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
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once('libs/common.php');
require_once('libs/chat.php');
require_once('libs/operator.php');
require_once('libs/groups.php');
require_once('libs/expand.php');
require_once('libs/captcha.php');

loadsettings();
if($settings['enablessl'] == "1" && $settings['forcessl'] == "1") {
	if(!is_secure_request()) {
		$requested = $_SERVER['PHP_SELF'];
		if($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['QUERY_STRING']) {
			header("Location: ".get_app_location(true,true)."/client.php?".$_SERVER['QUERY_STRING']);
		} else {
			die("only https connections are handled");
		} 		
		exit;
	}
}

if( !isset($_GET['token']) || !isset($_GET['thread']) ) {

	$thread = NULL;
	if( isset($_SESSION['threadid']) ) {
		$thread = reopen_thread($_SESSION['threadid']);
	}

	if( !$thread ) {
		$groupid = "";
		$groupname = "";
		if($settings['enablegroups'] == '1') {
			$groupid = verifyparam( "group", "/^\d{1,8}$/", "");
			if($groupid) {
				$group = group_by_id($groupid);
				if(!$group) {
					$groupid = "";
				} else {
					$groupname = get_group_name($group);
				}
			}
		}

		$visitor = visitor_from_request();
		
		if(isset($_POST['survey']) && $_POST['survey'] == 'on') {
			$firstmessage = getparam("message");
			$info = getparam("info");
			$email = getparam("email");
			$referrer = urldecode(getparam("referrer"));

			if($settings['usercanchangename'] == "1" && isset($_POST['name'])) {
				$newname = getparam("name");
				if($newname != $visitor['name']) {
					$data = strtr(base64_encode(myiconv($webim_encoding,"utf-8",$newname)), '+/=', '-_,');
					setcookie($namecookie, $data, time()+60*60*24*365);
					$visitor['name'] = $newname;
				}
			}
		} else {
			$firstmessage = NULL;
			$info = getgetparam('info');
			$email = getgetparam('email');
			$referrer = isset($_GET['url']) ? $_GET['url'] :
				(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "");
			if(isset($_GET['referrer']) && $_GET['referrer']) {
				$referrer .= "\n".$_GET['referrer'];
			}
		}

		if(!has_online_operators($groupid)) {
			$page = array();
			setup_logo();
			setup_leavemessage($visitor['name'],$email,$firstmessage,$groupid,$groupname,$info,$referrer,can_show_captcha());
			expand("styles", getchatstyle(), "leavemessage.tpl");
			exit;
		}

		if($settings['enablepresurvey'] == '1' && !(isset($_POST['survey']) && $_POST['survey'] == 'on')) {
			$page = array();
			setup_logo();
			setup_survey($visitor['name'], $email, $groupid, $info, $referrer);
			expand("styles", getchatstyle(), "survey.tpl");
			exit;
		}

		$remoteHost = get_remote_host();
		$userbrowser = $_SERVER['HTTP_USER_AGENT'];

		$link = connect();
		if(!check_connections_from_remote($remoteHost, $link)) {
			mysql_close($link);
			die("number of connections from your IP is exceeded, try again later");
		}
		$thread = create_thread($groupid,$visitor['name'], $remoteHost, $referrer,$current_locale,$visitor['id'], $userbrowser,$state_loading,$link);
		$_SESSION['threadid'] = $thread['threadid'];
		
		if( $referrer ) {
			post_message_($thread['threadid'],$kind_for_agent,getstring2('chat.came.from',array($referrer)),$link);
		}
		post_message_($thread['threadid'],$kind_info,getstring('chat.wait'),$link);
		if($email) {
			post_message_($thread['threadid'],$kind_for_agent,getstring2('chat.visitor.email',array($email)),$link);
		}
		if($info) {
			post_message_($thread['threadid'],$kind_for_agent,getstring2('chat.visitor.info',array($info)),$link);
		}
		if($firstmessage) {
			$postedid = post_message_($thread['threadid'],$kind_user,$firstmessage,$link,$visitor['name']);
			commit_thread( $thread['threadid'], array('shownmessageid' => $postedid), $link);
		}
		mysql_close($link);
	}
	$threadid = $thread['threadid'];
	$token = $thread['ltoken'];
	$level = get_remote_level($_SERVER['HTTP_USER_AGENT']);
	$chatstyle = verifyparam( "style", "/^\w+$/", "");
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