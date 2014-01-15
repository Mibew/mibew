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

// Import namespaces and classes of the core
use Mibew\Database;
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(MIBEW_FS_ROOT.'/libs/canned.php');
require_once(MIBEW_FS_ROOT.'/libs/operator.php');
require_once(MIBEW_FS_ROOT.'/libs/settings.php');
require_once(MIBEW_FS_ROOT.'/libs/groups.php');
require_once(MIBEW_FS_ROOT.'/libs/pagination.php');

$operator = check_login();
force_password($operator);
csrfchecktoken();

$errors = array();
$page = array();

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
$groupid = verifyparam("group", "/^\d{0,8}$/", "");
if ($groupid) {
	$group = group_by_id($groupid);
	if (!$group) {
		$errors[] = getlocal("page.group.no_such");
		$groupid = "";
	}
}

$allgroups = in_isolation($operator)?get_all_groups_for_operator($operator):get_all_groups();
$page['groups'] = array();
$page['groups'][] = array(
	'groupid' => '',
	'vclocalname' => getlocal("page.gen_button.default_group"),
	'level' => 0
);
foreach ($allgroups as $g) {
	$page['groups'][] = $g;
}

# delete

if (isset($_GET['act']) && $_GET['act'] == 'delete') {
	$key = isset($_GET['key']) ? $_GET['key'] : "";

	if (!preg_match("/^\d+$/", $key)) {
		$errors[] = "Wrong key";
	}

	if (count($errors) == 0) {
		$db = Database::getInstance();
		$db->query("delete from {chatresponses} where id = ?", array($key));
		header("Location: " . MIBEW_WEB_ROOT . "/operator/canned.php?lang=" . urlencode($lang) . "&group=" . intval($groupid));
		exit;
	}
}

# get messages

$messages = load_canned_messages($lang, $groupid);
setup_pagination($messages);

# form values

$page['formlang'] = $lang;
$page['formgroup'] = $groupid;
$page['title'] = getlocal("canned.title");
$page['menuid'] = "canned";

prepare_menu($operator);

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('canned');

?>