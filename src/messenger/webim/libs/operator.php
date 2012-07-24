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

$can_administrate = 0;
$can_takeover = 1;
$can_viewthreads = 2;
$can_modifyprofile = 3;

$can_count = 4;

$permission_ids = array(
	$can_administrate => "admin",
	$can_takeover => "takeover",
	$can_viewthreads => "viewthreads",
	$can_modifyprofile => "modifyprofile"
);

function operator_by_login($login)
{
	$db = Database::getInstance();
	return $db->query(
		"select * from {chatoperator} where vclogin = ?",
		array($login),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
}

function operator_by_email($mail)
{
	$db = Database::getInstance();
	return $db->query(
		"select * from {chatoperator} where vcemail = ?",
		array($mail),
		array('return_rows', Database::RETURN_ONE_ROW)
	);
}

function operator_by_id($id)
{
	$db = Database::getInstance();
	return $db->query(
		"select * from {chatoperator} where operatorid = ?",
		array($id),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
}

/**
 * Get list of operators taking into account $options
 * @param array $options Associative array of options. It can contains following keys:
 *  - 'sort': an associative array of sorting options.
 *  - 'isolated_operator_id': id of current operators. If it set - function would return
 *    only operators from adjacent groups.
 *
 * 'sort' array must contains two keys: 'by' and 'desc'.
 * 'by' means the field by which operators would be sort and can take following
 * values: 'commonname', 'localename', 'login', 'lastseen'. 'desc' means order in which operators would
 * be sort. If it's 'true' operators would be sort in descending order and in
 * ascending order overwise.
 *
 */
function get_operators_list($options)
{
	$db = Database::getInstance();

	if ( !empty($options['sort']) && isset($options['sort']['by']) && isset($options['sort']['desc'])) {
		switch ($options['sort']['by']) {
			case 'commonname':
				$orderby = 'vccommonname';
				break;
			case 'localename':
				$orderby = 'vclocalename';
				break;
			case 'lastseen':
				$orderby = 'time';
				break;
			default:
				$orderby = 'vclogin';
				break;
		}
		$orderby = $orderby . ' ' . ($options['sort']['desc']?'DESC':'ASC');
	} else {
		$orderby = "vclogin";
	}

	$query = "select distinct {chatoperator}.operatorid, vclogin, vclocalename, vccommonname, istatus, idisabled, (unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time " .
		 "from {chatoperator}" .
		 (
		 empty($options['isolated_operator_id']) ? "" :
			", {chatgroupoperator} " .
			" where {chatoperator}.operatorid = {chatgroupoperator}.operatorid and {chatgroupoperator}.groupid in " .
			"(select g.groupid from {chatgroup} g, " .
			"(select distinct parent from {chatgroup}, {chatgroupoperator} " .
			"where {chatgroup}.groupid = {chatgroupoperator}.groupid and {chatgroupoperator}.operatorid = :operatorid) i " .
			"where g.groupid = i.parent or g.parent = i.parent " .
			")"
		 ) .
		 " order by " . $orderby;

	$operators = $db->query(
		$query,
		(
			empty($options['isolated_operator_id']) 
			? array()
			: array(':operatorid' => $options['isolated_operator_id'])
		),
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);

	return $operators;
}

function operator_get_all()
{
	$db = Database::getInstance();
	return $operators = $db->query(
		"select operatorid, vclogin, vclocalename, vccommonname, istatus, idisabled, " .
		"(unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time " .
		"from {chatoperator} order by vclogin",
		NULL,
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
}

function get_operators_from_adjacent_groups($operator)
{
	$db = Database::getInstance();
	$query = "select distinct {chatoperator}.operatorid, vclogin, vclocalename,vccommonname, " .
		"istatus, idisabled, " .
		"(unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time " .
		"from {chatoperator}, {chatgroupoperator} " .
		"where {chatoperator}.operatorid = {chatgroupoperator}.operatorid " .
		"and {chatgroupoperator}.groupid in " .
		"(select g.groupid from {chatgroup} g, " .
		"(select distinct parent from {chatgroup}, {chatgroupoperator} " .
		"where {chatgroup}.groupid = {chatgroupoperator}.groupid " .
		"and {chatgroupoperator}.operatorid = ?) i " .
		"where g.groupid = i.parent or g.parent = i.parent " .
		") order by vclogin";
	
	
	return $db->query(
		$query,
		array($operator['operatorid']),
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
}

function operator_is_online($operator)
{
	return $operator['time'] < Settings::get('online_timeout');
}

function operator_is_available($operator)
{
	return $operator['istatus'] == 0 && $operator['time'] < Settings::get('online_timeout') ? "1" : "";
}

function operator_is_away($operator)
{
	return $operator['istatus'] != 0 && $operator['time'] < Settings::get('online_timeout') ? "1" : "";
}

function operator_is_disabled($operator)
{
	return $operator['idisabled'] == '1';
}

function update_operator($operatorid, $login, $email, $password, $localename, $commonname)
{
	$db = Database::getInstance();
	$values = array(
		':login' => $login,
		':localname' => $localename,
		':commonname' => $commonname,
		':email' => $email,
		':jabbername' => '',
		':operatorid' => $operatorid
	);
	if ($password) {
		$values[':password'] = md5($password);
	}
	$db->query(
		"update {chatoperator} set vclogin = :login, " .
		($password ? " vcpassword=:password, " : "") .
		"vclocalename = :localname, vccommonname = :commonname, " .
		"vcemail = :email, vcjabbername= :jabbername " .
		"where operatorid = :operatorid",
		$values

	);
}

function update_operator_avatar($operatorid, $avatar)
{
	$db = Database::getInstance();
	$db->query(
		"update {chatoperator} set vcavatar = ? where operatorid = ?",
		array($avatar, $operatorid)
	);
}

function create_operator($login, $email, $password, $localename, $commonname, $avatar)
{
	$db = Database::getInstance();
	$db->query(
		"insert into {chatoperator} " .
		"(vclogin,vcpassword,vclocalename,vccommonname,vcavatar,vcemail,vcjabbername) " .
		"values (?, ?, ?, ?, ?, ?, ?)",
		array(
			$login,
			md5($password),
			$localename,
			$commonname,
			$avatar,
			$email,
			''
		)
	);

	$id = $db->insertedId();

	return $db->query(
		"select * from {chatoperator} where operatorid = ?",
		array($id),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
}

function notify_operator_alive($operatorid, $istatus)
{
	$db = Database::getInstance();
	$db->query(
		"update {chatoperator} set istatus = ?, dtmlastvisited = CURRENT_TIMESTAMP " .
		"where operatorid = ?",
		array($istatus, $operatorid)
	);
}

function has_online_operators($groupid = "")
{
	$db = Database::getInstance();

	$query = "select count(*) as total, min(unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time from {chatoperator}";
	if ($groupid) {
		$query .= ", {chatgroupoperator}, {chatgroup} where {chatgroup}.groupid = {chatgroupoperator}.groupid and " .
			"({chatgroup}.groupid = :groupid or {chatgroup}.parent = :groupid) and {chatoperator}.operatorid = " .
			"{chatgroupoperator}.operatorid and istatus = 0";
	} else {
		if (Settings::get('enablegroups') == 1) {
			$query .= ", {chatgroupoperator} where {chatoperator}.operatorid = " .
				"{chatgroupoperator}.operatorid and istatus = 0";
		} else {
			$query .= " where istatus = 0";
		}
	}

	$row = $db->query(
		$query,
		array(':groupid'=>$groupid),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
	return $row['time'] < Settings::get('online_timeout') && $row['total'] > 0;
}

function is_operator_online($operatorid)
{
	$db = Database::getInstance();
	$row = $db->query(
		"select count(*) as total, " .
		"min(unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time " .
		"from {chatoperator} where operatorid = ?",
		array($operatorid),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
	
	return $row['time'] < Settings::get('online_timeout') && $row['total'] == 1;
}

function get_operator_name($operator)
{
	global $home_locale, $current_locale;
	if ($home_locale == $current_locale)
		return $operator['vclocalename'];
	else
		return $operator['vccommonname'];
}

function append_query($link, $pv)
{
	$infix = '?';
	if (strstr($link, $infix) !== FALSE)
		$infix = '&amp;';
	return "$link$infix$pv";
}

function check_login($redirect = true)
{
	global $webimroot, $mysqlprefix;
	if (!isset($_SESSION["${mysqlprefix}operator"])) {
		if (isset($_COOKIE['webim_lite'])) {
			list($login, $pwd) = preg_split("/,/", $_COOKIE['webim_lite'], 2);
			$op = operator_by_login($login);
			if ($op && isset($pwd) && isset($op['vcpassword']) && md5($op['vcpassword']) == $pwd && !operator_is_disabled($op)) {
				$_SESSION["${mysqlprefix}operator"] = $op;
				return $op;
			}
		}
		$requested = $_SERVER['PHP_SELF'];
		if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['QUERY_STRING']) {
			$requested .= "?" . $_SERVER['QUERY_STRING'];
		}
		if ($redirect) {
			$_SESSION['backpath'] = $requested;
			header("Location: $webimroot/operator/login.php");
			exit;
		} else {
			return null;
		}
	}
	return $_SESSION["${mysqlprefix}operator"];
}

// Force the admin to set a password after the installation
function force_password($operator)
{
	global $webimroot;
	if($operator['vcpassword']==md5(''))
	{
		header("Location: $webimroot/operator/operator.php?op=1");
		exit;
	}
}

function get_logged_in()
{
	global $mysqlprefix;
	return isset($_SESSION["${mysqlprefix}operator"]) ? $_SESSION["${mysqlprefix}operator"] : FALSE;
}

function login_operator($operator, $remember)
{
	global $webimroot, $mysqlprefix;
	$_SESSION["${mysqlprefix}operator"] = $operator;
	if ($remember) {
		$value = $operator['vclogin'] . "," . md5($operator['vcpassword']);
		setcookie('webim_lite', $value, time() + 60 * 60 * 24 * 1000, "$webimroot/");

	} else if (isset($_COOKIE['webim_lite'])) {
		setcookie('webim_lite', '', time() - 3600, "$webimroot/");
	}
}

function logout_operator()
{
	global $webimroot, $mysqlprefix;
	unset($_SESSION["${mysqlprefix}operator"]);
	unset($_SESSION['backpath']);
	if (isset($_COOKIE['webim_lite'])) {
		setcookie('webim_lite', '', time() - 3600, "$webimroot/");
	}
}

function setup_redirect_links($threadid, $operator, $token)
{
	global $page, $webimroot;

	$operator_in_isolation = in_isolation($operator);

	$list_options = $operator_in_isolation?array('isolated_operator_id' => $operator['operatorid']):array();
	$operators = get_operators_list($list_options);
	$operatorscount = count($operators);

	$groupscount = 0;
	$groups = array();
	if (Settings::get('enablegroups') == "1") {
		$groupslist = $operator_in_isolation?get_groups_for_operator($operator, true):get_groups(true);
		foreach ($groupslist as $group) {
			if ($group['inumofagents'] == 0) {
				continue;
			}
			$groups[] = $group;
		}
		$groupscount = count($groups);
	}

	prepare_pagination(max($operatorscount, $groupscount), 8);
	$p = $page['pagination'];
	$limit = $p['limit'];

	$operators = array_slice($operators, $p['start'], $p['end'] - $p['start']);
	$groups = array_slice($groups, $p['start'], $p['end'] - $p['start']);

	$agent_list = "";
	$params = array('thread' => $threadid, 'token' => $token);
	foreach ($operators as $agent) {
		$params['nextAgent'] = $agent['operatorid'];
		$status = $agent['time'] < Settings::get('online_timeout')
				? ($agent['istatus'] == 0
						? getlocal("char.redirect.operator.online_suff")
						: getlocal("char.redirect.operator.away_suff")
				)
				: "";
		$agent_list .= "<li><a href=\"" . add_params($webimroot . "/operator/redirect.php", $params) .
					   "\" title=\"" . topage(get_operator_name($agent)) . "\">" .
					   topage(get_operator_name($agent)) .
					   "</a> $status</li>";
	}
	$page['redirectToAgent'] = $agent_list;

	$group_list = "";
	if (Settings::get('enablegroups') == "1") {
		$params = array('thread' => $threadid, 'token' => $token);
		foreach ($groups as $group) {
			$params['nextGroup'] = $group['groupid'];
			$status = $group['ilastseen'] !== NULL && $group['ilastseen'] < Settings::get('online_timeout')
					? getlocal("char.redirect.operator.online_suff")
					: ($group['ilastseenaway'] !== NULL && $group['ilastseenaway'] < Settings::get('online_timeout')
							? getlocal("char.redirect.operator.away_suff")
							: "");
			$group_list .= "<li><a href=\"" . add_params($webimroot . "/operator/redirect.php", $params) .
						   "\" title=\"" . topage(get_group_name($group)) . "\">" .
						   topage(get_group_name($group)) .
						   "</a> $status</li>";
		}
	}
	$page['redirectToGroup'] = $group_list;
}

$permission_list = array();

function get_permission_list()
{
	global $permission_list, $permission_ids;
	if (count($permission_list) == 0) {
		foreach ($permission_ids as $permid) {
			$permission_list[] = array(
				'id' => $permid,
				'descr' => getlocal("permission.$permid")
			);
		}
	}
	return $permission_list;
}

function is_capable($perm, $operator)
{
	$permissions = $operator && isset($operator['iperm']) ? $operator['iperm'] : 0;
	return $perm >= 0 && $perm < 32 && ($permissions & (1 << $perm)) != 0;
}

function in_isolation($operator)
{
	global $can_administrate;
	return (!is_capable($can_administrate, $operator) && Settings::get('enablegroups') && Settings::get('enablegroupsisolation'));
}

function prepare_menu($operator, $hasright = true)
{
	global $page, $can_administrate;
	$page['operator'] = topage(get_operator_name($operator));
	if ($hasright) {
		$page['showban'] = Settings::get('enableban') == "1";
		$page['showstat'] = Settings::get('enablestatistics') == "1";
		$page['showadmin'] = is_capable($can_administrate, $operator);
		$page['currentopid'] = $operator['operatorid'];
	}
}

function get_all_groups()
{
	$db = Database::getInstance();
	$groups = $db->query(
		"select {chatgroup}.groupid as groupid, parent, vclocalname, vclocaldescription " .
		"from {chatgroup} order by vclocalname",
		NULL,
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
	return get_sorted_child_groups_($groups);
}

function get_all_groups_for_operator($operator)
{
	$db = Database::getInstance();
	$query = "select g.groupid as groupid, g.parent, g.vclocalname, g.vclocaldescription " .
		"from {chatgroup} g, " .
		"(select distinct parent from {chatgroup}, {chatgroupoperator} " .
		"where {chatgroup}.groupid = {chatgroupoperator}.groupid " .
		"and {chatgroupoperator}.operatorid = ?) i " .
		"where g.groupid = i.parent or g.parent = i.parent " .
		"order by vclocalname";
	
	$groups = $db->query(
		$query,
		array($operator['operatorid']),
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
	return get_sorted_child_groups_($groups);
}

function get_sorted_child_groups_($groupslist, $skipgroups = array(), $maxlevel = -1, $groupid = NULL, $level = 0)
{
	$child_groups = array();
	foreach ($groupslist as $index => $group) {
		if ($group['parent'] == $groupid && !in_array($group['groupid'], $skipgroups)) {
			$group['level'] = $level;
			$child_groups[] = $group;
			if ($maxlevel == -1 || $level < $maxlevel) {
				$child_groups = array_merge($child_groups, get_sorted_child_groups_($groupslist, $skipgroups, $maxlevel, $group['groupid'], $level+1));
			}
		}
	}
	return $child_groups;
}

function get_groups_($checkaway, $operator, $order = NULL)
{
	$db = Database::getInstance();
	if($order){
		switch($order['by']){
			case 'weight':
				$orderby = "iweight";
				break;
			case 'lastseen':
				$orderby = "ilastseen";
				break;
			default:
				$orderby = "{chatgroup}.vclocalname";
		}
		$orderby = sprintf(" IF(ISNULL({chatgroup}.parent),CONCAT('_',%s),'') %s, {chatgroup}.iweight ",
					$orderby,
					($order['desc']?'DESC':'ASC'));
	}else{
		$orderby = "iweight, vclocalname";
	}

	$values = array();
	$query = "select {chatgroup}.groupid as groupid, {chatgroup}.parent as parent, vclocalname, vclocaldescription, iweight" .
		", (SELECT count(*) from {chatgroupoperator} where {chatgroup}.groupid = " .
		"{chatgroupoperator}.groupid) as inumofagents" .
		", (SELECT min(unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time " .
		"from {chatgroupoperator}, {chatoperator} where istatus = 0 and " .
		"{chatgroup}.groupid = {chatgroupoperator}.groupid " .
		"and {chatgroupoperator}.operatorid = {chatoperator}.operatorid) as ilastseen" .
		($checkaway
			? ", (SELECT min(unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time " .
			"from {chatgroupoperator}, {chatoperator} where istatus <> 0 and " .
			"{chatgroup}.groupid = {chatgroupoperator}.groupid " .
			"and {chatgroupoperator}.operatorid = {chatoperator}.operatorid) as ilastseenaway"
			: ""
		) .
		" from {chatgroup} ";
	if ($operator) {
		$query .= ", (select distinct parent from {chatgroup}, {chatgroupoperator} " .
			"where {chatgroup}.groupid = {chatgroupoperator}.groupid and {chatgroupoperator}.operatorid = ?) i " .
			"where {chatgroup}.groupid = i.parent or {chatgroup}.parent = i.parent ";
		$values[] = $operator['operatorid'];
	}
	$query .= " order by " . $orderby;
	$groups = $db->query(
		$query,
		$values,
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
	return get_sorted_child_groups_($groups);
}

function get_groups($checkaway)
{
	return get_groups_($checkaway, NULL);
}

function get_groups_for_operator($operator, $checkaway)
{
	return get_groups_($checkaway, $operator);
}

function get_sorted_groups($order)
{
	return get_groups_(true, NULL, $order);
}

function get_operator_groupids($operatorid)
{
	$db = Database::getInstance();
	return $db->query(
		"select groupid from {chatgroupoperator} where operatorid = ?",
		array($operatorid),
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
}

?>