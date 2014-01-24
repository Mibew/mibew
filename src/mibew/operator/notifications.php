<?php
/*
 * Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once('../libs/common.php');
require_once('../libs/chat.php');
require_once('../libs/operator.php');
require_once('../libs/pagination.php');

$operator = check_login();
check_permissions($operator, $can_administrate, $can_viewnotifications);

$page = array();
$errors = array();

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
$link = connect();

$conditions = array();
if ($kind) {
	$conditions[] = "vckind = '" . mysql_real_escape_string($kind, $link) . "'";
}
if ($lang) {
	$conditions[] = "locale = '" . mysql_real_escape_string($lang, $link) . "'";
}

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