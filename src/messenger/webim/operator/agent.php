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

require_once('../libs/init.php');
require_once('../libs/canned.php');
require_once('../libs/chat.php');
require_once('../libs/groups.php');
require_once('../libs/operator.php');
require_once('../libs/pagination.php');
require_once('../libs/expand.php');
require_once('../libs/classes/thread.php');

$operator = check_login();

if (Settings::get('enablessl') == "1" && Settings::get('forcessl') == "1") {
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

$threadid = verifyparam("thread", "/^\d{1,8}$/");
$page = array();

if (!isset($_GET['token'])) {

	$remote_level = get_remote_level($_SERVER['HTTP_USER_AGENT']);
	if ($remote_level != "ajaxed") {
		$errors = array(getlocal("thread.error.old_browser"));
		start_html_output();
		expand("../styles/dialogs", getchatstyle(), "error.tpl");
		exit;
	}

	$thread = Thread::load($threadid);
	if (!$thread || !isset($thread->lastToken)) {
		$errors = array(getlocal("thread.error.wrong_thread"));
		start_html_output();
		expand("../styles/dialogs", getchatstyle(), "error.tpl");
		exit;
	}

	$viewonly = verifyparam("viewonly", "/^true$/", false);

	$forcetake = verifyparam("force", "/^true$/", false);
	if (!$viewonly && $thread->state == Thread::STATE_CHATTING && $operator['operatorid'] != $thread->agentId) {

		if (!is_capable($can_takeover, $operator)) {
			$errors = array(getlocal("thread.error.cannot_take_over"));
			start_html_output();
			expand("../styles/dialogs", getchatstyle(), "error.tpl");
			exit;
		}

		if ($forcetake == false) {
			$page = array(
				'user' => topage($thread->userName),
				'agent' => topage($thread->agentName),
				'link' => $_SERVER['PHP_SELF'] . "?thread=$threadid&amp;force=true"
			);
			start_html_output();
			require('../view/confirm.php');
			exit;
		}
	}

	if (!$viewonly) {
		if(! $thread->take($operator)){
			$errors = array(getlocal("thread.error.cannot_take"));
			start_html_output();
			expand("../styles/dialogs", getchatstyle(), "error.tpl");
			exit;
		}
	} else if (!is_capable($can_viewthreads, $operator)) {
		$errors = array(getlocal("thread.error.cannot_view"));
		start_html_output();
		expand("../styles/dialogs", getchatstyle(), "error.tpl");
		exit;
	}

	$token = $thread->lastToken;
	header("Location: $webimroot/operator/agent.php?thread=$threadid&token=$token&level=$remote_level");
	exit;
}

$token = verifyparam("token", "/^\d{1,8}$/");

$thread = Thread::load($threadid, $token);
if (!$thread) {
	die("wrong thread");
}

if ($thread->agentId != $operator['operatorid'] && !is_capable($can_viewthreads, $operator)) {
	$errors = array("Cannot view threads");
	start_html_output();
	expand("../styles/dialogs", getchatstyle(), "error.tpl");
	exit;
}

$page = array_merge_recursive(
	$page,
	setup_chatview_for_operator($thread, $operator)
);

start_html_output();

$pparam = verifyparam("act", "/^(redirect)$/", "default");
if ($pparam == "redirect") {
	setup_redirect_links($threadid, $operator, $token);
	expand("../styles/dialogs", getchatstyle(), "redirect.tpl");
} else {
	// Load JavaScript plugins and JavaScripts, CSS files required by them
	$page['additional_css'] = get_additional_css('agent_chat_window');
	$page['additional_js'] = get_additional_js('agent_chat_window');
	$page['js_plugin_options'] = get_js_plugin_options('agent_chat_window');
	// Build js application options
	$page['chatModule'] = json_encode($page['chat']);
	// Expand page
	expand("../styles/dialogs", getchatstyle(), "chat.tpl");
}

?>