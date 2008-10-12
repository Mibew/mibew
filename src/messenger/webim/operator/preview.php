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

$show = verifyparam("show", "/^(chat|chatsimple|nochat|mail|mailsent|leavemessage|leavemessagesent)$/", "");

if($show == 'chat' || $show == 'mail' || $show == 'leavemessage' || $show == 'leavemessagesent' || $show == 'chatsimple' || $show == 'nochat') {
	setup_chatview_for_user(array('threadid' => 0,'userName' => getstring("chat.default.username"), 'ltoken' => 123), "ajaxed");
	$page['mailLink'] = "$webimroot/operator/preview.php?preview=$preview&amp;show=mail";
	expand("../design/$preview/$show.tpl");
	exit;
}
if($show == 'mailsent') {
	$page['email'] = "admin@yourdomain.com";
	expand("../design/$preview/$show.tpl");
	exit;
}

$templateList = array(
	array('label' => getlocal("page.preview.userchat"), 'id' => 'chat', 'h' => 420, 'w' => 600),
	array('label' => getlocal("page.preview.chatsimple"), 'id' => 'chatsimple', 'h' => 420, 'w' => 600),
	array('label' => getlocal("page.preview.nochat"), 'id' => 'nochat', 'h' => 420, 'w' => 600),
	array('label' => getlocal("page.preview.leavemessage"), 'id' => 'leavemessage', 'h' => 420, 'w' => 600),
	array('label' => getlocal("page.preview.leavemessagesent"), 'id' => 'leavemessagesent', 'h' => 420, 'w' => 600),
	array('label' => getlocal("page.preview.mail"), 'id' => 'mail', 'h' => 254, 'w' => 603),
	array('label' => getlocal("page.preview.mailsent"), 'id' => 'mailsent', 'h' => 254, 'w' => 603),
);

$template = verifyparam("template", "/^\w+$/", "chat");

$page['formpreview'] = $preview;
$page['formtemplate'] = $template;
$page['availablePreviews'] = $designlist;
$page['availableTemplates'] = array("chat", "chatsimple", "nochat", "leavemessage", "leavemessagesent", "mail", "mailsent", "all");
$page['operator'] = topage(get_operator_name($operator));
$page['showlink'] = "$webimroot/operator/preview.php?preview=$preview&amp;show=";

$page['previewList'] = array();
foreach($templateList as $tpl) {
	if($tpl['id'] == $template || $template == 'all') {
		 $page['previewList'][] = $tpl;
	}
}

start_html_output();
require('../view/preview.php');
?>