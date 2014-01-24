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

$page = array('grid' => '');
$errors = array();
$groupid = '';

function group_by_name($name)
{
	global $mysqlprefix;
	$link = connect();
	$group = select_one_row(
		"select * from ${mysqlprefix}chatgroup where vclocalname = '" . mysql_real_escape_string($name) . "'", $link);
	mysql_close($link);
	return $group;
}

function create_group($name, $descr, $commonname, $commondescr, $email)
{
	global $mysqlprefix;
	$link = connect();
	$query = sprintf(
		"insert into ${mysqlprefix}chatgroup (vclocalname,vclocaldescription,vccommonname,vccommondescription,vcemail) values ('%s','%s','%s','%s','%s')",
		mysql_real_escape_string($name, $link),
		mysql_real_escape_string($descr, $link),
		mysql_real_escape_string($commonname, $link),
		mysql_real_escape_string($commondescr, $link),
		mysql_real_escape_string($email, $link));

	perform_query($query, $link);
	$id = mysql_insert_id($link);

	$newdep = select_one_row("select * from ${mysqlprefix}chatgroup where groupid = " . intval($id), $link);
	mysql_close($link);
	return $newdep;
}

function update_group($groupid, $name, $descr, $commonname, $commondescr, $email)
{
	global $mysqlprefix;
	$link = connect();
	$query = sprintf(
		"update ${mysqlprefix}chatgroup set vclocalname = '%s', vclocaldescription = '%s', vccommonname = '%s', vccommondescription = '%s', vcemail = '%s' where groupid = %s",
		mysql_real_escape_string($name, $link),
		mysql_real_escape_string($descr, $link),
		mysql_real_escape_string($commonname, $link),
		mysql_real_escape_string($commondescr, $link),
		mysql_real_escape_string($email, $link),
		intval($groupid));

	perform_query($query, $link);
	mysql_close($link);
}


if (isset($_POST['name'])) {
	$groupid = verifyparam("gid", "/^(\d{1,10})?$/", "");
	$name = getparam('name');
	$description = getparam('description');
	$commonname = getparam('commonname');
	$commondescription = getparam('commondescription');
	$email = getparam('email');

	if (!$name)
		$errors[] = no_field("form.field.groupname");

	if ($email != '' && !is_valid_email($email))
		$errors[] = wrong_field("form.field.mail");

	$existing_group = group_by_name($name);
	if ((!$groupid && $existing_group) ||
		($groupid && $existing_group && $groupid != $existing_group['groupid']))
		$errors[] = getlocal("page.group.duplicate_name");

	if (count($errors) == 0) {
		if (!$groupid) {
			$newdep = create_group($name, $description, $commonname, $commondescription, $email);
			header("Location: $mibewroot/operator/groupmembers.php?gid=" . intval($newdep['groupid']));
			exit;
		} else {
			update_group($groupid, $name, $description, $commonname, $commondescription, $email);
			header("Location: $mibewroot/operator/group.php?gid=" . intval($groupid) . "&stored");
			exit;
		}
	} else {
		$page['formname'] = topage($name);
		$page['formdescription'] = topage($description);
		$page['formcommonname'] = topage($commonname);
		$page['formcommondescription'] = topage($commondescription);
		$page['formemail'] = topage($email);
		$page['grid'] = topage($groupid);
	}

} else if (isset($_GET['gid'])) {
	$groupid = verifyparam('gid', "/^\d{1,10}$/");
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
		$page['grid'] = topage($group['groupid']);
	}
}

$page['stored'] = isset($_GET['stored']);
prepare_menu($operator);
setup_group_settings_tabs($groupid, 0);
start_html_output();
require('../view/group.php');
?>