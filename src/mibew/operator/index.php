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

// Import namespaces and classes of the core
use Mibew\Settings;

// Initialize libraries
require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(MIBEW_FS_ROOT.'/libs/operator.php');
require_once(MIBEW_FS_ROOT.'/libs/interfaces/style.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/style.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/page_style.php');

$operator = check_login();
force_password($operator);

$isonline = is_operator_online($operator['operatorid']);

$page = array(
	'version' => $version,
	'localeLinks' => get_locale_links("$mibewroot/operator/index.php"),
	'needUpdate' => Settings::get('dbversion') != $dbversion,
	'needChangePassword' => check_password_hash($operator['vclogin'], '', $operator['vcpassword']),
	'profilePage' => "$mibewroot/operator/operator.php?op=".$operator['operatorid'],
	'updateWizard' => "$mibewroot/install/",
	'newFeatures' => Settings::get('featuresversion') != $featuresversion,
	'featuresPage' => "$mibewroot/operator/features.php",
	'isOnline' => $isonline,
	'title' => getlocal("topMenu.admin"),
	'menuid' => "main",
);

prepare_menu($operator);

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('menu');

?>