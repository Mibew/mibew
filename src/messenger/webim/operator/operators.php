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

require_once('../libs/init.php');
require_once('../libs/operator.php');

$operator = check_login();
force_password($operator);
csrfchecktoken();

if (isset($_GET['act'])) {

	$errors = array();
	$operatorid = isset($_GET['id']) ? $_GET['id'] : "";
	if (!preg_match("/^\d+$/", $operatorid)) {
		$errors[] = getlocal("no_such_operator");
	}

	if ($_GET['act'] == 'del') {
		if (!is_capable(CAN_ADMINISTRATE, $operator)) {
			$errors[] = getlocal("page_agents.error.forbidden_remove");
		}

		if ($operatorid == $operator['operatorid']) {
			$errors[] = getlocal("page_agents.error.cannot_remove_self");
		}

		if (count($errors) == 0) {
			$op = operator_by_id($operatorid);
			if (!$op) {
				$errors[] = getlocal("no_such_operator");
			} else if ($op['vclogin'] == 'admin') {
				$errors[] = getlocal("page_agents.error.cannot_remove_admin");
			}
		}

		if (count($errors) == 0) {
			delete_operator($operatorid);
			header("Location: $webimroot/operator/operators.php");
			exit;
		}
	}
	if ($_GET['act'] == 'disable' || $_GET['act'] == 'enable') {
		$act_disable = ($_GET['act'] == 'disable');
		if (!is_capable(CAN_ADMINISTRATE, $operator)) {
			$errors[] = $act_disable?getlocal('page_agents.disable.not.allowed'):getlocal('page_agents.enable.not.allowed');
		}

		if ($operatorid == $operator['operatorid'] && $act_disable) {
			$errors[] = getlocal('page_agents.cannot.disable.self');
		}

		if (count($errors) == 0) {
			$op = operator_by_id($operatorid);
			if (!$op) {
				$errors[] = getlocal("no_such_operator");
			} else if ($op['vclogin'] == 'admin' && $act_disable) {
				$errors[] = getlocal('page_agents.cannot.disable.admin');
			}
		}

		if (count($errors) == 0) {
			$db = Database::getInstance();
			$db->query(
				"update {chatoperator} set idisabled = ? where operatorid = ?",
				array(($act_disable ? '1' : '0'), $operatorid)
			);

			header("Location: $webimroot/operator/operators.php");
			exit;
		}
	}
}

$page = array();
$sort['by'] = verifyparam("sortby", "/^(login|commonname|localename|lastseen)$/", "login");
$sort['desc'] = (verifyparam("sortdirection", "/^(desc|asc)$/", "desc") == "desc");
$page['formsortby'] = $sort['by'];
$page['formsortdirection'] = $sort['desc']?'desc':'asc';
$list_options['sort'] = $sort;
if (in_isolation($operator)) {
	$list_options['isolated_operator_id'] = $operator['operatorid'];
}
$page['allowedAgents'] = get_operators_list($list_options);
$page['canmodify'] = is_capable(CAN_ADMINISTRATE, $operator);
$page['availableOrders'] = array(
	array('id' => 'login', 'name' => getlocal('page_agents.login')),
	array('id' => 'localename', 'name' => getlocal('page_agents.agent_name')),
	array('id' => 'commonname', 'name' => getlocal('page_agents.commonname')),
	array('id' => 'lastseen', 'name' => getlocal('page_agents.status'))
);
$page['availableDirections'] = array(
	array('id' => 'desc', 'name' => getlocal('page_agents.sortdirection.desc')),
	array('id' => 'asc', 'name' => getlocal('page_agents.sortdirection.asc')),
);

setlocale(LC_TIME, getstring("time.locale"));

prepare_menu($operator);
start_html_output();
require('../view/agents.php');
?>