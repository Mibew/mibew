<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
 * 
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('../libs/settings.php');

$operator = check_login();

$page = array('agentId' => '');
$errors = array();

$options = array(
	'enableban', 'usercanchangename', 'enablegroups', 'enablestatistics',
	'enablessl', 'forcessl',
	'enablepresurvey', 'surveyaskmail', 'surveyaskgroup', 'surveyaskmessage',
	'enablepopupnotification', 'showonlineoperators',
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
		header("Location: $webimroot/operator/features.php?stored");
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
