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


// Do not support old browsers at all
if (get_remote_level($_SERVER['HTTP_USER_AGENT']) == 'old') {
	// Create page array
	$page = array_merge_recursive(
		setup_logo()
	);
	expand("styles/dialogs", getchatstyle(), "nochat.tpl");
	exit;
}

if (verifyparam("act", "/^(invitation)$/", "default") == 'invitation') {
	// Check if user invited to chat
	$invitation_state = invitation_state($_SESSION['visitorid']);

	if ($invitation_state['invited'] && $invitation_state['threadid']) {
		$thread = Thread::load($invitation_state['threadid']);

		// Prepare page
		$page = setup_invitation_view($thread);

		// Build js application options
		$page['invitationOptions'] = json_encode($page['invitation']);
		// Expand page
		expand("styles/dialogs", getchatstyle(), "chat.tpl");
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
			expand("styles/dialogs", getchatstyle(), "chat.tpl");
			exit;
		}

		// Get invitation info
		if (Settings::get('enabletracking')) {
			$invitation_state = invitation_state($_SESSION['visitorid']);
			$visitor_is_invited = $invitation_state['invited'];
		} else {
			$visitor_is_invited = false;
		}

		// Check if survey should be displayed
		if(Settings::get('enablepresurvey') == '1' && !$visitor_is_invited) {
			// Display prechat survey
			$page = array_merge_recursive(
				setup_logo($group),
				setup_survey($visitor['name'], $email, $groupid, $info, $referrer)
			);
			$page['surveyOptions'] = json_encode($page['survey']);
			expand("styles/dialogs", getchatstyle(), "chat.tpl");
			exit;
		}

		// Start chat thread
		$thread = chat_start_for_user($groupid, $visitor['id'], $visitor['name'], $referrer, $info);
	}
	$threadid = $thread->id;
	$token = $thread->lastToken;
	$chatstyle = verifyparam( "style", "/^\w+$/", "");
	header("Location: $webimroot/client.php?thread=$threadid&token=$token".($chatstyle ? "&style=$chatstyle" : ""));
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
	expand("styles/dialogs", getchatstyle(), "mail.tpl");
} else {
	// Build js application options
	$page['chatOptions'] = json_encode($page['chat']);
	// Expand page
	expand("styles/dialogs", getchatstyle(), "chat.tpl");
}

?>