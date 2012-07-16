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

require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('../libs/settings.php');

$operator = check_login();
csrfchecktoken();

$page = array('agentId' => '');
$errors = array();

$options = array(
	'online_timeout', 'updatefrequency_operator', 'updatefrequency_chat',
	'updatefrequency_oldchat', 'max_connections_from_one_host',
	'updatefrequency_tracking', 'visitors_limit', 'invitation_lifetime',
	'tracking_lifetime', 'thread_lifetime' );

$params = array();
foreach ($options as $opt) {
	$params[$opt] = Settings::get($opt);
}

if (isset($_POST['onlinetimeout'])) {
	$params['online_timeout'] = getparam('onlinetimeout');
	if (!is_numeric($params['online_timeout'])) {
		$errors[] = wrong_field("settings.onlinetimeout");
	}

	$params['updatefrequency_operator'] = getparam('frequencyoperator');
	if (!is_numeric($params['updatefrequency_operator'])) {
		$errors[] = wrong_field("settings.frequencyoperator");
	}

	$params['updatefrequency_chat'] = getparam('frequencychat');
	if (!is_numeric($params['updatefrequency_chat'])) {
		$errors[] = wrong_field("settings.frequencychat");
	}

	$params['updatefrequency_oldchat'] = getparam('frequencyoldchat');
	if (!is_numeric($params['updatefrequency_oldchat'])) {
		$errors[] = wrong_field("settings.frequencyoldchat");
	}

	$params['max_connections_from_one_host'] = getparam('onehostconnections');
	if (!is_numeric($params['max_connections_from_one_host'])) {
		$errors[] = getlocal("settings.wrong.onehostconnections");
	}

	$params['thread_lifetime'] = getparam('threadlifetime');
	if (!is_numeric($params['thread_lifetime'])) {
		$errors[] = getlocal("settings.wrong.threadlifetime");
	}

	if (Settings::get('enabletracking')) {

	    $params['updatefrequency_tracking'] = getparam('frequencytracking');
	    if (!is_numeric($params['updatefrequency_tracking'])) {
		    $errors[] = wrong_field("settings.frequencytracking");
	    }

	    $params['visitors_limit'] = getparam('visitorslimit');
	    if (!is_numeric($params['visitors_limit'])) {
		    $errors[] = wrong_field("settings.visitorslimit");
	    }

	    $params['invitation_lifetime'] = getparam('invitationlifetime');
	    if (!is_numeric($params['invitation_lifetime'])) {
		    $errors[] = wrong_field("settings.invitationlifetime");
	    }

	    $params['tracking_lifetime'] = getparam('trackinglifetime');
	    if (!is_numeric($params['tracking_lifetime'])) {
		    $errors[] = wrong_field("settings.trackinglifetime");
	    }

	}

	if (count($errors) == 0) {
		foreach ($options as $opt) {
			Settings::set($opt,$params[$opt]);
		}
		Settings::update();
		header("Location: $webimroot/operator/performance.php?stored");
		exit;
	}
}

$page['formonlinetimeout'] = $params['online_timeout'];
$page['formfrequencyoperator'] = $params['updatefrequency_operator'];
$page['formfrequencychat'] = $params['updatefrequency_chat'];
$page['formfrequencyoldchat'] = $params['updatefrequency_oldchat'];
$page['formthreadlifetime'] = $params['thread_lifetime'];
$page['formonehostconnections'] = $params['max_connections_from_one_host'];
$page['formthreadlifetime'] = $params['thread_lifetime'];

if (Settings::get('enabletracking')) {

	$page['formfrequencytracking'] = $params['updatefrequency_tracking'];
	$page['formvisitorslimit'] = $params['visitors_limit'];
	$page['forminvitationlifetime'] = $params['invitation_lifetime'];
	$page['formtrackinglifetime'] = $params['tracking_lifetime'];

}

$page['enabletracking'] = Settings::get('enabletracking');

$page['stored'] = isset($_GET['stored']);

prepare_menu($operator);
setup_settings_tabs(2);
start_html_output();
require('../view/performance.php');
?>