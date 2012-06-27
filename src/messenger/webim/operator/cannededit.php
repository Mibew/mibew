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

require_once('../libs/canned.php');
require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('../libs/pagination.php');

$operator = check_login();
csrfchecktoken();
loadsettings();

$stringid = verifyparam("key", "/^\d{0,9}$/", "");

$errors = array();
$page = array();

if ($stringid) {
	$canned_message = load_canned_message($stringid);
	if (!$canned_message) {
		$errors[] = getlocal("cannededit.no_such");
		$stringid = "";
	}else{
		$title = $canned_message['vctitle'];
		$message = $canned_message['vcvalue'];
	}
} else {
	$message = '';
	$title = '';
	$page['locale'] = verifyparam("lang", "/^[\w-]{2,5}$/", "");
	$page['groupid'] = "";
	$page['groupid'] = verifyparam("group", "/^\d{0,8}$/");
}

if (isset($_POST['message']) && isset($_POST['title'])) {
	$title = getparam('title');
	if (!$title) {
		$errors[] = no_field("form.field.title");
	}

	$message = getparam('message');
	if (!$message) {
		$errors[] = no_field("form.field.message");
	}

	if (count($errors) == 0) {
		if ($stringid) {
			save_canned_message($stringid, $title, $message);
		} else {
			add_canned_message($page['locale'], $page['groupid'], $title, $message);
		}
		$page['saved'] = true;
		prepare_menu($operator, false);
		start_html_output();
		require('../view/cannededit.php');
		exit;
	}
}

$page['saved'] = false;
$page['key'] = $stringid;
$page['formtitle'] = topage($title);
$page['formmessage'] = topage($message);
prepare_menu($operator, false);
start_html_output();
require('../view/cannededit.php');
exit;
?>
