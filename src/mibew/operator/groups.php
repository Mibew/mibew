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

$operator = check_login();
csrfchecktoken();
check_permissions($operator, $can_administrate);

if (isset($_GET['act']) && $_GET['act'] == 'del') {

	$groupid = isset($_GET['gid']) ? $_GET['gid'] : "";

	if (!preg_match("/^\d+$/", $groupid)) {
		$errors[] = "Cannot delete: wrong argument";
	}

	if (!is_capable($can_administrate, $operator)) {
		$errors[] = "You are not allowed to remove groups";
	}

	if (count($errors) == 0) {
		$link = connect();
		perform_query("delete from ${mysqlprefix}chatgroup where groupid = " . intval($groupid), $link);
		perform_query("delete from ${mysqlprefix}chatgroupoperator where groupid = " . intval($groupid), $link);
		perform_query("update ${mysqlprefix}chatthread set groupid = 0 where groupid = " . intval($groupid), $link);
		mysql_close($link);
		header("Location: $mibewroot/operator/groups.php");
		exit;
	}
}

function is_online($group)
{
	global $settings;
	return $group['ilastseen'] !== NULL && $group['ilastseen'] < $settings['online_timeout'] ? "1" : "";
}

function is_away($group)
{
	global $settings;
	return $group['ilastseenaway'] !== NULL && $group['ilastseenaway'] < $settings['online_timeout'] ? "1" : "";
}


$page = array();
$link = connect();
$page['groups'] = get_groups($link, true);
mysql_close($link);
$page['canmodify'] = is_capable($can_administrate, $operator);

prepare_menu($operator);
start_html_output();
require('../view/groups.php');
?>