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

$errors = array();
if( isset($_POST['login']) && isset($_POST['password']) ) {
	$login = getparam('login');
	$password = getparam('password');
	$remember = isset($_POST['isRemember']) && $_POST['isRemember'] == "on";

	$operator = operator_by_login( $login );
	if( $operator && isset($operator['vcpassword']) && $operator['vcpassword'] == md5($password) ) {

		$target = isset($_SESSION['backpath']) 
				? $_SESSION['backpath'] 
				: dirname($_SERVER['PHP_SELF'])."/index.php";
		
        login_operator($operator,$remember);
		header("Location: $target");
		exit;
	} else {
		$errors[] = getstring("page_login.error");
	}
}

$page = array( 'backPath' => '' );
start_html_output();
require('../view/login.php');
?>