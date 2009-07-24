<?php
/*
 * This file is part of Mibew Messenger project.
 *
 * Copyright (c) 2005-2009 Mibew Messenger Community
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

$options = array(
		'online_timeout', 'updatefrequency_operator', 'updatefrequency_chat', 'updatefrequency_oldchat');

loadsettings();
$params = array();
foreach($options as $opt) {
	$params[$opt] = $settings[$opt];
}

if (isset($_POST['onlinetimeout'])) {
    $params['online_timeout'] = getparam('onlinetimeout');
    if(!is_numeric($params['online_timeout'])) {
    	$errors[] = wrong_field("settings.onlinetimeout");
    }
    
    $params['updatefrequency_operator'] = getparam('frequencyoperator');
    if(!is_numeric($params['updatefrequency_operator'])) {
    	$errors[] = wrong_field("settings.frequencyoperator");
    }
    
    $params['updatefrequency_chat'] = getparam('frequencychat');
    if(!is_numeric($params['updatefrequency_chat'])) {
    	$errors[] = wrong_field("settings.frequencychat");
    }
    
    $params['updatefrequency_oldchat'] = getparam('frequencyoldchat');
    if(!is_numeric($params['updatefrequency_oldchat'])) {
    	$errors[] = wrong_field("settings.frequencyoldchat");
    }
    
    if (count($errors) == 0) {
		foreach($options as $opt) {
			$settings[$opt] = $params[$opt];
		}
    	update_settings();
        header("Location: $webimroot/operator/performance.php?stored");
        exit;
    }
}

$page['formonlinetimeout'] = $params['online_timeout'];
$page['formfrequencyoperator'] = $params['updatefrequency_operator'];
$page['formfrequencychat'] = $params['updatefrequency_chat'];
$page['formfrequencyoldchat'] = $params['updatefrequency_oldchat'];
$page['stored'] = isset($_GET['stored']);

prepare_menu($operator);
setup_settings_tabs(2);
start_html_output();
require('../view/performance.php');
?>