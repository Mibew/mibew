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
csrfchecktoken();
check_permissions($operator, $can_administrate);

$page = array('agentId' => '');
$errors = array();

$stylelist = array();
$stylesfolder = "../styles";
if ($handle = opendir($stylesfolder)) {
	while (false !== ($file = readdir($handle))) {
		if (preg_match("/^\w+$/", $file) && is_dir("$stylesfolder/$file")) {
			$stylelist[] = $file;
		}
	}
	closedir($handle);
}

$options = array(
	'email', 'title', 'logo', 'hosturl', 'usernamepattern',
	'chatstyle', 'chattitle', 'geolink', 'geolinkparams', 'sendmessagekey');

loadsettings();
$params = array();
foreach ($options as $opt) {
	$params[$opt] = $settings[$opt];
}

if (isset($_POST['email']) && isset($_POST['title']) && isset($_POST['logo'])) {
	$params['email'] = getparam('email');
	$params['title'] = getparam('title');
	$params['logo'] = getparam('logo');
	$params['hosturl'] = getparam('hosturl');
	$params['usernamepattern'] = getparam('usernamepattern');
	$params['chattitle'] = getparam('chattitle');
	$params['geolink'] = getparam('geolink');
	$params['geolinkparams'] = getparam('geolinkparams');
	$params['sendmessagekey'] = verifyparam('sendmessagekey', "/^c?enter$/");

	$params['chatstyle'] = verifyparam("chatstyle", "/^\w+$/", $params['chatstyle']);
	if (!in_array($params['chatstyle'], $stylelist)) {
		$params['chatstyle'] = $stylelist[0];
	}

	if ($params['email'] && !is_valid_email($params['email'])) {
		$errors[] = getlocal("settings.wrong.email");
	}

	if ($params['geolinkparams']) {
		foreach (preg_split("/,/", $params['geolinkparams']) as $oneparam) {
			if (!preg_match("/^\s*(toolbar|scrollbars|location|status|menubar|width|height|resizable)=\d{1,4}$/", $oneparam)) {
				$errors[] = "Wrong link parameter: \"" . safe_htmlspecialchars($oneparam) . "\", should be one of 'toolbar, scrollbars, location, status, menubar, width, height or resizable'";
			}
		}
	}

	if (count($errors) == 0) {
		foreach ($options as $opt) {
			$settings[$opt] = $params[$opt];
		}
		update_settings();
		header("Location: $mibewroot/operator/settings.php?stored");
		exit;
	}
}

$page['formemail'] = topage($params['email']);
$page['formtitle'] = topage($params['title']);
$page['formlogo'] = topage($params['logo']);
$page['formhosturl'] = topage($params['hosturl']);
$page['formgeolink'] = topage($params['geolink']);
$page['formgeolinkparams'] = topage($params['geolinkparams']);
$page['formusernamepattern'] = topage($params['usernamepattern']);
$page['formchatstyle'] = $params['chatstyle'];
$page['formchattitle'] = topage($params['chattitle']);
$page['formsendmessagekey'] = $params['sendmessagekey'];
$page['availableStyles'] = $stylelist;
$page['stored'] = isset($_GET['stored']);

prepare_menu($operator);
setup_settings_tabs(0);
start_html_output();
require('../view/settings.php');
?>