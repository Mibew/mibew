<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2007 Internet Services Ltd.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require('../libs/common.php');
require('../libs/operator.php');

$operator = check_login();

// collect available images and locales
$imageLocales = array();
$imagesDir = '../images/webim';
if($handle = opendir($imagesDir)) {
	while (false !== ($file = readdir($handle))) {
		if (preg_match("/^(\w+)_([\w-]+)_on.gif$/", $file, $matches) 
				&& is_file("$imagesDir/".$matches[1]."_".$matches[2]."_off.gif")) {
			$image = $matches[1];
			if( !isset($imageLocales[$image]) ) {
				$imageLocales[$image] = array();
			}
			$imageLocales[$image][] = $matches[2];
		}
	}
	closedir($handle);
}

$image = verifyparam("image","/^\w+$/", "webim");
$image_locales = $imageLocales[$image];

$showhost = verifyparam("hostname","/^on$/", "") == "on";
$forcesecure = verifyparam("secure","/^on$/", "") == "on";

$lang = verifyparam("lang", "/^\w\w$/", "");
if( !$lang || !in_array($lang,$image_locales) )
	$lang = in_array($current_locale,$image_locales) ? $current_locale : $image_locales[0];

$file = "../images/webim/${image}_${lang}_on.gif";
$size = get_gifimage_size($file);

$message = get_image(get_app_location($showhost,$forcesecure)."/button.php?image=$image&lang=$lang",$size[0],$size[1]);

$page = array();
$page['operator'] = get_operator_name($operator);
$page['buttonCode'] = generate_button("",$lang,$message,$showhost,$forcesecure);
$page['availableImages'] = array_keys($imageLocales);
$page['availableLocales'] = $image_locales;

$page['formimage'] = $image;
$page['formlang'] = $lang;
$page['formhostname'] = $showhost;
$page['formsecure'] = $forcesecure;

start_html_output();
require('../view/gen_button.php');
?>