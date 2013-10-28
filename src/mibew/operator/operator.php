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

require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(dirname(dirname(__FILE__)).'/libs/operator.php');
require_once(dirname(dirname(__FILE__)).'/libs/operator_settings.php');

$operator = check_login();
csrfchecktoken();

$page = array('opid' => '');
$errors = array();
$opId = '';

if ((isset($_POST['login']) || !is_capable(CAN_ADMINISTRATE, $operator)) && isset($_POST['password'])) {
	$opId = verifyparam("opid", "/^(\d{1,9})?$/", "");
	if (is_capable(CAN_ADMINISTRATE, $operator)) {
		$login = getparam('login');
	} else {
		$login = $operator['vclogin'];
	}
	$email = getparam('email');
	$password = getparam('password');
	$passwordConfirm = getparam('passwordConfirm');
	$localname = getparam('name');
	$commonname = getparam('commonname');
	$code = getparam('code');

	if (!$localname)
		$errors[] = no_field("form.field.agent_name");

	if (!$commonname)
		$errors[] = no_field("form.field.agent_commonname");

	if (!$login) {
		$errors[] = no_field("form.field.login");
	} else if (!preg_match("/^[\w_\.]+$/", $login)) {
		$errors[] = getlocal("page_agent.error.wrong_login");
	}

	if ($email == '' || !is_valid_email($email)) {
		$errors[] = wrong_field("form.field.mail");
	}

	if ($code != '' && (! preg_match("/^[A-z0-9_]+$/", $code))) {
		$errors[] = getlocal("page_agent.error.wrong_agent_code");
	}

	if (!$opId && !$password)
		$errors[] = no_field("form.field.password");

	if ($password != $passwordConfirm)
		$errors[] = getlocal("my_settings.error.password_match");

	$existing_operator = operator_by_login($login);
	if ((!$opId && $existing_operator) ||
		($opId && $existing_operator && $opId != $existing_operator['operatorid']))
		$errors[] = getlocal("page_agent.error.duplicate_login");

	// Check if operator with specified email already exists in the database
	$existing_operator = operator_by_email($email);
	if (
		// Create operator with email already in database
		(!$opId && $existing_operator) ||
		// Update operator email to existing one
		($opId && $existing_operator && $opId != $existing_operator['operatorid'])
	) {
		$errors[] = getlocal("page_agent.error.duplicate_email");
	}

	$canmodify = ($opId == $operator['operatorid'] && is_capable(CAN_MODIFYPROFILE, $operator))
				 || is_capable(CAN_ADMINISTRATE, $operator);
	if (!$canmodify) {
		$errors[] = getlocal('page_agent.cannot_modify');
	}

	if (count($errors) == 0) {
		if (!$opId) {
			$newop = create_operator($login, $email, $password, $localname, $commonname, "", $code);
			header("Location: $mibewroot/operator/avatar.php?op=" . $newop['operatorid']);
			exit;
		} else {
			update_operator($opId, $login, $email, $password, $localname, $commonname, $code);
			// update the session password
			if (!empty($password) && $opId == $operator['operatorid']) {
				$toDashboard = check_password_hash($login, '', $operator['vcpassword']) && $password != '';
				$_SESSION[$session_prefix."operator"]['vcpassword'] = calculate_password_hash($login, $password);
				if($toDashboard) {
					header("Location: $mibewroot/operator/index.php");
					exit;
				}
			}
			header("Location: $mibewroot/operator/operator.php?op=$opId&stored");
			exit;
		}
	} else {
		$page['formlogin'] = topage($login);
		$page['formname'] = topage($localname);
		$page['formemail'] = topage($email);
		$page['formcommonname'] = topage($commonname);
		$page['formcode'] = topage($code);
		$page['opid'] = topage($opId);
	}

} else if (isset($_GET['op'])) {
	$opId = verifyparam('op', "/^\d{1,9}$/");
	$op = operator_by_id($opId);

	if (!$op) {
		$errors[] = getlocal("no_such_operator");
		$page['opid'] = topage($opId);
	} else {
		//show an error if the admin password hasn't been set yet.
		if (check_password_hash($operator['vclogin'], '', $operator['vcpassword']) && !isset($_GET['stored']))
		{
			$errors[] = getlocal("my_settings.error.no_password");
		}

		$page['formlogin'] = topage($op['vclogin']);
		$page['formname'] = topage($op['vclocalename']);
		$page['formemail'] = topage($op['vcemail']);
		$page['formcommonname'] = topage($op['vccommonname']);
		$page['formcode'] = topage($op['code']);
		$page['opid'] = topage($op['operatorid']);
	}
}

if (!$opId && !is_capable(CAN_ADMINISTRATE, $operator)) {
	$errors[] = getlocal("page_agent.error.forbidden_create");
}

$canmodify = ($opId == $operator['operatorid'] && is_capable(CAN_MODIFYPROFILE, $operator))
			 || is_capable(CAN_ADMINISTRATE, $operator);

$page['stored'] = isset($_GET['stored']);
$page['canmodify'] = $canmodify ? "1" : "";
$page['canchangelogin'] = is_capable(CAN_ADMINISTRATE, $operator);
$page['needChangePassword'] = check_password_hash($operator['vclogin'], '', $operator['vcpassword']);

prepare_menu($operator);
setup_operator_settings_tabs($opId, 0);
start_html_output();
require(dirname(dirname(__FILE__)).'/view/agent.php');
?>