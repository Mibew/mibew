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

$errors = array();
if( isset($_POST['login']) && isset($_POST['password']) ) {
	$login = getparam('login');
	$password = getparam('password');
	$remember = isset($_POST['isRemember']) && $_POST['isRemember'] == "on";

	$operator = operator_by_login( $login );
	if( $operator && isset($operator['vcpassword']) && $operator['vcpassword'] == md5($password) ) {

		$target = isset($_SESSION['backpath'])
				? $_SESSION['backpath']
				: "$webimroot/operator/index.php";

        login_operator($operator,$remember);
		header("Location: $target");
		exit;
	} else {
		$errors[] = getlocal("page_login.error");
	}
}

$page = array( 'backPath' => '', 'formisRemember' => true, 'version' => $version, 'localeLinks' => get_locale_links("$webimroot/operator/login.php") );
start_html_output();
require('../view/login.php');
?>