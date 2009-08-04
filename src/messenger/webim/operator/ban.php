<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
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
$page = array('banId' => '');
$page['saved'] = false;
$page['thread'] = '';
$page['threadid'] = '';
$errors = array();

if( isset($_POST['address']) ) {
	$banId = verifyparam( "banId", "/^(\d{1,9})?$/", "");
	$address = getparam("address");
	$days = getparam("days");
	$comment = getparam('comment');
	$threadid = isset($_POST['threadid']) ? getparam('threadid') : "";

	if( !$address ) {
		$errors[] = no_field("form.field.address");
	}

	if( !preg_match( "/^\d+$/", $days )) {
		$errors[] = wrong_field("form.field.ban_days");
	}

	if( !$comment ) {
		$errors[] = no_field("form.field.ban_comment");
	}

	$link = connect();
	$existing_ban = ban_for_addr_($address,$link);
	mysql_close($link);

	if( (!$banId && $existing_ban) ||
		( $banId && $existing_ban && $banId != $existing_ban['banid']) ) {
		$errors[] = getlocal2("ban.error.duplicate",array($address,$existing_ban['banid']));
	}

	if( count($errors) == 0 ) {
		$link = connect();
		$utime = time() + $days * 24*60*60;
		if (!$banId) {
			$query = sprintf(
				"insert into chatban (dtmcreated,dtmtill,address,comment) values (CURRENT_TIMESTAMP,%s,'%s','%s')",
				"FROM_UNIXTIME($utime)",
				mysql_real_escape_string($address,$link),
				mysql_real_escape_string($comment,$link));
			perform_query($query,$link);
		} else {
			$query = sprintf(
				"update chatban set dtmtill = %s,address = '%s',comment = '%s' where banid = $banId",
				"FROM_UNIXTIME($utime)",
				mysql_real_escape_string($address,$link),
				mysql_real_escape_string($comment,$link));
			perform_query($query,$link);
					}
		mysql_close($link);

		if(!$threadid) {
			header("Location: $webimroot/operator/blocked.php");
			exit;
		} else {
			$page['saved'] = true;
			$page['address'] = $address;
		}
	} else {
		$page['banId'] = topage($banId);
		$page['formaddress'] = topage($address);
		$page['formdays'] = topage($days);
		$page['formcomment'] = topage($comment);
		$page['threadid'] = $threadid;
	}
} else if(isset($_GET['id'])) {
	$banId = verifyparam( 'id', "/^\d{1,9}$/");
	$link = connect();
	$ban = select_one_row("select banid,(unix_timestamp(dtmtill)-unix_timestamp(CURRENT_TIMESTAMP)) as days,address,comment from chatban where banid = $banId", $link);
	mysql_close($link);

	if( $ban ) {
		$page['banId'] = topage($ban['banid']);
		$page['formaddress'] = topage($ban['address']);
		$page['formdays'] = topage(round($ban['days']/86400));
		$page['formcomment'] = topage($ban['comment']);
	} else {
		$errors[] = "Wrong id";
	}
} else if(isset($_GET['thread'])) {
	$threadid = verifyparam( 'thread', "/^\d{1,9}$/");
	$thread = thread_by_id($threadid);
	if( $thread ) {
		$page['thread'] = topage($thread['userName']);
		$page['threadid'] = $threadid;
		$page['formaddress'] = topage($thread['remote']);
		$page['formdays'] = 15;
	}
}

prepare_menu($operator, false);
start_html_output();
require('../view/ban.php');
exit;
?>