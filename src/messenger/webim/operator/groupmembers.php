<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
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

function get_operators() {
	$link = connect();

	$query = "select * from chatoperator order by vclogin";
	$result = select_multi_assoc($query, $link);
	mysql_close($link);
	return $result;
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