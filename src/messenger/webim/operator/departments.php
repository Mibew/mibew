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

function get_departments() {
	$link = connect();
	$query = "select departmentid, vclocalname, vclocaldescription, 0 as inumofagents ".
			 "from chatdepartment order by vclocalname";
	$result = select_multi_assoc($query, $link);
	mysql_close($link);
	return $result;
}

if( isset($_GET['act']) && $_GET['act'] == 'del' ) {
	
	// TODO check permissions, delete in other places
	
	$departmentid = verifyparam( "dep", "/^(\d{1,9})?$/");

	$link = connect();
	perform_query("delete from chatdepartment where departmentid = $departmentid",$link);
	mysql_close($link);
	header("Location: $webimroot/operator/departments.php");
	exit;
}

$page = array();
$page['departments'] = get_departments();

prepare_menu($operator);
start_html_output();
require('../view/departments.php');
?>