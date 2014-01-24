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

require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('../libs/settings.php');
require_once('../libs/groups.php');
require_once('../libs/pagination.php');

$operator = check_login();
csrfchecktoken();
loadsettings();

$errors = array();
$page = array();

function load_canned_messages($locale, $groupid)
{
	global $mysqlprefix;
	$link = connect();
	$query = "select id, vcvalue from ${mysqlprefix}chatresponses " .
			 "where locale = '" . mysql_real_escape_string($locale, $link) . "' AND (" .
			 ($groupid
					 ? "groupid = " . intval($groupid)
					 : "groupid is NULL OR groupid = 0") .
			 ") order by vcvalue";

	$result = select_multi_assoc($query, $link);
	if (!$groupid && count($result) == 0) {
		foreach (explode("\n", getstring_('chat.predefined_answers', $locale)) as $answer) {
			$result[] = array('id' => '', 'vcvalue' => $answer);
		}
		if (count($result) > 0) {
			$updatequery = "insert into ${mysqlprefix}chatresponses (vcvalue,locale,groupid) values ";
			for ($i = 0; $i < count($result); $i++) {
				if ($i > 0) {
					$updatequery .= ", ";
				}
				$updatequery .= "('" . mysql_real_escape_string($result[$i]['vcvalue'], $link) . "','". mysql_real_escape_string($locale, $link) . "', NULL)";
			}
			perform_query($updatequery, $link);
			$result = select_multi_assoc($query, $link);
		}
	}
	mysql_close($link);
	return $result;
}

# locales

$all_locales = get_available_locales();
$locales_with_label = array();
foreach ($all_locales as $id) {
	$locales_with_label[] = array('id' => $id, 'name' => getlocal_($id, "names"));
}
$page['locales'] = $locales_with_label;

$lang = verifyparam("lang", "/^[\w-]{2,5}$/", "");
if (!$lang || !in_array($lang, $all_locales)) {
	$lang = in_array($current_locale, $all_locales) ? $current_locale : $all_locales[0];
}

# groups

$groupid = "";
if ($settings['enablegroups'] == '1') {
	$groupid = verifyparam("group", "/^\d{0,10}$/", "");
	if ($groupid) {
		$group = group_by_id($groupid);
		if (!$group) {
			$errors[] = getlocal("page.group.no_such");
			$groupid = "";
		}
	}

	$link = connect();
	$allgroups = get_all_groups($link);
	mysql_close($link);
	$page['groups'] = array();
	$page['groups'][] = array('groupid' => '', 'vclocalname' => getlocal("page.gen_button.default_group"));
	foreach ($allgroups as $g) {
		$page['groups'][] = $g;
	}
}

# delete

if (isset($_GET['act']) && $_GET['act'] == 'delete') {
	$key = isset($_GET['key']) ? $_GET['key'] : "";

	if (!preg_match("/^\d+$/", $key)) {
		$errors[] = "Wrong key";
	}

	if (count($errors) == 0) {
		$link = connect();
		perform_query("delete from ${mysqlprefix}chatresponses where id = " . intval($key), $link);
		mysql_close($link);
		header("Location: $mibewroot/operator/canned.php?lang=" . urlencode($lang) . "&group=" . intval($groupid));
		exit;
	}
}

# get messages

$messages = load_canned_messages($lang, $groupid);
setup_pagination($messages);

# form values

$page['formlang'] = $lang;
$page['formgroup'] = $groupid;

prepare_menu($operator);
start_html_output();
require('../view/canned.php');
?>