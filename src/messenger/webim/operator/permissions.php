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

function update_operator_permissions($operatorid,$newvalue) {
	$link = connect();
	$query = "update chatoperator set iperm = $newvalue where operatorid = $operatorid";

	perform_query($query,$link);
	mysql_close($link);
}

$opId = verifyparam( "op","/^\d{1,9}$/");
$page = array('op' => $opId);
$page['operator'] = topage(get_operator_name($operator));
$errors = array();

$op = operator_by_id($opId);

if( !$op ) {
	$errors[] = getlocal("no_such_operator");

} else if( isset($_POST['op']) ) {

	$new_permissions = isset($op['iperm']) ? $op['iperm'] : 0;

	foreach($permission_ids as $perm => $id) {
		if( verifyparam("permissions$id","/^on$/", "") == "on") {
			$new_permissions |= (1 << $perm);
		} else {
			$new_permissions &= ~ (1 << $perm);
		}
	}

	if(count($errors) == 0) {
		update_operator_permissions($op['operatorid'],$new_permissions);

		if ($opId && $_SESSION['operator'] && $operator['operatorid'] == $opId) {
			$_SESSION['operator']['iperm'] = $new_permissions;
		}
		header("Location: $webimroot/operator/operator.php?op=$opId");
		exit;
	}

}

$page['tabs'] = array(
	getlocal("page_agent.tab.main") => "$webimroot/operator/operator.php?op=$opId",
	getlocal("page_agent.tab.avatar") => "$webimroot/operator/avatar.php?op=$opId",
	getlocal("page_agent.tab.permissions") => ""
);

$page['permissionsList'] = get_permission_list();
$page['formpermissions'] = array("");
$page['currentop'] = topage(get_operator_name($op))." (".$op['vclogin'].")";

foreach($permission_ids as $perm => $id) {
	if(is_capable($perm,$op)) {
		$page['formpermissions'][] = $id;
	}
}

start_html_output();
require('../view/permissions.php');
?>