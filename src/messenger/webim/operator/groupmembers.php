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
require_once('../libs/groups.php');

$operator = check_login();

function get_group_members($groupid) {
	$link = connect();
	$query = "select operatorid from chatgroupoperator where groupid = $groupid";
	$result = select_multi_assoc($query, $link);
	mysql_close($link);
	return $result;
}

function update_group_members($groupid,$newvalue) {
	$link = connect();
	perform_query("delete from chatgroupoperator where groupid = $groupid", $link);
	foreach($newvalue as $opid) {
		perform_query("insert into chatgroupoperator (groupid, operatorid) values ($groupid,$opid)", $link);
	}
	mysql_close($link);
}

$groupid = verifyparam( "gid","/^\d{1,9}$/");
$page = array('groupid' => $groupid);
$page['operators'] = get_operators();
$errors = array();

$group = group_by_id($groupid);

if( !$group ) {
	$errors[] = getlocal("page.group.no_such");

} else if( isset($_POST['gid']) ) {

	$new_members = array();
	foreach($page['operators'] as $op) {
		if( verifyparam("op".$op['operatorid'],"/^on$/", "") == "on") {
			$new_members[] = $op['operatorid'];
		}
	}
	
	update_group_members($groupid, $new_members);
	header("Location: $webimroot/operator/groupmembers.php?gid=$groupid&stored");
	exit;
}

$page['formop'] = array();
$page['currentgroup'] = $group ? topage(htmlspecialchars($group['vclocalname'])) : "";

foreach(get_group_members($groupid) as $rel) {
	$page['formop'][] = $rel['operatorid'];
}

$page['stored'] = isset($_GET['stored']);
prepare_menu($operator);
setup_group_settings_tabs($groupid, 1);
start_html_output();
require('../view/groupmembers.php');
?>