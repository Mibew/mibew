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
require_once('../libs/operator.php');
require_once('../libs/pagination.php');

$source = "en";
$target = verifyparam("target", "/^[\w-]{2,5}$/", "en");

$operator = check_login();

$page = array(
	'operator' => topage(get_operator_name($operator)),
	'lang1' => $source,
	'lang2' => $target
);

if(!isset($messages[$source])) {
	load_messages($source);
}
if(!isset($messages[$target])) {
	load_messages($target);
}
$lang1 = $messages[$source];
$lang2 = $messages[$target];

$page["title1"] = isset($lang1["localeid"]) ? $lang1["localeid"] : $source;
$page["title2"] = isset($lang2["localeid"]) ? $lang2["localeid"] : $target;

$result = array();
$allkeys = array_keys($lang1);
foreach($allkeys as $key) {
	$result[] = array('id' => $key, 'l1' => $lang1[$key], 'l2' => (isset($lang2[$key]) ? $lang2[$key] : "<font color=\"#c13030\"><b>absent</b></font>") );
}

setup_pagination($result);

start_html_output();
require('../view/translate.php');
?>