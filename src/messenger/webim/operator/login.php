<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
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

$errors = array();
$page = array( 'formisRemember' => true, 'version' => $version);

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
		$page['formlogin'] = $login;
	}
}

$page['localeLinks'] = get_locale_links("$webimroot/operator/login.php");
start_html_output();
require('../view/login.php');
?>