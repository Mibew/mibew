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
require_once('../libs/groups.php');

$operator = check_login();
csrfchecktoken();
check_permissions($operator, $can_administrate);

function get_group_members($groupid)
{
	global $mysqlprefix;
	$link = connect();
	$query = "select operatorid from ${mysqlprefix}chatgroupoperator where groupid = " . intval($groupid);
	$result = select_multi_assoc($query, $link);
	mysql_close($link);
	return $result;
}

function update_group_members($groupid, $newvalue)
{
	global $mysqlprefix;
	$link = connect();
	perform_query("delete from ${mysqlprefix}chatgroupoperator where groupid = " . intval($groupid), $link);
	foreach ($newvalue as $opid) {
		perform_query(sprintf("insert into ${mysqlprefix}chatgroupoperator (groupid, operatorid) values (%s, %s)", intval($groupid), intval($opid)), $link);
	}
	mysql_close($link);
}

function get_operators()
{
	global $mysqlprefix;
	$link = connect();

	$query = "select * from ${mysqlprefix}chatoperator order by vclogin";
	$result = select_multi_assoc($query, $link);
	mysql_close($link);
	return $result;
}

$groupid = verifyparam("gid", "/^\d{1,10}$/");
$page = array('groupid' => $groupid);
$page['operators'] = get_operators();
$errors = array();

$group = group_by_id($groupid);

if (!$group) {
	$errors[] = getlocal("page.group.no_such");

} else if (isset($_POST['gid'])) {

	$new_members = array();
	foreach ($page['operators'] as $op) {
		if (verifyparam("op" . $op['operatorid'], "/^on$/", "") == "on") {
			$new_members[] = $op['operatorid'];
		}
	}

	update_group_members($groupid, $new_members);
	header("Location: $mibewroot/operator/groupmembers.php?gid=" . intval($groupid) . "&stored");
	exit;
}

$page['formop'] = array();
$page['currentgroup'] = $group ? topage($group['vclocalname']) : "";

foreach (get_group_members($groupid) as $rel) {
	$page['formop'][] = $rel['operatorid'];
}

$page['stored'] = isset($_GET['stored']);
prepare_menu($operator);
setup_group_settings_tabs($groupid, 1);
start_html_output();
require('../view/groupmembers.php');
?>