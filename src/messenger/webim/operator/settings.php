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

$options = array(
		'email', 'title', 'logo', 'hosturl', 'enableban', 'usernamepattern', 'usercanchangename',
		'chatstyle', 'chattitle', 'geolink', 'geolinkparams');

loadsettings();
$params = array();
foreach($options as $opt) {
	$params[$opt] = $settings[$opt];
}

if (isset($_POST['email']) && isset($_POST['title']) && isset($_POST['logo'])) {
    $params['email'] = getparam('email');
    $params['title'] = getparam('title');
    $params['logo']  = getparam('logo');
    $params['hosturl'] = getparam('hosturl');
    $params['enableban'] = verifyparam("enableban","/^on$/", "") == "on" ? "1" : "0";
    $params['usernamepattern'] = getparam('usernamepattern');
    $params['usercanchangename'] = verifyparam("usercanchangename", "/^on$/", "") == "on" ? "1" : "0";
    $params['chattitle'] = getparam('chattitle');
    $params['geolink'] = getparam('geolink');
	$params['geolinkparams'] = getparam('geolinkparams');

	$params['chatstyle'] = verifyparam("chatstyle","/^\w+$/", $params['chatstyle']);
	if(!in_array($params['chatstyle'], $stylelist)) {
		$params['chatstyle'] = $stylelist[0];
	}

    if($params['email'] && !is_valid_email($params['email'])) {
        $errors[] = getlocal("settings.wrong.email");
    }

    if($params['geolinkparams']) {
    	foreach(split(",", $params['geolinkparams']) as $oneparam) {
    		if(!preg_match("/^\s*(toolbar|scrollbars|location|status|menubar|width|height|resizable)=\d{1,4}$/", $oneparam)) {
    			$errors[] = "Wrong link parameter: \"$oneparam\", should be one of 'toolbar, scrollbars, location, status, menubar, width, height or resizable'";
    		}
    	}
    }

    if (count($errors) == 0) {
		foreach($options as $opt) {
			$settings[$opt] = $params[$opt];
		}
    	update_settings();
        header("Location: $webimroot/operator/index.php");
        exit;
    }
}

$page['operator']  = topage(get_operator_name($operator));
$page['formemail'] = topage($params['email']);
$page['formtitle'] = topage($params['title']);
$page['formlogo']  = topage($params['logo']);
$page['formhosturl']  = topage($params['hosturl']);
$page['formgeolink'] = topage($params['geolink']);
$page['formgeolinkparams'] = topage($params['geolinkparams']);
$page['formenableban'] = $params['enableban'] == "1";
$page['formusernamepattern'] = topage($params['usernamepattern']);
$page['formusercanchangename'] = $params['usercanchangename'] == "1";
$page['formchatstyle'] = $params['chatstyle'];
$page['formchattitle'] = topage($params['chattitle']);
$page['availableStyles'] = $stylelist;

start_html_output();
require('../view/settings.php');
?>