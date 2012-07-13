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

require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('../libs/operator_settings.php');

$operator = check_login();
csrfchecktoken();

function update_operator_permissions($operatorid, $newvalue)
{
	$db = Database::getInstance();
	$db->query(
		"update {chatoperator} set iperm = ? where operatorid = ?",
		array($newvalue, $operatorid)
	);
}

$opId = verifyparam("op", "/^\d{1,9}$/");
$page = array('opid' => $opId, 'canmodify' => is_capable($can_administrate, $operator) ? "1" : "");
$errors = array();

$op = operator_by_id($opId);

if (!$op) {
	$errors[] = getlocal("no_such_operator");

} else if (isset($_POST['op'])) {

	if (!is_capable($can_administrate, $operator)) {
		$errors[] = getlocal('page_agent.cannot_modify');
	}

	$new_permissions = isset($op['iperm']) ? $op['iperm'] : 0;

	foreach ($permission_ids as $perm => $id) {
		if (verifyparam("permissions$id", "/^on$/", "") == "on") {
			$new_permissions |= (1 << $perm);
		} else {
			$new_permissions &= ~(1 << $perm);
		}
	}

	if (count($errors) == 0) {
		update_operator_permissions($op['operatorid'], $new_permissions);

		if ($opId && $_SESSION["${mysqlprefix}operator"] && $operator['operatorid'] == $opId) {
			$_SESSION["${mysqlprefix}operator"]['iperm'] = $new_permissions;
		}
		header("Location: $webimroot/operator/permissions.php?op=$opId&stored");
		exit;
	}

}

$page['permissionsList'] = get_permission_list();
$page['formpermissions'] = array("");
$page['currentop'] = $op ? topage(get_operator_name($op)) . " (" . $op['vclogin'] . ")" : "-not found-";

if ($op) {
	foreach ($permission_ids as $perm => $id) {
		if (is_capable($perm, $op)) {
			$page['formpermissions'][] = $id;
		}
	}
}

$page['stored'] = isset($_GET['stored']);
prepare_menu($operator);
setup_operator_settings_tabs($opId, 3);
start_html_output();
require('../view/permissions.php');
?>