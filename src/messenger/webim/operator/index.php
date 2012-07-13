<?php
/*
 * Copyright 2005-2013 the original author or authors.
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

$operator = check_login();
force_password($operator);

loadsettings();
$isonline = is_operator_online($operator['operatorid']);

$page = array(
	'version' => $version,
	'localeLinks' => get_locale_links("$webimroot/operator/index.php"),
	'needUpdate' => $settings['dbversion'] != $dbversion,
	'needChangePassword' => $operator['vcpassword'] == md5(''),
	'profilePage' => "$webimroot/operator/operator.php?op=".$operator['operatorid'],
	'updateWizard' => "$webimroot/install/",
	'newFeatures' => $settings['featuresversion'] != $featuresversion,
	'featuresPage' => "$webimroot/operator/features.php",
	'isOnline' => $isonline
);

prepare_menu($operator);
start_html_output();
require('../view/menu.php');
?>