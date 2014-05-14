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

$page = array(
    'grid' => '',
    'errors' => array(),
);

$group_id = '';

if (isset($_POST['name'])) {
    $group_id = verify_param("gid", "/^(\d{1,9})?$/", "");
    $name = get_param('name');
    $description = get_param('description');
    $common_name = get_param('commonname');
    $common_description = get_param('commondescription');
    $email = get_param('email');
    $weight = get_param('weight');
    $parent_group = verify_param("parentgroup", "/^(\d{1,9})?$/", "");
    $title = get_param('title');
    $chat_title = get_param('chattitle');
    $host_url = get_param('hosturl');
    $logo = get_param('logo');

    if (!$name) {
        $page['errors'][] = no_field("form.field.groupname");
    }

    if ($email != '' && !is_valid_email($email)) {
        $page['errors'][] = wrong_field("form.field.mail");
    }

    if (!preg_match("/^(\d{1,9})?$/", $weight)) {
        $page['errors'][] = wrong_field("form.field.groupweight");
    }

    if ($weight == '') {
        $weight = 0;
    }

    if (!$parent_group) {
        $parent_group = null;
    }

    $existing_group = group_by_name($name);
    $duplicate_name = (!$group_id && $existing_group)
        || ($group_id
            && $existing_group
            && $group_id != $existing_group['groupid']);

    if ($duplicate_name) {
        $page['errors'][] = getlocal("page.group.duplicate_name");
    }

    if (count($page['errors']) == 0) {
        if (!$group_id) {
            $new_dep = create_group(array(
                'name' => $name,
                'description' => $description,
                'commonname' => $common_name,
                'commondescription' => $common_description,
                'email' => $email,
                'weight' => $weight,
                'parent' => $parent_group,
                'title' => $title,
                'chattitle' => $chat_title,
                'hosturl' => $host_url,
                'logo' => $logo));
            header("Location: " . MIBEW_WEB_ROOT . "/operator/groupmembers.php?gid=" . intval($new_dep['groupid']));
            exit;
        } else {
            update_group(array(
                'id' => $group_id,
                'name' => $name,
                'description' => $description,
                'commonname' => $common_name,
                'commondescription' => $common_description,
                'email' => $email,
                'weight' => $weight,
                'parent' => $parent_group,
                'title' => $title,
                'chattitle' => $chat_title,
                'hosturl' => $host_url,
                'logo' => $logo));
            header("Location: " . MIBEW_WEB_ROOT . "/operator/group.php?gid=" . intval($group_id) . "&stored");
            exit;
        }
    } else {
        $page['formname'] = $name;
        $page['formdescription'] = $description;
        $page['formcommonname'] = $common_name;
        $page['formcommondescription'] = $common_description;
        $page['formemail'] = $email;
        $page['formweight'] = $weight;
        $page['formparentgroup'] = $parent_group;
        $page['grid'] = $group_id;
        $page['formtitle'] = $title;
        $page['formchattitle'] = $chat_title;
        $page['formhosturl'] = $host_url;
        $page['formlogo'] = $logo;
    }
} elseif (isset($_GET['gid'])) {
    $group_id = verify_param('gid', "/^\d{1,9}$/");
    $group = group_by_id($group_id);

    if (!$group) {
        $page['errors'][] = getlocal("page.group.no_such");
        $page['grid'] = $group_id;
    } else {
        $page['formname'] = $group['vclocalname'];
        $page['formdescription'] = $group['vclocaldescription'];
        $page['formcommonname'] = $group['vccommonname'];
        $page['formcommondescription'] = $group['vccommondescription'];
        $page['formemail'] = $group['vcemail'];
        $page['formweight'] = $group['iweight'];
        $page['formparentgroup'] = $group['parent'];
        $page['grid'] = $group['groupid'];
        $page['formtitle'] = $group['vctitle'];
        $page['formchattitle'] = $group['vcchattitle'];
        $page['formhosturl'] = $group['vchosturl'];
        $page['formlogo'] = $group['vclogo'];
    }
}

$page['stored'] = isset($_GET['stored']);
$page['availableParentGroups'] = get_available_parent_groups($group_id);
$page['title'] = getlocal("page.group.title");
$page['menuid'] = "groups";

$page = array_merge($page, prepare_menu($operator));

$page['tabs'] = setup_group_settings_tabs($group_id, 0);

$page_style = new PageStyle(PageStyle::getCurrentStyle());
$page_style->render('group', $page);
