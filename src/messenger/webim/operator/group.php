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
require_once('../libs/groups.php');

$operator = check_login();

$page = array('grid' => '');
$errors = array();
$groupid = '';

function group_by_name($name)
{
	global $mysqlprefix;
	$link = connect();
	$group = select_one_row(
		"select * from ${mysqlprefix}chatgroup where vclocalname = '" . db_escape_string($name) . "'", $link);
	close_connection($link);
	return $group;
}

function check_group_params($group, $extra_params = NULL)
{
	$obligatory_params = array(
		'name',
		'description',
		'commonname',
		'commondescription',
		'email',
		'weight',
		'parent',
		'chattitle',
		'hosturl',
		'logo');
	$params = is_null($extra_params)?$obligatory_params:array_merge($obligatory_params,$extra_params);
	if(count(array_diff($params, array_keys($group))) != 0){
		die('Wrong parameters set!');
	}
}

/**
 * @param array $group Operators' group.
 * The $group array must contains following keys:
 * name, description, commonname, commondescription,
 * email, weight, parent, title, chattitle, hosturl, logo
 */
function create_group($group)
{
	global $mysqlprefix;
	check_group_params($group);
	$link = connect();
	$query = sprintf(
		"insert into ${mysqlprefix}chatgroup (parent, vclocalname,vclocaldescription,vccommonname,vccommondescription,vcemail,vctitle,vcchattitle,vchosturl,vclogo,iweight) values (%s, '%s','%s','%s','%s','%s','%s','%s','%s','%s',%u)",
		($group['parent']?(int)$group['parent']:'NULL'),
		db_escape_string($group['name']),
		db_escape_string($group['description']),
		db_escape_string($group['commonname']),
		db_escape_string($group['commondescription']),
		db_escape_string($group['email']),
		db_escape_string($group['title']),
		db_escape_string($group['chattitle']),
		db_escape_string($group['hosturl']),
		db_escape_string($group['logo']),
		$group['weight']);

	perform_query($query, $link);
	$id = db_insert_id($link);

	$newdep = select_one_row("select * from ${mysqlprefix}chatgroup where groupid = $id", $link);
	close_connection($link);
	return $newdep;
}

/**
 * @param array $group Operators' group.
 * The $group array must contains following keys:
 * id, name, description, commonname, commondescription,
 * email, weight, parent, title, chattitle, hosturl, logo
 */
function update_group($group)
{
	global $mysqlprefix;
	check_group_params($group, array('id'));
	$link = connect();
	$query = sprintf(
		"update ${mysqlprefix}chatgroup set parent = %s, vclocalname = '%s', vclocaldescription = '%s', vccommonname = '%s', vccommondescription = '%s', vcemail = '%s', vctitle = '%s', vcchattitle = '%s', vchosturl = '%s', vclogo = '%s', iweight = %u where groupid = %s",
		($group['parent']?(int)$group['parent']:'NULL'),
		db_escape_string($group['name']),
		db_escape_string($group['description']),
		db_escape_string($group['commonname']),
		db_escape_string($group['commondescription']),
		db_escape_string($group['email']),
		db_escape_string($group['title']),
		db_escape_string($group['chattitle']),
		db_escape_string($group['hosturl']),
		db_escape_string($group['logo']),
		$group['weight'],
		$group['id']);
	perform_query($query, $link);

	if ($group['parent']) {
		$query = sprintf("update ${mysqlprefix}chatgroup set parent = NULL where parent = %u", $group['id']);
		perform_query($query, $link);
	}
	close_connection($link);
}

if (isset($_POST['name'])) {
	$groupid = verifyparam("gid", "/^(\d{1,9})?$/", "");
	$name = getparam('name');
	$description = getparam('description');
	$commonname = getparam('commonname');
	$commondescription = getparam('commondescription');
	$email = getparam('email');
	$weight = getparam('weight');
	$parentgroup = verifyparam("parentgroup", "/^(\d{1,9})?$/", "");
	$title = getparam('title');
	$chattitle = getparam('chattitle');
	$hosturl = getparam('hosturl');
	$logo = getparam('logo');

	if (!$name)
		$errors[] = no_field("form.field.groupname");

	if ($email != '' && !is_valid_email($email))
		$errors[] = wrong_field("form.field.mail");

	if (! preg_match("/^(\d{1,9})?$/", $weight))
		$errors[] = wrong_field("form.field.groupweight");

	if ($weight == '')
		$weight = 0;

	if (! $parentgroup)
		$parentgroup = NULL;

	$existing_group = group_by_name($name);
	if ((!$groupid && $existing_group) ||
		($groupid && $existing_group && $groupid != $existing_group['groupid']))
		$errors[] = getlocal("page.group.duplicate_name");

	if (count($errors) == 0) {
		if (!$groupid) {
			$newdep = create_group(array(
				'name' => $name,
				'description' => $description,
				'commonname' => $commonname,
				'commondescription' => $commondescription,
				'email' => $email,
				'weight' => $weight,
				'parent' => $parentgroup,
				'title' => $title,
				'chattitle' => $chattitle,
				'hosturl' => $hosturl,
				'logo' => $logo));
			header("Location: $webimroot/operator/groupmembers.php?gid=" . $newdep['groupid']);
			exit;
		} else {
			update_group(array(
				'id' => $groupid,
				'name' => $name,
				'description' => $description,
				'commonname' => $commonname,
				'commondescription' => $commondescription,
				'email' => $email,
				'weight' => $weight,
				'parent' => $parentgroup,
				'title' => $title,
				'chattitle' => $chattitle,
				'hosturl' => $hosturl,
				'logo' => $logo));
			header("Location: $webimroot/operator/group.php?gid=$groupid&stored");
			exit;
		}
	} else {
		$page['formname'] = topage($name);
		$page['formdescription'] = topage($description);
		$page['formcommonname'] = topage($commonname);
		$page['formcommondescription'] = topage($commondescription);
		$page['formemail'] = topage($email);
		$page['formweight'] = topage($weight);
		$page['formparentgroup'] = topage($parentgroup);
		$page['grid'] = topage($groupid);
		$page['formtitle'] = topage($title);
		$page['formchattitle'] = topage($chattitle);
		$page['formhosturl'] = topage($hosturl);
		$page['formlogo'] = topage($logo);
	}

} else if (isset($_GET['gid'])) {
	$groupid = verifyparam('gid', "/^\d{1,9}$/");
	$group = group_by_id($groupid);

	if (!$group) {
		$errors[] = getlocal("page.group.no_such");
		$page['grid'] = topage($groupid);
	} else {
		$page['formname'] = topage($group['vclocalname']);
		$page['formdescription'] = topage($group['vclocaldescription']);
		$page['formcommonname'] = topage($group['vccommonname']);
		$page['formcommondescription'] = topage($group['vccommondescription']);
		$page['formemail'] = topage($group['vcemail']);
		$page['formweight'] = topage($group['iweight']);
		$page['formparentgroup'] = topage($group['parent']);
		$page['grid'] = topage($group['groupid']);
		$page['formtitle'] = topage($group['vctitle']);
		$page['formchattitle'] = topage($group['vcchattitle']);
		$page['formhosturl'] = topage($group['vchosturl']);
		$page['formlogo'] = topage($group['vclogo']);
	}
}

$page['stored'] = isset($_GET['stored']);
$page['availableParentGroups'] = get_available_parent_groups($groupid);
prepare_menu($operator);
setup_group_settings_tabs($groupid, 0);
start_html_output();
require('../view/group.php');
?>
