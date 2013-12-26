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
require_once(MIBEW_FS_ROOT.'/libs/operator.php');
require_once(MIBEW_FS_ROOT.'/libs/interfaces/style.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/style.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/page_style.php');

$operator = check_login();
csrfchecktoken();

if (isset($_GET['act']) && $_GET['act'] == 'del') {

	$groupid = isset($_GET['gid']) ? $_GET['gid'] : "";

	if (!preg_match("/^\d+$/", $groupid)) {
		$errors[] = getlocal("page.groups.error.cannot_delete");
	}

	if (!is_capable(CAN_ADMINISTRATE, $operator)) {
		$errors[] = getlocal("page.groups.error.forbidden_remove");
	}

	if (count($errors) == 0) {
		$db = Database::getInstance();
		$db->query("delete from {chatgroup} where groupid = ?", array($groupid));
		$db->query("delete from {chatgroupoperator} where groupid = ?", array($groupid));
		$db->query("update {chatthread} set groupid = 0 where groupid = ?",array($groupid));
		header("Location: $mibewroot/operator/groups.php");
		exit;
	}
}

function is_online($group)
{
	return $group['ilastseen'] !== NULL && $group['ilastseen'] < Settings::get('online_timeout') ? "1" : "";
}

function is_away($group)
{
	return $group['ilastseenaway'] !== NULL && $group['ilastseenaway'] < Settings::get('online_timeout') ? "1" : "";
}


$page = array();
$sort['by'] = verifyparam("sortby", "/^(name|lastseen|weight)$/", "name");
$sort['desc'] = (verifyparam("sortdirection", "/^(desc|asc)$/", "desc") == "desc");
$page['groups'] = get_sorted_groups($sort);
$page['formsortby'] = $sort['by'];
$page['formsortdirection'] = $sort['desc']?'desc':'asc';
$page['canmodify'] = is_capable(CAN_ADMINISTRATE, $operator);
$page['availableOrders'] = array(
	array('id' => 'name', 'name' => getlocal('form.field.groupname')),
	array('id' => 'lastseen', 'name' => getlocal('page_agents.status')),
	array('id' => 'weight', 'name' => getlocal('page.groups.weight'))
);
$page['availableDirections'] = array(
	array('id' => 'desc', 'name' => getlocal('page.groups.sortdirection.desc')),
	array('id' => 'asc', 'name' => getlocal('page.groups.sortdirection.asc')),
);

$page['title'] = getlocal("page.groups.title");
$page['menuid'] = "groups";

prepare_menu($operator);

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('groups');

?>