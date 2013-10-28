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

require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(dirname(dirname(__FILE__)).'/libs/operator.php');
require_once(dirname(dirname(__FILE__)).'/libs/groups.php');

$operator = check_login();
csrfchecktoken();

function get_group_members($groupid)
{
	$db = Database::getInstance();
	return $db->query(
		"select operatorid from {chatgroupoperator} where groupid = ?",
		array($groupid),
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
}

function update_group_members($groupid, $newvalue)
{
	$db = Database::getInstance();
	$db->query("delete from {chatgroupoperator} where groupid = ?", array($groupid));

	foreach ($newvalue as $opid) {
		$db->query(
			"insert into {chatgroupoperator} (groupid, operatorid) values (?, ?)",
			array($groupid,$opid)
		);
	}
}

function get_operators()
{
	$db = Database::getInstance();
	return $db->query(
		"select * from {chatoperator} order by vclogin",
		NULL,
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
}

$groupid = verifyparam("gid", "/^\d{1,9}$/");
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
$page['currentgroup'] = $group ? topage(htmlspecialchars($group['vclocalname'])) : "";

foreach (get_group_members($groupid) as $rel) {
	$page['formop'][] = $rel['operatorid'];
}

$page['stored'] = isset($_GET['stored']);
prepare_menu($operator);
setup_group_settings_tabs($groupid, 1);
start_html_output();
require(dirname(dirname(__FILE__)).'/view/groupmembers.php');
?>