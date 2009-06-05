<?php
/*
 * This file is part of Mibew Messenger project.
 *
 * Copyright (c) 2005-2009 Mibew Messenger Community
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
$can_modifyprofile = 3;

$can_count = 4;

$permission_ids = array(
	$can_administrate => "admin",
	$can_takeover => "takeover",
	$can_viewthreads => "viewthreads",
	$can_modifyprofile => "modifyprofile"
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

function has_online_operators($groupid="") {
	global $settings;
	loadsettings();
	$link = connect();
	$query = "select count(*) as total, min(unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time from chatoperator";
	if($groupid) {
		$query .= ", chatgroupoperator where groupid = $groupid and chatoperator.operatorid = chatgroupoperator.operatorid";
	}
	$row = select_one_row($query,$link);
	mysql_close($link);
	return $row['time'] < $settings['online_timeout'] && $row['total'] > 0;
}

function get_operator_name($operator) {
	global $home_locale, $current_locale;
	if( $home_locale == $current_locale )
		return $operator['vclocalename'];
	else
		return $operator['vccommonname'];
}

function append_query($link,$pv) {
	$infix = '?';
	if( strstr($link,$infix) !== FALSE )
		$infix = '&amp;';
	return "$link$infix$pv";
}

function generate_button($title,$locale,$style,$group,$inner,$showhost,$forcesecure) {
	$link = get_app_location($showhost,$forcesecure)."/client.php";
	if($locale)
		$link = append_query($link, "locale=$locale");
	if($style)
		$link = append_query($link, "style=$style");
	if($group)
		$link = append_query($link, "group=$group");

	$jslink = append_query("'".$link,"url='+escape(document.location.href)+'&amp;referrer='+escape(document.referrer)");	
	$temp = get_popup($link, "$jslink",
			$inner, $title, "webim", "toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1" );
	return "<!-- webim button -->".$temp."<!-- / webim button -->";
}

function check_login($redirect=true) {
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
		if($redirect) {
			$_SESSION['backpath'] = $requested;
			header("Location: $webimroot/operator/login.php");
			exit;
		} else {
			return null;
		}
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
	unset($_SESSION['operator']);
	unset($_SESSION['backpath']);
	if( isset($_COOKIE['webim_lite']) ) {
		setcookie('webim_lite', '', time() - 3600, "$webimroot/");
	}
}

function setup_redirect_links($threadid,$token) {
	global $page, $webimroot, $settings;
	loadsettings();
	$link = connect();

	$operatorscount = rows_count($link, "chatoperator");
	$groupscount = $settings['enablegroups'] == "1" ? rows_count($link, "chatgroup") : 0;
	
	prepare_pagination(max($operatorscount,$groupscount),8);
	$limit = $page['pagination']['limit'];

	$query = "select operatorid, vclogin, vclocalename, vccommonname, (unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(dtmlastvisited)) as time ".
			 "from chatoperator order by vclogin $limit";
	$operators = select_multi_assoc($query, $link);

	if($settings['enablegroups'] == "1") {
		$query = "select groupid, vclocalname, vccommonname from chatgroup order by vclocalname $limit";
		$groups = select_multi_assoc($query, $link);
	}
	
	mysql_close($link);

	$agent_list = "";
	$params = array('thread' => $threadid, 'token' => $token);
	foreach($operators as $agent) {
		$params['nextAgent'] = $agent['operatorid'];
		$online = $agent['time'] < $settings['online_timeout'] ? getlocal("char.redirect.operator.online_suff") : "";
		$agent_list .= "<li><a href=\"".add_params($webimroot."/operator/redirect.php",$params).
						"\" title=\"".topage(get_operator_name($agent))."\">".
						    topage(get_operator_name($agent)).
						"</a> $online</li>";
	}
	$page['redirectToAgent'] = $agent_list;

	$group_list = "";
	if($settings['enablegroups'] == "1") {
		$params = array('thread' => $threadid, 'token' => $token);
		foreach($groups as $group) {
			$params['nextGroup'] = $group['groupid'];
			$group_list .= "<li><a href=\"".add_params($webimroot."/operator/redirect.php",$params).
								"\" title=\"".topage(get_group_name($group))."\">".
								topage(get_group_name($group)).
							"</a></li>";
		}
	}
	$page['redirectToGroup'] = $group_list;
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
		$page['showgroups'] = $settings['enablegroups'] == "1";
		$page['showstat'] = $settings['enablestatistics'] == "1";
		$page['showadmin'] = is_capable($can_administrate, $operator);
		$page['currentopid'] = $operator['operatorid'];
	}
}

function get_groups($countagents) {
	$link = connect();
	$query = "select chatgroup.groupid as groupid, vclocalname, vclocaldescription".
			($countagents 
					? ", (SELECT count(*) from chatgroupoperator where chatgroup.groupid = chatgroupoperator.groupid) as inumofagents" 
					: "").
			 " from chatgroup order by vclocalname";
	$result = select_multi_assoc($query, $link);
	mysql_close($link);
	return $result;
}

function get_operator_groupids($operatorid) {
	$link = connect();
	$query = "select groupid from chatgroupoperator where operatorid = $operatorid";
	$result = select_multi_assoc($query, $link);
	mysql_close($link);
	return $result;
}

?>
