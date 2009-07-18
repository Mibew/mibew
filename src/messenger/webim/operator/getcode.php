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
require_once('../libs/groups.php');

$operator = check_login();
loadsettings();

$imageLocales = array();
$allLocales = get_available_locales();
foreach($allLocales as $curr) {
	$imagesDir = "../locales/$curr/button";
	if($handle = @opendir($imagesDir)) {
		while (false !== ($file = readdir($handle))) {
			if (preg_match("/^(\w+)_on.gif$/", $file, $matches)
					&& is_file("$imagesDir/".$matches[1]."_off.gif")) {
				$image = $matches[1];
				if( !isset($imageLocales[$image]) ) {
					$imageLocales[$image] = array();
				}
				$imageLocales[$image][] = $curr;
			}
		}
		closedir($handle);
	}
}

$image = verifyparam("image","/^\w+$/", "webim");
$image_locales = $imageLocales[$image];

$stylelist = array("" => getlocal("page.preview.style_default"));
$stylesfolder = "../styles";
if($handle = opendir($stylesfolder)) {
	while (false !== ($file = readdir($handle))) {
		if (preg_match("/^\w+$/", $file) && is_dir("$stylesfolder/$file")) {
			$stylelist[$file] = $file;
		}
	}
	closedir($handle);
}

$style = verifyparam("style","/^\w*$/", "");
if($style && !in_array($style, $stylelist)) {
	$style = "";
}

$groupid = "";
if($settings['enablegroups'] == '1') {
	$groupid = verifyparam( "group", "/^\d{0,8}$/", "");
	if($groupid) {
		$group = group_by_id($groupid);
		if(!$group) {
			$errors[] = getlocal("page.group.no_such");
			$groupid = "";
		}
	}
}

$showhost = verifyparam("hostname","/^on$/", "") == "on";
$forcesecure = verifyparam("secure","/^on$/", "") == "on";

$lang = verifyparam("lang", "/^[\w-]{2,5}$/", "");
if( !$lang || !in_array($lang,$image_locales) )
	$lang = in_array($current_locale,$image_locales) ? $current_locale : $image_locales[0];

$file = "../locales/${lang}/button/${image}_on.gif";
$size = get_gifimage_size($file);

$imagehref = get_app_location($showhost,$forcesecure)."/button.php?image=$image&amp;lang=$lang";
if($groupid) {
	$imagehref .= "&amp;group=$groupid";
}
$message = get_image($imagehref,$size[0],$size[1]);

$page = array();
$page['buttonCode'] = generate_button("",$lang,$style,$groupid,$message,$showhost,$forcesecure);
$page['availableImages'] = array_keys($imageLocales);
$page['availableLocales'] = $image_locales;
$page['availableStyles'] = $stylelist;

if($settings['enablegroups'] == '1') {
	$allgroups = get_groups(false);
	$page['groups'] = array();
	$page['groups'][] = array('groupid' => '', 'vclocalname' => getlocal("page.gen_button.default_group"));
	foreach($allgroups as $g) {
		$page['groups'][] = $g;
	}
}  

$page['formgroup'] = $groupid;
$page['formstyle'] = $style;
$page['formimage'] = $image;
$page['formlang'] = $lang;
$page['formhostname'] = $showhost;
$page['formsecure'] = $forcesecure;

prepare_menu($operator);
start_html_output();
require('../view/gen_button.php');
?>