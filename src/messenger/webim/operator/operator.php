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
require_once('../libs/operator_settings.php');

$operator = check_login();

$page = array('opid' => '');
$errors = array();
$opId = '';

if (isset($_POST['login']) && isset($_POST['password'])) {
	$opId = verifyparam("opid", "/^(\d{1,9})?$/", "");
	$login = getparam('login');
	$email = getparam('email');
	$password = getparam('password');
	$passwordConfirm = getparam('passwordConfirm');
	$localname = getparam('name');
	$commonname = getparam('commonname');

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
			$newop = create_operator($login, $email, $password, $localname, $commonname, "");
			header("Location: $webimroot/operator/avatar.php?op=" . $newop['operatorid']);
			exit;
		} else {
			update_operator($opId, $login, $email, $password, $localname, $commonname);
			header("Location: $webimroot/operator/operator.php?op=$opId&stored");
			exit;
		}
	} else {
		$page['formlogin'] = topage($login);
		$page['formname'] = topage($localname);
		$page['formemail'] = topage($email);
		$page['formcommonname'] = topage($commonname);
		$page['opid'] = topage($opId);
	}

} else if (isset($_GET['op'])) {
	$opId = verifyparam('op', "/^\d{1,9}$/");
	$op = operator_by_id($opId);

	if (!$op) {
		$errors[] = getlocal("no_such_operator");
		$page['opid'] = topage($opId);
	} else {
		$page['formlogin'] = topage($op['vclogin']);
		$page['formname'] = topage($op['vclocalename']);
		$page['formemail'] = topage($op['vcemail']);
		$page['formcommonname'] = topage($op['vccommonname']);
		$page['opid'] = topage($op['operatorid']);
	}
}

if (!$opId && !is_capable($can_administrate, $operator)) {
	$errors[] = "You are not allowed to create operators";
}

$canmodify = ($opId == $operator['operatorid'] && is_capable($can_modifyprofile, $operator))
			 || is_capable($can_administrate, $operator);

$page['stored'] = isset($_GET['stored']);
$page['canmodify'] = $canmodify ? "1" : "";

prepare_menu($operator);
setup_operator_settings_tabs($opId, 0);
start_html_output();
require('../view/agent.php');
?>