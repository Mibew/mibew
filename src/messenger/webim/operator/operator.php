<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Web Messenger Community
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

$page = array('agentId' => '');
$errors = array();

if( isset($_POST['login']) && isset($_POST['password']) ) {
	$agentId = verifyparam( "agentId", "/^(\d{1,9})?$/", "");
	$login = getparam('login');
	$password = getparam('password');
	$passwordConfirm = getparam('passwordConfirm');
	$localname = getparam('name');
	$commonname = getparam('commonname');

	if( !$localname )
		$errors[] = no_field("form.field.agent_name");

	if( !$commonname )
		$errors[] = no_field("form.field.agent_commonname");

	if( !$login ) {
		$errors[] = no_field("form.field.login");
	} else if( !preg_match( "/^[\w_]+$/",$login) ) {
		$errors[] = getlocal("page_agent.error.wrong_login");
	}

	if( !$agentId && !$password )
		$errors[] = no_field("form.field.password");

	if( $password != $passwordConfirm )
		$errors[] = getlocal("my_settings.error.password_match");

	$existing_operator = operator_by_login($login);
	if( (!$agentId && $existing_operator) ||
		( $agentId && $existing_operator && $agentId != $existing_operator['operatorid']) )
		$errors[] = getlocal("page_agent.error.duplicate_login");

	if( count($errors) == 0 ) {
		if (!$agentId) {
			create_operator($login,$password,$localname,$commonname,"");
		} else {
			update_operator($agentId,$login,$password,$localname,$commonname);
		}
		header("Location: $webimroot/operator/operators.php");
		exit;
	} else {
		$page['formlogin'] = topage($login);
		$page['formname'] = topage($localname);
		$page['formcommonname'] = topage($commonname);
		$page['agentId'] = topage($agentId);
	}

} else if( isset($_GET['op']) ) {
	$login = verifyparam( 'op', "/^[\w_]+$/");
	$op = operator_by_login( $login );

	if( !$op ) {
		$errors[] = getlocal("no_such_operator");
		$page['formlogin'] = topage($login);
	} else {
		$page['formlogin'] = topage($op['vclogin']);
		$page['formname'] = topage($op['vclocalename']);
		$page['formcommonname'] = topage($op['vccommonname']);
		$page['agentId'] = topage($op['operatorid']);
	}
}

$page['operator'] = topage(get_operator_name($operator));

$page['tabs'] = isset($login) ? array(
	getlocal("page_agent.tab.main") => "",
	getlocal("page_agent.tab.avatar") => "$webimroot/operator/avatar.php?op=".topage($login)
) : array();

start_html_output();
require('../view/agent.php');
?>