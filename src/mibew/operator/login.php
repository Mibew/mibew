<?php
/*
 * Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once('../libs/common.php');
require_once('../libs/operator.php');

if (check_login(false)) {
	header("Location: $mibewroot/operator/");
	exit;
}

$errors = array();
$page = array('formisRemember' => true, 'version' => $version);

if (isset($_POST['login']) && isset($_POST['password'])) {
	$login = getparam('login');
	$password = getparam('password');
	$remember = isset($_POST['isRemember']) && $_POST['isRemember'] == "on";

	$operator = operator_by_login($login);
	if ($operator && isset($operator['vcpassword']) && check_password_hash($login, $password, $operator['vcpassword'])) {

		$target = $password == ''
				? "$mibewroot/operator/operator.php?op=" . intval($operator['operatorid'])
				: (isset($_SESSION['backpath'])
					? $_SESSION['backpath']
					: "$mibewroot/operator/index.php");

		login_operator($operator, $remember, is_secure_request());
		header("Location: $target");
		exit;
	} else {
		$errors[] = getlocal("page_login.error");
		$page['formlogin'] = $login;
	}
} else if(isset($_GET['login'])) {
	$login = getgetparam('login');
	if (preg_match("/^(\w{1,15})$/", $login))
		$page['formlogin'] = $login;
}

$page['localeLinks'] = get_locale_links("$mibewroot/operator/login.php");
start_html_output();
require('../view/login.php');
?>