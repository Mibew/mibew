<?php
/*
 * Copyright 2005-2013 the original author or authors.
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

require_once('../libs/canned.php');
require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('../libs/pagination.php');

$operator = check_login();
csrfchecktoken();

$stringid = verifyparam("key", "/^\d{0,9}$/", "");

$errors = array();
$page = array();

if ($stringid) {
	$canned_message = load_canned_message($stringid);
	if (!$canned_message) {
		$errors[] = getlocal("cannededit.no_such");
		$stringid = "";
	}else{
		$title = $canned_message['vctitle'];
		$message = $canned_message['vcvalue'];
	}
} else {
	$message = '';
	$title = '';
	$page['locale'] = verifyparam("lang", "/^[\w-]{2,5}$/", "");
	$page['groupid'] = "";
	$page['groupid'] = verifyparam("group", "/^\d{0,8}$/");
}

if (isset($_POST['message']) && isset($_POST['title'])) {
	$title = getparam('title');
	if (!$title) {
		$errors[] = no_field("form.field.title");
	}

	$message = getparam('message');
	if (!$message) {
		$errors[] = no_field("form.field.message");
	}

	if (count($errors) == 0) {
		if ($stringid) {
			save_canned_message($stringid, $title, $message);
		} else {
			add_canned_message($page['locale'], $page['groupid'], $title, $message);
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
$page['formtitle'] = topage($title);
$page['formmessage'] = topage($message);
prepare_menu($operator, false);
start_html_output();
require('../view/cannededit.php');
exit;
?>