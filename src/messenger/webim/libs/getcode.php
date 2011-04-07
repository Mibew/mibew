<?php
/*
 * This file is part of Mibew Messenger project.
 *
 * Copyright (c) 2005-2011 Mibew Messenger Community
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

function generate_button($title, $locale, $style, $group, $inner, $showhost, $forcesecure, $modsecurity)
{
	$link = get_app_location($showhost, $forcesecure) . "/client.php";
	if ($locale)
		$link = append_query($link, "locale=$locale");
	if ($style)
		$link = append_query($link, "style=$style");
	if ($group)
		$link = append_query($link, "group=$group");

	$modsecfix = $modsecurity ? ".replace('http://','').replace('https://','')" : "";
	$jslink = append_query("'" . $link, "url='+escape(document.location.href$modsecfix)+'&amp;referrer='+escape(document.referrer$modsecfix)");
	$temp = get_popup($link, "$jslink",
					  $inner, $title, "webim", "toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1");
	return "<!-- mibew button -->" . $temp . "<!-- / mibew button -->";
}

function get_style_list($stylesfolder)
{
	$stylelist = array("" => getlocal("page.preview.style_default"));
	if ($handle = opendir($stylesfolder)) {
		while (false !== ($file = readdir($handle))) {
			if (preg_match("/^\w+$/", $file) && is_dir("$stylesfolder/$file")) {
				$stylelist[$file] = $file;
			}
		}
		closedir($handle);
	}
	return $stylelist;
}

function verifyparam_groupid($paramid)
{
	global $settings, $errors;
	$groupid = "";
	if ($settings['enablegroups'] == '1') {
		$groupid = verifyparam($paramid, "/^\d{0,8}$/", "");
		if ($groupid) {
			$group = group_by_id($groupid);
			if (!$group) {
				$errors[] = getlocal("page.group.no_such");
				$groupid = "";
			}
		}
	}
	return $groupid;
}

function get_groups_list()
{
	global $settings;
	$result = array();
	if ($settings['enablegroups'] == '1') {
		$link = connect();
		$allgroups = get_all_groups($link);
		mysql_close($link);
		$result[] = array('groupid' => '', 'vclocalname' => getlocal("page.gen_button.default_group"));
		foreach ($allgroups as $g) {
			$result[] = $g;
		}
	}
	return $result;
}

function get_image_locales_map($localesdir)
{
	$imageLocales = array();
	$allLocales = get_available_locales();
	foreach ($allLocales as $curr) {
		$imagesDir = "$localesdir/$curr/button";
		if ($handle = @opendir($imagesDir)) {
			while (false !== ($file = readdir($handle))) {
				if (preg_match("/^(\w+)_on.gif$/", $file, $matches)
					&& is_file("$imagesDir/" . $matches[1] . "_off.gif")) {
					$image = $matches[1];
					if (!isset($imageLocales[$image])) {
						$imageLocales[$image] = array();
					}
					$imageLocales[$image][] = $curr;
				}
			}
			closedir($handle);
		}
	}
	return $imageLocales;
}

?>