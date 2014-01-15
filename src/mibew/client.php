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

// Import namespaces and classes of the core
use Mibew\Settings;
use Mibew\Thread;
use Mibew\Style\ChatStyle;

// Initialize libraries
require_once(dirname(__FILE__).'/libs/init.php');
require_once(MIBEW_FS_ROOT.'/libs/chat.php');
require_once(MIBEW_FS_ROOT.'/libs/operator.php');
require_once(MIBEW_FS_ROOT.'/libs/groups.php');
require_once(MIBEW_FS_ROOT.'/libs/expand.php');
require_once(MIBEW_FS_ROOT.'/libs/captcha.php');
require_once(MIBEW_FS_ROOT.'/libs/invitation.php');
require_once(MIBEW_FS_ROOT.'/libs/track.php');

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

// Initialize chat style which is currently used in system
$chat_style = new ChatStyle(ChatStyle::currentStyle());

// Do not support old browsers at all
if (get_remote_level($_SERVER['HTTP_USER_AGENT']) == 'old') {
	// Create page array
	$page = array_merge_recursive(
		setup_logo()
	);
	$chat_style->render('nochat');
	exit;
}

if (verifyparam("act", "/^(invitation)$/", "default") == 'invitation'
	&& Settings::get('enabletracking')
) {
	// Check if user invited to chat
	$invitation_state = invitation_state($_SESSION['visitorid']);

	if ($invitation_state['invited'] && $invitation_state['threadid']) {
		$thread = Thread::load($invitation_state['threadid']);

		// Prepare page
		$page = setup_invitation_view($thread);

		// Build js application options
		$page['invitationOptions'] = json_encode($page['invitation']);
		// Expand page
		$chat_style->render('chat');
		exit;
	}
}

if( !isset($_GET['token']) || !isset($_GET['thread']) ) {

	$thread = NULL;
	if( isset($_SESSION['threadid']) ) {
		$thread = Thread::reopen($_SESSION['threadid']);
	}

	if( !$thread ) {

		// Load group info
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

		// Get operator code
		$operator_code = empty($_GET['operator_code'])
			? ''
			: $_GET['operator_code'];
		if (! preg_match("/^[A-z0-9_]+$/", $operator_code)) {
			$operator_code = false;
		}

		// Get visitor info
		$visitor = visitor_from_request();
		$info = getgetparam('info');
		$email = getgetparam('email');

		// Get referrer
		$referrer = isset($_GET['url'])
			? $_GET['url']
			: (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "");

		if(isset($_GET['referrer']) && $_GET['referrer']) {
			$referrer .= "\n".$_GET['referrer'];
		}

		// Check if there are online operators
		if(!has_online_operators($groupid)) {
			// Display leave message page
			$page = array_merge_recursive(
				setup_logo($group),
				setup_leavemessage(
					$visitor['name'],
					$email,
					$groupid,
					$info,
					$referrer
				)
			);
			$page['leaveMessageOptions'] = json_encode($page['leaveMessage']);
			$chat_style->render('chat');
			exit;
		}

		// Get invitation info
		if (Settings::get('enabletracking')) {
			$invitation_state = invitation_state($_SESSION['visitorid']);
			$visitor_is_invited = $invitation_state['invited'];
		} else {
			$visitor_is_invited = false;
		}

		// Get operator info
		$requested_operator = false;
		if ($operator_code) {
			$requested_operator = operator_by_code($operator_code);
		}

		// Check if survey should be displayed
		if(Settings::get('enablepresurvey') == '1'
			&& !$visitor_is_invited
			&& !$requested_operator
		) {
			// Display prechat survey
			$page = array_merge_recursive(
				setup_logo($group),
				setup_survey($visitor['name'], $email, $groupid, $info, $referrer)
			);
			$page['surveyOptions'] = json_encode($page['survey']);
			$chat_style->render('chat');
			exit;
		}

		// Start chat thread
		$thread = chat_start_for_user($groupid, $requested_operator, $visitor['id'], $visitor['name'], $referrer, $info);
	}
	$threadid = $thread->id;
	$token = $thread->lastToken;
	$chatstyle = verifyparam( "style", "/^\w+$/", "");
	header("Location: " . MIBEW_WEB_ROOT . "/client.php?thread=" . intval($threadid) . "&token=" . urlencode($token) . ($chatstyle ? "&style=" . urlencode($chatstyle) : ""));
	exit;
}

$token = verifyparam( "token", "/^\d{1,8}$/");
$threadid = verifyparam( "thread", "/^\d{1,8}$/");

$thread = Thread::load($threadid, $token);
if (! $thread) {
	die("wrong thread");
}

$page = setup_chatview_for_user($thread);

$pparam = verifyparam( "act", "/^(mailthread)$/", "default");
if( $pparam == "mailthread" ) {
	$chat_style->render('mail');
} else {
	// Build js application options
	$page['chatOptions'] = json_encode($page['chat']);
	// Expand page
	$chat_style->render('chat');
}

?>