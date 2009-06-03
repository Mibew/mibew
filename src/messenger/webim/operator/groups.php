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
	
	// TODO check permissions
	
	$groupid = verifyparam( "gid", "/^(\d{1,9})?$/");

	$link = connect();
	perform_query("delete from chatgroup where groupid = $groupid",$link);
	perform_query("delete from chatgroupoperator where groupid = $groupid",$link);
	perform_query("update chatthread set groupid = 0 where groupid = $groupid",$link);
	mysql_close($link);
	header("Location: $webimroot/operator/groups.php");
	exit;
}

$page = array();
$page['groups'] = get_groups(true);

prepare_menu($operator);
start_html_output();
require('../view/groups.php');
?>