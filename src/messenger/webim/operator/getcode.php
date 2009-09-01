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

$image = verifyparam(isset($_GET['image']) ? "image" : "i", "/^\w+$/", "webim");
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
$modsecurity = verifyparam("modsecurity","/^on$/", "") == "on";

$lang = verifyparam("lang", "/^[\w-]{2,5}$/", "");
if( !$lang || !in_array($lang,$image_locales) )
	$lang = in_array($current_locale,$image_locales) ? $current_locale : $image_locales[0];

$file = "../locales/${lang}/button/${image}_on.gif";
$size = get_gifimage_size($file);

$imagehref = get_app_location($showhost,$forcesecure)."/button.php?i=$image&amp;lang=$lang";
if($groupid) {
	$imagehref .= "&amp;group=$groupid";
}
$message = get_image($imagehref,$size[0],$size[1]);

$page = array();
$page['buttonCode'] = generate_button("",$lang,$style,$groupid,$message,$showhost,$forcesecure,$modsecurity);
$page['availableImages'] = array_keys($imageLocales);
$page['availableLocales'] = $image_locales;
$page['availableStyles'] = $stylelist;

if($settings['enablegroups'] == '1') {
	$link = connect();
	$allgroups = get_all_groups($link);
	mysql_close($link);
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
$page['formmodsecurity'] = $modsecurity;

prepare_menu($operator);
start_html_output();
require('../view/gen_button.php');
?>