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
require_once('../libs/pagination.php');
require_once('../libs/operator.php');
require_once('../libs/expand.php');

$operator = check_login();

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

$preview = verifyparam("preview","/^\w+$/", "default");
if(!in_array($preview, $stylelist)) {
	$preview = $stylelist[0];
}

$show = verifyparam("show", "/^(chat|chatsimple|nochat|mail|mailsent|leavemessage|leavemessagesent|redirect|redirected|agentchat|agentrochat)$/", "");

if($show == 'chat' || $show == 'mail' || $show == 'leavemessage' || $show == 'leavemessagesent' || $show == 'chatsimple' || $show == 'nochat') {
	setup_chatview_for_user(array('threadid' => 0,'userName' => getstring("chat.default.username"), 'ltoken' => 123), "ajaxed");
	$page['mailLink'] = "$webimroot/operator/preview.php?preview=$preview&amp;show=mail";
	expand("../styles", "$preview", "$show.tpl");
	exit;
}
if($show == 'mailsent') {
	$page['email'] = "admin@yourdomain.com";
	expand("../styles", "$preview", "$show.tpl");
	exit;
}
if($show == 'redirect' || $show == 'redirected' || $show == 'agentchat' || $show == 'agentrochat' ) {
	setup_chatview_for_operator(
		array(
			'threadid' => 0,
			'userName' => getstring("chat.default.username"),
			'remote' => "1.2.3.4",
			'agentId' => 1,
			'userid' => 'visitor1',
			'locale' => $current_locale,
			'ltoken' => $show=='agentrochat' ? 124 : 123),
		array(
			'operatorid' => ($show=='agentrochat' ? 2 : 1),
			));
	if($show=='redirect') {
		$page['pagination_list'] = get_redirect_links( 0,$show=='agentrochat' ? 124 : 123);
	} elseif($show=='redirected') {
		$page['nextAgent'] = "Administrator";
	}
	$page['redirectLink'] = "$webimroot/operator/preview.php?preview=$preview&amp;show=redirect";
	expand("../styles", "$preview", "$show.tpl");
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
	array('label' => getlocal("page.preview.redirect"), 'id' => 'redirect', 'h' => 420, 'w' => 600),
	array('label' => getlocal("page.preview.redirected"), 'id' => 'redirected', 'h' => 420, 'w' => 600),
	array('label' => getlocal("page.preview.agentchat"), 'id' => 'agentchat', 'h' => 420, 'w' => 600),
	array('label' => getlocal("page.preview.agentrochat"), 'id' => 'agentrochat', 'h' => 420, 'w' => 600),
);

$template = verifyparam("template", "/^\w+$/", "chat");

$page['formpreview'] = $preview;
$page['formtemplate'] = $template;
$page['availablePreviews'] = $stylelist;
$page['availableTemplates'] = array(
	"chat", "chatsimple", "nochat",
	"leavemessage", "leavemessagesent",
	"mail", "mailsent",
	"redirect", "redirected",
	"agentchat", "agentrochat",
	"all");

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