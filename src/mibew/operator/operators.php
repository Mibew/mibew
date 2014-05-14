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

if (isset($_GET['act'])) {

    $operator_id = isset($_GET['id']) ? $_GET['id'] : "";
    if (!preg_match("/^\d+$/", $operator_id)) {
        $page['errors'][] = getlocal("no_such_operator");
    }

    if ($_GET['act'] == 'del') {
        if (!is_capable(CAN_ADMINISTRATE, $operator)) {
            $page['errors'][] = getlocal("page_agents.error.forbidden_remove");
        }

        if ($operator_id == $operator['operatorid']) {
            $page['errors'][] = getlocal("page_agents.error.cannot_remove_self");
        }

        if (count($page['errors']) == 0) {
            $op = operator_by_id($operator_id);
            if (!$op) {
                $page['errors'][] = getlocal("no_such_operator");
            } elseif ($op['vclogin'] == 'admin') {
                $page['errors'][] = getlocal("page_agents.error.cannot_remove_admin");
            }
        }

        if (count($page['errors']) == 0) {
            delete_operator($operator_id);
            header("Location: " . MIBEW_WEB_ROOT . "/operator/operators.php");
            exit;
        }
    }
    if ($_GET['act'] == 'disable' || $_GET['act'] == 'enable') {
        $act_disable = ($_GET['act'] == 'disable');
        if (!is_capable(CAN_ADMINISTRATE, $operator)) {
            $page['errors'][] = $act_disable
                ? getlocal('page_agents.disable.not.allowed')
                : getlocal('page_agents.enable.not.allowed');
        }

        if ($operator_id == $operator['operatorid'] && $act_disable) {
            $page['errors'][] = getlocal('page_agents.cannot.disable.self');
        }

        if (count($page['errors']) == 0) {
            $op = operator_by_id($operator_id);
            if (!$op) {
                $page['errors'][] = getlocal("no_such_operator");
            } elseif ($op['vclogin'] == 'admin' && $act_disable) {
                $page['errors'][] = getlocal('page_agents.cannot.disable.admin');
            }
        }

        if (count($page['errors']) == 0) {
            $db = Database::getInstance();
            $db->query(
                "update {chatoperator} set idisabled = ? where operatorid = ?",
                array(($act_disable ? '1' : '0'), $operator_id)
            );

            header("Location: " . MIBEW_WEB_ROOT . "/operator/operators.php");
            exit;
        }
    }
}

$sort['by'] = verify_param("sortby", "/^(login|commonname|localename|lastseen)$/", "login");
$sort['desc'] = (verify_param("sortdirection", "/^(desc|asc)$/", "desc") == "desc");
$page['formsortby'] = $sort['by'];
$page['formsortdirection'] = $sort['desc'] ? 'desc' : 'asc';
$list_options['sort'] = $sort;
if (in_isolation($operator)) {
    $list_options['isolated_operator_id'] = $operator['operatorid'];
}

$operators_list = get_operators_list($list_options);

// Prepare operator to render in template
foreach ($operators_list as &$item) {
    $item['vclogin'] = $item['vclogin'];
    $item['vclocalename'] = $item['vclocalename'];
    $item['vccommonname'] = $item['vccommonname'];
    $item['isAvailable'] = operator_is_available($item);
    $item['isAway'] = operator_is_away($item);
    $item['lastTimeOnline'] = time() - $item['time'];
    $item['isDisabled'] = operator_is_disabled($item);
}
unset($item);

$page['allowedAgents'] = $operators_list;
$page['canmodify'] = is_capable(CAN_ADMINISTRATE, $operator);
$page['availableOrders'] = array(
    array('id' => 'login', 'name' => getlocal('page_agents.login')),
    array('id' => 'localename', 'name' => getlocal('page_agents.agent_name')),
    array('id' => 'commonname', 'name' => getlocal('page_agents.commonname')),
    array('id' => 'lastseen', 'name' => getlocal('page_agents.status')),
);
$page['availableDirections'] = array(
    array('id' => 'desc', 'name' => getlocal('page_agents.sortdirection.desc')),
    array('id' => 'asc', 'name' => getlocal('page_agents.sortdirection.asc')),
);

$page['title'] = getlocal("page_agents.title");
$page['menuid'] = "operators";

setlocale(LC_TIME, getstring("time.locale"));

$page = array_merge($page, prepare_menu($operator));

$page_style = new PageStyle(PageStyle::getCurrentStyle());
$page_style->render('operators', $page);
