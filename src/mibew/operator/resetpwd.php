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
use Mibew\Database;
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');
require_once(MIBEW_FS_ROOT . '/libs/operator.php');
require_once(MIBEW_FS_ROOT . '/libs/settings.php');

$page = array(
    'version' => MIBEW_VERSION,
    'showform' => true,
    'title' => getlocal("resetpwd.title"),
    'headertitle' => getlocal("app.title"),
    'show_small_login' => true,
    'fixedwrap' => true,
    'errors' => array(),
);

$page_style = new PageStyle(PageStyle::getCurrentStyle());

$op_id = verify_param("id", "/^\d{1,9}$/");
$token = verify_param("token", "/^[\dabcdef]+$/");

$operator = operator_by_id($op_id);

if (!$operator) {
    $page['errors'][] = "No such operator";
    $page['showform'] = false;
} elseif ($token != $operator['vcrestoretoken']) {
    $page['errors'][] = "Wrong token";
    $page['showform'] = false;
}

if (count($page['errors']) == 0 && isset($_POST['password'])) {
    $password = get_param('password');
    $password_confirm = get_param('passwordConfirm');

    if (!$password) {
        $page['errors'][] = no_field("form.field.password");
    }

    if ($password != $password_confirm) {
        $page['errors'][] = getlocal("my_settings.error.password_match");
    }

    if (count($page['errors']) == 0) {
        $page['isdone'] = true;

        $db = Database::getInstance();
        $db->query(
            ("UPDATE {chatoperator} "
                . "SET vcpassword = ?, vcrestoretoken = '' "
                . "WHERE operatorid = ?"),
            array(
                calculate_password_hash($operator['vclogin'], $password),
                $op_id,
            )
        );

        $page['loginname'] = $operator['vclogin'];
        $page_style->render('resetpwd', $page);
        exit;
    }
}

$page['id'] = $op_id;
$page['token'] = $token;
$page['isdone'] = false;

$page_style->render('resetpwd', $page);
