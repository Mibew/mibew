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

// Import namespaces and classes of the core
use Mibew\Settings;
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');

$operator = check_login();
csrf_check_token();

$op_id = verify_param("op", "/^\d{1,9}$/");
$page = array(
    'opid' => $op_id,
    'avatar' => '',
    'errors' => array(),
);

$can_modify = ($op_id == $operator['operatorid'] && is_capable(CAN_MODIFYPROFILE, $operator))
    || is_capable(CAN_ADMINISTRATE, $operator);

$op = operator_by_id($op_id);

if (!$op) {
    $page['errors'][] = getlocal("no_such_operator");
} elseif (isset($_POST['op'])) {
    $avatar = $op['vcavatar'];

    if (!$can_modify) {
        $page['errors'][] = getlocal('page_agent.cannot_modify');
    } elseif (isset($_FILES['avatarFile']) && $_FILES['avatarFile']['name']) {
        $valid_types = array("gif", "jpg", "png", "tif", "jpeg");

        $orig_filename = $_FILES['avatarFile']['name'];
        $tmp_file_name = $_FILES['avatarFile']['tmp_name'];

        $ext = preg_replace('/\//', '', strtolower(substr($orig_filename, 1 + strrpos($orig_filename, "."))));
        $new_file_name = intval($op_id) . ".$ext";

        $file_size = $_FILES['avatarFile']['size'];
        if ($file_size == 0 || $file_size > Settings::get('max_uploaded_file_size')) {
            $page['errors'][] = failed_uploading_file($orig_filename, "errors.file.size.exceeded");
        } elseif (!in_array($ext, $valid_types)) {
            $page['errors'][] = failed_uploading_file($orig_filename, "errors.invalid.file.type");
        } else {
            $avatar_local_dir = MIBEW_FS_ROOT . '/files/avatar/';
            $full_file_path = $avatar_local_dir . $new_file_name;
            if (file_exists($full_file_path)) {
                unlink($full_file_path);
            }
            if (!@move_uploaded_file($_FILES['avatarFile']['tmp_name'], $full_file_path)) {
                $page['errors'][] = failed_uploading_file($orig_filename, "errors.file.move.error");
            } else {
                $avatar = MIBEW_WEB_ROOT . "/files/avatar/$new_file_name";
            }
        }
    } else {
        $page['errors'][] = "No file selected";
    }

    if (count($page['errors']) == 0) {
        update_operator_avatar($op['operatorid'], $avatar);

        if ($op_id && $avatar && $_SESSION[SESSION_PREFIX . "operator"] && $operator['operatorid'] == $op_id) {
            $_SESSION[SESSION_PREFIX . "operator"]['vcavatar'] = $avatar;
        }
        header("Location: " . MIBEW_WEB_ROOT . "/operator/avatar.php?op=" . intval($op_id));
        exit;
    } else {
        $page['avatar'] = $op['vcavatar'];
    }
} else {
    if (isset($_GET['delete']) && $_GET['delete'] == "true" && $can_modify) {
        update_operator_avatar($op['operatorid'], '');
        header("Location: " . MIBEW_WEB_ROOT . "/operator/avatar.php?op=" . intval($op_id));
        exit;
    }
    $page['avatar'] = $op['vcavatar'];
}

$page['currentop'] = $op ? get_operator_name($op) . " (" . $op['vclogin'] . ")" : getlocal("not_found");
$page['canmodify'] = $can_modify ? "1" : "";
$page['title'] = getlocal("page_avatar.title");
$page['menuid'] = ($operator['operatorid'] == $op_id) ? "profile" : "operators";

$page = array_merge($page, prepare_menu($operator));
$page['tabs'] = setup_operator_settings_tabs($op_id, 1);

$page_style = new PageStyle(PageStyle::getCurrentStyle());
$page_style->render('avatar', $page);
