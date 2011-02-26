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

$opId = verifyparam("op", "/^\d{1,9}$/");
$page = array('opid' => $opId, 'avatar' => '');
$errors = array();

$canmodify = ($opId == $operator['operatorid'] && is_capable($can_modifyprofile, $operator))
			 || is_capable($can_administrate, $operator);

$op = operator_by_id($opId);

if (!$op) {
	$errors[] = getlocal("no_such_operator");

} else if (isset($_POST['op'])) {
	$avatar = $op['vcavatar'];

	if (!$canmodify) {
		$errors[] = getlocal('page_agent.cannot_modify');

	} else if (isset($_FILES['avatarFile']) && $_FILES['avatarFile']['name']) {
		$valid_types = array("gif", "jpg", "png", "tif");

		$orig_filename = $_FILES['avatarFile']['name'];
		$tmp_file_name = $_FILES['avatarFile']['tmp_name'];

		$ext = strtolower(substr($orig_filename, 1 + strrpos($orig_filename, ".")));
		$new_file_name = "$opId.$ext";
		loadsettings();

		$file_size = $_FILES['avatarFile']['size'];
		if ($file_size == 0 || $file_size > $settings['max_uploaded_file_size']) {
			$errors[] = failed_uploading_file($orig_filename, "errors.file.size.exceeded");
		} elseif (!in_array($ext, $valid_types)) {
			$errors[] = failed_uploading_file($orig_filename, "errors.invalid.file.type");
		} else {
			$avatar_local_dir = "../images/avatar/";
			$full_file_path = $avatar_local_dir . $new_file_name;
			if (file_exists($full_file_path)) {
				unlink($full_file_path);
			}
			if (!move_uploaded_file($_FILES['avatarFile']['tmp_name'], $full_file_path)) {
				$errors[] = failed_uploading_file($orig_filename, "errors.file.move.error");
			} else {
				$avatar = "$webimroot/images/avatar/$new_file_name";
			}
		}
	} else {
		$errors[] = "No file selected";
	}

	if (count($errors) == 0) {
		update_operator_avatar($op['operatorid'], $avatar);

		if ($opId && $avatar && $_SESSION["${mysqlprefix}operator"] && $operator['operatorid'] == $opId) {
			$_SESSION["${mysqlprefix}operator"]['vcavatar'] = $avatar;
		}
		header("Location: $webimroot/operator/avatar.php?op=$opId");
		exit;
	} else {
		$page['avatar'] = topage($op['vcavatar']);
	}

} else {
	if (isset($_GET['delete']) && $_GET['delete'] == "true" && $canmodify) {
		update_operator_avatar($op['operatorid'], '');
		header("Location: $webimroot/operator/avatar.php?op=$opId");
		exit;
	}
	$page['avatar'] = topage($op['vcavatar']);
}

$page['currentop'] = $op ? topage(get_operator_name($op)) . " (" . $op['vclogin'] . ")" : "-not found-";
$page['canmodify'] = $canmodify ? "1" : "";

prepare_menu($operator);
setup_operator_settings_tabs($opId, 1);
start_html_output();
require('../view/avatar.php');
?>