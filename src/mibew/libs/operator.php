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

$remember_cookie_name = 'mibew_operator';

$can_administrate = 0;
$can_takeover = 1;
$can_viewthreads = 2;
$can_modifyprofile = 3;
$can_count = 4;
$can_viewnotifications = 5;

$permission_ids = array(
	$can_administrate => "admin",
	$can_takeover => "takeover",
	$can_viewthreads => "viewthreads",
	$can_modifyprofile => "modifyprofile",
	$can_viewnotifications => "viewnotifications"
);

function operator_by_login($login)
{
	global $mysqlprefix;
	$link = connect();
	$operator = select_one_row(
		"select * from ${mysqlprefix}chatoperator where vclogin = '" . mysql_real_escape_string($login, $link) . "'", $link);
	mysql_close($link);
	return $operator;
}

function operator_by_email($mail)
{
	global $mysqlprefix;
	$link = connect();
	$operator = select_one_row(
		"select * from ${mysqlprefix}chatoperator where vcemail = '" . mysql_real_escape_string($mail) . "'", $link);
	mysql_close($link);
	return $operator;
}

function operator_by_id_($id, $link)
{
	global $mysqlprefix;
	return select_one_row(
		"select * from ${mysqlprefix}chatoperator where operatorid = " . intval($id), $link);
}

function operator_by_id($id)
{
	$link = connect();
	$operator = operator_by_id_($id, $link);
	mysql_close($link);
	return $operator;
}

function operator_get_all()
{
	global $mysqlprefix;
	$link = connect();

	$query = "select operatorid, vclogin, vclocalename, vccommonname, istatus, (unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time " .
			 "from ${mysqlprefix}chatoperator order by vclogin";
	$operators = select_multi_assoc($query, $link);
	mysql_close($link);
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

function update_operator($operatorid, $login, $email, $jabber, $password, $localename, $commonname, $notify)
{
	global $mysqlprefix;
	$link = connect();
	$query = sprintf(
		"update ${mysqlprefix}chatoperator set vclogin = '%s',%s vclocalename = '%s', vccommonname = '%s'" .
		", vcemail = '%s', vcjabbername= '%s', inotify = %s" .
		" where operatorid = %s",
		mysql_real_escape_string($login, $link),
		($password ? " vcpassword='" . mysql_real_escape_string(calculate_password_hash($login, $password), $link) . "'," : ""),
		mysql_real_escape_string($localename, $link),
		mysql_real_escape_string($commonname, $link),
		mysql_real_escape_string($email, $link),
		mysql_real_escape_string($jabber, $link),
		intval($notify),
		intval($operatorid));

	perform_query($query, $link);
	mysql_close($link);
}

function update_operator_avatar($operatorid, $avatar)
{
	global $mysqlprefix;
	$link = connect();
	$query = sprintf(
		"update ${mysqlprefix}chatoperator set vcavatar = '%s' where operatorid = %s",
		mysql_real_escape_string($avatar, $link), intval($operatorid));

	perform_query($query, $link);
	mysql_close($link);
}

function create_operator_($login, $email, $jabber, $password, $localename, $commonname, $notify, $avatar, $link)
{
	global $mysqlprefix;
	$query = sprintf(
		"insert into ${mysqlprefix}chatoperator (vclogin,vcpassword,vclocalename,vccommonname,vcavatar,vcemail,vcjabbername,inotify) values ('%s','%s','%s','%s','%s','%s','%s',%s)",
		mysql_real_escape_string($login, $link),
		mysql_real_escape_string(calculate_password_hash($login, $password), $link),
		mysql_real_escape_string($localename, $link),
		mysql_real_escape_string($commonname, $link),
		mysql_real_escape_string($avatar, $link),
		mysql_real_escape_string($email, $link),
		mysql_real_escape_string($jabber, $link),
		intval($notify));

	perform_query($query, $link);
	$id = mysql_insert_id($link);

	return select_one_row("select * from ${mysqlprefix}chatoperator where operatorid = " . intval($id), $link);
}

function create_operator($login, $email, $jabber, $password, $localename, $commonname, $notify, $avatar)
{
	$link = connect();
	$newop = create_operator_($login, $email, $jabber, $password, $localename, $commonname, $notify, $avatar, $link);
	mysql_close($link);
	return $newop;
}

function notify_operator_alive($operatorid, $istatus)
{
	global $mysqlprefix;
	$link = connect();
	perform_query(sprintf("update ${mysqlprefix}chatoperator set istatus = %s, dtmlastvisited = CURRENT_TIMESTAMP where operatorid = %s", intval($istatus), intval($operatorid)), $link);
	mysql_close($link);
}

function has_online_operators($groupid = "")
{
	global $settings, $mysqlprefix;
	loadsettings();
	$link = connect();
	$query = "select count(*) as total, min(unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time from ${mysqlprefix}chatoperator";
	if ($groupid) {
		$query .= ", ${mysqlprefix}chatgroupoperator where groupid = " . intval($groupid) . " and ${mysqlprefix}chatoperator.operatorid = " .
				  "${mysqlprefix}chatgroupoperator.operatorid and istatus = 0";
	} else {
		$query .= " where istatus = 0";
	}
	$row = select_one_row($query, $link);
	mysql_close($link);
	return $row['time'] < $settings['online_timeout'] && $row['total'] > 0;
}

function is_operator_online($operatorid, $link)
{
	global $settings, $mysqlprefix;
	loadsettings_($link);
	$query = "select count(*) as total, min(unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time " .
			 "from ${mysqlprefix}chatoperator where operatorid = " . intval($operatorid);
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
		$infix = '&';
	return "$link$infix$pv";
}

function check_login($redirect = true)
{
	global $mibewroot, $mysqlprefix, $remember_cookie_name;
	if (!isset($_SESSION["${mysqlprefix}operator"])) {
		if (isset($_COOKIE[$remember_cookie_name])) {
			list($login, $pwd) = preg_split('/\x0/', base64_decode($_COOKIE[$remember_cookie_name]), 2);
			$op = operator_by_login($login);
			if ($op && isset($pwd) && isset($op['vcpassword']) && calculate_password_hash($op['vclogin'], $op['vcpassword']) == $pwd) {
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
			header("Location: $mibewroot/operator/login.php");
			exit;
		} else {
			return null;
		}
	}
	return $_SESSION["${mysqlprefix}operator"];
}

function check_permissions()
{
	$check = false;
	if (func_num_args() > 1) {
	    $args = func_get_args();
	    $operator = array_shift($args);
	    foreach ($args as $permission) {
		$check = $check || is_capable($permission, $operator);
	    }
	}
	if (!$check) {
	    die("Permission denied.");
	}
}

function get_logged_in()
{
	global $mysqlprefix;
	return isset($_SESSION["${mysqlprefix}operator"]) ? $_SESSION["${mysqlprefix}operator"] : FALSE;
}

function login_operator($operator, $remember, $https = FALSE)
{
	global $mibewroot, $mysqlprefix, $remember_cookie_name;
	$_SESSION["${mysqlprefix}operator"] = $operator;
	if ($remember) {
		$value = base64_encode($operator['vclogin'] . "\x0" . calculate_password_hash($operator['vclogin'], $operator['vcpassword']));
		setcookie($remember_cookie_name, $value, time() + 60 * 60 * 24 * 1000, "$mibewroot/", NULL, $https, TRUE);

	} else if (isset($_COOKIE[$remember_cookie_name])) {
		setcookie($remember_cookie_name, '', time() - 3600, "$mibewroot/");
	}
}

function logout_operator()
{
	global $mibewroot, $mysqlprefix, $remember_cookie_name;
	unset($_SESSION["${mysqlprefix}operator"]);
	unset($_SESSION['backpath']);
	if (isset($_COOKIE[$remember_cookie_name])) {
		setcookie($remember_cookie_name, '', time() - 3600, "$mibewroot/");
	}
}

function setup_redirect_links($threadid, $token)
{
	global $page, $mibewroot, $settings, $mysqlprefix;
	loadsettings();
	$link = connect();

	$operatorscount = db_rows_count("${mysqlprefix}chatoperator", array(), "", $link);

	$groupscount = 0;
	$groups = array();
	if ($settings['enablegroups'] == "1") {
		foreach (get_groups($link, true) as $group) {
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

	$operators = select_multi_assoc(db_build_select(
										"operatorid, vclogin, vclocalename, vccommonname, istatus, (unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time",
										"${mysqlprefix}chatoperator", array(), "order by vclogin " . $limit), $link);

	$groups = array_slice($groups, $p['start'], $p['end'] - $p['start']);
	mysql_close($link);

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
		$agent_list .= "<li><a href=\"" . add_params($mibewroot . "/operator/redirect.php", $params) .
					   "\" title=\"" . safe_htmlspecialchars(topage(get_operator_name($agent))) . "\">" .
					   safe_htmlspecialchars(topage(get_operator_name($agent))) .
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
			$group_list .= "<li><a href=\"" . add_params($mibewroot . "/operator/redirect.php", $params) .
						   "\" title=\"" . safe_htmlspecialchars(topage(get_group_name($group))) . "\">" .
						   safe_htmlspecialchars(topage(get_group_name($group))) .
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

function prepare_menu($operator, $hasright = true)
{
	global $page, $settings, $can_administrate,$can_viewnotifications;
	$page['operator'] = topage(get_operator_name($operator));
	if ($hasright) {
		loadsettings();
		$page['showban'] = $settings['enableban'] == "1";
		$page['showgroups'] = $settings['enablegroups'] == "1";
		$page['showstat'] = $settings['enablestatistics'] == "1";
		$page['shownotifications'] = is_capable($can_viewnotifications, $operator);
		$page['showadmin'] = is_capable($can_administrate, $operator);
		$page['currentopid'] = $operator['operatorid'];
	}
}

function get_all_groups($link)
{
	global $mysqlprefix;
	$query = "select ${mysqlprefix}chatgroup.groupid as groupid, vclocalname, vclocaldescription from ${mysqlprefix}chatgroup order by vclocalname";
	return select_multi_assoc($query, $link);
}

function get_groups($link, $checkaway)
{
	global $mysqlprefix;
	$query = "select ${mysqlprefix}chatgroup.groupid as groupid, vclocalname, vclocaldescription" .
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
			 " from ${mysqlprefix}chatgroup order by vclocalname";
	return select_multi_assoc($query, $link);
}

function get_operator_groupids($operatorid)
{
	global $mysqlprefix;
	$link = connect();
	$query = "select groupid from ${mysqlprefix}chatgroupoperator where operatorid = " . intval($operatorid);
	$result = select_multi_assoc($query, $link);
	mysql_close($link);
	return $result;
}

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