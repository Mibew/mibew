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
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');

$operator = check_login();
csrf_check_token();

$op_id = verify_param("op", "/^\d{1,9}$/");
$page = array(
    'opid' => $op_id,
    'canmodify' => is_capable(CAN_ADMINISTRATE, $operator) ? "1" : "",
    'errors' => array(),
);

$op = operator_by_id($op_id);

if (!$op) {
    $page['errors'][] = getlocal("no_such_operator");
} elseif (isset($_POST['op'])) {

    if (!is_capable(CAN_ADMINISTRATE, $operator)) {
        $page['errors'][] = getlocal('page_agent.cannot_modify');
    }

    $new_permissions = isset($op['iperm']) ? $op['iperm'] : 0;

    foreach (permission_ids() as $perm => $id) {
        if (verify_param("permissions$id", "/^on$/", "") == "on") {
            $new_permissions |= (1 << $perm);
        } else {
            $new_permissions &= ~(1 << $perm);
        }
    }

    if (count($page['errors']) == 0) {
        update_operator_permissions($op['operatorid'], $new_permissions);

        if ($op_id && $_SESSION[SESSION_PREFIX . "operator"] && $operator['operatorid'] == $op_id) {
            $_SESSION[SESSION_PREFIX . "operator"]['iperm'] = $new_permissions;
        }
        header("Location: " . MIBEW_WEB_ROOT . "/operator/permissions.php?op=" . intval($op_id) . "&stored");
        exit;
    }
}

$page['currentop'] = $op
    ? get_operator_name($op) . " (" . $op['vclogin'] . ")"
    : getlocal("not_found");

$checked_permissions = array();
if ($op) {
    foreach (permission_ids() as $perm => $id) {
        if (is_capable($perm, $op)) {
            $checked_permissions[] = $id;
        }
    }
}

$page['permissionsList'] = array();
foreach (get_permission_list() as $perm) {
    $perm['checked'] = in_array($perm['id'], $checked_permissions);
    $page['permissionsList'][] = $perm;
}

$page['stored'] = isset($_GET['stored']);
$page['title'] = getlocal("permissions.title");
$page['menuid'] = ($operator['operatorid'] == $op_id) ? "profile" : "operators";

$page = array_merge($page, prepare_menu($operator));

$page['tabs'] = setup_operator_settings_tabs($op_id, 3);

$page_style = new PageStyle(PageStyle::getCurrentStyle());
$page_style->render('permissions', $page);
