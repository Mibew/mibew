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
require_once(dirname(dirname(__FILE__)).'/libs/groups.php');
require_once(dirname(dirname(__FILE__)).'/libs/interfaces/style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/page_style.php');

$operator = check_login();
csrfchecktoken();

$page = array('grid' => '');
$errors = array();
$groupid = '';

function group_by_name($name)
{
	$db = Database::getInstance();
	$group = $db->query(
		"select * from {chatgroup} where vclocalname = ?",
		array($name),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
	return $group;
}

function check_group_params($group, $extra_params = NULL)
{
	$obligatory_params = array(
		'name',
		'description',
		'commonname',
		'commondescription',
		'email',
		'weight',
		'parent',
		'chattitle',
		'hosturl',
		'logo');
	$params = is_null($extra_params)?$obligatory_params:array_merge($obligatory_params,$extra_params);
	if(count(array_diff($params, array_keys($group))) != 0){
		die('Wrong parameters set!');
	}
}

/**
 * Creates group
 *
 * @param array $group Operators' group.
 * The $group array must contains following keys:
 * name, description, commonname, commondescription,
 * email, weight, parent, title, chattitle, hosturl, logo
 * @return array Created group
 */
function create_group($group)
{
	$db = Database::getInstance();
	check_group_params($group);
	$db->query(
		"insert into {chatgroup} (parent, vclocalname,vclocaldescription,vccommonname, " .
		"vccommondescription,vcemail,vctitle,vcchattitle,vchosturl,vclogo,iweight) " .
		"values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
		array(
			($group['parent'] ? (int)$group['parent'] : NULL),
			$group['name'],
			$group['description'],
			$group['commonname'],
			$group['commondescription'],
			$group['email'],
			$group['title'],
			$group['chattitle'],
			$group['hosturl'],
			$group['logo'],
			$group['weight']
		)
	);
	$id = $db->insertedId();

	$newdep = $db->query(
		"select * from {chatgroup} where groupid = ?",
		array($id),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
	return $newdep;
}

/**
 * Updates group info
 *
 * @param array $group Operators' group.
 * The $group array must contains following keys:
 * id, name, description, commonname, commondescription,
 * email, weight, parent, title, chattitle, hosturl, logo
 */
function update_group($group)
{
	$db = Database::getInstance();
	check_group_params($group, array('id'));
	$db->query(
		"update {chatgroup} set parent = ?, vclocalname = ?, vclocaldescription = ?, " .
		"vccommonname = ?, vccommondescription = ?, vcemail = ?, vctitle = ?, " .
		"vcchattitle = ?, vchosturl = ?, vclogo = ?, iweight = ? where groupid = ?",
		array(
			($group['parent'] ? (int)$group['parent'] : NULL),
			$group['name'],
			$group['description'],
			$group['commonname'],
			$group['commondescription'],
			$group['email'],
			$group['title'],
			$group['chattitle'],
			$group['hosturl'],
			$group['logo'],
			$group['weight'],
			$group['id']
		)
	);

	if ($group['parent']) {
		$db->query(
			"update {chatgroup} set parent = NULL where parent = ?",
			array($group['id'])
		);
	}
}

if (isset($_POST['name'])) {
	$groupid = verifyparam("gid", "/^(\d{1,9})?$/", "");
	$name = getparam('name');
	$description = getparam('description');
	$commonname = getparam('commonname');
	$commondescription = getparam('commondescription');
	$email = getparam('email');
	$weight = getparam('weight');
	$parentgroup = verifyparam("parentgroup", "/^(\d{1,9})?$/", "");
	$title = getparam('title');
	$chattitle = getparam('chattitle');
	$hosturl = getparam('hosturl');
	$logo = getparam('logo');

	if (!$name)
		$errors[] = no_field("form.field.groupname");

	if ($email != '' && !is_valid_email($email))
		$errors[] = wrong_field("form.field.mail");

	if (! preg_match("/^(\d{1,9})?$/", $weight))
		$errors[] = wrong_field("form.field.groupweight");

	if ($weight == '')
		$weight = 0;

	if (! $parentgroup)
		$parentgroup = NULL;

	$existing_group = group_by_name($name);
	if ((!$groupid && $existing_group) ||
		($groupid && $existing_group && $groupid != $existing_group['groupid']))
		$errors[] = getlocal("page.group.duplicate_name");

	if (count($errors) == 0) {
		if (!$groupid) {
			$newdep = create_group(array(
				'name' => $name,
				'description' => $description,
				'commonname' => $commonname,
				'commondescription' => $commondescription,
				'email' => $email,
				'weight' => $weight,
				'parent' => $parentgroup,
				'title' => $title,
				'chattitle' => $chattitle,
				'hosturl' => $hosturl,
				'logo' => $logo));
			header("Location: $mibewroot/operator/groupmembers.php?gid=" . intval($newdep['groupid']));
			exit;
		} else {
			update_group(array(
				'id' => $groupid,
				'name' => $name,
				'description' => $description,
				'commonname' => $commonname,
				'commondescription' => $commondescription,
				'email' => $email,
				'weight' => $weight,
				'parent' => $parentgroup,
				'title' => $title,
				'chattitle' => $chattitle,
				'hosturl' => $hosturl,
				'logo' => $logo));
			header("Location: $mibewroot/operator/group.php?gid=" . intval($groupid) . "&stored");
			exit;
		}
	} else {
		$page['formname'] = topage($name);
		$page['formdescription'] = topage($description);
		$page['formcommonname'] = topage($commonname);
		$page['formcommondescription'] = topage($commondescription);
		$page['formemail'] = topage($email);
		$page['formweight'] = topage($weight);
		$page['formparentgroup'] = topage($parentgroup);
		$page['grid'] = topage($groupid);
		$page['formtitle'] = topage($title);
		$page['formchattitle'] = topage($chattitle);
		$page['formhosturl'] = topage($hosturl);
		$page['formlogo'] = topage($logo);
	}

} else if (isset($_GET['gid'])) {
	$groupid = verifyparam('gid', "/^\d{1,9}$/");
	$group = group_by_id($groupid);

	if (!$group) {
		$errors[] = getlocal("page.group.no_such");
		$page['grid'] = topage($groupid);
	} else {
		$page['formname'] = topage($group['vclocalname']);
		$page['formdescription'] = topage($group['vclocaldescription']);
		$page['formcommonname'] = topage($group['vccommonname']);
		$page['formcommondescription'] = topage($group['vccommondescription']);
		$page['formemail'] = topage($group['vcemail']);
		$page['formweight'] = topage($group['iweight']);
		$page['formparentgroup'] = topage($group['parent']);
		$page['grid'] = topage($group['groupid']);
		$page['formtitle'] = topage($group['vctitle']);
		$page['formchattitle'] = topage($group['vcchattitle']);
		$page['formhosturl'] = topage($group['vchosturl']);
		$page['formlogo'] = topage($group['vclogo']);
	}
}

$page['stored'] = isset($_GET['stored']);
$page['availableParentGroups'] = get_available_parent_groups($groupid);

prepare_menu($operator);
setup_group_settings_tabs($groupid, 0);

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('group');

?>