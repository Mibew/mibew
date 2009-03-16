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

$operator = check_login();

$imageLocales = array();
$allLocales = get_available_locales();
foreach($allLocales as $curr) {
	$imagesDir = "../locales/$curr/button";
	if($handle = opendir($imagesDir)) {
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

$showhost = verifyparam("hostname","/^on$/", "") == "on";
$forcesecure = verifyparam("secure","/^on$/", "") == "on";

$lang = verifyparam("lang", "/^[\w-]{2,5}$/", "");
if( !$lang || !in_array($lang,$image_locales) )
	$lang = in_array($current_locale,$image_locales) ? $current_locale : $image_locales[0];

$file = "../locales/${lang}/button/${image}_on.gif";
$size = get_gifimage_size($file);

$message = get_image(get_app_location($showhost,$forcesecure)."/button.php?image=$image&amp;lang=$lang",$size[0],$size[1]);

$page = array();
$page['buttonCode'] = generate_button("",$lang,$style,$message,$showhost,$forcesecure);
$page['availableImages'] = array_keys($imageLocales);
$page['availableLocales'] = $image_locales;
$page['availableStyles'] = $stylelist;

$page['formstyle'] = $style;
$page['formimage'] = $image;
$page['formlang'] = $lang;
$page['formhostname'] = $showhost;
$page['formsecure'] = $forcesecure;

prepare_menu($operator);
start_html_output();
require('../view/gen_button.php');
?>