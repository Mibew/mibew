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

require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(dirname(dirname(__FILE__)).'/libs/canned.php');
require_once(dirname(dirname(__FILE__)).'/libs/chat.php');
require_once(dirname(dirname(__FILE__)).'/libs/groups.php');
require_once(dirname(dirname(__FILE__)).'/libs/operator.php');
require_once(dirname(dirname(__FILE__)).'/libs/pagination.php');
require_once(dirname(dirname(__FILE__)).'/libs/expand.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/thread.php');
require_once(dirname(dirname(__FILE__)).'/libs/interfaces/style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/chat_style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/page_style.php');

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

// Initialize chat style which is currently used in system
$chat_style = new ChatStyle(ChatStyle::currentStyle());

$page_style = new PageStyle(PageStyle::currentStyle());

if (!isset($_GET['token'])) {

	$remote_level = get_remote_level($_SERVER['HTTP_USER_AGENT']);
	if ($remote_level != "ajaxed") {
		$errors = array(getlocal("thread.error.old_browser"));
		$chat_style->render('error');
		exit;
	}

	$thread = Thread::load($threadid);
	if (!$thread || !isset($thread->lastToken)) {
		$errors = array(getlocal("thread.error.wrong_thread"));
		$chat_style->render('error');
		exit;
	}

	$viewonly = verifyparam("viewonly", "/^true$/", false);

	$forcetake = verifyparam("force", "/^true$/", false);
	if (!$viewonly && $thread->state == Thread::STATE_CHATTING && $operator['operatorid'] != $thread->agentId) {

		if (!is_capable(CAN_TAKEOVER, $operator)) {
			$errors = array(getlocal("thread.error.cannot_take_over"));
			$chat_style->render('error');
			exit;
		}

		if ($forcetake == false) {
			$page = array(
				'user' => topage($thread->userName),
				'agent' => topage($thread->agentName),
				'link' => $_SERVER['PHP_SELF'] . "?thread=$threadid&amp;force=true",
				'title' => getlocal("confirm.take.head"),
			);
			$page_style->render('confirm');
			exit;
		}
	}

	if (!$viewonly) {
		if(! $thread->take($operator)){
			$errors = array(getlocal("thread.error.cannot_take"));
			$chat_style->render('error');
			exit;
		}
	} else if (!is_capable(CAN_VIEWTHREADS, $operator)) {
		$errors = array(getlocal("thread.error.cannot_view"));
		$chat_style->render('error');
		exit;
	}

	$token = $thread->lastToken;
	header("Location: $mibewroot/operator/agent.php?thread=" . intval($threadid) . "&token=" . urlencode($token));
	exit;
}

$token = verifyparam("token", "/^\d{1,8}$/");

$thread = Thread::load($threadid, $token);
if (!$thread) {
	die("wrong thread");
}

if ($thread->agentId != $operator['operatorid'] && !is_capable(CAN_VIEWTHREADS, $operator)) {
	$errors = array("Cannot view threads");
	$chat_style->render('error');
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
	$chat_style->render('redirect');
} else {
	// Build js application options
	$page['chatOptions'] = json_encode($page['chat']);
	// Expand page
	$chat_style->render('chat');
}

?>