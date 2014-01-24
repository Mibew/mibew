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

$opId = verifyparam("op", "/^\d{1,10}$/");
$page = array('opid' => $opId, 'avatar' => '');
$errors = array();

if ($opId && ($opId != $operator['operatorid'])) {
	check_permissions($operator, $can_administrate);
}

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
		$valid_types = array("gif", "jpg", "png", "tif", "jpeg");

		$orig_filename = $_FILES['avatarFile']['name'];
		$tmp_file_name = $_FILES['avatarFile']['tmp_name'];

		$ext = preg_replace('/\//', '', strtolower(substr($orig_filename, 1 + strrpos($orig_filename, "."))));
		$new_file_name = intval($opId). ".$ext";
		loadsettings();

		$file_size = $_FILES['avatarFile']['size'];
		if ($file_size == 0 || $file_size > $settings['max_uploaded_file_size']) {
			$errors[] = failed_uploading_file($orig_filename, "errors.file.size.exceeded");
		} elseif (!in_array($ext, $valid_types)) {
			$errors[] = failed_uploading_file($orig_filename, "errors.invalid.file.type");
		} else {
			$avatar_local_dir = dirname(__FILE__) . "/../images/avatar/";
			$full_file_path = $avatar_local_dir . $new_file_name;
			if (file_exists($full_file_path)) {
				unlink($full_file_path);
			}
			if (!@move_uploaded_file($_FILES['avatarFile']['tmp_name'], $full_file_path)) {
				$errors[] = failed_uploading_file($orig_filename, "errors.file.move.error");
			} else {
				$avatar = "$mibewroot/images/avatar/$new_file_name";
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
		header("Location: $mibewroot/operator/avatar.php?op=" . intval($opId));
		exit;
	} else {
		$page['avatar'] = topage($op['vcavatar']);
	}

} else {
	if (isset($_GET['delete']) && $_GET['delete'] == "true" && $canmodify) {
		update_operator_avatar($op['operatorid'], '');
		header("Location: $mibewroot/operator/avatar.php?op=" . intval($opId));
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