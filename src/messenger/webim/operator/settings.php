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
require_once('../libs/settings.php');

$operator = check_login();

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
		'email', 'title', 'logo', 'hosturl', 'usernamepattern',
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
    $params['usernamepattern'] = getparam('usernamepattern');
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
        header("Location: $webimroot/operator/settings.php?stored");
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
$page['formusernamepattern'] = topage($params['usernamepattern']);
$page['formchatstyle'] = $params['chatstyle'];
$page['formchattitle'] = topage($params['chattitle']);
$page['availableStyles'] = $stylelist;
$page['stored'] = isset($_GET['stored']);

setup_settings_tabs(0);
start_html_output();
require('../view/settings.php');
?>