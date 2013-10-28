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

function generate_button($title, $locale, $style, $invitationstyle, $group, $inner, $showhost, $forcesecure, $modsecurity, $operator_code)
{
	$app_location = get_app_location($showhost, $forcesecure);
	$link = $app_location . "/client.php";
	if ($locale)
		$link = append_query($link, "locale=$locale");
	if ($style)
		$link = append_query($link, "style=$style");
	if ($group)
		$link = append_query($link, "group=$group");

	$modsecfix = $modsecurity ? ".replace('http://','').replace('https://','')" : "";
	$jslink = append_query("'" . $link, "url='+escape(document.location.href$modsecfix)+'&amp;referrer='+escape(document.referrer$modsecfix)");
	$popup_options = "toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1";

	// Generate operator code field
	if ($operator_code) {
		$form_on_submit = "if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 " .
			"&amp;&amp; window.event.preventDefault) window.event.preventDefault();" .
			"this.newWindow = window.open({$jslink} + '&amp;operator_code=' + document.getElementById('mibewOperatorCodeField').value, 'mibew', '{$popup_options}');" .
			"this.newWindow.focus();this.newWindow.opener=window;return false;";
		$temp = '<form action="" onsubmit="' . $form_on_submit . '" id="mibewOperatorCodeForm">' .
			'<input type="text" id="mibewOperatorCodeField" />' .
			'</form>';
		return "<!-- mibew operator code field -->" . $temp . "<!-- / mibew operator code field -->";
	}

	// Generate button
	$temp = get_popup($link, "$jslink",
					$inner, $title, "mibew", $popup_options);
	if (Settings::get('enabletracking')) {
		$widget_data = array();

		// URL of file with additional CSS rules for invitation popup
		$widget_data['inviteStyle'] = $app_location . '/styles/invitations/' .
			($invitationstyle
				? $invitationstyle
				: (Settings::get('invitationstyle'))
			) .	'/invite.css';

		// Time between requests to the server in milliseconds
		$widget_data['requestTimeout'] = Settings::get('updatefrequency_tracking')
			* 1000;

		// URL for requests
		$widget_data['requestURL'] = $app_location . '/widget.php';

		// Locale for invitation
		$widget_data['locale'] = $locale;

		// Name of the cookie to track user. Use if third-party cookie blocked
		$widget_data['visitorCookieName'] = VISITOR_COOKIE_NAME;

		// Build additional button code
		$temp = preg_replace('/^(<a )/', '\1id="mibewAgentButton" ', $temp) .
			'<div id="mibewinvitation"></div>' .
			'<script type="text/javascript" src="' .
				$app_location .	'/js/compiled/widget.js' .
			'"></script>' .
			'<script type="text/javascript">' .
				'Mibew.Widget.init('.json_encode($widget_data).')' .
			'</script>';
	}
	return "<!-- mibew button -->" . $temp . "<!-- / mibew button -->";
}

function verifyparam_groupid($paramid)
{
	global $errors;
	$groupid = "";
	$groupid = verifyparam($paramid, "/^\d{0,8}$/", "");
	if ($groupid) {
		$group = group_by_id($groupid);
		if (!$group) {
			$errors[] = getlocal("page.group.no_such");
			$groupid = "";
		}
	}
	return $groupid;
}

function get_groups_list()
{
	$result = array();
	$allgroups = get_all_groups();
	$result[] = array('groupid' => '', 'vclocalname' => getlocal("page.gen_button.default_group"), 'level' => 0);
	foreach ($allgroups as $g) {
		$result[] = $g;
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