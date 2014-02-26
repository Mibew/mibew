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
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');
require_once(MIBEW_FS_ROOT . '/libs/operator.php');
require_once(MIBEW_FS_ROOT . '/libs/groups.php');

$operator = check_login();
csrf_check_token();

$group_id = verify_param("gid", "/^\d{1,9}$/");
$page = array(
    'groupid' => $group_id,
    'errors' => array(),
);

$operators = get_operators_list();
$group = group_by_id($group_id);

if (!$group) {
    $page['errors'][] = getlocal("page.group.no_such");
} elseif (isset($_POST['gid'])) {

    $new_members = array();
    foreach ($operators as $op) {
        if (verify_param("op" . $op['operatorid'], "/^on$/", "") == "on") {
            $new_members[] = $op['operatorid'];
        }
    }

    update_group_members($group_id, $new_members);
    header("Location: " . MIBEW_WEB_ROOT . "/operator/groupmembers.php?gid=" . intval($group_id) . "&stored");
    exit;
}

$page['formop'] = array();
$page['currentgroup'] = $group ? to_page(htmlspecialchars($group['vclocalname'])) : "";

$checked_operators = array();
foreach (get_group_members($group_id) as $rel) {
    $checked_operators[] = $rel['operatorid'];
}

$page['operators'] = array();
foreach ($operators as $op) {
    $op['vclocalename'] = to_page($op['vclocalename']);
    $op['vclogin'] = to_page($op['vclogin']);
    $op['checked'] = in_array($op['operatorid'], $checked_operators);

    $page['operators'][] = $op;
}

$page['stored'] = isset($_GET['stored']);
$page['title'] = getlocal("page.groupmembers.title");
$page['menuid'] = "groups";

$page = array_merge($page, prepare_menu($operator));

$page['tabs'] = setup_group_settings_tabs($group_id, 1);

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('group_members', $page);
