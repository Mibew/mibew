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

// Import namespaces and classes of the core
use Mibew\Database;
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');

$operator = check_login();
force_password($operator);
csrf_check_token();

$page = array(
    'errors' => array(),
);

# locales

$all_locales = get_available_locales();
$locales_with_label = array();
foreach ($all_locales as $id) {
    $locales_with_label[] = array('id' => $id, 'name' => getlocal_($id, "names"));
}
$page['locales'] = $locales_with_label;

$lang = verify_param("lang", "/^[\w-]{2,5}$/", "");
if (!$lang || !in_array($lang, $all_locales)) {
    $lang = in_array(CURRENT_LOCALE, $all_locales) ? CURRENT_LOCALE : $all_locales[0];
}

# groups

$group_id = verify_param("group", "/^\d{0,8}$/", "");
if ($group_id) {
    $group = group_by_id($group_id);
    if (!$group) {
        $page['errors'][] = getlocal("page.group.no_such");
        $group_id = "";
    }
}

$all_groups = in_isolation($operator) ? get_all_groups_for_operator($operator) : get_all_groups();
$page['groups'] = array();
$page['groups'][] = array(
    'groupid' => '',
    'vclocalname' => getlocal("page.gen_button.default_group"),
    'level' => 0,
);
foreach ($all_groups as $g) {
    $page['groups'][] = $g;
}

# delete

if (isset($_GET['act']) && $_GET['act'] == 'delete') {
    $key = isset($_GET['key']) ? $_GET['key'] : "";

    if (!preg_match("/^\d+$/", $key)) {
        $page['errors'][] = "Wrong key";
    }

    if (count($page['errors']) == 0) {
        $db = Database::getInstance();
        $db->query("DELETE FROM {chatresponses} WHERE id = ?", array($key));
        $redirect_to =  MIBEW_WEB_ROOT . "/operator/canned.php?lang="
            . urlencode($lang) . "&group=" . intval($group_id);
        header("Location: " . $redirect_to);
        exit;
    }
}

// Get messages and setup pagination

$canned_messages = load_canned_messages($lang, $group_id);
foreach ($canned_messages as &$message) {
    $message['vctitle'] = $message['vctitle'];
    $message['vcvalue'] = $message['vcvalue'];
}
unset($message);

$pagination = setup_pagination($canned_messages);
$page['pagination'] = $pagination['info'];
$page['pagination.items'] = $pagination['items'];

# form values

$page['formlang'] = $lang;
$page['formgroup'] = $group_id;
$page['title'] = getlocal("canned.title");
$page['menuid'] = "canned";

$page = array_merge($page, prepare_menu($operator));

$page_style = new PageStyle(PageStyle::getCurrentStyle());
$page_style->render('canned', $page);
