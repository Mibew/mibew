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
use Mibew\Database;
use Mibew\Thread;

// Initialize libraries
require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(MIBEW_FS_ROOT.'/libs/operator.php');
require_once(MIBEW_FS_ROOT.'/libs/chat.php');
require_once(MIBEW_FS_ROOT.'/libs/expand.php');
require_once(MIBEW_FS_ROOT.'/libs/groups.php');
require_once(MIBEW_FS_ROOT.'/libs/interfaces/style.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/style.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/chat_style.php');

$operator = check_login();

$threadid = verifyparam("thread", "/^\d{1,8}$/");
$token = verifyparam("token", "/^\d{1,8}$/");

$thread = Thread::load($threadid, $token);
if (! $thread) {
	die("wrong thread");
}

$page = array();
$errors = array();

// Initialize chat style which is currently used in system
$chat_style = new ChatStyle(ChatStyle::currentStyle());

if (isset($_GET['nextGroup'])) {
	$nextid = verifyparam("nextGroup", "/^\d{1,8}$/");
	$nextGroup = group_by_id($nextid);

	if ($nextGroup) {
		$page['message'] = getlocal2("chat.redirected.group.content", array(topage(get_group_name($nextGroup))));
		if ($thread->state == Thread::STATE_CHATTING) {
			$thread->state = Thread::STATE_WAITING;
			$thread->nextAgent = 0;
			$thread->groupId = $nextid;
			$thread->agentId = 0;
			$thread->agentName = '';
			$thread->save();

			$thread->postMessage(
				Thread::KIND_EVENTS,
				getstring2_(
					"chat.status.operator.redirect",
					array(get_operator_name($operator)),
					$thread->locale
				)
			);
		} else {
			$errors[] = getlocal("chat.redirect.cannot");
		}
	} else {
		$errors[] = "Unknown group";
	}

} else {
	$nextid = verifyparam("nextAgent", "/^\d{1,8}$/");
	$nextOperator = operator_by_id($nextid);

	if ($nextOperator) {
		$page['message'] = getlocal2("chat.redirected.content", array(topage(get_operator_name($nextOperator))));
		if ($thread->state == Thread::STATE_CHATTING) {
			$thread->state = Thread::STATE_WAITING;
			$thread->nextAgent = $nextid;
			$thread->agentId = 0;
			if ($thread->groupId != 0) {
				$db = Database::getInstance();
				list($groups_count) = $db->query(
					"select count(*) AS count from {chatgroupoperator} " .
					"where operatorid = ? and groupid = ?",
					array($nextid, $thread->groupId),
					array(
						'return_rows' => Database::RETURN_ONE_ROW, 
						'fetch_type' => Database::FETCH_NUM
					)
				);
				if ($groups_count === 0) {
					$thread->groupId = 0;
				}
			}
			$thread->save();
			$thread->postMessage(
				Thread::KIND_EVENTS,
				getstring2_(
					"chat.status.operator.redirect",
					array(get_operator_name($operator)),
					$thread->locale
				)
			);
		} else {
			$errors[] = getlocal("chat.redirect.cannot");
		}
	} else {
		$errors[] = "Unknown operator";
	}
}

$page = array_merge_recursive(
	$page,
	setup_logo()
);

if (count($errors) > 0) {
	$chat_style->render('error');
} else {
	$chat_style->render('redirected');
}

?>