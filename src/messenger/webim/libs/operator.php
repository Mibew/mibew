<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2009 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

$can_administrate = 0;
$can_takeover = 1;
$can_viewthreads = 2;

$can_count = 3;

$permission_ids = array(
	$can_administrate => "admin",
	$can_takeover => "takeover",
	$can_viewthreads => "viewthreads"
);

function operator_by_login($login) {
	$link = connect();
	$operator = select_one_row(
		 "select * from chatoperator where vclogin = '".mysql_real_escape_string($login)."'", $link );
	mysql_close($link);
	return $operator;
}

function operator_by_id_($id,$link) {
	return select_one_row(
		 "select * from chatoperator where operatorid = $id", $link );
}

function operator_by_id($id) {
	$link = connect();
	$operator = operator_by_id_($id,$link);
	mysql_close($link);
	return $operator;
}

function get_operators() {
	$link = connect();

	$query = "select * from chatoperator order by vclogin";
	$result = select_multi_assoc($query, $link);
	mysql_close($link);
	return $result;
}

function update_operator($operatorid,$login,$password,$localename,$commonname) {
	$link = connect();
	$query = sprintf(
		"update chatoperator set vclogin = '%s',%s vclocalename = '%s', vccommonname = '%s'".
		", vcjabbername= '%s'".
		" where operatorid = %s",
		mysql_real_escape_string($login),
		($password ? " vcpassword='".md5($password)."'," : ""),
		mysql_real_escape_string($localename),
		mysql_real_escape_string($commonname),
		'',
		$operatorid );

	perform_query($query,$link);
	mysql_close($link);
}

function update_operator_avatar($operatorid,$avatar) {
	$link = connect();
	$query = sprintf(
		"update chatoperator set vcavatar = '%s' where operatorid = %s",
		mysql_real_escape_string($avatar), $operatorid );

	perform_query($query,$link);
	mysql_close($link);
}

function create_operator_($login,$password,$localename,$commonname,$avatar,$link) {
	$query = sprintf(
		"insert into chatoperator (vclogin,vcpassword,vclocalename,vccommonname,vcavatar,vcjabbername) values ('%s','%s','%s','%s','%s','%s')",
			mysql_real_escape_string($login),
			md5($password),
			mysql_real_escape_string($localename),
			mysql_real_escape_string($commonname),
			mysql_real_escape_string($avatar), '');

	perform_query($query,$link);
	$id = mysql_insert_id($link);

	return select_one_row("select * from chatoperator where operatorid = $id", $link );
}

function create_operator($login,$password,$localename,$commonname,$avatar) {
	$link = connect();
	$newop = create_operator_($login,$password,$localename,$commonname,$avatar,$link);
	mysql_close($link);
	return $newop;
}

function notify_operator_alive($operatorid) {
	$link = connect();
	perform_query("update chatoperator set dtmlastvisited = CURRENT_TIMESTAMP where operatorid = $operatorid",$link);
	mysql_close($link);
}

function has_online_operators() {
	global $settings;
	loadsettings();
	$link = connect();
	$row = select_one_row("select min(unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time from chatoperator",$link);
	mysql_close($link);
	return $row['time'] < $settings['online_timeout'];
}

function get_operator_name($operator) {
	global $home_locale, $current_locale;
	if( $home_locale == $current_locale )
		return $operator['vclocalename'];
	else
		return $operator['vccommonname'];
}

function generate_button($title,$locale,$style,$inner,$showhost,$forcesecure) {
	$link = get_app_location($showhost,$forcesecure)."/client.php". ($locale?"?locale=$locale" : "").($style ? ($locale?"&amp;":"?")."style=$style" : "");
	$temp = get_popup($link, "'$link".($locale||$style?"&amp;":"?")."url='+escape(document.location.href)+'&amp;referrer='+escape(document.referrer)",
			$inner, $title, "webim", "toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1" );
	return "<!-- webim button -->".$temp."<!-- / webim button -->";
}

function check_login() {
	global $webimroot;
	if( !isset( $_SESSION['operator'] ) ) {
		if( isset($_COOKIE['webim_lite']) ) {
			list($login,$pwd) = split(",", $_COOKIE['webim_lite'], 2);
			$op = operator_by_login($login);
			if( $op && isset($pwd) && isset($op['vcpassword']) && md5($op['vcpassword']) == $pwd ) {
				$_SESSION['operator'] = $op;
				return $op;
			}
		}
		$requested = $_SERVER['PHP_SELF'];
		if($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['QUERY_STRING']) {
			$requested .= "?".$_SERVER['QUERY_STRING'];
		}
		$_SESSION['backpath'] = $requested;
		header("Location: $webimroot/operator/login.php");
		exit;
	}
	return $_SESSION['operator'];
}

function get_logged_in() {
	return isset( $_SESSION['operator'] ) ? $_SESSION['operator'] : FALSE;
}

function login_operator($operator,$remember) {
	global $webimroot;
	$_SESSION['operator'] = $operator;
	if( $remember ) {
		$value = $operator['vclogin'].",".md5($operator['vcpassword']);
		setcookie('webim_lite', $value, time()+60*60*24*1000, "$webimroot/");

	} else if( isset($_COOKIE['webim_lite']) ) {
		setcookie('webim_lite', '', time() - 3600, "$webimroot/");
	}
}

function logout_operator() {
	global $webimroot;
	$_SESSION['operator'] = NULL;
	$_SESSION['backpath'] = NULL;
	if( isset($_COOKIE['webim_lite']) ) {
		setcookie('webim_lite', '', time() - 3600, "$webimroot/");
	}
}

function get_redirect_links($threadid,$token) {
	global $page, $webimroot;
	$found = get_operators();
	setup_pagination($found);

	$agent_list = "";
	$params = array('thread' => $threadid, 'token' => $token);
	for( $indagent = 0; $indagent < count($page['pagination.items']); $indagent += 1 ) {
		$agent = $page['pagination.items'][$indagent];
		$params['nextAgent'] = $agent['operatorid'];
		$agent_list .= "<li><a href=\"".add_params($webimroot."/operator/redirect.php",$params)."\" title=\"".topage($agent['vclocalename'])."\">".topage($agent['vclocalename'])."</a>";
	}
	return $agent_list;
}

$permission_list = array();

function get_permission_list() {
	global $permission_list, $permission_ids;
	if(count($permission_list) == 0) {
		foreach($permission_ids as $permid) {
			$permission_list[] = array(
				'id' => $permid,
				'descr' => getlocal("permission.$permid")
			);
		}
	}
	return $permission_list;
}

function is_capable($perm,$operator) {
	$permissions = $operator && isset($operator['iperm']) ? $operator['iperm'] : 0;
	return $perm >= 0 && $perm < 32 && ($permissions & (1 << $perm)) != 0;
}

function prepare_menu($operator,$hasright=true) {
	global $page, $settings, $can_administrate;
	$page['operator'] = topage(get_operator_name($operator));
	if($hasright) {
		loadsettings();
		$page['showban'] = $settings['enableban'] == "1";
		$page['showdep'] = $settings['enabledepartments'] == "1";
		$page['showadmin'] = is_capable($can_administrate, $operator);
	}
}

?>
