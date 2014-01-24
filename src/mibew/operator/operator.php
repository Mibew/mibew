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
require_once('../libs/operator_settings.php');

$operator = check_login();
csrfchecktoken();

$page = array('opid' => '');
$errors = array();
$opId = '';

loadsettings();
if (isset($_POST['login']) && isset($_POST['password'])) {
	$opId = verifyparam("opid", "/^(\d{1,10})?$/", "");
	$login = getparam('login');
	$email = getparam('email');
	$jabber = getparam('jabber');
	$password = getparam('password');
	$passwordConfirm = getparam('passwordConfirm');
	$localname = getparam('name');
	$commonname = getparam('commonname');
	$jabbernotify = verifyparam("jabbernotify", "/^on$/", "") == "on";

	if (!$localname)
		$errors[] = no_field("form.field.agent_name");

	if (!$commonname)
		$errors[] = no_field("form.field.agent_commonname");

	if (!$login) {
		$errors[] = no_field("form.field.login");
	} else if (!preg_match("/^[\w_\.]+$/", $login)) {
		$errors[] = getlocal("page_agent.error.wrong_login");
	}

	if ($email != '' && !is_valid_email($email))
		$errors[] = wrong_field("form.field.mail");

	if ($jabber != '' && !is_valid_email($jabber))
		$errors[] = wrong_field("form.field.jabber");

	if ($jabbernotify && $jabber == '') {
		if ($settings['enablejabber'] == "1") {
			$errors[] = no_field("form.field.jabber");
		} else {
			$jabbernotify = false;
		}
	}

	if (!$opId && !$password)
		$errors[] = no_field("form.field.password");

	if ($password != $passwordConfirm)
		$errors[] = getlocal("my_settings.error.password_match");

	$existing_operator = operator_by_login($login);
	if ((!$opId && $existing_operator) ||
		($opId && $existing_operator && $opId != $existing_operator['operatorid']))
		$errors[] = getlocal("page_agent.error.duplicate_login");

	$canmodify = ($opId == $operator['operatorid'] && is_capable($can_modifyprofile, $operator))
				 || is_capable($can_administrate, $operator);
	if (!$canmodify) {
		$errors[] = getlocal('page_agent.cannot_modify');
	}

	if (count($errors) == 0) {
		if (!$opId) {
			$newop = create_operator($login, $email, $jabber, $password, $localname, $commonname, $jabbernotify ? 1 : 0, "");
			header("Location: $mibewroot/operator/avatar.php?op=" . intval($newop['operatorid']));
			exit;
		} else {
			update_operator($opId, $login, $email, $jabber, $password, $localname, $commonname, $jabbernotify ? 1 : 0);
			// update the session password
			if (!empty($password) && $opId == $operator['operatorid']) {
				$toDashboard = check_password_hash($login, '', $operator['vcpassword']) && $password != '';
				$_SESSION["${mysqlprefix}operator"]['vcpassword'] = calculate_password_hash($login, $password);
				if($toDashboard) {
					header("Location: $mibewroot/operator/index.php");
					exit;
				}
			}
			header("Location: $mibewroot/operator/operator.php?op=" . intval($opId) . "&stored");
			exit;
		}
	} else {
		$page['formlogin'] = topage($login);
		$page['formname'] = topage($localname);
		$page['formemail'] = topage($email);
		$page['formjabber'] = topage($jabber);
		$page['formjabbernotify'] = $jabbernotify;
		$page['formcommonname'] = topage($commonname);
		$page['opid'] = topage($opId);
	}

} else if (isset($_GET['op'])) {
	$opId = verifyparam('op', "/^\d{1,10}$/");
	$op = operator_by_id($opId);

	if (!$op) {
		$errors[] = getlocal("no_such_operator");
		$page['opid'] = topage($opId);
	} else {
		$page['formlogin'] = topage($op['vclogin']);
		$page['formname'] = topage($op['vclocalename']);
		$page['formemail'] = topage($op['vcemail']);
		$page['formjabber'] = topage($op['vcjabbername']);
		$page['formjabbernotify'] = $op['inotify'] != 0;
		$page['formcommonname'] = topage($op['vccommonname']);
		$page['opid'] = topage($op['operatorid']);
	}
}

if (!$opId && !is_capable($can_administrate, $operator)) {
	$errors[] = "You are not allowed to create operators";
}
elseif ($opId && ($opId != $operator['operatorid'])) {
	check_permissions($operator, $can_administrate);
}

$canmodify = ($opId == $operator['operatorid'] && is_capable($can_modifyprofile, $operator))
			 || is_capable($can_administrate, $operator);

$page['stored'] = isset($_GET['stored']);
$page['canmodify'] = $canmodify ? "1" : "";
$page['showjabber'] = $settings['enablejabber'] == "1";
$page['needChangePassword'] = ($opId == $operator['operatorid']) ? check_password_hash($operator['vclogin'], '', $operator['vcpassword']) : FALSE;

prepare_menu($operator);
setup_operator_settings_tabs($opId, 0);
start_html_output();
require('../view/agent.php');
?>