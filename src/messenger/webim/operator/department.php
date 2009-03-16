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

$page = array('depid' => '');
$errors = array();
$departmentid = '';

function department_by_id($id) {
	$link = connect();
	$department = select_one_row(
		 "select * from chatdepartment where departmentid = $id", $link );
	mysql_close($link);
	return $department;
}

function department_by_name($name) {
	$link = connect();
	$department = select_one_row(
		 "select * from chatdepartment where vclocalname = '".mysql_real_escape_string($name)."'", $link );
	mysql_close($link);
	return $department;
}

function create_department($name,$descr,$commonname,$commondescr) {
	$link = connect();
	$query = sprintf(
		"insert into chatdepartment (vclocalname,vclocaldescription,vccommonname,vccommondescription) values ('%s','%s','%s','%s')",
			mysql_real_escape_string($name),
			mysql_real_escape_string($descr),
			mysql_real_escape_string($commonname),
			mysql_real_escape_string($commondescr));
			
	perform_query($query,$link);
	$id = mysql_insert_id($link);

	$newdep = select_one_row("select * from chatdepartment where departmentid = $id", $link );
	mysql_close($link);
	return $newdep;
}

function update_department($departmentid,$name,$descr,$commonname,$commondescr) {
	$link = connect();
	$query = sprintf(
		"update chatdepartment set vclocalname = '%s', vclocaldescription = '%s', vccommonname = '%s', vccommondescription = '%s' where departmentid = %s",
		mysql_real_escape_string($name),
		mysql_real_escape_string($descr),
		mysql_real_escape_string($commonname),
		mysql_real_escape_string($commondescr),
		$departmentid );

	perform_query($query,$link);
	mysql_close($link);
}


if( isset($_POST['name'])) {
	$departmentid = verifyparam( "dep", "/^(\d{1,9})?$/", "");
	$name = getparam('name');
	$description = getparam('description');
	$commonname = getparam('commonname');
	$commondescription = getparam('commondescription');
	
	if( !$name )
		$errors[] = no_field("form.field.depname");

	$existing_department = department_by_name($name);
	if( (!$departmentid && $existing_department) ||
		( $departmentid && $existing_department && $departmentid != $existing_department['departmentid']) )
		$errors[] = getlocal("page.department.duplicate_name");

	if( count($errors) == 0 ) {
		if (!$departmentid) {
			$newdep = create_department($name,$description,$commonname,$commondescription);
			header("Location: $webimroot/operator/departments.php");
			exit;
		} else {
			update_department($departmentid,$name,$description,$commonname,$commondescription);
			header("Location: $webimroot/operator/departments.php");
			exit;
		}
	} else {
		$page['formname'] = topage($name);
		$page['formdescription'] = topage($description);
		$page['formcommonname'] = topage($commonname);
		$page['formcommondescription'] = topage($commondescription);
		$page['depid'] = topage($departmentid);
	}

} else if( isset($_GET['dep']) ) {
	$departmentid = verifyparam( 'dep', "/^\d{1,9}$/");
	$department = department_by_id($departmentid);

	if( !$department ) {
		$errors[] = getlocal("page.department.no_such");
		$page['depid'] = topage($departmentid);
	} else {
		$page['formname'] = topage($department['vclocalname']);
		$page['formdescription'] = topage($department['vclocaldescription']);
		$page['formcommonname'] = topage($department['vccommonname']);
		$page['formcommondescription'] = topage($department['vccommondescription']);
		$page['depid'] = topage($department['departmentid']);
	}
}

prepare_menu($operator);
start_html_output();
require('../view/department.php');
?>