<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2009 Web Messenger Community
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

function compare_localization_by_l1($a, $b) {
    if ($a == $b) {
        return 0;
    }
    return ($a['l1'] < $b['l1']) ? -1 : 1;
}

function compare_localization_by_id($a, $b) {
    if ($a == $b) {
        return 0;
    }
    return ($a['id'] < $b['id']) ? -1 : 1;
}

function load_idlist($name) {
	$result = array();
	$fp = @fopen(dirname(__FILE__)."/../locales/names/$name", "r");
	if($fp !== FALSE) {
		while (!feof($fp)) {
			$line = trim(fgets($fp, 4096));
			if($line && preg_match("/^[\w_\.]+$/", $line)) {
				$result[] = $line;
			}
		}
		fclose($fp);
	}
	return $result;
}

function save_message($locale,$key,$value) {
	global $webim_encoding;
	$result = "";
	$added = false;
	$current_encoding = $webim_encoding;
	$fp = fopen(dirname(__FILE__)."/../locales/$locale/properties", "r");
	while (!feof($fp)) {
		$line = fgets($fp, 4096);
		$keyval = split("=", $line, 2 );
		if( isset($keyval[1]) ) {
			if($keyval[0] == 'encoding') {
				$current_encoding = trim($keyval[1]);
			} else if(!$added && $keyval[0] == $key) {
				$line = "$key=".myiconv($webim_encoding, $current_encoding, str_replace("\r", "",str_replace("\n", "\\n",trim($value))))."\n";
				$added = true;
			}
		}
		$result .= $line;
	}
	fclose($fp);
	if(!$added) {
		$result .= "$key=".myiconv($webim_encoding, $current_encoding, str_replace("\r", "",str_replace("\n", "\\n",trim($value))))."\n";
	}
	$fp = @fopen(dirname(__FILE__)."/../locales/$locale/properties", "w");
	if($fp !== FALSE) {
		fwrite($fp, $result);
		fclose($fp);
	} else {
		die("cannot write /locales/$locale/properties, please check file permissions on your server");
	}
	$fp = @fopen(dirname(__FILE__)."/../locales/$locale/properties.log", "a");
	if($fp !== FALSE) {
		$extAddr = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
		          $_SERVER['HTTP_X_FORWARDED_FOR'] != $_SERVER['REMOTE_ADDR']) {
			$extAddr = $_SERVER['REMOTE_ADDR'].' ('.$_SERVER['HTTP_X_FORWARDED_FOR'].')';
		}
		$userbrowser = $_SERVER['HTTP_USER_AGENT'];
		$remoteHost = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : $extAddr;

		fwrite($fp,"# ".date(DATE_RFC822)." by $remoteHost using $userbrowser\n");
		fwrite($fp,"$key=".myiconv($webim_encoding, $current_encoding, str_replace("\r", "",str_replace("\n", "\\n",trim($value))))."\n");
		fclose($fp);
	}
}

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
			if (get_magic_quotes_gpc()) {
				$translation = stripslashes($translation);
			}
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
	$page['formoriginal'] = isset($lang1[$stringid]) ? htmlspecialchars($lang1[$stringid]) : "<b><unknown></b>";
	$page['formtranslation'] = htmlspecialchars($translation);
	start_html_output();
	require('../view/translate.php');
	exit;
}

$localesList = array();
$allLocales = get_available_locales();
foreach($allLocales as $loc) {
	$localesList[] = array("id" => $loc, "name" => getlocal_("localeid", $loc));
}

$show = verifyparam("show", "/^(all|s1|s2|s3)$/", "all");

$result = array();
$allkeys = array_keys($lang1);
if($show == 's1') {
	$allkeys = array_intersect($allkeys, load_idlist('level1'));
} else if($show == 's2') {
	$allkeys = array_intersect($allkeys, load_idlist('level2'));
} else if($show == 's3') {
	$allkeys = array_diff($allkeys, load_idlist('level1'), load_idlist('level2'));
}

foreach($allkeys as $key) {
	if($key != 'output_charset') {
		$tsource = htmlspecialchars($lang1[$key]);
		$value = isset($lang2[$key]) ? htmlspecialchars($lang2[$key]) : "<font color=\"#c13030\"><b>absent</b></font>";
		$result[] = array(
				'id' => $key,
				'l1' => $tsource,
				'l2' => $value );
	}
}

$order = verifyparam("sort", "/^(id|l1)$/", "id");
usort($result, "compare_localization_by_$order");
setup_pagination($result,100);

$page['formtarget'] = $target;
$page['formsource'] = $source;
$page['availableLocales'] = $localesList;
$page['availableOrders'] = array(
	array("id" => "id", "name" => getlocal("translate.sort.key")),
	array("id" => "l1", "name" => getlocal("translate.sort.lang")),
);
$page['formsort'] = $order;
$page['showOptions'] = array(
	array("id" => "all", "name" => getlocal("translate.show.all")),
	array("id" => "s1", "name" => getlocal("translate.show.forvisitor")),
	array("id" => "s2", "name" => getlocal("translate.show.foroperator")),
	array("id" => "s3", "name" => getlocal("translate.show.foradmin")),
);
$page['formshow'] = $show;
start_html_output();
require('../view/translatelist.php');
?>