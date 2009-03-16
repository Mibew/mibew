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
require_once('../libs/chat.php');
require_once('../libs/operator.php');
require_once('../libs/pagination.php');

$operator = check_login();
$page = array();
$errors = array();

$link = connect();

if( isset($_GET['act']) && $_GET['act'] == 'del' ) {
	$banId = isset($_GET['id']) ? $_GET['id'] : "";

	if( !preg_match( "/^\d+$/", $banId )) {
		$errors[] = "Wrong argument";
	}

	if( count($errors) == 0 ) {
		perform_query("delete from chatban where banid = $banId",$link);
		header("Location: $webimroot/operator/blocked.php");
		exit;
	}
}

$result = mysql_query("select banid,unix_timestamp(dtmtill) as till,address,comment from chatban", $link)
	or die(' Query failed: ' .mysql_error());

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