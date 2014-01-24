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

$operator = check_login();
csrfchecktoken();
check_permissions($operator, $can_administrate);

if (isset($_GET['act']) && $_GET['act'] == 'del') {
	$operatorid = isset($_GET['id']) ? $_GET['id'] : "";

	if (!preg_match("/^\d+$/", $operatorid)) {
		$errors[] = "Cannot delete: wrong argument";
	}

	if (!is_capable($can_administrate, $operator)) {
		$errors[] = "You are not allowed to remove operators";
	}

	if ($operatorid == $operator['operatorid']) {
		$errors[] = "Cannot remove self";
	}

	if (count($errors) == 0) {
		$op = operator_by_id($operatorid);
		if (!$op) {
			$errors[] = getlocal("no_such_operator");
		} else if ($op['vclogin'] == 'admin') {
			$errors[] = 'Cannot remove operator "admin"';
		}
	}

	if (count($errors) == 0) {
		$link = connect();
		perform_query("delete from ${mysqlprefix}chatgroupoperator where operatorid = " . intval($operatorid), $link);
		perform_query("delete from ${mysqlprefix}chatoperator where operatorid = " . intval($operatorid), $link);
		mysql_close($link);

		header("Location: $mibewroot/operator/operators.php");
		exit;
	}
}

$page = array();
$page['allowedAgents'] = operator_get_all();
$page['canmodify'] = is_capable($can_administrate, $operator);

setlocale(LC_TIME, getstring("time.locale"));

prepare_menu($operator);
start_html_output();
require('../view/agents.php');
?>