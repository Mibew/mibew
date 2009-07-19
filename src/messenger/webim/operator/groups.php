<?php
/*
 * This file is part of Mibew Messenger project.
 *
 * Copyright (c) 2005-2009 Mibew Messenger Community
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

if( isset($_GET['act']) && $_GET['act'] == 'del' ) {
	
	$groupid = isset($_GET['gid']) ? $_GET['gid'] : "";

	if( !preg_match( "/^\d+$/", $groupid )) {
		$errors[] = "Cannot delete: wrong argument";
	}
	
	if( !is_capable($can_administrate, $operator)) {
		$errors[] = "You are not allowed to remove groups";
	}
	
	if( count($errors) == 0 ) {
		$link = connect();
		perform_query("delete from chatgroup where groupid = $groupid",$link);
		perform_query("delete from chatgroupoperator where groupid = $groupid",$link);
		perform_query("update chatthread set groupid = 0 where groupid = $groupid",$link);
		mysql_close($link);
		header("Location: $webimroot/operator/groups.php");
		exit;
	}
}

function is_online($group) {
	global $settings;
	return $group['ilastseen'] && $group['ilastseen'] < $settings['online_timeout'] ? "1" : "";	
}

$page = array();
$link = connect();
$page['groups'] = get_groups($link, true, true);
mysql_close($link);
$page['canmodify'] = is_capable($can_administrate, $operator);

prepare_menu($operator);
start_html_output();
require('../view/groups.php');
?>