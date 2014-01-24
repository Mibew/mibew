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
require_once('../libs/userinfo.php');

$operator = check_login();

$page = array();

loadsettings();
setlocale(LC_TIME, getstring("time.locale"));

function thread_info($id)
{
	global $mysqlprefix;
	$link = connect();
	$thread = select_one_row("select userName,agentName,remote,userAgent," .
							 "unix_timestamp(dtmmodified) as modified, unix_timestamp(dtmcreated) as created," .
							 "vclocalname as groupName " .
							 "from ${mysqlprefix}chatthread left join ${mysqlprefix}chatgroup on ${mysqlprefix}chatthread.groupid = ${mysqlprefix}chatgroup.groupid " .
							 "where threadid = " . intval($id), $link);
	mysql_close($link);
	return $thread;
}


if (isset($_GET['threadid'])) {
	$threadid = verifyparam("threadid", "/^(\d{1,10})?$/", "");
	$lastid = -1;
	$page['threadMessages'] = get_messages($threadid, "html", false, $lastid);
	$page['thread'] = thread_info($threadid);
}

prepare_menu($operator, false);
start_html_output();
require('../view/thread_log.php');
?>