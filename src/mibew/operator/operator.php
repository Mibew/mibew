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

$page = array(
    'opid' => '',
    'errors' => array(),
);

$op_id = '';

if ((isset($_POST['login']) || !is_capable(CAN_ADMINISTRATE, $operator)) && isset($_POST['password'])) {
    $op_id = verify_param("opid", "/^(\d{1,9})?$/", "");
    if (is_capable(CAN_ADMINISTRATE, $operator)) {
        $login = get_param('login');
    } else {
        $login = $operator['vclogin'];
    }
    $email = get_param('email');
    $password = get_param('password');
    $password_confirm = get_param('passwordConfirm');
    $local_name = get_param('name');
    $common_name = get_param('commonname');
    $code = get_param('code');

    if (!$local_name) {
        $page['errors'][] = no_field("form.field.agent_name");
    }

    if (!$common_name) {
        $page['errors'][] = no_field("form.field.agent_commonname");
    }

    if (!$login) {
        $page['errors'][] = no_field("form.field.login");
    } elseif (!preg_match("/^[\w_\.]+$/", $login)) {
        $page['errors'][] = getlocal("page_agent.error.wrong_login");
    }

    if ($email == '' || !is_valid_email($email)) {
        $page['errors'][] = wrong_field("form.field.mail");
    }

    if ($code != '' && (!preg_match("/^[A-z0-9_]+$/", $code))) {
        $page['errors'][] = getlocal("page_agent.error.wrong_agent_code");
    }

    if (!$op_id && !$password) {
        $page['errors'][] = no_field("form.field.password");
    }

    if ($password != $password_confirm) {
        $page['errors'][] = getlocal("my_settings.error.password_match");
    }

    $existing_operator = operator_by_login($login);
    $duplicate_login = (!$op_id && $existing_operator)
        || ($op_id
            && $existing_operator
            && $op_id != $existing_operator['operatorid']);
    if ($duplicate_login) {
        $page['errors'][] = getlocal("page_agent.error.duplicate_login");
    }

    // Check if operator with specified email already exists in the database
    $existing_operator = operator_by_email($email);
    $duplicate_email =
        // Create operator with email already in database
        (!$op_id && $existing_operator)
        // Update operator email to existing one
        || ($op_id
            && $existing_operator
            && $op_id != $existing_operator['operatorid']);
    if ($duplicate_email) {
        $page['errors'][] = getlocal("page_agent.error.duplicate_email");
    }

    $can_modify = ($op_id == $operator['operatorid'] && is_capable(CAN_MODIFYPROFILE, $operator))
        || is_capable(CAN_ADMINISTRATE, $operator);
    if (!$can_modify) {
        $page['errors'][] = getlocal('page_agent.cannot_modify');
    }

    if (count($page['errors']) == 0) {
        if (!$op_id) {
            $new_operator = create_operator($login, $email, $password, $local_name, $common_name, "", $code);
            header("Location: " . MIBEW_WEB_ROOT . "/operator/avatar.php?op=" . intval($new_operator['operatorid']));
            exit;
        } else {
            update_operator($op_id, $login, $email, $password, $local_name, $common_name, $code);
            // update the session password
            if (!empty($password) && $op_id == $operator['operatorid']) {
                $to_dashboard = check_password_hash($login, '', $operator['vcpassword']) && $password != '';
                $_SESSION[SESSION_PREFIX . "operator"]['vcpassword'] = calculate_password_hash($login, $password);
                if ($to_dashboard) {
                    header("Location: " . MIBEW_WEB_ROOT . "/operator/index.php");
                    exit;
                }
            }
            header("Location: " . MIBEW_WEB_ROOT . "/operator/operator.php?op=" . intval($op_id) . "&stored");
            exit;
        }
    } else {
        $page['formlogin'] = $login;
        $page['formname'] = $local_name;
        $page['formemail'] = $email;
        $page['formcommonname'] = $common_name;
        $page['formcode'] = $code;
        $page['opid'] = $op_id;
    }
} elseif (isset($_GET['op'])) {
    $op_id = verify_param('op', "/^\d{1,9}$/");
    $op = operator_by_id($op_id);

    if (!$op) {
        $page['errors'][] = getlocal("no_such_operator");
        $page['opid'] = $op_id;
    } else {
        //show an error if the admin password hasn't been set yet.
        if (check_password_hash($operator['vclogin'], '', $operator['vcpassword']) && !isset($_GET['stored'])) {
            $page['errors'][] = getlocal("my_settings.error.no_password");
        }

        $page['formlogin'] = $op['vclogin'];
        $page['formname'] = $op['vclocalename'];
        $page['formemail'] = $op['vcemail'];
        $page['formcommonname'] = $op['vccommonname'];
        $page['formcode'] = $op['code'];
        $page['opid'] = $op['operatorid'];
    }
}

if (!$op_id && !is_capable(CAN_ADMINISTRATE, $operator)) {
    $page['errors'][] = getlocal("page_agent.error.forbidden_create");
}

$can_modify = ($op_id == $operator['operatorid'] && is_capable(CAN_MODIFYPROFILE, $operator))
    || is_capable(CAN_ADMINISTRATE, $operator);

$page['stored'] = isset($_GET['stored']);
$page['canmodify'] = $can_modify ? "1" : "";
$page['canchangelogin'] = is_capable(CAN_ADMINISTRATE, $operator);
$page['needChangePassword'] = check_password_hash($operator['vclogin'], '', $operator['vcpassword']);
$page['title'] = getlocal("page_agent.title");
$page['menuid'] = ($op_id == $operator['operatorid']) ? "profile" : "operators";
$page['requirePassword'] = (!$op_id || $page['needChangePassword']);

$page = array_merge($page, prepare_menu($operator));

$page['tabs'] = setup_operator_settings_tabs($op_id, 0);

$page_style = new PageStyle(PageStyle::getCurrentStyle());
$page_style->render('operator', $page);
