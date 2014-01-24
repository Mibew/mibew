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

require_once('../libs/common.php');
require_once('../libs/chat.php');
require_once('../libs/groups.php');
require_once('../libs/operator.php');
require_once('../libs/pagination.php');
require_once('../libs/expand.php');

$operator = check_login();

loadsettings();
if ($settings['enablessl'] == "1" && $settings['forcessl'] == "1") {
	if (!is_secure_request()) {
		$requested = $_SERVER['PHP_SELF'];
		if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['QUERY_STRING']) {
			header("Location: " . get_app_location(true, true) . "/operator/agent.php?" . $_SERVER['QUERY_STRING']);
		} else {
			die("only https connections are handled");
		}
		exit;
	}
}

$threadid = verifyparam("thread", "/^\d{1,10}$/");

if (!isset($_GET['token'])) {

	$remote_level = get_remote_level($_SERVER['HTTP_USER_AGENT']);
	if ($remote_level != "ajaxed") {
		die("old browser is used, please update it");
	}

	$thread = thread_by_id($threadid);
	if (!$thread || !isset($thread['ltoken'])) {
		die("wrong thread");
	}

	$viewonly = verifyparam("viewonly", "/^true$/", false);

	$forcetake = verifyparam("force", "/^true$/", false);
	if (!$viewonly && $thread['istate'] == $state_chatting && $operator['operatorid'] != $thread['agentId']) {

		if (!is_capable($can_takeover, $operator)) {
			$errors = array("Cannot take over");
			start_html_output();
			expand("../styles", getchatstyle(), "error.tpl");
			exit;
		}

		if ($forcetake == false) {
			$page = array(
				'user' => topage($thread['userName']), 'agent' => topage($thread['agentName']), 'link' => $_SERVER['PHP_SELF'] . "?thread=$threadid&force=true"
			);
			start_html_output();
			require('../view/confirm.php');
			exit;
		}
	}

	if (!$viewonly) {
		take_thread($thread, $operator);
	} else if (!is_capable($can_viewthreads, $operator)) {
		$errors = array("Cannot view threads");
		start_html_output();
		expand("../styles", getchatstyle(), "error.tpl");
		exit;
	}

	$token = $thread['ltoken'];
	header("Location: $mibewroot/operator/agent.php?thread=" . intval($threadid) . "&token=" . intval($token) . "&level=" . urlencode($remote_level));
	exit;
}

$token = verifyparam("token", "/^\d{1,10}$/");

$thread = thread_by_id($threadid);
if (!$thread || !isset($thread['ltoken']) || $token != $thread['ltoken']) {
	die("wrong thread");
}

if ($thread['agentId'] != $operator['operatorid'] && !is_capable($can_viewthreads, $operator)) {
	$errors = array("Cannot view threads");
	start_html_output();
	expand("../styles", getchatstyle(), "error.tpl");
	exit;
}

setup_chatview_for_operator($thread, $operator);

start_html_output();

$pparam = verifyparam("act", "/^(redirect)$/", "default");
if ($pparam == "redirect") {
	setup_redirect_links($threadid, $token);
	expand("../styles", getchatstyle(), "redirect.tpl");
} else {
	expand("../styles", getchatstyle(), "chat.tpl");
}

?>