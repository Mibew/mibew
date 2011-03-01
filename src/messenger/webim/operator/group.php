<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
 * 
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
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
		mysql_real_escape_string($name),
		mysql_real_escape_string($descr),
		mysql_real_escape_string($commonname),
		mysql_real_escape_string($commondescr),
		mysql_real_escape_string($email));

	perform_query($query, $link);
	$id = mysql_insert_id($link);

	$newdep = select_one_row("select * from ${mysqlprefix}chatgroup where groupid = $id", $link);
	mysql_close($link);
	return $newdep;
}

function update_group($groupid, $name, $descr, $commonname, $commondescr, $email)
{
	global $mysqlprefix;
	$link = connect();
	$query = sprintf(
		"update ${mysqlprefix}chatgroup set vclocalname = '%s', vclocaldescription = '%s', vccommonname = '%s', vccommondescription = '%s', vcemail = '%s' where groupid = %s",
		mysql_real_escape_string($name),
		mysql_real_escape_string($descr),
		mysql_real_escape_string($commonname),
		mysql_real_escape_string($commondescr),
		mysql_real_escape_string($email),
		$groupid);

	perform_query($query, $link);
	mysql_close($link);
}


if (isset($_POST['name'])) {
	$groupid = verifyparam("gid", "/^(\d{1,9})?$/", "");
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
			header("Location: $webimroot/operator/groupmembers.php?gid=" . $newdep['groupid']);
			exit;
		} else {
			update_group($groupid, $name, $description, $commonname, $commondescription, $email);
			header("Location: $webimroot/operator/group.php?gid=$groupid&stored");
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
		$page['grid'] = topage($group['groupid']);
	}
}

$page['stored'] = isset($_GET['stored']);
prepare_menu($operator);
setup_group_settings_tabs($groupid, 0);
start_html_output();
require('../view/group.php');
?>
