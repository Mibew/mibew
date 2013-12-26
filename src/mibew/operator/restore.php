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
require_once(MIBEW_FS_ROOT.'/libs/operator.php');
require_once(MIBEW_FS_ROOT.'/libs/settings.php');
require_once(MIBEW_FS_ROOT.'/libs/notify.php');
require_once(MIBEW_FS_ROOT.'/libs/interfaces/style.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/style.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/page_style.php');

$errors = array();
$page = array(
	'version' => $version,
	'title' => getlocal("restore.title"),
	'headertitle' => getlocal("app.title"),
	'show_small_login' => true,
	'fixedwrap' => true,
);

$loginoremail = "";

$page_style = new PageStyle(PageStyle::currentStyle());

if (isset($_POST['loginoremail'])) {
	$loginoremail = getparam("loginoremail");

	$torestore = is_valid_email($loginoremail) ? operator_by_email($loginoremail) : operator_by_login($loginoremail);
	if (!$torestore) {
		$errors[] = getlocal("no_such_operator");
	}

	$email = $torestore['vcemail'];
	if (count($errors) == 0 && !is_valid_email($email)) {
		$errors[] = "Operator hasn't set his e-mail";
	}

	if (count($errors) == 0) {
		$token = sha1($torestore['vclogin'] . (function_exists('openssl_random_pseudo_bytes') ? openssl_random_pseudo_bytes(32) : (time() + microtime()) . mt_rand(0, 99999999)));

		$db = Database::getInstance();
		$db->query(
			"update {chatoperator} set dtmrestore = :now, " .
			"vcrestoretoken = :token where operatorid = :operatorid",
			array(
				':now' => time(),
				':token' => $token,
				':operatorid' => $torestore['operatorid']
			)
		);

		$href = get_app_location(true, false) . "/operator/resetpwd.php?id=" . $torestore['operatorid'] . "&token=$token";
		mibew_mail($email, $email, getstring("restore.mailsubj"), getstring2("restore.mailtext", array(get_operator_name($torestore), $href)));

		$page['isdone'] = true;
		$page_style->render('restore');
		exit;
	}
}

$page['formloginoremail'] = topage($loginoremail);

$page['localeLinks'] = get_locale_links("$mibewroot/operator/restore.php");
$page['isdone'] = false;

$page_style->render('restore');

?>