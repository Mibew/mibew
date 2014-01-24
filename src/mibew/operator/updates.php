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

require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('../libs/settings.php');

$operator = check_login();
check_permissions($operator, $can_administrate);

$default_extensions = array('mysql', 'gd', 'iconv');

$errors = array();
$page = array(
	'localizations' => get_available_locales(),
	'phpVersion' => phpversion(),
	'version' => $version,
);

foreach ($default_extensions as $ext) {
	if (!extension_loaded($ext)) {
		$page['phpVersion'] .= " $ext/absent";
	} else {
		$ver = phpversion($ext);
		$page['phpVersion'] .= $ver ? " $ext/$ver" : " $ext";
	}
}

prepare_menu($operator);
setup_settings_tabs(3);
start_html_output();
require('../view/updates.php');
?>