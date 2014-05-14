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

$operator = check_login();
csrf_check_token();

$operator_in_isolation = in_isolation($operator);

$op_id = verify_param("op", "/^\d{1,9}$/");
$page = array(
    'opid' => $op_id,
    'errors' => array()
);

$groups = $operator_in_isolation
    ? get_all_groups_for_operator($operator)
    : get_all_groups();

$can_modify = is_capable(CAN_ADMINISTRATE, $operator);

$op = operator_by_id($op_id);

if (!$op) {
    $page['errors'][] = getlocal("no_such_operator");
} elseif (isset($_POST['op'])) {

    if (!$can_modify) {
        $page['errors'][] = getlocal('page_agent.cannot_modify');
    }

    if (count($page['errors']) == 0) {
        $new_groups = array();
        foreach ($groups as $group) {
            if (verify_param("group" . $group['groupid'], "/^on$/", "") == "on") {
                $new_groups[] = $group['groupid'];
            }
        }

        update_operator_groups($op['operatorid'], $new_groups);
        header("Location: " . MIBEW_WEB_ROOT . "/operator/opgroups.php?op=" . intval($op_id) . "&stored");
        exit;
    }
}

$page['currentop'] = $op
    ? get_operator_name($op) . " (" . $op['vclogin'] . ")"
    : getlocal("not_found");
$page['canmodify'] = $can_modify ? "1" : "";

$checked_groups = array();
if ($op) {
    foreach (get_operator_group_ids($op_id) as $rel) {
        $checked_groups[] = $rel['groupid'];
    }
}

$page['groups'] = array();
foreach ($groups as $group) {
    $group['vclocalname'] = $group['vclocalname'];
    $group['vclocaldescription'] = $group['vclocaldescription'];
    $group['checked'] = in_array($group['groupid'], $checked_groups);

    $page['groups'][] = $group;
}

$page['stored'] = isset($_GET['stored']);
$page['title'] = getlocal("operator.groups.title");
$page['menuid'] = ($operator['operatorid'] == $op_id) ? "profile" : "operators";

$page = array_merge($page, prepare_menu($operator));

$page['tabs'] = setup_operator_settings_tabs($op_id, 2);

$page_style = new PageStyle(PageStyle::getCurrentStyle());
$page_style->render('operator_groups', $page);
