<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
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

require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('../libs/settings.php');

csrfchecktoken();

$operator = check_login();

$page = array('agentId' => '');
$errors = array();

$options = array(
	'online_timeout', 'updatefrequency_operator', 'updatefrequency_chat',
	'updatefrequency_oldchat', 'max_connections_from_one_host',
	'updatefrequency_tracking', 'visitors_limit', 'invitation_lifetime',
	'tracking_lifetime', 'thread_lifetime' );

loadsettings();
$params = array();
foreach ($options as $opt) {
	$params[$opt] = $settings[$opt];
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

	if ($settings['enabletracking']) {

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
			$settings[$opt] = $params[$opt];
		}
		update_settings();
		header("Location: $webimroot/operator/performance.php?stored");
		exit;
	}
}

$page['formonlinetimeout'] = $params['online_timeout'];
$page['formfrequencyoperator'] = $params['updatefrequency_operator'];
$page['formfrequencychat'] = $params['updatefrequency_chat'];
$page['formfrequencyoldchat'] = $params['updatefrequency_oldchat'];
$page['formonehostconnections'] = $params['max_connections_from_one_host'];
$page['formthreadlifetime'] = $params['thread_lifetime'];

if ($settings['enabletracking']) {

	$page['formfrequencytracking'] = $params['updatefrequency_tracking'];
	$page['formvisitorslimit'] = $params['visitors_limit'];
	$page['forminvitationlifetime'] = $params['invitation_lifetime'];
	$page['formtrackinglifetime'] = $params['tracking_lifetime'];

}

$page['enabletracking'] = $settings['enabletracking'];

$page['stored'] = isset($_GET['stored']);

prepare_menu($operator);
setup_settings_tabs(2);
start_html_output();
require('../view/performance.php');
?>
