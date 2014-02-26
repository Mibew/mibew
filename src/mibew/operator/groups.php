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
require_once(MIBEW_FS_ROOT . '/libs/operator.php');
require_once(MIBEW_FS_ROOT . '/libs/groups.php');

$operator = check_login();
csrf_check_token();

$page = array(
    'errors' => array(),
);

if (isset($_GET['act']) && $_GET['act'] == 'del') {

    $group_id = isset($_GET['gid']) ? $_GET['gid'] : "";

    if (!preg_match("/^\d+$/", $group_id)) {
        $page['errors'][] = getlocal("page.groups.error.cannot_delete");
    }

    if (!is_capable(CAN_ADMINISTRATE, $operator)) {
        $page['errors'][] = getlocal("page.groups.error.forbidden_remove");
    }

    if (count($page['errors']) == 0) {
        $db = Database::getInstance();
        $db->query("delete from {chatgroup} where groupid = ?", array($group_id));
        $db->query("delete from {chatgroupoperator} where groupid = ?", array($group_id));
        $db->query("update {chatthread} set groupid = 0 where groupid = ?", array($group_id));
        header("Location: " . MIBEW_WEB_ROOT . "/operator/groups.php");
        exit;
    }
}

$sort['by'] = verify_param("sortby", "/^(name|lastseen|weight)$/", "name");
$sort['desc'] = (verify_param("sortdirection", "/^(desc|asc)$/", "desc") == "desc");

// Load and prepare groups
$groups = get_sorted_groups($sort);
foreach ($groups as &$group) {
    $group['vclocalname'] = to_page($group['vclocalname']);
    $group['vclocaldescription'] = to_page($group['vclocaldescription']);
    $group['isOnline'] = group_is_online($group);
    $group['isAway'] = group_is_away($group);
    $group['lastTimeOnline'] = time() - ($group['ilastseen'] ? $group['ilastseen'] : time());
    $group['inumofagents'] = to_page($group['inumofagents']);
}
unset($group);

$page['groups'] = $groups;
$page['formsortby'] = $sort['by'];
$page['formsortdirection'] = $sort['desc'] ? 'desc' : 'asc';
$page['canmodify'] = is_capable(CAN_ADMINISTRATE, $operator);
$page['availableOrders'] = array(
    array('id' => 'name', 'name' => getlocal('form.field.groupname')),
    array('id' => 'lastseen', 'name' => getlocal('page_agents.status')),
    array('id' => 'weight', 'name' => getlocal('page.groups.weight')),
);
$page['availableDirections'] = array(
    array('id' => 'desc', 'name' => getlocal('page.groups.sortdirection.desc')),
    array('id' => 'asc', 'name' => getlocal('page.groups.sortdirection.asc')),
);

$page['title'] = getlocal("page.groups.title");
$page['menuid'] = "groups";

$page = array_merge($page, prepare_menu($operator));

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('groups', $page);
