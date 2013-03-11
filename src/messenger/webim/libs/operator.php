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
	global $mysqlprefix;
	$link = connect();
	$operator = select_one_row(
		"select * from ${mysqlprefix}chatoperator where vclogin = '" . db_escape_string($login) . "'", $link);
	close_connection($link);
	return $operator;
}

function operator_by_email($mail)
{
	global $mysqlprefix;
	$link = connect();
	$operator = select_one_row(
		"select * from ${mysqlprefix}chatoperator where vcemail = '" . db_escape_string($mail) . "'", $link);
	close_connection($link);
	return $operator;
}

function operator_by_id_($id, $link)
{
	global $mysqlprefix;
	return select_one_row(
		"select * from ${mysqlprefix}chatoperator where operatorid = $id", $link);
}

function operator_by_id($id)
{
	$link = connect();
	$operator = operator_by_id_($id, $link);
	close_connection($link);
	return $operator;
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
	global $mysqlprefix;
	$link = connect();

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

	$query = "select distinct ${mysqlprefix}chatoperator.operatorid, vclogin, vclocalename, vccommonname, istatus, idisabled, (unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time " .
		 "from ${mysqlprefix}chatoperator" .
		 (
		 empty($options['isolated_operator_id']) ? "" :
			sprintf(", ${mysqlprefix}chatgroupoperator " .
				" where ${mysqlprefix}chatoperator.operatorid = ${mysqlprefix}chatgroupoperator.operatorid and ${mysqlprefix}chatgroupoperator.groupid in " .
				"(select g.groupid from ${mysqlprefix}chatgroup g, " .
				"(select distinct parent from ${mysqlprefix}chatgroup, ${mysqlprefix}chatgroupoperator " .
				"where ${mysqlprefix}chatgroup.groupid = ${mysqlprefix}chatgroupoperator.groupid and ${mysqlprefix}chatgroupoperator.operatorid = %u) i " .
				"where g.groupid = i.parent or g.parent = i.parent " .
				")", $options['isolated_operator_id'])
		 ) .
		 " order by " . $orderby;

	$operators = select_multi_assoc($query, $link);
	close_connection($link);
	return $operators;
}

function operator_get_all()
{
	global $mysqlprefix;
	$link = connect();

	$query = "select operatorid, vclogin, vclocalename, vccommonname, istatus, idisabled, (unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time " .
			 "from ${mysqlprefix}chatoperator order by vclogin";
	$operators = select_multi_assoc($query, $link);
	close_connection($link);
	return $operators;
}

function get_operators_from_adjacent_groups($operator)
{
	global $mysqlprefix;
	$link = connect();

	$query = "select distinct ${mysqlprefix}chatoperator.operatorid, vclogin, vclocalename, vccommonname, istatus, idisabled, (unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time " .
		 "from ${mysqlprefix}chatoperator, ${mysqlprefix}chatgroupoperator " .
		 " where ${mysqlprefix}chatoperator.operatorid = ${mysqlprefix}chatgroupoperator.operatorid and ${mysqlprefix}chatgroupoperator.groupid in " .
		 "(select g.groupid from ${mysqlprefix}chatgroup g, " .
		 "(select distinct parent from ${mysqlprefix}chatgroup, ${mysqlprefix}chatgroupoperator " .
		 "where ${mysqlprefix}chatgroup.groupid = ${mysqlprefix}chatgroupoperator.groupid and ${mysqlprefix}chatgroupoperator.operatorid = ".$operator['operatorid'].") i " .
		 "where g.groupid = i.parent or g.parent = i.parent " .
		 ") order by vclogin";

	$operators = select_multi_assoc($query, $link);
	close_connection($link);
	return $operators;
}

function operator_is_online($operator)
{
	global $settings;
	return $operator['time'] < $settings['online_timeout'];
}

function operator_is_available($operator)
{
	global $settings;
	return $operator['istatus'] == 0 && $operator['time'] < $settings['online_timeout'] ? "1" : "";
}

function operator_is_away($operator)
{
	global $settings;
	return $operator['istatus'] != 0 && $operator['time'] < $settings['online_timeout'] ? "1" : "";
}

function operator_is_disabled($operator)
{
	return $operator['idisabled'] == '1';
}

function update_operator($operatorid, $login, $email, $password, $localename, $commonname)
{
	global $mysqlprefix;
	$link = connect();
	$query = sprintf(
		"update ${mysqlprefix}chatoperator set vclogin = '%s',%s vclocalename = '%s', vccommonname = '%s'" .
		", vcemail = '%s', vcjabbername= '%s'" .
		" where operatorid = %s",
		db_escape_string($login),
		($password ? " vcpassword='" . md5($password) . "'," : ""),
		db_escape_string($localename),
		db_escape_string($commonname),
		db_escape_string($email),
		'',
		$operatorid);

	perform_query($query, $link);
	close_connection($link);
}

function update_operator_avatar($operatorid, $avatar)
{
	global $mysqlprefix;
	$link = connect();
	$query = sprintf(
		"update ${mysqlprefix}chatoperator set vcavatar = '%s' where operatorid = %s",
		db_escape_string($avatar), $operatorid);

	perform_query($query, $link);
	close_connection($link);
}

function create_operator_($login, $email, $password, $localename, $commonname, $avatar, $link)
{
	global $mysqlprefix;
	$query = sprintf(
		"insert into ${mysqlprefix}chatoperator (vclogin,vcpassword,vclocalename,vccommonname,vcavatar,vcemail,vcjabbername) values ('%s','%s','%s','%s','%s','%s','%s')",
		db_escape_string($login),
		md5($password),
		db_escape_string($localename),
		db_escape_string($commonname),
		db_escape_string($avatar),
		db_escape_string($email), '');

	perform_query($query, $link);
	$id = db_insert_id($link);

	return select_one_row("select * from ${mysqlprefix}chatoperator where operatorid = $id", $link);
}

function create_operator($login, $email, $password, $localename, $commonname, $avatar)
{
	$link = connect();
	$newop = create_operator_($login, $email, $password, $localename, $commonname, $avatar, $link);
	close_connection($link);
	return $newop;
}

function notify_operator_alive($operatorid, $istatus)
{
	global $mysqlprefix;
	$link = connect();
	perform_query("update ${mysqlprefix}chatoperator set istatus = $istatus, dtmlastvisited = CURRENT_TIMESTAMP where operatorid = $operatorid", $link);
	close_connection($link);
}

function has_online_operators($groupid = "")
{
	global $settings, $mysqlprefix;
	loadsettings();
	$link = connect();
	$query = "select count(*) as total, min(unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time from ${mysqlprefix}chatoperator";
	if ($groupid) {
		$query .= ", ${mysqlprefix}chatgroupoperator, ${mysqlprefix}chatgroup where ${mysqlprefix}chatgroup.groupid = ${mysqlprefix}chatgroupoperator.groupid and " .
				  "(${mysqlprefix}chatgroup.groupid = $groupid or ${mysqlprefix}chatgroup.parent = $groupid) and ${mysqlprefix}chatoperator.operatorid = " .
				  "${mysqlprefix}chatgroupoperator.operatorid and istatus = 0";
	} else {
		if ($settings['enablegroups'] == 1) {
			$query .= ", ${mysqlprefix}chatgroupoperator where ${mysqlprefix}chatoperator.operatorid = " .
				"${mysqlprefix}chatgroupoperator.operatorid and istatus = 0";
		} else {
			$query .= " where istatus = 0";
		}
	}
	$row = select_one_row($query, $link);
	close_connection($link);
	return $row['time'] < $settings['online_timeout'] && $row['total'] > 0;
}

function is_operator_online($operatorid, $link)
{
	global $settings, $mysqlprefix;
	loadsettings_($link);
	$query = "select count(*) as total, min(unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time " .
			 "from ${mysqlprefix}chatoperator where operatorid = $operatorid";
	$row = select_one_row($query, $link);
	return $row['time'] < $settings['online_timeout'] && $row['total'] == 1;
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
	global $page, $webimroot, $settings, $mysqlprefix;
	loadsettings();

	$operator_in_isolation = in_isolation($operator);

	$list_options = $operator_in_isolation?array('isolated_operator_id' => $operator['operatorid']):array();
	$operators = get_operators_list($list_options);
	$operatorscount = count($operators);

	$link = connect();

	$groupscount = 0;
	$groups = array();
	if ($settings['enablegroups'] == "1") {
		$groupslist = $operator_in_isolation?get_groups_for_operator($link, $operator, true):get_groups($link, true);
		foreach ($groupslist as $group) {
			if ($group['inumofagents'] == 0) {
				continue;
			}
			$groups[] = $group;
		}
		$groupscount = count($groups);
	}
	close_connection($link);

	prepare_pagination(max($operatorscount, $groupscount), 8);
	$p = $page['pagination'];
	$limit = $p['limit'];

	$operators = array_slice($operators, $p['start'], $p['end'] - $p['start']);
	$groups = array_slice($groups, $p['start'], $p['end'] - $p['start']);

	$agent_list = "";
	$params = array('thread' => $threadid, 'token' => $token);
	foreach ($operators as $agent) {
		$params['nextAgent'] = $agent['operatorid'];
		$status = $agent['time'] < $settings['online_timeout']
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
	if ($settings['enablegroups'] == "1") {
		$params = array('thread' => $threadid, 'token' => $token);
		foreach ($groups as $group) {
			$params['nextGroup'] = $group['groupid'];
			$status = $group['ilastseen'] !== NULL && $group['ilastseen'] < $settings['online_timeout']
					? getlocal("char.redirect.operator.online_suff")
					: ($group['ilastseenaway'] !== NULL && $group['ilastseenaway'] < $settings['online_timeout']
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
	global $settings, $can_administrate;
	loadsettings();
	return (!is_capable($can_administrate, $operator) && $settings['enablegroups'] && $settings['enablegroupsisolation']);
}

function prepare_menu($operator, $hasright = true)
{
	global $page, $settings, $can_administrate;
	$page['operator'] = topage(get_operator_name($operator));
	if ($hasright) {
		loadsettings();
		$page['showban'] = $settings['enableban'] == "1";
		$page['showstat'] = $settings['enablestatistics'] == "1";
		$page['showadmin'] = is_capable($can_administrate, $operator);
		$page['currentopid'] = $operator['operatorid'];
	}
}

function get_all_groups($link)
{
	global $mysqlprefix;
	$query = "select ${mysqlprefix}chatgroup.groupid as groupid, parent, vclocalname, vclocaldescription from ${mysqlprefix}chatgroup order by vclocalname";
	return get_sorted_child_groups_(select_multi_assoc($query, $link));
}

function get_all_groups_for_operator($operator, $link)
{
	global $mysqlprefix;
	$query = "select g.groupid as groupid, g.parent, g.vclocalname, g.vclocaldescription " .
		 "from ${mysqlprefix}chatgroup g, " .
		 "(select distinct parent from ${mysqlprefix}chatgroup, ${mysqlprefix}chatgroupoperator " .
		 "where ${mysqlprefix}chatgroup.groupid = ${mysqlprefix}chatgroupoperator.groupid and ${mysqlprefix}chatgroupoperator.operatorid = ".$operator['operatorid'].") i " .
		 "where g.groupid = i.parent or g.parent = i.parent " .
		 "order by vclocalname";
	return get_sorted_child_groups_(select_multi_assoc($query, $link));
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

function get_groups_($link, $checkaway, $operator, $order = NULL)
{
	global $mysqlprefix;

	if($order){
		switch($order['by']){
			case 'weight':
				$orderby = "iweight";
				break;
			case 'lastseen':
				$orderby = "ilastseen";
				break;
			default:
				$orderby = "${mysqlprefix}chatgroup.vclocalname";
		}
		$orderby = sprintf(" IF(ISNULL(${mysqlprefix}chatgroup.parent),CONCAT('_',%s),'') %s, ${mysqlprefix}chatgroup.iweight ",
					$orderby,
					($order['desc']?'DESC':'ASC'));
	}else{
		$orderby = "iweight, vclocalname";
	}

	$query = "select ${mysqlprefix}chatgroup.groupid as groupid, ${mysqlprefix}chatgroup.parent as parent, vclocalname, vclocaldescription, iweight" .
			 ", (SELECT count(*) from ${mysqlprefix}chatgroupoperator where ${mysqlprefix}chatgroup.groupid = " .
			 "${mysqlprefix}chatgroupoperator.groupid) as inumofagents" .
			 ", (SELECT min(unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time " .
			 "from ${mysqlprefix}chatgroupoperator, ${mysqlprefix}chatoperator where istatus = 0 and " .
			 "${mysqlprefix}chatgroup.groupid = ${mysqlprefix}chatgroupoperator.groupid " .
			 "and ${mysqlprefix}chatgroupoperator.operatorid = ${mysqlprefix}chatoperator.operatorid) as ilastseen" .
			 ($checkaway
					 ? ", (SELECT min(unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time " .
					   "from ${mysqlprefix}chatgroupoperator, ${mysqlprefix}chatoperator where istatus <> 0 and " .
					   "${mysqlprefix}chatgroup.groupid = ${mysqlprefix}chatgroupoperator.groupid " .
					   "and ${mysqlprefix}chatgroupoperator.operatorid = ${mysqlprefix}chatoperator.operatorid) as ilastseenaway"
					 : ""
			 ) .
			 " from ${mysqlprefix}chatgroup" .
			 ($operator
					 ? ", (select distinct parent from ${mysqlprefix}chatgroup, ${mysqlprefix}chatgroupoperator " .
					   "where ${mysqlprefix}chatgroup.groupid = ${mysqlprefix}chatgroupoperator.groupid and ${mysqlprefix}chatgroupoperator.operatorid = ".$operator['operatorid'].") i " .
					   "where ${mysqlprefix}chatgroup.groupid = i.parent or ${mysqlprefix}chatgroup.parent = i.parent "
					 : ""
			 ) .
			 " order by " . $orderby;
	return get_sorted_child_groups_(select_multi_assoc($query, $link));
}

function get_groups($link, $checkaway)
{
	return get_groups_($link, $checkaway, NULL);
}

function get_groups_for_operator($link, $operator, $checkaway)
{
	return get_groups_($link, $checkaway, $operator);
}

function get_sorted_groups($link, $order)
{
	return get_groups_($link, true, NULL, $order);
}

function get_operator_groupids($operatorid)
{
	global $mysqlprefix;
	$link = connect();
	$query = "select groupid from ${mysqlprefix}chatgroupoperator where operatorid = $operatorid";
	$result = select_multi_assoc($query, $link);
	close_connection($link);
	return $result;
}

?>
