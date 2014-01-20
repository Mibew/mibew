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
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(MIBEW_FS_ROOT.'/libs/operator.php');
require_once(MIBEW_FS_ROOT.'/libs/settings.php');

$operator = check_login();
force_password($operator);

$default_extensions = array('mysql', 'gd', 'iconv');

$errors = array();
$page = array(
	'localizations' => get_available_locales(),
	'phpVersion' => phpversion(),
	'version' => $version,
	'title' => getlocal("updates.title"),
	'menuid' => "updates",
);

foreach ($default_extensions as $ext) {
	if (!extension_loaded($ext)) {
		$page['phpVersion'] .= " $ext/absent";
	} else {
		$ver = phpversion($ext);
		$page['phpVersion'] .= $ver ? " $ext/$ver" : " $ext";
	}
}

$page = array_merge(
	$page,
	prepare_menu($operator)
);

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('updates');

?>