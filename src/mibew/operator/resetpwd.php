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
require_once('../libs/settings.php');

if (check_login(false)) {
	header("Location: $mibewroot/operator/");
	exit;
}

$errors = array();
$page = array('version' => $version, 'showform' => true);

$opId = verifyparam("id", "/^\d{1,10}$/");
$token = verifyparam("token", "/^[\dabcdef]+$/");

$operator = operator_by_id($opId);

if (!$operator) {
	$errors[] = "No such operator";
	$page['showform'] = false;
} else if ($token != $operator['vcrestoretoken']) {
	$errors[] = "Wrong token";
	$page['showform'] = false;
}

if (count($errors) == 0 && isset($_POST['password'])) {
	$password = getparam('password');
	$passwordConfirm = getparam('passwordConfirm');

	if (!$password)
		$errors[] = no_field("form.field.password");

	if ($password != $passwordConfirm)
		$errors[] = getlocal("my_settings.error.password_match");

	if (count($errors) == 0) {
		$page['isdone'] = true;

		$link = connect();
		$query = "update ${mysqlprefix}chatoperator set vcpassword = '" . mysql_real_escape_string(calculate_password_hash($operator['vclogin'], $password), $link) . "', vcrestoretoken = '' where operatorid = " . intval($opId);
		perform_query($query, $link);
		mysql_close($link);

		$page['loginname'] = $operator['vclogin'];
		start_html_output();
		require('../view/resetpwd.php');
		exit;
	}
}

$page['id'] = $opId;
$page['token'] = $token;
$page['isdone'] = false;
start_html_output();
require('../view/resetpwd.php');
?>