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
require_once('../libs/userinfo.php');

$operator = check_login();

$page = array();

setlocale(LC_TIME, getstring("time.locale"));

function thread_info($id)
{
	$db = Database::getInstance();
	$thread_info = $db->query(
		"select {chatthread}.*, {chatgroup}.vclocalname as groupName " .
		"from {chatthread} left join {chatgroup} on {chatthread}.groupid = {chatgroup}.groupid " .
		"where threadid = ?",
		array($id),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
	$thread = Thread::createFromDbInfo($thread_info);
	return array(
		'thread' => $thread,
		'groupName' => $thread_info['groupName']
	);
}


if (isset($_GET['threadid'])) {
	// Load thread info
	$threadid = verifyparam("threadid", "/^(\d{1,9})?$/", "");
	$thread_info = thread_info($threadid);
	$page['thread_info'] = $thread_info;

	// Build messages list
	$lastid = -1;
	$messages = $thread_info['thread']->getMessages(false, $lastid);
	foreach ($messages as $msg) {
		if ($msg['kind'] == Thread::KIND_AVATAR) {
			continue;
		}
		$page['threadMessages'][] = Thread::themeMessage($msg);
	}
}

prepare_menu($operator, false);
start_html_output();
require('../view/thread_log.php');
?>