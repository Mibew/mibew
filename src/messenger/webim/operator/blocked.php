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
require_once('../libs/chat.php');
require_once('../libs/operator.php');
require_once('../libs/pagination.php');

$operator = check_login();
$page = array();
$errors = array();

setlocale(LC_TIME, getstring("time.locale"));

$link = connect();

if (isset($_GET['act']) && $_GET['act'] == 'del') {
	$banId = isset($_GET['id']) ? $_GET['id'] : "";

	if (!preg_match("/^\d+$/", $banId)) {
		$errors[] = "Cannot delete: wrong argument";
	}

	if (count($errors) == 0) {
		perform_query("delete from ${mysqlprefix}chatban where banid = $banId", $link);
		header("Location: $webimroot/operator/blocked.php");
		exit;
	}
}

$result = mysql_query("select banid,unix_timestamp(dtmtill) as till,address,comment from ${mysqlprefix}chatban", $link)
		or die(' Query failed: ' . mysql_error($link));

$blockedList = array();
while ($ban = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$blockedList[] = $ban;
}

mysql_free_result($result);
mysql_close($link);

setup_pagination($blockedList);

prepare_menu($operator);
start_html_output();

require('../view/blocked_visitors.php');
exit;
?>