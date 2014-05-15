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

$page = array(
    'formisRemember' => true,
    'version' => MIBEW_VERSION,
    'errors' => array(),
);

if (isset($_POST['login']) && isset($_POST['password'])) {
    $login = get_param('login');
    $password = get_param('password');
    $remember = isset($_POST['isRemember']) && $_POST['isRemember'] == "on";

    $operator = operator_by_login($login);
    $operator_can_login = $operator
        && isset($operator['vcpassword'])
        && check_password_hash($operator['vclogin'], $password, $operator['vcpassword'])
        && !operator_is_disabled($operator);

    if ($operator_can_login) {
        $target = $password == ''
            ? MIBEW_WEB_ROOT . "/operator/operator.php?op=" . intval($operator['operatorid'])
            : (isset($_SESSION['backpath']) ? $_SESSION['backpath'] : MIBEW_WEB_ROOT . "/operator/index.php");

        login_operator($operator, $remember, is_secure_request());
        header("Location: $target");
        exit;
    } else {
        if (operator_is_disabled($operator)) {
            $page['errors'][] = getlocal('page_login.operator.disabled');
        } else {
            $page['errors'][] = getlocal("page_login.error");
        }
        $page['formlogin'] = $login;
    }
} elseif (isset($_GET['login'])) {
    $login = get_get_param('login');
    if (preg_match("/^(\w{1,15})$/", $login)) {
        $page['formlogin'] = $login;
    }
}

$page['localeLinks'] = get_locale_links();
$page['title'] = getlocal("page_login.title");
$page['headertitle'] = getlocal("app.title");
$page['show_small_login'] = false;
$page['fixedwrap'] = true;

$page_style = new PageStyle(PageStyle::getCurrentStyle());
$page_style->render('login', $page);
