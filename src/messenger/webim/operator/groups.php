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

$operator = check_login();

if (isset($_GET['act']) && $_GET['act'] == 'del') {

	$groupid = isset($_GET['gid']) ? $_GET['gid'] : "";

	if (!preg_match("/^\d+$/", $groupid)) {
		$errors[] = "Cannot delete: wrong argument";
	}

	if (!is_capable($can_administrate, $operator)) {
		$errors[] = "You are not allowed to remove groups";
	}

	if (count($errors) == 0) {
		$link = connect();
		perform_query("delete from ${mysqlprefix}chatgroup where groupid = $groupid", $link);
		perform_query("delete from ${mysqlprefix}chatgroupoperator where groupid = $groupid", $link);
		perform_query("update ${mysqlprefix}chatthread set groupid = 0 where groupid = $groupid", $link);
		mysql_close($link);
		header("Location: $webimroot/operator/groups.php");
		exit;
	}
}

function is_online($group)
{
	global $settings;
	return $group['ilastseen'] !== NULL && $group['ilastseen'] < $settings['online_timeout'] ? "1" : "";
}

function is_away($group)
{
	global $settings;
	return $group['ilastseenaway'] !== NULL && $group['ilastseenaway'] < $settings['online_timeout'] ? "1" : "";
}


$page = array();
$link = connect();
$page['groups'] = get_groups($link, true);
mysql_close($link);
$page['canmodify'] = is_capable($can_administrate, $operator);

prepare_menu($operator);
start_html_output();
require('../view/groups.php');
?>