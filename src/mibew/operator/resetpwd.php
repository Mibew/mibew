<?php
/*
 * Copyright 2005-2013 the original author or authors.
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

// Import namespaces and classes of the core
use Mibew\Database;

// Initialize libraries
require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(MIBEW_FS_ROOT.'/libs/operator.php');
require_once(MIBEW_FS_ROOT.'/libs/settings.php');
require_once(MIBEW_FS_ROOT.'/libs/interfaces/style.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/style.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/page_style.php');

$errors = array();
$page = array(
	'version' => $version,
	'showform' => true,
	'title' => getlocal("resetpwd.title"),
	'headertitle' => getlocal("app.title"),
	'show_small_login' => true,
	'fixedwrap' => true,
);

$page_style = new PageStyle(PageStyle::currentStyle());

$opId = verifyparam("id", "/^\d{1,9}$/");
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

		$db = Database::getInstance();
		$db->query(
			"update {chatoperator} set vcpassword = ?, vcrestoretoken = '' " .
			"where operatorid = ?",
			array(calculate_password_hash($operator['vclogin'], $password), $opId)
		);

		$page['loginname'] = $operator['vclogin'];
		$page_style->render('resetpwd');
		exit;
	}
}

$page['id'] = $opId;
$page['token'] = $token;
$page['isdone'] = false;

$page_style->render('resetpwd');

?>