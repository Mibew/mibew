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

function setup_getcode_tabs($active)
{
	global $page, $mibewroot;
	$page['tabselected'] = $active;
	$page['tabs'] = array(
		array('title' => getlocal("page_getcode.tab.image"), 'link' => "$mibewroot/operator/getcode.php"),
		array('title' => getlocal("page_getcode.tab.text"), 'link' => "$mibewroot/operator/gettextcode.php"),
	);
}

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
	$jslink = safe_htmlspecialchars(append_query("'" . $link, "url='+escape(document.location.href$modsecfix)+'&referrer='+escape(document.referrer$modsecfix)"));
	$temp = get_popup(safe_htmlspecialchars($link), "$jslink",
					  $inner, safe_htmlspecialchars($title), "mibew", "toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1");
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
		$groupid = verifyparam($paramid, "/^\d{0,10}$/", "");
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