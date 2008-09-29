<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Internet Services Ltd.
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

$operator = check_login();

function update_settings() {
	global $settings, $settings_in_db;
	$link = connect();
	foreach ($settings as $key => $value) {
		if(!isset($settings_in_db[$key])) {
			perform_query("insert into chatconfig (vckey) values ('$key')",$link);
		}
        $query = sprintf("update chatconfig set vcvalue='%s' where vckey='$key'", mysql_real_escape_string($value));
		perform_query($query,$link);
	}

	mysql_close($link);
}

$page = array('agentId' => '');
$errors = array();

loadsettings();
$email = $settings['email'];
$title = $settings['title'];
$logo  = $settings['logo'];
$hosturl = $settings['hosturl'];

if (isset($_POST['email']) && isset($_POST['title']) && isset($_POST['logo'])) {
    $email = getparam('email');
    $title = getparam('title');
    $logo  = getparam('logo');
    $hosturl = getparam('hosturl');

    if($email && !is_valid_email($email)) {
        $errors[] = getlocal("settings.wrong.email");
    }

    if (count($errors) == 0) {
    	$settings['email'] = $email;
    	$settings['title'] = $title;
    	$settings['logo'] = $logo;
    	$settings['hosturl'] = $hosturl;
        update_settings();
        header("Location: $webimroot/operator/index.php");
        exit;
    }
}

$page['operator']  = topage(get_operator_name($operator));
$page['formemail'] = topage($email);
$page['formtitle'] = topage($title);
$page['formlogo']  = topage($logo);
$page['formhosturl']  = topage($hosturl);

start_html_output();
require('../view/settings.php');
?>