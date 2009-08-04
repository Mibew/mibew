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

require_once('../libs/common.php');
require_once('../libs/chat.php');
require_once('../libs/groups.php');
require_once('../libs/operator.php');
require_once('../libs/pagination.php');
require_once('../libs/expand.php');

$operator = check_login();

loadsettings();
if($settings['enablessl'] == "1" && $settings['forcessl'] == "1") {
	if(!is_secure_request()) {
		$requested = $_SERVER['PHP_SELF'];
		if($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['QUERY_STRING']) {
			header("Location: ".get_app_location(true,true)."/operator/agent.php?".$_SERVER['QUERY_STRING']);
		} else {
			die("only https connections are handled");
		} 		
		exit;
	}
}

$threadid = verifyparam( "thread", "/^\d{1,8}$/");

if( !isset($_GET['token']) ) {

	$remote_level = get_remote_level($_SERVER['HTTP_USER_AGENT']);
	if( $remote_level != "ajaxed" ) {
		die("old browser is used, please update it");
	}

	$thread = thread_by_id($threadid);
	if( !$thread || !isset($thread['ltoken']) ) {
		die("wrong thread");
	}

	$viewonly = verifyparam( "viewonly", "/^true$/", false);

	$forcetake = verifyparam("force", "/^true$/", false);
	if( !$viewonly && $thread['istate'] == $state_chatting && $operator['operatorid'] != $thread['agentId'] ) {

		if(!is_capable($can_takeover, $operator)) {
			$errors = array("Cannot take over");
			start_html_output();
			expand("../styles", getchatstyle(), "error.tpl");
			exit;
		}

		if( $forcetake == false ) {
			$page = array(
				'user' => topage($thread['userName']), 'agent' => topage($thread['agentName']), 'link' => $_SERVER['PHP_SELF']."?thread=$threadid&amp;force=true"
			);
			start_html_output();
			require('../view/confirm.php');
			exit;
		}
	}

	if (!$viewonly) {
		take_thread($thread,$operator);
	} else if(!is_capable($can_viewthreads, $operator)) {
		$errors = array("Cannot view threads");
		start_html_output();
		expand("../styles", getchatstyle(), "error.tpl");
		exit;
	}

	$token = $thread['ltoken'];
	header("Location: $webimroot/operator/agent.php?thread=$threadid&token=$token&level=$remote_level");
	exit;
}

$token = verifyparam( "token", "/^\d{1,8}$/");

$thread = thread_by_id($threadid);
if( !$thread || !isset($thread['ltoken']) || $token != $thread['ltoken'] ) {
	die("wrong thread");
}

if($thread['agentId'] != $operator['operatorid'] && !is_capable($can_viewthreads, $operator)) {
	$errors = array("Cannot view threads");
	start_html_output();
	expand("../styles", getchatstyle(), "error.tpl");
	exit;
}

setup_chatview_for_operator($thread, $operator);

start_html_output();

$pparam = verifyparam( "act", "/^(redirect)$/", "default");
if( $pparam == "redirect" ) {
	setup_redirect_links($threadid,$token);
	expand("../styles", getchatstyle(), "redirect.tpl");
} else {
	expand("../styles", getchatstyle(), "chat.tpl");
}

?>