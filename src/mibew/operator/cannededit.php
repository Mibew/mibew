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
require_once('../libs/pagination.php');

function load_message($key)
{
	global $mysqlprefix;
	$link = connect();
	$result = select_one_row("select vcvalue from ${mysqlprefix}chatresponses where id = " . intval($key), $link);
	mysql_close($link);
	return $result ? $result['vcvalue'] : null;
}

function save_message($key, $message)
{
	global $mysqlprefix;
	$link = connect();
	perform_query("update ${mysqlprefix}chatresponses set vcvalue = '" . mysql_real_escape_string($message, $link) . "' " .
				  "where id = " . intval($key), $link);
	mysql_close($link);
}

function add_message($locale, $groupid, $message)
{
	global $mysqlprefix;
	$link = connect();
	perform_query("insert into ${mysqlprefix}chatresponses (locale,groupid,vcvalue) values ('" . mysql_real_escape_string($locale, $link) . "'," .
				  ($groupid ? intval($groupid) . ", " : "null, ") .
				  "'" . mysql_real_escape_string($message, $link) . "')", $link);
	mysql_close($link);
}

$operator = check_login();
csrfchecktoken();
loadsettings();

$stringid = verifyparam("key", "/^\d{0,10}$/", "");

$errors = array();
$page = array();

if ($stringid) {
	$message = load_message($stringid);
	if (!$message) {
		$errors[] = getlocal("cannededit.no_such");
		$stringid = "";
	}
} else {
	$message = "";
	$page['locale'] = verifyparam("lang", "/^[\w-]{2,5}$/", "");
	$page['groupid'] = "";
	if ($settings['enablegroups'] == '1') {
		$page['groupid'] = verifyparam("group", "/^\d{0,10}$/");
	}
}

if (isset($_POST['message'])) {
	$message = getparam('message');
	if (!$message) {
		$errors[] = no_field("form.field.message");
	}

	if (count($errors) == 0) {
		if ($stringid) {
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