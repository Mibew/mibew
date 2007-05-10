<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2007 Internet Services Ltd.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require('../libs/common.php');
require('../libs/operator.php');

$operator = check_login();

$page = array('agentId' => '');
$errors = array();

if( isset($_POST['login']) && isset($_POST['password']) ) {
	$agentId = verifyparam( "agentId", "/^(\d{1,9})?$/", "");
	$login = getparam('login');
	$password = getparam('password');
	$passwordConfirm = getparam('passwordConfirm');
	$name = getparam('name');
	$commonname = getparam('commonname');

	if( !$name )
		$errors[] = no_field("form.field.agent_name");

	if( !$commonname )
		$errors[] = no_field("form.field.agent_commonname");

	if( !$login )
		$errors[] = no_field("form.field.login");

	if( !$agentId && !$password )
		$errors[] = no_field("form.field.password");

	if( $password != $passwordConfirm )
		$errors[] = getstring("my_settings.error.password_match");

	$login_operator = operator_by_login($login);
	if( (!$agentId && $login_operator) || 
		( $agentId && $login_operator && $agentId != $login_operator['operatorid']) )
		$errors[] = getstring("page_agent.error.duplicate_login");

	if( count($errors) == 0 ) {
		if( $agentId ) {
			update_operator($agentId,$login,$password,$name,$commonname);
		} else {
			create_operator($login,$password,$name,$commonname);
		}
		header("Location: ".dirname($_SERVER['PHP_SELF'])."/operators.php");
		exit;
	} else {
		$page['formlogin'] = $login;
		$page['formname'] = $name;
		$page['formcommonname'] = $commonname;
		$page['agentId'] = $agentId;
	}

} else if( isset($_GET['op']) ) {
	$login = verifyparam( 'op', "/^[\w_]+$/");
	$op = operator_by_login( $login );

	if( !$op ) {
		$errors[] = getstring("no_such_operator");
		$page['formlogin'] = $login;
	} else {
		$page['formlogin'] = $op['vclogin'];
		$page['formname'] = $op['vclocalename'];
		$page['formcommonname'] = $op['vccommonname'];
		$page['agentId'] = $op['operatorid'];
	}
}

$page['operator'] = get_operator_name($operator);

start_html_output();
require('../view/agent.php');
?>