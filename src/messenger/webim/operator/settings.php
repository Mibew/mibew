<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Web Messenger Community
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

$stylelist = array();
$stylesfolder = "../styles";
if($handle = opendir($stylesfolder)) {
	while (false !== ($file = readdir($handle))) {
		if (preg_match("/^\w+$/", $file) && is_dir("$stylesfolder/$file")) {
			$stylelist[] = $file;
		}
	}
	closedir($handle);
}

loadsettings();
$email = $settings['email'];
$title = $settings['title'];
$logo  = $settings['logo'];
$hosturl = $settings['hosturl'];
$enableban = $settings['enableban'];
$usernamepattern = $settings['usernamepattern'];
$usercanchangename = $settings['usercanchangename'];
$chatstyle = $settings['chatstyle'];

if (isset($_POST['email']) && isset($_POST['title']) && isset($_POST['logo'])) {
    $email = getparam('email');
    $title = getparam('title');
    $logo  = getparam('logo');
    $hosturl = getparam('hosturl');
    $enableban = verifyparam("enableban","/^on$/", "") == "on" ? "1" : "0";
    $usernamepattern = getparam('usernamepattern');
    $usercanchangename = verifyparam("usercanchangename", "/^on$/", "") == "on" ? "1" : "0";

	$chatstyle = verifyparam("chatstyle","/^\w+$/", $chatstyle);
	if(!in_array($chatstyle, $stylelist)) {
		$chatstyle = $stylelist[0];
	}

    if($email && !is_valid_email($email)) {
        $errors[] = getlocal("settings.wrong.email");
    }

    if (count($errors) == 0) {
    	$settings['email'] = $email;
    	$settings['title'] = $title;
    	$settings['logo'] = $logo;
    	$settings['hosturl'] = $hosturl;
    	$settings['enableban'] = $enableban;
    	$settings['usernamepattern'] = $usernamepattern;
    	$settings['usercanchangename'] = $usercanchangename;
    	$settings['chatstyle'] = $chatstyle;
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
$page['formenableban'] = $enableban == "1";
$page['formusernamepattern'] = topage($usernamepattern);
$page['formusercanchangename'] = $usercanchangename == "1";
$page['formchatstyle'] = $chatstyle;
$page['availableStyles'] = $stylelist;

start_html_output();
require('../view/settings.php');
?>