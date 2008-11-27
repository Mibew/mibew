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

$operator = check_login();

$source = verifyparam("source", "/^[\w-]{2,5}$/", $default_locale);
$target = verifyparam("target", "/^[\w-]{2,5}$/", $home_locale);
$stringid = verifyparam("key", "/^[_\.\w]+$/", "");

if(!isset($messages[$source])) {
	load_messages($source);
}
$lang1 = $messages[$source];
if(!isset($messages[$target])) {
	load_messages($target);
}
$lang2 = $messages[$target];

$errors = array();
$page = array(
	'operator' => topage(get_operator_name($operator)),
	'lang1' => $source,
	'lang2' => $target,
	'title1' => isset($lang1["localeid"]) ? $lang1["localeid"] : $source,
	'title2' => isset($lang2["localeid"]) ? $lang2["localeid"] : $target
);

if($stringid) {
	$translation = isset($lang2[$stringid]) ? $lang2[$stringid] : "";
	if(isset($_POST['translation'])) {

		$translation = getparam('translation');

		if(!$translation) {
			$errors[] = no_field("form.field.translation");
		}

		if(count($errors) == 0) {
			save_message($target, $stringid, $translation);

			$page['saved'] = true;
			start_html_output();
			require('../view/translate.php');
			exit;
		}
	}

	$page['saved'] = false;
	$page['key'] = $stringid;
	$page['target'] = $target;
	$page['formoriginal'] = isset($lang1[$stringid]) ? $lang1[$stringid] : "<b><unknown></b>";
	$page['formtranslation'] = $translation;
	start_html_output();
	require('../view/translate.php');
	exit;
}

$localesList = array();
foreach($available_locales as $loc) {
	$localesList[] = array("id" => $loc, "name" => getlocal_("localeid", $loc));
}

$result = array();
$allkeys = array_keys($lang1);
foreach($allkeys as $key) {
	$result[] = array('id' => $key, 'l1' => $lang1[$key], 'l2' => (isset($lang2[$key]) ? $lang2[$key] : "<font color=\"#c13030\"><b>absent</b></font>") );
}
setup_pagination($result,100);

$page['formtarget'] = $target;
$page['formsource'] = $source;
$page['availableLocales'] = $localesList;
start_html_output();
require('../view/translatelist.php');
?>