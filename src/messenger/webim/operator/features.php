<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2009 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
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

$options = array('enableban', 'usercanchangename', 'enablessl', 'enabledepartments');

loadsettings();
$params = array();
foreach($options as $opt) {
	$params[$opt] = $settings[$opt];
}

if (isset($_POST['sent'])) {
	foreach($options as $opt) {
    	$settings[$opt] = verifyparam($opt,"/^on$/", "") == "on" ? "1" : "0";
	}
    update_settings();
    header("Location: $webimroot/operator/features.php?stored");
    exit;
}

$page['stored'] = isset($_GET['stored']);
foreach($options as $opt) {
   	$page["form$opt"] = $params[$opt] == "1";
}

prepare_menu($operator);
setup_settings_tabs(1);
start_html_output();
require('../view/features.php');
?>