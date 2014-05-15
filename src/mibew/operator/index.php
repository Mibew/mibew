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
force_password($operator);

$is_online = is_operator_online($operator['operatorid']);

$page = array(
    'version' => MIBEW_VERSION,
    'localeLinks' => get_locale_links(),
    'needUpdate' => Settings::get('dbversion') != DB_VERSION,
    'needChangePassword' => check_password_hash($operator['vclogin'], '', $operator['vcpassword']),
    'profilePage' => MIBEW_WEB_ROOT . "/operator/operator.php?op=" . $operator['operatorid'],
    'updateWizard' => MIBEW_WEB_ROOT . "/install/",
    'newFeatures' => Settings::get('featuresversion') != FEATURES_VERSION,
    'featuresPage' => MIBEW_WEB_ROOT . "/operator/features.php",
    'isOnline' => $is_online,
    'warnOffline' => true,
    'title' => getlocal("topMenu.admin"),
    'menuid' => "main",
);

$page = array_merge($page, prepare_menu($operator));

$page_style = new PageStyle(PageStyle::getCurrentStyle());
$page_style->render('index', $page);
