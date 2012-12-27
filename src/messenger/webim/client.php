<?php
/*
 * Copyright 2005-2013 the original author or authors.
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

require_once('libs/init.php');
require_once('libs/chat.php');
require_once('libs/operator.php');
require_once('libs/groups.php');
require_once('libs/expand.php');
require_once('libs/captcha.php');
require_once('libs/invitation.php');
require_once('libs/classes/thread.php');

if(Settings::get('enablessl') == "1" && Settings::get('forcessl') == "1") {
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
		$thread = Thread::reopen($_SESSION['threadid']);
	}

	if( !$thread ) {
		$groupid = "";
		$groupname = "";
		$group = NULL;
		if(Settings::get('enablegroups') == '1') {
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

			if(Settings::get('usercanchangename') == "1" && isset($_POST['name'])) {
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
			setup_logo($group);
			setup_leavemessage($visitor['name'],$email,$firstmessage,$groupid,$groupname,$info,$referrer,can_show_captcha());
			expand("styles/dialogs", getchatstyle(), "leavemessage.tpl");
			exit;
		}

		$invitation_state = invitation_state($_SESSION['visitorid']);
		$visitor_is_invited = Settings::get('enabletracking') && $invitation_state['invited'] && !$invitation_state['threadid'];
		if(Settings::get('enablepresurvey') == '1' && !(isset($_POST['survey']) && $_POST['survey'] == 'on') && !$visitor_is_invited) {
			$page = array();
			setup_logo($group);
			setup_survey($visitor['name'], $email, $groupid, $info, $referrer);
			expand("styles/dialogs", getchatstyle(), "survey.tpl");
			exit;
		}

		$remoteHost = get_remote_host();
		$userbrowser = $_SERVER['HTTP_USER_AGENT'];

		if(Thread::connectionLimitReached($remoteHost)) {
			die("number of connections from your IP is exceeded, try again later");
		}
		$thread = Thread::create();
		$thread->groupId = $groupid;
		$thread->userName = $visitor['name'];
		$thread->remote = $remoteHost;
		$thread->referer = $referrer;
		$thread->locale = $current_locale;
		$thread->userId = $visitor['id'];
		$thread->userAgent = $userbrowser;
		$thread->state = Thread::STATE_LOADING;
		$thread->save();

		$_SESSION['threadid'] = $thread->id;

		$operator = invitation_accept($_SESSION['visitorid'], $thread->id);
		if ($operator) {
		    $operator = operator_by_id($operator);
		    $operatorName = ($current_locale == $home_locale) ? $operator['vclocalename'] : $operator['vccommonname'];
			$thread->postMessage(
				Thread::KIND_FOR_AGENT,
				getstring2('chat.visitor.invitation.accepted', array($operatorName))
			);
		}

		if( $referrer ) {
			$thread->postMessage(
				Thread::KIND_FOR_AGENT,
				getstring2('chat.came.from',array($referrer))
			);
		}
		$thread->postMessage(Thread::KIND_INFO, getstring('chat.wait'));
		if($email) {
			$thread->postMessage(Thread::KIND_FOR_AGENT, getstring2('chat.visitor.email',array($email)));
		}
		if($info) {
			$thread->postMessage(Thread::KIND_FOR_AGENT, getstring2('chat.visitor.info',array($info)));
		}
		if($firstmessage) {
			$postedid = $thread->postMessage(Thread::KIND_USER, $firstmessage, $visitor['name']);
			$thread->shownMessageId = $postedid;
			$thread->save();
		}
	}
	$threadid = $thread->id;
	$token = $thread->lastToken;
	$level = get_remote_level($_SERVER['HTTP_USER_AGENT']);
	$chatstyle = verifyparam( "style", "/^\w+$/", "");
	header("Location: $webimroot/client.php?thread=$threadid&token=$token&level=$level".($chatstyle ? "&style=$chatstyle" : ""));
	exit;
}

$token = verifyparam( "token", "/^\d{1,8}$/");
$threadid = verifyparam( "thread", "/^\d{1,8}$/");
$level = verifyparam( "level", "/^(ajaxed|old)$/");

$thread = Thread::load($threadid, $token);
if (! $thread) {
	die("wrong thread");
}

setup_chatview_for_user($thread, $level);

$pparam = verifyparam( "act", "/^(mailthread)$/", "default");
if( $pparam == "mailthread" ) {
	expand("styles/dialogs", getchatstyle(), "mail.tpl");
} else if( $level == "ajaxed" ) {
	// Load JavaScript plugins and JavaScripts, CSS files required by them
	$page['additional_css'] = get_additional_css('chatWindow');
	$page['additional_js'] = get_additional_js('chatWindow');
	$page['js_plugin_options'] = get_js_plugin_options('chatWindow');
	// Expand page
	expand("styles/dialogs", getchatstyle(), "chat.tpl");
} else if( $level == "old" ) {
	expand("styles/dialogs", getchatstyle(), "nochat.tpl");
}

?>