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
require_once('../libs/operator.php');
require_once('../libs/chat.php');

$operator = check_login();

$page = array();

setlocale(LC_TIME, getstring("time.locale"));

function notification_info($id) {
	global $mysqlprefix;
	$link = connect();
	$notification = select_one_row(db_build_select(
		"id, locale, vckind, vcto, unix_timestamp(dtmcreated) as created, vcsubject, tmessage, refoperator", "${mysqlprefix}chatnotification",
		array("id = $id"), ""), $link);
	mysql_close($link);
	return $notification;
}


$notificationid = verifyparam( "id", "/^(\d{1,9})$/");
$page['notification'] = notification_info($notificationid);

prepare_menu($operator, false);
start_html_output();
require('../view/notification.php');
?>