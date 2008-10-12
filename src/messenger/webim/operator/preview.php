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
require_once('../libs/chat.php');
require_once('../libs/operator.php');
require_once('../libs/expand.php');

$operator = check_login();

$designlist = array();
$designfolder = "../design";
if($handle = opendir($designfolder)) {
	while (false !== ($file = readdir($handle))) {
		if (preg_match("/^\w+$/", $file) && is_dir("$designfolder/$file")) {
			$designlist[] = $file;
		}
	}
	closedir($handle);
}

$preview = verifyparam("preview","/^\w+$/", "default");
if(!in_array($preview, $designlist)) {
	$preview = $designlist[0];
}

$show = verifyparam("show", "/^(chat)$/", "");

if($show == 'chat') {
	setup_chatview_for_user(array('threadid' => 0,'userName' => getstring("chat.default.username"), 'ltoken' => 123), "ajaxed");
	expand("../design/$preview/chat.tpl");
	exit;
}

$page['formpreview'] = $preview;
$page['availablePreviews'] = $designlist;
$page['operator'] = topage(get_operator_name($operator));
$page['showlink'] = "$webimroot/operator/preview.php?preview=$preview&amp;show=";

start_html_output();
require('../view/preview.php');
?>