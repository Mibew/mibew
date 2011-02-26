<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
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
require_once('../libs/settings.php');

$errors = array();
$page = array('version' => $version, 'showform' => true);

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

		$link = connect();
		$query = "update ${mysqlprefix}chatoperator set vcpassword = '" . md5($password) . "', vcrestoretoken = '' where operatorid = " . $opId;
		perform_query($query, $link);
		mysql_close($link);

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