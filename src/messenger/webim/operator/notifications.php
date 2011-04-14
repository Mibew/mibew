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
require_once('../libs/operator.php');
require_once('../libs/pagination.php');

$operator = check_login();

$page = array();
$errors = array();

if (!is_capable($can_administrate, $operator)) {
	die("Permission denied.");
}

setlocale(LC_TIME, getstring("time.locale"));

# locales

$all_locales = get_available_locales();
$locales_with_label = array(array('id' => '', 'name' => getlocal("notifications.locale.all")));
foreach ($all_locales as $id) {
	$locales_with_label[] = array('id' => $id, 'name' => getlocal_($id, "names"));
}
$page['locales'] = $locales_with_label;

$lang = verifyparam("lang", "/^([\w-]{2,5})?$/", "");
if ($lang && !in_array($lang, $all_locales)) {
	$lang = "";
}

# kind

$kind = verifyparam("kind", "/^(mail|xmpp)?$/", "");
$page['allkinds'] = array('', 'mail', 'xmpp');

# fetch

$conditions = array();
if ($kind) {
	$conditions[] = "vckind = '$kind'";
}
if ($lang) {
	$conditions[] = "locale = '$lang'";
}

$link = connect();
select_with_pagintation(
	"id, locale, vckind, vcto, unix_timestamp(dtmcreated) as created, vcsubject, tmessage, refoperator", "${mysqlprefix}chatnotification",
	$conditions,
	"order by created desc", "", $link);

mysql_close($link);

$page['formlang'] = $lang;
$page['formkind'] = $kind;

prepare_menu($operator);
start_html_output();

require('../view/notifications.php');
exit;
?>
