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
require_once('../libs/operator.php');
require_once('../libs/chat.php');

$operator = check_login();
check_permissions($operator, $can_administrate, $can_viewnotifications);

$page = array();

setlocale(LC_TIME, getstring("time.locale"));

function notification_info($id)
{
	global $mysqlprefix;
	$link = connect();
	$notification = select_one_row(db_build_select(
									   "id, locale, vckind, vcto, unix_timestamp(dtmcreated) as created, vcsubject, tmessage, refoperator", "${mysqlprefix}chatnotification",
									   array("id = " . intval($id)), ""), $link);
	mysql_close($link);
	return $notification;
}


$notificationid = verifyparam("id", "/^(\d{1,10})$/");
$page['notification'] = notification_info($notificationid);

prepare_menu($operator, false);
start_html_output();
require('../view/notification.php');
?>