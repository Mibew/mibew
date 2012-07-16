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

function group_by_id($id)
{
	$db = Database::getInstance();
	$group = $db->query(
		"select * from {chatgroup} where groupid = ?",
		array($id),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
	return $group;
}

function get_group_name($group)
{
	global $home_locale, $current_locale;
	if ($home_locale == $current_locale || !isset($group['vccommonname']) || !$group['vccommonname'])
		return $group['vclocalname'];
	else
		return $group['vccommonname'];
}

function setup_group_settings_tabs($gid, $active)
{
	global $page, $webimroot;
	if ($gid) {
		$page['tabs'] = array(
			getlocal("page_group.tab.main") => $active != 0 ? "$webimroot/operator/group.php?gid=$gid" : "",
			getlocal("page_group.tab.members") => $active != 1 ? "$webimroot/operator/groupmembers.php?gid=$gid" : "",
		);
	} else {
		$page['tabs'] = array();
	}
}

function get_operator_groupslist($operatorid)
{
	$db = Database::getInstance();
	if (Settings::get('enablegroups') == '1') {
		$groupids = array(0);
		$allgroups = $db->query(
			"select groupid from {chatgroupoperator} where operatorid = ? order by groupid",
			array($operatorid),
			array('return_rows' => Database::RETURN_ALL_ROWS)
		);
		foreach ($allgroups as $g) {
			$groupids[] = $g['groupid'];
		}
		return implode(",", $groupids);
	} else {
		return "";
	}
}

function get_available_parent_groups($skipgroup)
{
	$db = Database::getInstance();
	$groupslist = $db->query(
		"select {chatgroup}.groupid as groupid, parent, vclocalname " .
		"from {chatgroup} order by vclocalname",
		NULL,
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
	$result = array(array('groupid' => '', 'level' => '', 'vclocalname' => getlocal("form.field.groupparent.root")));

	if ($skipgroup) {
		$skipgroup = (array)$skipgroup;
	} else {
		$skipgroup = array();
	}

	$result = array_merge($result, get_sorted_child_groups_($groupslist, $skipgroup, 0) );
	return $result;
}

function group_has_children($groupid)
{
	$db = Database::getInstance();
	$children = $db->query(
		"select COUNT(*) as count from {chatgroup} where parent = ?",
		array($groupid),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
	return ($children['count'] > 0);
}

function get_top_level_group($group)
{
	return is_null($group['parent'])?$group:group_by_id($group['parent']);
}

?>