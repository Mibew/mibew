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

// Import namespaces and classes of the core
use Mibew\Database;
use Mibew\EventDispatcher;
use Mibew\Settings;

/**
 * Name of the cookie to remember an operator
 */
define('REMEMBER_OPERATOR_COOKIE_NAME', 'mibew_operator');

/** Permissions constants */

/**
 * Operator can administer Mibew instalation
 */
define('CAN_ADMINISTRATE', 0);

/**
 * Operator can take over threads
 */
define('CAN_TAKEOVER', 1);

/**
 * Operator can view threads of other operators
 */
define('CAN_VIEWTHREADS', 2);

/**
 * Operator can modify own profile
 */
define('CAN_MODIFYPROFILE', 3);

/** End of permissions constants */

/**
 * Map numerical permissions ids onto string names.
 * @return array Associativa array whose keys are numerical permission ids and
 * values are string permission names.
 */
function permission_ids() {
	return array(
		CAN_ADMINISTRATE => "admin",
		CAN_TAKEOVER => "takeover",
		CAN_VIEWTHREADS => "viewthreads",
		CAN_MODIFYPROFILE => "modifyprofile"
	);
}

/**
 * Set new permissions to operator
 * @param int $operator_id Operator ID
 * @param int $perm New permissions value
 */
function update_operator_permissions($operator_id, $perm) {
	$db = Database::getInstance();
	$db->query(
		"update {chatoperator} set iperm = ? where operatorid = ?",
		array($perm, $operator_id)
	);
}

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
		array('return_rows' => Database::RETURN_ONE_ROW)
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
 * Load operator info by specified operators code
 * @param string $code Operators code
 * @return array|boolean Operators info array or boolean false if there is no
 * operator with specified code.
 */
function operator_by_code($code) {
	$db = Database::getInstance();
	return $db->query(
		"select * from {chatoperator} where code = ?",
		array($code),
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

	$query = "select distinct {chatoperator}.operatorid, vclogin, vclocalename, vccommonname, code, istatus, idisabled, (:now - dtmlastvisited) as time " .
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

	$values = array(
		':now' => time()
	);

	if (! empty($options['isolated_operator_id'])) {
		$values[':operatorid'] = $options['isolated_operator_id'];
	}

	$operators = $db->query(
		$query,
		$values,
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);

	return $operators;
}

function operator_get_all() {
	$db = Database::getInstance();
	return $operators = $db->query(
		"select operatorid, vclogin, vclocalename, vccommonname, istatus, code, idisabled, " .
		"(:now - dtmlastvisited) as time " .
		"from {chatoperator} order by vclogin",
		array(':now' => time()),
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
}

function get_operators_from_adjacent_groups($operator)
{
	$db = Database::getInstance();
	$query = "select distinct {chatoperator}.operatorid, vclogin, vclocalename,vccommonname, " .
		"istatus, idisabled, code, " .
		"(:now - dtmlastvisited) as time " .
		"from {chatoperator}, {chatgroupoperator} " .
		"where {chatoperator}.operatorid = {chatgroupoperator}.operatorid " .
		"and {chatgroupoperator}.groupid in " .
		"(select g.groupid from {chatgroup} g, " .
		"(select distinct parent from {chatgroup}, {chatgroupoperator} " .
		"where {chatgroup}.groupid = {chatgroupoperator}.groupid " .
		"and {chatgroupoperator}.operatorid = :operatorid) i " .
		"where g.groupid = i.parent or g.parent = i.parent " .
		") order by vclogin";
	
	
	return $db->query(
		$query,
		array(
			':operatorid' => $operator['operatorid'],
			':now' => time()
		),
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

/**
 * Update existing operator's info.
 *
 * If $password argument is empty operators password will not be changed.
 *
 * @param int $operatorid ID of operator to update
 * @param string $login Operator's login
 * @param string $email Operator's
 * @param string $password Operator's password
 * @param string $localename Operator's local name
 * @param string $commonname Operator's international name
 * @param string $code Operator's code which use to start chat with specified
 * operator
 */
function update_operator($operatorid, $login, $email, $password, $localename, $commonname, $code) {
	$db = Database::getInstance();
	$values = array(
		':login' => $login,
		':localname' => $localename,
		':commonname' => $commonname,
		':email' => $email,
		':jabbername' => '',
		':operatorid' => $operatorid,
		':code' => $code
	);
	if ($password) {
		$values[':password'] = calculate_password_hash($login, $password);
	}
	$db->query(
		"update {chatoperator} set vclogin = :login, " .
		($password ? " vcpassword=:password, " : "") .
		"vclocalename = :localname, vccommonname = :commonname, " .
		"vcemail = :email, code = :code, vcjabbername= :jabbername " .
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

/**
 * Create new operator
 *
 * @param string $login Operator's login
 * @param string $email Operator's
 * @param string $password Operator's password
 * @param string $localename Operator's local name
 * @param string $commonname Operator's international name
 * @param string $avatar Operator's avatar
 * @param string $code Operator's code which use to start chat with specified
 * operator
 * @return array Operator's array
 */
function create_operator($login, $email, $password, $localename, $commonname, $avatar, $code) {
	$db = Database::getInstance();
	$db->query(
		"INSERT INTO {chatoperator} (" .
			"vclogin, vcpassword, vclocalename, vccommonname, vcavatar, " .
			"vcemail, code, vcjabbername " .
		") VALUES (" .
			":login, :pass, :localename, :commonname, :avatar, " .
			":email, :code, :jabber".
		")",
		array(
			':login' => $login,
			':pass' => calculate_password_hash($login, $password),
			':localename' => $localename,
			':commonname' => $commonname,
			':avatar' => $avatar,
			':email' => $email,
			':code' => $code,
			':jabber' => ''
		)
	);

	$id = $db->insertedId();

	return $db->query(
		"select * from {chatoperator} where operatorid = ?",
		array($id),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
}

/**
 * Delete operator
 *
 * This function remove operator and associations with groups for this operator
 * from datatabse.
 * It trigger 'operatorDelete' event and pass to event listeners associative
 * array with following keys:
 *  - 'id': int, deleted operator ID.
 *
 * @param int $operator_id Operator ID
 */
function delete_operator($operator_id) {
	$db = Database::getInstance();
	$db->query(
		"delete from {chatgroupoperator} where operatorid = ?",
		array($operator_id)
	);
	$db->query(
		"delete from {chatoperator} where operatorid = ?",
		array($operator_id)
	);

	// Trigger 'operatorDelete' event
	$dispatcher = EventDispatcher::getInstance();
	$args = array('id' => $operator_id);
	$dispatcher->triggerEvent('operatorDelete', $args);
}

/**
 * Set current status of the operator('available' or 'away')
 *
 * @param int $operatorid Id of the operator
 * @param int $istatus Operator status: '0' means 'available' and '1' means
 * 'away'
 */
function notify_operator_alive($operatorid, $istatus)
{
	global $session_prefix;
	$db = Database::getInstance();
	$db->query(
		"update {chatoperator} set istatus = :istatus, dtmlastvisited = :now " .
		"where operatorid = :operatorid",
		array(
			':istatus' => $istatus,
			':now' => time(),
			':operatorid' => $operatorid
		)
	);
	if (isset($_SESSION[$session_prefix."operator"])) {
		if ($_SESSION[$session_prefix."operator"]['operatorid'] == $operatorid) {
			$_SESSION[$session_prefix."operator"]['istatus'] = $istatus;
		}
	}
}

/**
 * Indicates if at least one operator of the group is online
 * @param int $groupid Id of the group
 * @return boolean true if the group have online operators and false otherwise
 */
function has_online_operators($groupid = "")
{
	$db = Database::getInstance();

	$query = "select count(*) as total, min(:now - dtmlastvisited) as time from {chatoperator}";
	$values = array(':now' => time());
	if ($groupid) {
		$query .= ", {chatgroupoperator}, {chatgroup} where {chatgroup}.groupid = {chatgroupoperator}.groupid and " .
			"({chatgroup}.groupid = :groupid or {chatgroup}.parent = :groupid) and {chatoperator}.operatorid = " .
			"{chatgroupoperator}.operatorid and istatus = 0";
		$values[':groupid'] = $groupid;
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
		$values,
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
	return $row['time'] < Settings::get('online_timeout') && $row['total'] > 0;
}

/**
 * Indicates if operator online or not
 *
 * @param int $operatorid Id of the operator
 * @return boolean true if operator is online and false otherwise
 */
function is_operator_online($operatorid)
{
	$db = Database::getInstance();
	$row = $db->query(
		"select count(*) as total, " .
		"min(:now - dtmlastvisited) as time " .
		"from {chatoperator} where operatorid = :operatorid",
		array(
			':now' => time(),
			':operatorid' => $operatorid
		),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
	
	return $row['time'] < Settings::get('online_timeout') && $row['total'] == 1;
}

/**
 * Returns name of the operator. Choose between vclocalname and vccommonname
 *
 * @global string $home_locale Code of the operator's home locale
 * @global string $current_locale Code of the current locale
 * @param array $operator Operator's array
 * @return string Operator's name
 */
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

/**
 * Check if operator is logged in or not.
 *
 * It can automatically redirect operators, who not logged in to the login page.
 * Triggers 'operatorCheckLoginFail' event when check failed and pass into it
 * an associative array with folloing keys:
 *  - 'requested_page': string, page where login check was failed.
 *
 * @global string $mibewroot Path of the mibew instalation from server root.
 * It defined in libs/config.php
 * @global string $session_prefix Use as prefix for all session variables to
 * allow many instalation of the mibew messenger at one server. It defined in
 * libs/common/constants.php
 *
 * @param boolean $redirect Indicates if operator should be redirected to
 * login page. Default value is true.
 * @return null|array Array with operator info if operator is logged in and
 * null otherwise.
 */
function check_login($redirect = true) {
	global $mibewroot, $session_prefix;
	if (!isset($_SESSION[$session_prefix."operator"])) {
		if (isset($_COOKIE[REMEMBER_OPERATOR_COOKIE_NAME])) {
			list($login, $pwd) = preg_split('/\x0/', base64_decode($_COOKIE[REMEMBER_OPERATOR_COOKIE_NAME]), 2);
			$op = operator_by_login($login);
			if ($op && isset($pwd) && isset($op['vcpassword']) && calculate_password_hash($op['vclogin'], $op['vcpassword']) == $pwd && !operator_is_disabled($op)) {
				$_SESSION[$session_prefix."operator"] = $op;
				return $op;
			}
		}

		// Get requested page
		$requested = $_SERVER['PHP_SELF'];
		if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['QUERY_STRING']) {
			$requested .= "?" . $_SERVER['QUERY_STRING'];
		}

		// Trigger fail event
		$args = array('requested_page' => $requested);
		$dispatcher = EventDispatcher::getInstance();
		$dispatcher->triggerEvent('operatorCheckLoginFail', $args);

		// Redirect operator if need
		if ($redirect) {
			$_SESSION['backpath'] = $requested;
			header("Location: $mibewroot/operator/login.php");
			exit;
		} else {
			return null;
		}
	}
	return $_SESSION[$session_prefix."operator"];
}

// Force the admin to set a password after the installation
function force_password($operator)
{
	global $mibewroot;
	if (check_password_hash($operator['vclogin'], $operator['vcpassword'], '')) {
		header("Location: $mibewroot/operator/operator.php?op=1");
		exit;
	}
}

function get_logged_in()
{
	global $session_prefix;
	return isset($_SESSION[$session_prefix."operator"]) ? $_SESSION[$session_prefix."operator"] : FALSE;
}

/**
 * Log in operator
 *
 * Triggers 'operatorLogin' event after operator logged in and pass to it an
 * associative array with following items:
 *  - 'operator': array of the logged in operator info;
 *  - 'remember': boolean, indicates if system should remember operator.
 *
 * @global string $mibewroot Path of the mibew instalation from server root.
 * It defined in libs/config.php
 * @global string $session_prefix Use as prefix for all session variables to
 * allow many instalation of the mibew messenger at one server. It defined in
 * libs/common/constants.php
 *
 * @param array $operator Operators info
 * @param boolean $remember Indicates if system should remember operator
 * @param boolean $https Indicates if cookie should be flagged as a secure one
 */
function login_operator($operator, $remember, $https = FALSE) {
	global $mibewroot, $session_prefix;
	$_SESSION[$session_prefix."operator"] = $operator;
	if ($remember) {
		$value = base64_encode($operator['vclogin'] . "\x0" . calculate_password_hash($operator['vclogin'], $operator['vcpassword']));
		setcookie(REMEMBER_OPERATOR_COOKIE_NAME, $value, time() + 60 * 60 * 24 * 1000, "$mibewroot/", NULL, $https, TRUE);

	} else if (isset($_COOKIE[REMEMBER_OPERATOR_COOKIE_NAME])) {
		setcookie(REMEMBER_OPERATOR_COOKIE_NAME, '', time() - 3600, "$mibewroot/");
	}

	// Trigger login event
	$args = array(
		'operator' => $operator,
		'remember' => $remember
	);
	$dispatcher = EventDispatcher::getInstance();
	$dispatcher->triggerEvent('operatorLogin', $args);
}

/**
 * Log out current operator
 *
 * Triggers 'operatorLogout' event after operator logged out.
 *
 * @global string $mibewroot Path of the mibew instalation from server root.
 * It defined in libs/config.php
 * @global string $session_prefix Use as prefix for all session variables to
 * allow many instalation of the mibew messenger at one server. It defined in
 * libs/common/constants.php
 */
function logout_operator() {
	global $mibewroot, $session_prefix;
	unset($_SESSION[$session_prefix."operator"]);
	unset($_SESSION['backpath']);
	if (isset($_COOKIE[REMEMBER_OPERATOR_COOKIE_NAME])) {
		setcookie(REMEMBER_OPERATOR_COOKIE_NAME, '', time() - 3600, "$mibewroot/");
	}

	// Trigger logout event
	$dispatcher = EventDispatcher::getInstance();
	$dispatcher->triggerEvent('operatorLogout');
}

function setup_redirect_links($threadid, $operator, $token)
{
	global $page, $mibewroot;

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
		$agent_list .= "<li><a href=\"" . add_params($mibewroot . "/operator/redirect.php", $params) .
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
			$group_list .= "<li><a href=\"" . add_params($mibewroot . "/operator/redirect.php", $params) .
						   "\" title=\"" . topage(get_group_name($group)) . "\">" .
						   topage(get_group_name($group)) .
						   "</a> $status</li>";
		}
	}
	$page['redirectToGroup'] = $group_list;
}

function get_permission_list()
{
	static $permission_list = array();
	if (count($permission_list) == 0) {
		foreach (permission_ids() as $permid) {
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
	return (!is_capable(CAN_ADMINISTRATE, $operator) && Settings::get('enablegroups') && Settings::get('enablegroupsisolation'));
}

function prepare_menu($operator, $hasright = true)
{
	global $page;
	$page['operator'] = topage(get_operator_name($operator));
	if ($hasright) {
		$page['showban'] = Settings::get('enableban') == "1";
		$page['showstat'] = Settings::get('enablestatistics') == "1";
		$page['showadmin'] = is_capable(CAN_ADMINISTRATE, $operator);
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

	$values = array(
		':now' => time()
	);
	$query = "select {chatgroup}.groupid as groupid, {chatgroup}.parent as parent, vclocalname, vclocaldescription, iweight" .
		", (SELECT count(*) from {chatgroupoperator} where {chatgroup}.groupid = " .
		"{chatgroupoperator}.groupid) as inumofagents" .
		", (SELECT min(:now - dtmlastvisited) as time " .
		"from {chatgroupoperator}, {chatoperator} where istatus = 0 and " .
		"{chatgroup}.groupid = {chatgroupoperator}.groupid " .
		"and {chatgroupoperator}.operatorid = {chatoperator}.operatorid) as ilastseen" .
		($checkaway
			? ", (SELECT min(:now - dtmlastvisited) as time " .
			"from {chatgroupoperator}, {chatoperator} where istatus <> 0 and " .
			"{chatgroup}.groupid = {chatgroupoperator}.groupid " .
			"and {chatgroupoperator}.operatorid = {chatoperator}.operatorid) as ilastseenaway"
			: ""
		) .
		" from {chatgroup} ";

	if ($operator) {
		$query .= ", (select distinct parent from {chatgroup}, {chatgroupoperator} " .
			"where {chatgroup}.groupid = {chatgroupoperator}.groupid and {chatgroupoperator}.operatorid = :operatorid) i " .
			"where {chatgroup}.groupid = i.parent or {chatgroup}.parent = i.parent ";
		$values[':operatorid'] = $operator['operatorid'];
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

/**
 * Calculate hashed password value based upon operator's login and password
 *
 * By default function tries to make us of Blowfish encryption algorithm,
 * with salted MD5 as a second possible choice, and unsalted MD5 as a fallback
 * option
 *
 * @param string $login operator's login
 * @param string $password Operator's password (as plain text)
 *
 * @return string hashed password value
 */
function calculate_password_hash($login, $password)
{
	$hash = '*0';
	if (CRYPT_BLOWFISH == 1) {
		if (defined('PHP_VERSION_ID') && (PHP_VERSION_ID > 50306)) {
			$hash = crypt($password, '$2y$08$' . $login);
		}
		else {
			$hash = crypt($password, '$2a$08$' . $login);
		}
        }

	if ( (CRYPT_MD5 == 1) && !strcmp($hash, '*0') ) {
		$hash = crypt($password, '$1$' . $login);
	}

	return strcmp($hash, '*0') ? $hash : md5($password);
}

/**
 * Validate incoming hashed value to be the hashed value of operator's password
 *
 * @param string $login operator's login
 * @param string $password Operator's password (as plain text)
 * @param string $hash incoming hashed value
 *
 * @return boolean true if incoming value is the correct hashed value of
 * operators' password and false otherwise
 */
function check_password_hash($login, $password, $hash)
{
	if (preg_match('/^\$/', $hash)) {
		return !strcmp(calculate_password_hash($login, $password), $hash);
	}
	else {
		return !strcmp(md5($password), $hash);
	}
}

?>