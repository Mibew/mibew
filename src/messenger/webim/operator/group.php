<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2009 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once('../libs/common.php');
require_once('../libs/operator.php');

$operator = check_login();

$page = array('grid' => '');
$errors = array();
$groupid = '';

function group_by_id($id) {
	$link = connect();
	$group = select_one_row(
		 "select * from chatgroup where groupid = $id", $link );
	mysql_close($link);
	return $group;
}

function group_by_name($name) {
	$link = connect();
	$group = select_one_row(
		 "select * from chatgroup where vclocalname = '".mysql_real_escape_string($name)."'", $link );
	mysql_close($link);
	return $group;
}

function create_group($name,$descr,$commonname,$commondescr) {
	$link = connect();
	$query = sprintf(
		"insert into chatgroup (vclocalname,vclocaldescription,vccommonname,vccommondescription) values ('%s','%s','%s','%s')",
			mysql_real_escape_string($name),
			mysql_real_escape_string($descr),
			mysql_real_escape_string($commonname),
			mysql_real_escape_string($commondescr));
			
	perform_query($query,$link);
	$id = mysql_insert_id($link);

	$newdep = select_one_row("select * from chatgroup where groupid = $id", $link );
	mysql_close($link);
	return $newdep;
}

function update_group($groupid,$name,$descr,$commonname,$commondescr) {
	$link = connect();
	$query = sprintf(
		"update chatgroup set vclocalname = '%s', vclocaldescription = '%s', vccommonname = '%s', vccommondescription = '%s' where groupid = %s",
		mysql_real_escape_string($name),
		mysql_real_escape_string($descr),
		mysql_real_escape_string($commonname),
		mysql_real_escape_string($commondescr),
		$groupid );

	perform_query($query,$link);
	mysql_close($link);
}


if( isset($_POST['name'])) {
	$groupid = verifyparam( "gid", "/^(\d{1,9})?$/", "");
	$name = getparam('name');
	$description = getparam('description');
	$commonname = getparam('commonname');
	$commondescription = getparam('commondescription');
	
	if( !$name )
		$errors[] = no_field("form.field.groupname");

	$existing_group = group_by_name($name);
	if( (!$groupid && $existing_group) ||
		( $groupid && $existing_group && $groupid != $existing_group['groupid']) )
		$errors[] = getlocal("page.group.duplicate_name");

	if( count($errors) == 0 ) {
		if (!$groupid) {
			$newdep = create_group($name,$description,$commonname,$commondescription);
			header("Location: $webimroot/operator/groups.php");
			exit;
		} else {
			update_group($groupid,$name,$description,$commonname,$commondescription);
			header("Location: $webimroot/operator/groups.php");
			exit;
		}
	} else {
		$page['formname'] = topage($name);
		$page['formdescription'] = topage($description);
		$page['formcommonname'] = topage($commonname);
		$page['formcommondescription'] = topage($commondescription);
		$page['grid'] = topage($groupid);
	}

} else if( isset($_GET['gid']) ) {
	$groupid = verifyparam( 'gid', "/^\d{1,9}$/");
	$group = group_by_id($groupid);

	if( !$group ) {
		$errors[] = getlocal("page.group.no_such");
		$page['grid'] = topage($groupid);
	} else {
		$page['formname'] = topage($group['vclocalname']);
		$page['formdescription'] = topage($group['vclocaldescription']);
		$page['formcommonname'] = topage($group['vccommonname']);
		$page['formcommondescription'] = topage($group['vccommondescription']);
		$page['grid'] = topage($group['groupid']);
	}
}

prepare_menu($operator);
start_html_output();
require('../view/group.php');
?>