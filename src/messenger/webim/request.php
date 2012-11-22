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
require_once('libs/request.php');

$invited = FALSE;
$operator = array();
if (Settings::get('enabletracking') == '1') {

    $entry = isset($_GET['entry']) ? $_GET['entry'] : "";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";

    if (isset($_SESSION['visitorid']) && preg_match('/^[0-9]+$/', $_SESSION['visitorid'])) {
	$invited = invitation_check($_SESSION['visitorid']);
	$visitorid = track_visitor($_SESSION['visitorid'], $entry, $referer);
    }
    else {
	$visitorid = track_visitor_start($entry, $referer);
    }

    if ($visitorid) {
	$_SESSION['visitorid'] = $visitorid;
    }

    if ($invited !== FALSE) {
	$operator = operator_by_id($invited);
    }

}

$response = array();
if ($invited !== FALSE) {
    $response['load']['mibewInvitationScript'] = get_app_location(true, is_secure_request()) . '/js/compiled/invite.js';
    $response['handlers'][] = 'mibewInviteOnResponse';
    $response['dependences']['mibewInviteOnResponse'] = array('mibewInvitationScript');
    $locale = isset($_GET['lang']) ? $_GET['lang'] : '';
    $operatorName = ($locale == $home_locale) ? $operator['vclocalename'] : $operator['vccommonname'];
    $response['data']['invitation']['operator'] = htmlspecialchars($operatorName);
    $response['data']['invitation']['message'] = getlocal("invitation.message");
    $response['data']['invitation']['avatar'] = htmlspecialchars($operator['vcavatar']);
}

start_js_output();
echo build_js_response($response);

exit;
?>