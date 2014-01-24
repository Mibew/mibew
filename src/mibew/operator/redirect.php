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
require_once('../libs/operator.php');
require_once('../libs/chat.php');
require_once('../libs/expand.php');
require_once('../libs/groups.php');

$operator = check_login();

$threadid = verifyparam("thread", "/^\d{1,10}$/");
$token = verifyparam("token", "/^\d{1,10}$/");

$thread = thread_by_id($threadid);
if (!$thread || !isset($thread['ltoken']) || $token != $thread['ltoken']) {
	die("wrong thread");
}

$page = array();
$errors = array();

if (isset($_GET['nextGroup'])) {
	$nextid = verifyparam("nextGroup", "/^\d{1,10}$/");
	$nextGroup = group_by_id($nextid);

	if ($nextGroup) {
		$page['message'] = getlocal2("chat.redirected.group.content", array(safe_htmlspecialchars(topage(get_group_name($nextGroup)))));
		if ($thread['istate'] == $state_chatting) {
			$link = connect();
			commit_thread($threadid,
						  array("istate" => intval($state_waiting), "nextagent" => 0, "groupid" => intval($nextid), "agentId" => 0, "agentName" => "''"), $link);
			post_message_($thread['threadid'], $kind_events,
						  getstring2_("chat.status.operator.redirect",
									  array(get_operator_name($operator)), $thread['locale'], true), $link);
			mysql_close($link);
		} else {
			$errors[] = getlocal("chat.redirect.cannot");
		}
	} else {
		$errors[] = getlocal("chat.redirect.unknown_group");
	}

} else {
	$nextid = verifyparam("nextAgent", "/^\d{1,10}$/");
	$nextOperator = operator_by_id($nextid);

	if ($nextOperator) {
		$page['message'] = getlocal2("chat.redirected.content", array(safe_htmlspecialchars(topage(get_operator_name($nextOperator)))));
		if ($thread['istate'] == $state_chatting) {
			$link = connect();
			$threadupdate = array("istate" => intval($state_waiting), "nextagent" => intval($nextid), "agentId" => 0);
			if ($thread['groupid'] != 0) {
				if (FALSE === select_one_row("select groupid from ${mysqlprefix}chatgroupoperator where operatorid = " . intval($nextid) . " and groupid = " . intval($thread['groupid']), $link)) {
					$threadupdate['groupid'] = 0;
				}
			}
			commit_thread($threadid, $threadupdate, $link);
			post_message_($thread['threadid'], $kind_events,
						  getstring2_("chat.status.operator.redirect",
									  array(get_operator_name($operator)), $thread['locale'], true), $link);
			mysql_close($link);
		} else {
			$errors[] = getlocal("chat.redirect.cannot");
		}
	} else {
		$errors[] = getlocal("chat.redirect.unknown_operator");
	}
}

setup_logo();
if (count($errors) > 0) {
	expand("../styles", getchatstyle(), "error.tpl");
} else {
	expand("../styles", getchatstyle(), "redirected.tpl");
}

?>