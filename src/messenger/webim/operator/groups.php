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

require_once('../libs/common.php');
require_once('../libs/operator.php');

$operator = check_login();

if (isset($_GET['act']) && $_GET['act'] == 'del') {

	$groupid = isset($_GET['gid']) ? $_GET['gid'] : "";

	if (!preg_match("/^\d+$/", $groupid)) {
		$errors[] = getlocal("page.groups.error.cannot_delete");
	}

	if (!is_capable($can_administrate, $operator)) {
		$errors[] = getlocal("page.groups.error.forbidden_remove");
	}

	if (count($errors) == 0) {
		$link = connect();
		perform_query("delete from ${mysqlprefix}chatgroup where groupid = $groupid", $link);
		perform_query("delete from ${mysqlprefix}chatgroupoperator where groupid = $groupid", $link);
		perform_query("update ${mysqlprefix}chatthread set groupid = 0 where groupid = $groupid", $link);
		close_connection($link);
		header("Location: $webimroot/operator/groups.php");
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
$sort['by'] = verifyparam("sortby", "/^(name|lastseen|weight)$/", "name");
$sort['desc'] = (verifyparam("sortdirection", "/^(desc|asc)$/", "desc") == "desc");
$link = connect();
$page['groups'] = get_sorted_groups($link, $sort);
close_connection($link);
$page['formsortby'] = $sort['by'];
$page['formsortdirection'] = $sort['desc']?'desc':'asc';
$page['canmodify'] = is_capable($can_administrate, $operator);
$page['availableOrders'] = array(
	array('id' => 'name', 'name' => getlocal('form.field.groupname')),
	array('id' => 'lastseen', 'name' => getlocal('page_agents.status')),
	array('id' => 'weight', 'name' => getlocal('page.groups.weight'))
);
$page['availableDirections'] = array(
	array('id' => 'desc', 'name' => getlocal('page.groups.sortdirection.desc')),
	array('id' => 'asc', 'name' => getlocal('page.groups.sortdirection.asc')),
);

prepare_menu($operator);
start_html_output();
require('../view/groups.php');
?>
