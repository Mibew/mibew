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

function group_by_id($id)
{
	global $mysqlprefix;
	$link = connect();
	$group = select_one_row(
		"select * from ${mysqlprefix}chatgroup where groupid = " . intval($id), $link);
	mysql_close($link);
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
	global $page, $mibewroot, $settings;
	if ($gid) {
		$page['tabselected'] = $active;
		$page['tabs'] = array(
			array('title' => getlocal("page_group.tab.main"), 'link' => "$mibewroot/operator/group.php?gid=$gid"),
			array('title' => getlocal("page_group.tab.members"), 'link' => "$mibewroot/operator/groupmembers.php?gid=$gid"),
		);
	} else {
		$page['tabs'] = array();
	}
}

function get_operator_groupslist($operatorid, $link)
{
	global $settings, $mysqlprefix;
	if ($settings['enablegroups'] == '1') {
		$groupids = array(0);
		$allgroups = select_multi_assoc("select groupid from ${mysqlprefix}chatgroupoperator where operatorid = " . intval($operatorid) . " order by groupid", $link);
		foreach ($allgroups as $g) {
			$groupids[] = $g['groupid'];
		}
		return implode(",", $groupids);
	} else {
		return "";
	}
}

?>