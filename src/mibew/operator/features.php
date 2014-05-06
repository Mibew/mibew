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

$page = array(
    'agentId' => '',
    'errors' => array(),
);

$options = array(
    'enableban',
    'usercanchangename',
    'enablegroups',
    'enablegroupsisolation',
    'enablestatistics',
    'enabletracking',
    'enablessl',
    'forcessl',
    'enablepresurvey',
    'surveyaskmail',
    'surveyaskgroup',
    'surveyaskmessage',
    'enablepopupnotification',
    'showonlineoperators',
    'enablecaptcha',
);

if (Settings::get('featuresversion') != FEATURES_VERSION) {
    Settings::set('featuresversion', FEATURES_VERSION);
    Settings::update();
}
$params = array();
foreach ($options as $opt) {
    $params[$opt] = Settings::get($opt);
}

if (isset($_POST['sent'])) {
    if (is_capable(CAN_ADMINISTRATE, $operator)) {
        foreach ($options as $opt) {
            Settings::set($opt, (verify_param($opt, "/^on$/", "") == "on" ? "1" : "0"));
        }
        Settings::update();
        header("Location: " . MIBEW_WEB_ROOT . "/operator/features.php?stored");
        exit;
    } else {
        $page['errors'][] = "Not an administrator";
    }
}

$page['canmodify'] = is_capable(CAN_ADMINISTRATE, $operator);
$page['stored'] = isset($_GET['stored']);
foreach ($options as $opt) {
    $page["form$opt"] = $params[$opt] == "1";
}

$page['title'] = getlocal("settings.title");
$page['menuid'] = "settings";

$page = array_merge($page, prepare_menu($operator));

$page['tabs'] = setup_settings_tabs(1);

$page_style = new PageStyle(PageStyle::getCurrentStyle());
$page_style->render('features', $page);
