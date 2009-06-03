<?php
/*
 * This file is part of Mibew Messenger project.
 *
 * Copyright (c) 2005-2009 Mibew Messenger Community
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
require_once('../libs/pagination.php');

function load_message($key) {
	$link = connect();
	$result = select_one_row("select vcvalue from chatresponses where id = $key", $link);
	mysql_close($link);
	return $result ? $result['vcvalue'] : null;
}

function save_message($key,$message) {
	$link = connect();
	perform_query("update chatresponses set vcvalue = '".mysql_real_escape_string($message,$link)."' ".
				"where id = $key", $link);
	mysql_close($link);
}

function add_message($locale,$groupid,$message) {
	$link = connect();
	perform_query("insert into chatresponses (locale,groupid,vcvalue) values ('$locale',".
				($groupid ? "$groupid, " : "null, ").
				"'".mysql_real_escape_string($message,$link)."')", $link);
	mysql_close($link);
}

$operator = check_login();
loadsettings();

$stringid = verifyparam("key", "/^\d{0,9}$/", "");

$errors = array();
$page = array();

if($stringid) {
	$message = load_message($stringid);
	if(!$message) {
		$errors[] = getlocal("cannededit.no_such");
		$stringid = "";
	}
} else {
	$message = "";
	$page['locale'] = verifyparam("lang", "/^[\w-]{2,5}$/", "");
	$page['groupid'] = "";
	if($settings['enablegroups'] == '1') {
		$page['groupid'] = verifyparam( "group", "/^\d{0,8}$/");
	}
}

if(isset($_POST['message'])) {
	$message = getparam('message');
	if(!$message) {
		$errors[] = no_field("form.field.message");
	}

	if(count($errors) == 0) {
		if($stringid) {
			save_message($stringid, $message);
		} else {
			add_message($page['locale'], $page['groupid'], $message);
		}
		$page['saved'] = true;
		prepare_menu($operator, false);
		start_html_output();
		require('../view/cannededit.php');
		exit;
	}
}

$page['saved'] = false;
$page['key'] = $stringid;
$page['formmessage'] = topage($message);
prepare_menu($operator, false);
start_html_output();
require('../view/cannededit.php');
exit;
?>