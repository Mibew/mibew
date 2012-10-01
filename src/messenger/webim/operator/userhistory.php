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
require_once('../libs/pagination.php');

$operator = check_login();

$page = array();

setlocale(LC_TIME, getstring("time.locale"));

$userid = "";
if (isset($_GET['userid'])) {
	$userid = verifyparam("userid", "/^.{0,63}$/", "");
}

function threads_by_userid($userid)
{
	$db = Database::getInstance();
	if ($userid == "") {
		return null;
	}

	return $db->query(
		"select {chatthread}.* " .
		"from {chatthread} " .
		"where userid=? order by dtmcreated DESC",
		array($userid),
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
}

$found = threads_by_userid($userid);

prepare_menu($operator);
setup_pagination($found, 6);
foreach ($page['pagination.items'] as $key => $item) {
	$page['pagination.items'][$key] = Thread::createFromDbInfo($item);
}
start_html_output();
require('../view/userhistory.php');
?>