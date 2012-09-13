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
require_once('../libs/operator.php');
require_once('../libs/chat.php');
require_once('../libs/expand.php');
require_once('../libs/groups.php');

$operator = check_login();

$threadid = verifyparam("thread", "/^\d{1,8}$/");
$token = verifyparam("token", "/^\d{1,8}$/");

$thread = thread_by_id($threadid);
if (!$thread || !isset($thread['ltoken']) || $token != $thread['ltoken']) {
	die("wrong thread");
}

$page = array();
$errors = array();

if (isset($_GET['nextGroup'])) {
	$nextid = verifyparam("nextGroup", "/^\d{1,8}$/");
	$nextGroup = group_by_id($nextid);

	if ($nextGroup) {
		$page['message'] = getlocal2("chat.redirected.group.content", array(topage(get_group_name($nextGroup))));
		if ($thread['istate'] == $state_chatting) {
			commit_thread($threadid,
						  array("istate" => $state_waiting, "nextagent" => 0, "groupid" => $nextid, "agentId" => 0, "agentName" => "''"));
			post_message_($thread['threadid'], $kind_events,
						  getstring2_("chat.status.operator.redirect",
									  array(get_operator_name($operator)), $thread['locale']));
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
		if ($thread['istate'] == $state_chatting) {
			$threadupdate = array("istate" => $state_waiting, "nextagent" => $nextid, "agentId" => 0);
			if ($thread['groupid'] != 0) {
				$db = Database::getInstance();
				list($groups_count) = $db->query(
					"select count(*) AS count from {chatgroupoperator} " .
					"where operatorid = ? and groupid = ?",
					array($nextid, $thread['groupid']),
					array(
						'return_rows' => Database::RETURN_ONE_ROW, 
						'fetch_type' => Database::FETCH_NUM
					)
				);
				if ($groups_count === 0) {
					$threadupdate['groupid'] = 0;
				}
			}
			commit_thread($threadid, $threadupdate);
			post_message_($thread['threadid'], $kind_events,
						  getstring2_("chat.status.operator.redirect",
									  array(get_operator_name($operator)), $thread['locale']));
		} else {
			$errors[] = getlocal("chat.redirect.cannot");
		}
	} else {
		$errors[] = "Unknown operator";
	}
}

setup_logo();
if (count($errors) > 0) {
	expand("../styles/dialogs", getchatstyle(), "error.tpl");
} else {
	expand("../styles/dialogs", getchatstyle(), "redirected.tpl");
}

?>