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

require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(dirname(dirname(__FILE__)).'/libs/operator.php');
require_once(dirname(dirname(__FILE__)).'/libs/operator_settings.php');
require_once(dirname(dirname(__FILE__)).'/libs/interfaces/style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/page_style.php');

$operator = check_login();
csrfchecktoken();

function update_operator_groups($operatorid, $newvalue)
{
	$db = Database::getInstance();
	$db->query(
		"delete from {chatgroupoperator} where operatorid = ?",
		array($operatorid)
	);

	foreach ($newvalue as $groupid) {
		$db->query(
			"insert into {chatgroupoperator} (groupid, operatorid) values (?,?)",
			array($groupid, $operatorid)
		);
	}
}

$operator_in_isolation = in_isolation($operator);

$opId = verifyparam("op", "/^\d{1,9}$/");
$page = array('opid' => $opId);
$page['groups'] = $operator_in_isolation?get_all_groups_for_operator($operator):get_all_groups();
$errors = array();

$canmodify = is_capable(CAN_ADMINISTRATE, $operator);

$op = operator_by_id($opId);

if (!$op) {
	$errors[] = getlocal("no_such_operator");

} else if (isset($_POST['op'])) {

	if (!$canmodify) {
		$errors[] = getlocal('page_agent.cannot_modify');
	}

	if (count($errors) == 0) {
		$new_groups = array();
		foreach ($page['groups'] as $group) {
			if (verifyparam("group" . $group['groupid'], "/^on$/", "") == "on") {
				$new_groups[] = $group['groupid'];
			}
		}

		update_operator_groups($op['operatorid'], $new_groups);
		header("Location: $mibewroot/operator/opgroups.php?op=" . intval($opId) . "&stored");
		exit;
	}
}

$page['formgroup'] = array();
$page['currentop'] = $op ? topage(get_operator_name($op)) . " (" . $op['vclogin'] . ")" : getlocal("not_found");
$page['canmodify'] = $canmodify ? "1" : "";

if ($op) {
	foreach (get_operator_groupids($opId) as $rel) {
		$page['formgroup'][] = $rel['groupid'];
	}
}

$page['stored'] = isset($_GET['stored']);

prepare_menu($operator);
setup_operator_settings_tabs($opId, 2);

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('operator_groups');

?>