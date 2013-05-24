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
require_once('libs/invitation.php');
require_once('libs/operator.php');
require_once('libs/track.php');

$invited = FALSE;
$operator = array();
$response = array();
if (Settings::get('enabletracking') == '1') {

	$entry = isset($_GET['entry']) ? $_GET['entry'] : "";
	$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
	$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : false;

	// Check if session start
	if (isset($_SESSION['visitorid'])
			&& preg_match('/^[0-9]+$/', $_SESSION['visitorid'])) {
		// Session started. Track visitor
		$invitation_state = invitation_state($_SESSION['visitorid']);
		$visitorid = track_visitor($_SESSION['visitorid'], $entry, $referer);
		$visitor = track_get_visitor_by_id($visitorid);
	} else {
		$visitor = track_get_visitor_by_user_id($user_id);
		if ($visitor !== false) {
			// Session not started but visitor exist in database.
			// Probably third-party cookies disabled by the browser.
			// Use tracking by local cookie at target site
			$invitation_state = invitation_state($visitor['visitorid']);
			$visitorid = track_visitor($visitor['visitorid'], $entry, $referer);
		} else {
			// Start tracking session
			$visitorid = track_visitor_start($entry, $referer);
			$visitor = track_get_visitor_by_id($visitorid);
		}
	}

	if ($visitorid) {
		$_SESSION['visitorid'] = $visitorid;
	}

	if ($user_id === false) {
		// Update local cookie value at target site
		$response['handlers'][] = 'updateUserId';
		$response['dependences']['updateUserId'] = array();
		$response['data']['user']['id'] = $visitor['userid'];
	}

	// Check if invitation closed
	if (! $invitation_state['invited']
		&& ! empty($_SESSION['invitation_threadid'])
	) {
		$response['handlers'][] = 'invitationClose';
		$response['dependences']['invitationClose'] = array();
		unset($_SESSION['invitation_threadid']);
	}

	// Check if visitor just invited to chat
	$is_invited = $invitation_state['invited']
		&& (empty($_SESSION['invitation_threadid'])
			? true
			: ($_SESSION['invitation_threadid'] != $invitation_state['threadid']));

	if ($is_invited) {
		// Load invitation thread
		$thread = Thread::load($invitation_state['threadid']);

		// Get operator info
		$operator = operator_by_id($thread->agentId);
		$locale = isset($_GET['locale']) ? $_GET['locale'] : '';
		$operator_name = ($locale == $home_locale)
			? $operator['vclocalename']
			: $operator['vccommonname'];

		// Show invitation dialog at widget side
		$response['handlers'][] = 'invitationCreate';
		$response['dependences']['invitationCreate'] = array();
		$response['data']['invitation'] = array(
			'operatorName' => htmlspecialchars($operator_name),
			'avatarUrl' => htmlspecialchars($operator['vcavatar']),
			'threadUrl' => get_app_location(true, is_secure_request())
				. '/client.php?act=invitation'
		);

		$_SESSION['invitation_threadid'] = $thread->id;
	}

	// Check if visitor reject invitation
	$invitation_state = invitation_state($visitorid);
	if ($invitation_state['invited'] && ! empty($_GET['invitation_rejected'])) {
		invitation_reject($visitorid);
	}
}

start_js_output();
echo build_widget_response($response);

exit;
?>