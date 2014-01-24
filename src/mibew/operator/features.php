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

$options = array(
	'enableban', 'usercanchangename', 'enablegroups', 'enablestatistics', 'enablejabber',
	'enablessl', 'forcessl',
	'enablepresurvey', 'surveyaskmail', 'surveyaskgroup', 'surveyaskmessage',
	'surveyaskcaptcha', 'enablepopupnotification', 'showonlineoperators',
	'enablecaptcha');

loadsettings();
if ($settings['featuresversion'] != $featuresversion) {
	$settings['featuresversion'] = $featuresversion;
	update_settings();
}
$params = array();
foreach ($options as $opt) {
	$params[$opt] = $settings[$opt];
}

if (isset($_POST['sent'])) {
	if (is_capable($can_administrate, $operator)) {
		foreach ($options as $opt) {
			$settings[$opt] = verifyparam($opt, "/^on$/", "") == "on" ? "1" : "0";
		}
		update_settings();
		header("Location: $mibewroot/operator/features.php?stored");
		exit;
	} else {
		$errors[] = "Not an administrator";
	}
}

$page['canmodify'] = is_capable($can_administrate, $operator);
$page['stored'] = isset($_GET['stored']);
foreach ($options as $opt) {
	$page["form$opt"] = $params[$opt] == "1";
}

prepare_menu($operator);
setup_settings_tabs(1);
start_html_output();
require('../view/features.php');
?>