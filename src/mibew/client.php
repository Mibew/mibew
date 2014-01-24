<?php
/*
 * Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once('libs/common.php');
require_once('libs/chat.php');
require_once('libs/operator.php');
require_once('libs/groups.php');
require_once('libs/expand.php');
require_once('libs/captcha.php');
require_once('libs/notify.php');

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
			$groupid = verifyparam( "group", "/^\d{1,10}$/", "");
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
			if ($settings["surveyaskcaptcha"] == "1") {
				$captcha = getparam('captcha');
				$original = isset($_SESSION["mibew_captcha"])
					? $_SESSION["mibew_captcha"]
					: "";
				$survey_captcha_failed = empty($original)
					|| empty($captcha)
					|| $captcha != $original;
				unset($_SESSION['mibew_captcha']);
			}

			if($settings['usercanchangename'] == "1" && isset($_POST['name'])) {
				$newname = getparam("name");
				if($newname != $visitor['name']) {
					$data = strtr(base64_encode(myiconv($mibew_encoding,"utf-8",$newname)), '+/=', '-_,');
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

		$show_survey = $settings['enablepresurvey'] == '1'
			&& (
			    !(isset($_POST['survey']) && $_POST['survey'] == 'on')
			    || ($settings["surveyaskcaptcha"] == "1" && !empty($survey_captcha_failed))
			);
		if($show_survey) {
			$page = array();
			setup_logo();
			if (!empty($survey_captcha_failed)) {
			    $errors[] = getlocal('errors.captcha');
			}
			setup_survey($visitor['name'], $email, $groupid, $info, $referrer, can_show_captcha());
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
			post_message_($thread['threadid'],$kind_for_agent,getstring2('chat.came.from',array($referrer),true),$link);
		}
		post_message_($thread['threadid'],$kind_info,getstring('chat.wait', true),$link);
		if($email) {
			post_message_($thread['threadid'],$kind_for_agent,getstring2('chat.visitor.email',array($email),true),$link);
		}
		if($info) {
			post_message_($thread['threadid'],$kind_for_agent,getstring2('chat.visitor.info',array($info),true),$link);
		}
		if($firstmessage) {
			$postedid = post_message_($thread['threadid'],$kind_user,$firstmessage,$link,$visitor['name']);
			if ($postedid) {
				commit_thread( $thread['threadid'], array('shownmessageid' => intval($postedid)), $link);
			}
		}
		notify_operators($thread, $firstmessage, $link);
		mysql_close($link);
	}
	$threadid = $thread['threadid'];
	$token = $thread['ltoken'];
	$level = get_remote_level($_SERVER['HTTP_USER_AGENT']);
	$chatstyle = verifyparam( "style", "/^\w+$/", "");
	header("Location: $mibewroot/client.php?thread=$threadid&token=$token&level=$level".($chatstyle ? "&style=$chatstyle" : ""));
	exit;
}

$token = verifyparam( "token", "/^\d{1,10}$/");
$threadid = verifyparam( "thread", "/^\d{1,10}$/");
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