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

$page = array(
    'version' => MIBEW_VERSION,
    'title' => getlocal("restore.title"),
    'headertitle' => getlocal("app.title"),
    'show_small_login' => true,
    'fixedwrap' => true,
    'errors' => array(),
);

$login_or_email = "";

$page_style = new PageStyle(PageStyle::getCurrentStyle());

if (isset($_POST['loginoremail'])) {
    $login_or_email = get_param("loginoremail");

    $to_restore = is_valid_email($login_or_email)
        ? operator_by_email($login_or_email)
        : operator_by_login($login_or_email);
    if (!$to_restore) {
        $page['errors'][] = getlocal("no_such_operator");
    }

    $email = $to_restore['vcemail'];
    if (count($page['errors']) == 0 && !is_valid_email($email)) {
        $page['errors'][] = "Operator hasn't set his e-mail";
    }

    if (count($page['errors']) == 0) {
        $token = sha1($to_restore['vclogin'] . (function_exists('openssl_random_pseudo_bytes')
            ? openssl_random_pseudo_bytes(32)
            : (time() + microtime()) . mt_rand(0, 99999999)));

        $db = Database::getInstance();
        $db->query(
            ("UPDATE {chatoperator} "
                . "SET dtmrestore = :now, vcrestoretoken = :token "
                . "WHERE operatorid = :operatorid"),
            array(
                ':now' => time(),
                ':token' => $token,
                ':operatorid' => $to_restore['operatorid'],
            )
        );

        $href = get_app_location(true, false) . "/operator/resetpwd.php?id="
            . $to_restore['operatorid'] . "&token=$token";
        mibew_mail(
            $email,
            $email,
            getstring("restore.mailsubj"),
            getstring2(
                "restore.mailtext",
                array(get_operator_name($to_restore), $href)
            )
        );

        $page['isdone'] = true;
        $page_style->render('restore', $page);
        exit;
    }
}

$page['formloginoremail'] = $login_or_email;

$page['localeLinks'] = get_locale_links();
$page['isdone'] = false;

$page_style->render('restore', $page);
