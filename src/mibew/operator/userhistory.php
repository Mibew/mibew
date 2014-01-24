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
require_once('../libs/pagination.php');

$operator = check_login();
loadsettings();

$page = array();

setlocale(LC_TIME, getstring("time.locale"));

$userid = "";
if (isset($_GET['userid'])) {
	$userid = verifyparam("userid", "/^.{0,63}$/", "");
}

function threads_by_userid($userid)
{
	global $mysqlprefix;
	if ($userid == "") {
		return null;
	}
	$link = connect();

	$query = sprintf("select unix_timestamp(dtmcreated) as created, unix_timestamp(dtmmodified) as modified, " .
					 " threadid, remote, agentName, userName " .
					 "from ${mysqlprefix}chatthread " .
					 "where userid='%s' order by created DESC", mysql_real_escape_string($userid, $link));

	$result = mysql_query($query, $link) or die(' Query failed: ' . mysql_error($link));

	$foundThreads = array();
	while ($thread = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$foundThreads[] = $thread;
	}

	mysql_free_result($result);
	mysql_close($link);
	return $foundThreads;
}

$found = threads_by_userid($userid);

prepare_menu($operator);
setup_pagination($found, 6);
start_html_output();
require('../view/userhistory.php');
?>