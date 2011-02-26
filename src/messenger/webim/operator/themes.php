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
require_once('../libs/chat.php');
require_once('../libs/pagination.php');
require_once('../libs/operator.php');
require_once('../libs/groups.php');
require_once('../libs/expand.php');
require_once('../libs/settings.php');

$operator = check_login();

$stylelist = array();
$stylesfolder = "../styles";
if ($handle = opendir($stylesfolder)) {
	while (false !== ($file = readdir($handle))) {
		if (preg_match("/^\w+$/", $file) && is_dir("$stylesfolder/$file")) {
			$stylelist[] = $file;
		}
	}
	closedir($handle);
}

$preview = verifyparam("preview", "/^\w+$/", "default");
if (!in_array($preview, $stylelist)) {
	$preview = $stylelist[0];
}

$show = verifyparam("show", "/^(chat|chatsimple|nochat|mail|mailsent|survey|leavemessage|leavemessagesent|redirect|redirected|agentchat|agentrochat|error)$/", "");
$showerrors = verifyparam("showerr", "/^on$/", "") == "on";
$errors = array();
if ($showerrors || $show == 'error') {
	$errors[] = "Test error";
}

if ($show == 'chat' || $show == 'mail' || $show == 'leavemessage' || $show == 'leavemessagesent' || $show == 'chatsimple' || $show == 'nochat') {
	setup_chatview_for_user(array('threadid' => 0, 'userName' => getstring("chat.default.username"), 'ltoken' => 123), "ajaxed");
	$page['mailLink'] = "$webimroot/operator/themes.php?preview=$preview&amp;show=mail";
	$page['info'] = "";
	expand("../styles", "$preview", "$show.tpl");
	exit;
}
if ($show == 'survey') {
	loadsettings();
	setup_survey("Visitor", "", "", "", "http://google.com");
	setup_logo();
	expand("../styles", "$preview", "$show.tpl");
	exit;
}
if ($show == 'mailsent' || $show == 'error') {
	$page['email'] = "admin@yourdomain.com";
	setup_logo();
	expand("../styles", "$preview", "$show.tpl");
	exit;
}
if ($show == 'redirect' || $show == 'redirected' || $show == 'agentchat' || $show == 'agentrochat') {
	setup_chatview_for_operator(
		array(
			 'threadid' => 0,
			 'userName' => getstring("chat.default.username"),
			 'remote' => "1.2.3.4",
			 'agentId' => 1,
			 'userid' => 'visitor1',
			 'locale' => $current_locale,
			 'ltoken' => $show == 'agentrochat' ? 124 : 123),
		array(
			 'operatorid' => ($show == 'agentrochat' ? 2 : 1),
		));
	if ($show == 'redirect') {
		setup_redirect_links(0, $show == 'agentrochat' ? 124 : 123);
	} elseif ($show == 'redirected') {
		$page['message'] = getlocal2("chat.redirected.content", array("Administrator"));
	}
	$page['redirectLink'] = "$webimroot/operator/themes.php?preview=$preview&amp;show=redirect";
	expand("../styles", "$preview", "$show.tpl");
	exit;
}

$templateList = array(
	array('label' => getlocal("page.preview.userchat"), 'id' => 'chat', 'h' => 480, 'w' => 640),
	array('label' => getlocal("page.preview.chatsimple"), 'id' => 'chatsimple', 'h' => 480, 'w' => 640),
	array('label' => getlocal("page.preview.nochat"), 'id' => 'nochat', 'h' => 480, 'w' => 640),
	array('label' => getlocal("page.preview.survey"), 'id' => 'survey', 'h' => 480, 'w' => 640),
	array('label' => getlocal("page.preview.leavemessage"), 'id' => 'leavemessage', 'h' => 480, 'w' => 640),
	array('label' => getlocal("page.preview.leavemessagesent"), 'id' => 'leavemessagesent', 'h' => 480, 'w' => 640),
	array('label' => getlocal("page.preview.mail"), 'id' => 'mail', 'h' => 254, 'w' => 603),
	array('label' => getlocal("page.preview.mailsent"), 'id' => 'mailsent', 'h' => 254, 'w' => 603),
	array('label' => getlocal("page.preview.redirect"), 'id' => 'redirect', 'h' => 480, 'w' => 640),
	array('label' => getlocal("page.preview.redirected"), 'id' => 'redirected', 'h' => 480, 'w' => 640),
	array('label' => getlocal("page.preview.agentchat"), 'id' => 'agentchat', 'h' => 480, 'w' => 640),
	array('label' => getlocal("page.preview.agentrochat"), 'id' => 'agentrochat', 'h' => 480, 'w' => 640),
	array('label' => getlocal("page.preview.error"), 'id' => 'error', 'h' => 480, 'w' => 640),
);

$template = verifyparam("template", "/^\w+$/", "chat");

$page['formpreview'] = $preview;
$page['formtemplate'] = $template;
$page['canshowerrors'] = $template == 'leavemessage' || $template == 'mail' || $template == 'all';
$page['formshowerr'] = $showerrors;
$page['availablePreviews'] = $stylelist;
$page['availableTemplates'] = array(
	"chat", "chatsimple", "nochat",
	"survey", "leavemessage", "leavemessagesent",
	"mail", "mailsent",
	"redirect", "redirected",
	"agentchat", "agentrochat", "error",
	"all");

$page['showlink'] = "$webimroot/operator/themes.php?preview=$preview&amp;" . ($showerrors ? "showerr=on&amp;" : "") . "show=";

$page['previewList'] = array();
foreach ($templateList as $tpl) {
	if ($tpl['id'] == $template || $template == 'all') {
		$page['previewList'][] = $tpl;
	}
}

prepare_menu($operator);
start_html_output();
setup_settings_tabs(3);
require('../view/themes.php');
?>