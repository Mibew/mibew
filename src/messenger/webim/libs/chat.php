<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
 * 
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 *    Pavel Petroshenko - history search
 */

$connection_timeout = 30; // sec

$namecookie = "WEBIM_Data";
$usercookie = "WEBIM_UserID";

$state_queue = 0;
$state_waiting = 1;
$state_chatting = 2;
$state_closed = 3;
$state_loading = 4;
$state_left = 5;

$kind_user = 1;
$kind_agent = 2;
$kind_for_agent = 3;
$kind_info = 4;
$kind_conn = 5;
$kind_events = 6;
$kind_avatar = 7;

$kind_to_string = array( $kind_user => "user", $kind_agent => "agent", $kind_for_agent => "hidden",
	$kind_info => "inf", $kind_conn => "conn", $kind_events => "event", $kind_avatar => "avatar" );

function get_user_id() {
	return (time() + microtime()).rand(0,99999999);
}

function next_token() {
	return rand(99999,99999999);
}

function next_revision($link) {
	perform_query("update chatrevision set id=LAST_INSERT_ID(id+1)",$link);
	$val = mysql_insert_id($link);
	return $val;
}

function post_message_($threadid,$kind,$message,$link,$from=null,$utime=null,$opid=null) {
	$query = sprintf(
		"insert into chatmessage (threadid,ikind,tmessage,tname,agentId,dtmcreated) values (%s, %s,'%s',%s,%s,%s)",
			$threadid,
			$kind,
			mysql_real_escape_string($message,$link),
			$from ? "'".mysql_real_escape_string($from,$link)."'" : "null",
			$opid ? $opid : "0",
			$utime ? "FROM_UNIXTIME($utime)" : "CURRENT_TIMESTAMP" );

	perform_query($query,$link);
	return mysql_insert_id($link);
}

function post_message($threadid,$kind,$message,$from=null,$agentid=null) {
	$link = connect();
	$id = post_message_($threadid,$kind,$message,$link,$from,null,$agentid);
	mysql_close($link);
	return $id;
}

function prepare_html_message($text) {
	$escaped_text = htmlspecialchars($text);
	$text_w_links = preg_replace('/(http|ftp):\/\/\S*/','<a href="$0" target="_blank">$0</a>',$escaped_text);
	$multiline = str_replace("\n","<br/>",$text_w_links);
	return $multiline;
}

function message_to_html($msg) {
	global $kind_to_string, $kind_avatar;
	if( $msg['ikind'] == $kind_avatar ) return "";
	$message = "<span>".date("H:i:s",$msg['created'])."</span> ";
	$kind = $kind_to_string{$msg['ikind']};
	if( $msg['tname'] )
		$message.= "<span class='n$kind'>".htmlspecialchars($msg['tname'])."</span>: ";
	$message.= "<span class='m$kind'>".prepare_html_message($msg['tmessage'])."</span><br/>";
	return $message;
}

function message_to_text($msg) {
	global $kind_user, $kind_agent, $kind_info, $kind_avatar;
	if( $msg['ikind'] == $kind_avatar ) return "";
	$message_time = date("H:i:s ",$msg['created']);
	if($msg['ikind'] == $kind_user || $msg['ikind'] == $kind_agent) {
		if( $msg['tname'] )
			return $message_time.$msg['tname'].": ".$msg['tmessage']."\n";
		else
			return $message_time.$msg['tmessage']."\n";
	} else if($msg['ikind'] == $kind_info ) {
		return $message_time.$msg['tmessage']."\n";
	} else {
		return $message_time."[".$msg['tmessage']."]\n";
	}
}

function get_messages($threadid,$meth,$isuser,&$lastid) {
	global $kind_for_agent, $kind_avatar, $webim_encoding;
	$link = connect();

	$query = sprintf(
		"select messageid,ikind,unix_timestamp(dtmcreated) as created,tname,tmessage from chatmessage ".
		"where threadid = %s and messageid > %s %s order by messageid",
		$threadid, $lastid, $isuser ? "and ikind <> $kind_for_agent" : "" );

	$messages = array();
	$msgs = select_multi_assoc($query, $link);
	foreach ($msgs as $msg) {
		$message = "";
		if ($meth == 'xml') {
			switch ($msg['ikind']) {
				case $kind_avatar:
					$message = "<avatar>".myiconv($webim_encoding,"utf-8",escape_with_cdata($msg['tmessage']))."</avatar>";
					break;
				default:
					$message = "<message>".myiconv($webim_encoding,"utf-8",escape_with_cdata(message_to_html($msg)))."</message>\n";
			}
		} else {
			if ($msg['ikind'] != $kind_avatar) {
				$message = (($meth == 'text') ? message_to_text($msg) : topage(message_to_html($msg)));
			}
		}

		$messages[] = $message;
		if( $msg['messageid'] > $lastid ) {
			$lastid = $msg['messageid'];
		}
	}

	mysql_close($link);
	return $messages;
}

function print_thread_messages($thread, $token, $lastid, $isuser, $format, $agentid=null) {
	global $webim_encoding, $webimroot, $connection_timeout, $settings;
	$threadid = $thread['threadid'];
	$istyping = abs($thread['current']-$thread[$isuser ? "lpagent" : "lpuser"]) < $connection_timeout
				&& $thread[$isuser?"agentTyping":"userTyping"] == "1" ? "1" : "0";

	if( $format == "xml" ) {
		$output = get_messages($threadid,"xml",$isuser,$lastid);

		start_xml_output();
		print("<thread lastid=\"$lastid\" typing=\"".$istyping."\" canpost=\"".(($isuser || $agentid != null && $agentid == $thread['agentId']) ? 1 : 0)."\">");
		foreach( $output as $msg ) {
			print $msg;
		}
		print("</thread>");
	} else if( $format == "html" ) {
		loadsettings();
		$output = get_messages($threadid,"html",$isuser,$lastid);

		start_html_output();
		$url = "$webimroot/thread.php?act=refresh&amp;thread=$threadid&amp;token=$token&amp;html=on&amp;user=".($isuser?"true":"false");

		print(
			"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">".
			"<html>\n<head>\n".
			"<link href=\"$webimroot/styles/default/chat.css\" rel=\"stylesheet\" type=\"text/css\">\n".
			"<meta http-equiv=\"Refresh\" content=\"".$settings['updatefrequency_oldchat']."; URL=$url&amp;sn=11\">\n".
			"<meta http-equiv=\"Pragma\" content=\"no-cache\">\n".
			"<title>chat</title>\n".
			"</head>\n".
			"<body bgcolor='#FFFFFF' text='#000000' link='#C28400' vlink='#C28400' alink='#C28400' onload=\"if( location.hash != '#aend' ){location.hash='#aend';}\">".
			"<table width='100%' cellspacing='0' cellpadding='0' border='0'><tr><td valign='top' class='message'>" );
		
		foreach( $output as $msg ) {
			print $msg;
		}

		print(
			"</td></tr></table><a name='aend'></a>".
			"</body></html>" );
	}
}

function get_user_name($username, $addr, $id) {
	global $settings;
	loadsettings();
	return str_replace("{addr}", $addr,
			str_replace("{id}", $id,
			str_replace("{name}", $username, $settings['usernamepattern'])));
}

function is_ajax_browser($browserid,$ver,$useragent) {
	if( $browserid == "opera" )
		return $ver >= 8.02;
	if( $browserid == "safari" )
		return $ver >= 125;
	if( $browserid == "msie" )
		return $ver >= 5.5 && !strstr($useragent, "powerpc");
	if( $browserid == "netscape" )
		return $ver >= 7.1;
	if( $browserid == "mozilla")
		return $ver >= 1.4;
	if( $browserid == "firefox")
		return $ver >= 1.0;
	if( $browserid == "chrome")
		return true;

	return false;
}

function is_old_browser($browserid,$ver) {
	if( $browserid == "opera" )
		return $ver < 7.0;
	if( $browserid == "msie" )
		return $ver < 5.0;
	return false;
}

$knownAgents = array("opera","msie","chrome","safari","firefox","netscape","mozilla");

function get_remote_level($useragent) {
	global $knownAgents;
	$useragent = strtolower($useragent);
	foreach( $knownAgents as $agent ) {
		if( strstr($useragent,$agent) ) {
			if( preg_match( "/".$agent."[\\s\/]?(\\d+(\\.\\d+)?)/", $useragent, $matches ) ) {
				$ver = $matches[1];

				if( is_ajax_browser($agent,$ver,$useragent) )
					return "ajaxed";
				else if( is_old_browser($agent,$ver) )
					return "old";

				return "simple";
			}
		}
	}
	return "simple";
}

function is_agent_opera95() {
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if( strstr($useragent,"opera") ) {
		if( preg_match( "/opera[\\s\/]?(\\d+(\\.\\d+)?)/", $useragent, $matches ) ) {
			$ver = $matches[1];

			if ($ver >= "9.5")
				return true;
		}
	}
	return false;
}

function is_mac_opera() {
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	return strstr($useragent,"opera") && strstr($useragent, "mac");
}

function needsFramesrc() {
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	return strstr($useragent,"safari/");
}

function setup_logo() {
	global $page, $settings;
	loadsettings();
	$page['ct.company.name'] = topage($settings['title']);
	$page['ct.company.chatLogoURL'] = topage($settings['logo']);
	$page['webimHost'] = topage($settings['hosturl']);
}

function setup_leavemessage($name, $email, $message, $groupid, $groupname, $info, $referrer,$canshowcaptcha) {
	global $settings, $page;
	$page['formname'] = topage($name);
	$page['formemail'] = topage($email);
	$page['formmessage'] = $message ? topage($message) : "";
	$page['showcaptcha'] = $settings["enablecaptcha"] == "1" && $canshowcaptcha ? "1" : "";
	$page['formgroupid'] = $groupid;
	$page['formgroupname'] = $groupname;
	$page['info'] = topage($info);
	$page['referrer'] = urlencode(topage($referrer));
}

function setup_survey($name, $email, $groupid, $info, $referrer) {
	global $settings, $page;
	
	$page['formname'] = topage($name);
	$page['formemail'] = topage($email);
	$page['formgroupid'] = $groupid;
	$page['forminfo'] = topage($info);
	$page['referrer'] = urlencode(topage($referrer));

	if($settings['enablegroups'] == '1' && $settings["surveyaskgroup"] == "1") {
		$link = connect();
		$allgroups = get_groups($link,false);
		mysql_close($link);
		$val = "";
		foreach($allgroups as $k) {
			$groupname = $k['vclocalname'];
			if($k['inumofagents'] == 0) {
				continue;
			}
			if($k['ilastseen'] !== NULL && $k['ilastseen'] < $settings['online_timeout']) {
				if(!$groupid) {
					$groupid = $k['groupid'];  // select first online group
				}
			} else {
				$groupname .= " (offline)";
			}
			$isselected = $k['groupid'] == $groupid;
			$val .= "<option value=\"".$k['groupid']."\"".($isselected ? " selected=\"selected\"" : "").">$groupname</option>";
		}
		$page['groups'] = $val;
	}
	
	$page['showemail'] = $settings["surveyaskmail"] == "1" ? "1" : "";
	$page['showmessage'] = $settings["surveyaskmessage"] == "1" ? "1" : "";
	$page['showname'] = $settings['usercanchangename'] == "1" ? "1" : "";
}

function setup_chatview_for_user($thread,$level) {
	global $page, $webimroot, $settings;
	loadsettings();
	$page = array();
	$page['agent'] = false;
	$page['user'] = true;
	$page['canpost'] = true;
	$nameisset = getstring("chat.default.username") != $thread['userName'];
	$page['displ1'] = $nameisset ? "none" : "inline";
	$page['displ2'] = $nameisset ? "inline" : "none";
	$page['level'] = $level;
	$page['ct.chatThreadId'] = $thread['threadid'];
	$page['ct.token'] = $thread['ltoken'];
	$page['ct.user.name'] = htmlspecialchars(topage($thread['userName']));
	$page['canChangeName'] = $settings['usercanchangename'] == "1";
	$page['chat.title'] = topage($settings['chattitle']);

	setup_logo();
	if($settings['sendmessagekey'] == 'enter') {
		$page['send_shortcut'] = "Enter";
		$page['ignorectrl'] =  1;
	} else {
		$page['send_shortcut'] = is_mac_opera() ? "&#8984;-Enter" : "Ctrl-Enter";
		$page['ignorectrl'] =  0;
	}

	$params = "thread=".$thread['threadid']."&amp;token=".$thread['ltoken'];
	$page['mailLink'] = "$webimroot/client.php?".$params."&amp;level=$level&amp;act=mailthread";

	if($settings['enablessl'] == "1" && !is_secure_request()) {
		$page['sslLink'] = get_app_location(true, true)."/client.php?".$params."&amp;level=$level";
	}

	$page['isOpera95'] = is_agent_opera95();
	$page['neediframesrc'] = needsFramesrc();

	$page['frequency'] = $settings['updatefrequency_chat'];
}

function load_canned_messages($locale, $groupid) {
	$link = connect();
	$result = select_multi_assoc(
			"select vcvalue from chatresponses where locale = '".$locale."' ".
			"AND (groupid is NULL OR groupid = 0) order by vcvalue", $link);
	if(count($result) == 0) {
		foreach(explode("\n", getstring_('chat.predefined_answers', $locale)) as $answer) {
			$result[] = array('vcvalue' => $answer);
		}
	}
	if($groupid) {
		$result2 = select_multi_assoc(
				"select vcvalue from chatresponses where locale = '".$locale."' ".
				"AND groupid = $groupid order by vcvalue", $link);
		foreach($result as $r) {
			$result2[] = $r;
		}
		$result = $result2;
	}
	mysql_close($link);
	return $result;
}

function setup_chatview_for_operator($thread,$operator) {
	global $page, $webimroot, $company_logo_link, $company_name, $settings;
	loadsettings();
	$page = array();
	$page['agent'] = true;
	$page['user'] = false;
	$page['canpost'] = $thread['agentId'] == $operator['operatorid'];
	$page['ct.chatThreadId'] = $thread['threadid'];
	$page['ct.token'] = $thread['ltoken'];
	$page['ct.user.name'] = htmlspecialchars(topage(get_user_name($thread['userName'],$thread['remote'],$thread['userid'])));
	$page['chat.title'] = topage($settings['chattitle']);

	setup_logo();
	if($settings['sendmessagekey'] == 'enter') {
		$page['send_shortcut'] = "Enter";
		$page['ignorectrl'] =  1;
	} else {
		$page['send_shortcut'] = is_mac_opera() ? "&#8984;-Enter" : "Ctrl-Enter";
		$page['ignorectrl'] =  0;
	}

	if($settings['enablessl'] == "1" && !is_secure_request()) {
		$page['sslLink'] = get_app_location(true, true)."/operator/agent.php?thread=".$thread['threadid']."&amp;token=".$thread['ltoken'];
	}
	$page['isOpera95'] = is_agent_opera95();
	$page['neediframesrc'] = needsFramesrc();
	$page['historyParams'] = array("userid" => "".$thread['userid']);
	$page['historyParamsLink'] = add_params($webimroot."/operator/userhistory.php",$page['historyParams']);
	$predefinedres = "";
	$canned_messages = load_canned_messages($thread['locale'], $thread['groupid']);
	foreach($canned_messages as $answer) {
		$predefinedres .= "<option>".htmlspecialchars(topage($answer['vcvalue']))."</option>";
	}
	$page['predefinedAnswers'] = $predefinedres;
	$params = "thread=".$thread['threadid']."&amp;token=".$thread['ltoken'];
	$page['redirectLink'] = "$webimroot/operator/agent.php?".$params."&amp;act=redirect";

	$page['namePostfix'] = "";
	$page['frequency'] = $settings['updatefrequency_chat'];
}

function update_thread_access($threadid, $params, $link) {
	$clause = "";
	foreach( $params as $k => $v ) {
		if( strlen($clause) > 0 )
			$clause .= ", ";
		$clause .= $k."=".$v;
	}
	perform_query(
		 "update chatthread set $clause ".
		 "where threadid = ".$threadid,$link);
}

function ping_thread($thread, $isuser,$istyping) {
	global $kind_for_agent, $state_queue, $state_loading, $state_chatting, $state_waiting, $kind_conn, $connection_timeout;
	$link = connect();
	$params = array(($isuser ? "lastpinguser" : "lastpingagent") => "CURRENT_TIMESTAMP",
					($isuser ? "userTyping" : "agentTyping") => ($istyping? "1" : "0") );

	$lastping = $thread[$isuser ? "lpagent" : "lpuser"];
	$current = $thread['current'];

	if( $thread['istate'] == $state_loading && $isuser) {
		$params['istate'] = $state_queue;
		commit_thread($thread['threadid'], $params, $link);
		mysql_close($link);
		return;
	}

 	if( $lastping > 0 && abs($current-$lastping) > $connection_timeout ) {
		$params[$isuser ? "lastpingagent" : "lastpinguser"] = "0";
		if( !$isuser ) {
			$message_to_post = getstring_("chat.status.user.dead", $thread['locale']);
			post_message_($thread['threadid'],$kind_for_agent,$message_to_post,$link,null,$lastping+$connection_timeout);
		} else if( $thread['istate'] == $state_chatting ) {

			$message_to_post = getstring_("chat.status.operator.dead", $thread['locale']);
			post_message_($thread['threadid'],$kind_conn,$message_to_post,$link,null,$lastping+$connection_timeout);
			$params['istate'] = $state_waiting;
			$params['nextagent'] = 0;
			commit_thread($thread['threadid'], $params, $link);
			mysql_close($link);
			return;
		}
	}

	update_thread_access($thread['threadid'], $params, $link);
	mysql_close($link);
}

function commit_thread($threadid,$params,$link) {
	$query = "update chatthread t set lrevision = ".next_revision($link).", dtmmodified = CURRENT_TIMESTAMP";
	foreach( $params as $k => $v ) {
		$query .= ", ".$k."=".$v;
	}
	$query .= " where threadid = ".$threadid;

	perform_query($query,$link);
}

function rename_user($thread, $newname) {
	global $kind_events;

	$link = connect();
	commit_thread( $thread['threadid'], array('userName' => "'".mysql_real_escape_string($newname,$link)."'"), $link);

	if( $thread['userName'] != $newname ) {
		post_message_($thread['threadid'],$kind_events,
			getstring2_("chat.status.user.changedname",array($thread['userName'], $newname), $thread['locale']), $link);
	}
	mysql_close($link);
}

function close_thread($thread,$isuser) {
	global $state_closed, $kind_events;

	$link = connect();
	if( $thread['istate'] != $state_closed ) {
		commit_thread( $thread['threadid'], array('istate' => $state_closed,
			'messageCount' => '(SELECT COUNT(*) FROM chatmessage WHERE chatmessage.threadid = t.threadid AND ikind = 1)'), $link);
	}

	$message =  $isuser ? getstring2_("chat.status.user.left", array($thread['userName']), $thread['locale'])
					: getstring2_("chat.status.operator.left", array($thread['agentName']), $thread['locale']);
	post_message_($thread['threadid'], $kind_events, $message, $link);
	mysql_close($link);
}

function thread_by_id_($id,$link) {
	return select_one_row("select threadid,userName,agentName,agentId,lrevision,istate,ltoken,userTyping,agentTyping".
			",unix_timestamp(dtmmodified) as modified, unix_timestamp(dtmcreated) as created".
			",remote,referer,locale,unix_timestamp(lastpinguser) as lpuser,unix_timestamp(lastpingagent) as lpagent, unix_timestamp(CURRENT_TIMESTAMP) as current,nextagent,shownmessageid,userid,userAgent,groupid".
			" from chatthread where threadid = ". $id, $link );
}

function ban_for_addr_($addr,$link) {
	return select_one_row("select banid,comment from chatban where unix_timestamp(dtmtill) > unix_timestamp(CURRENT_TIMESTAMP) AND address = '".mysql_real_escape_string($addr,$link)."'", $link );
}

function thread_by_id($id) {
	$link = connect();
	$thread = thread_by_id_($id,$link);
	mysql_close($link);
	return $thread;
}

function create_thread($groupid,$username,$remoteHost,$referer,$lang,$userid,$userbrowser,$initialState,$link) {
	$query = sprintf(
		 "insert into chatthread (userName,userid,ltoken,remote,referer,lrevision,locale,userAgent,dtmcreated,dtmmodified,istate".($groupid?",groupid":"").") values ".
								 "('%s','%s',%s,'%s','%s',%s,'%s','%s',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,$initialState".($groupid?",$groupid":"").")",
			mysql_real_escape_string($username, $link),
			mysql_real_escape_string($userid, $link),
			next_token(),
			mysql_real_escape_string($remoteHost, $link),
			mysql_real_escape_string($referer, $link),
			next_revision($link),
			mysql_real_escape_string($lang, $link),
			mysql_real_escape_string($userbrowser, $link));

	perform_query($query,$link);
	$id = mysql_insert_id($link);

	$newthread = thread_by_id_($id,$link);
	return $newthread;
}

function do_take_thread($threadid,$operatorId,$operatorName) {
	global $state_chatting;
	$link = connect();
	commit_thread( $threadid,
		array("istate" => $state_chatting,
			  "nextagent" => 0,
			  "agentId" => $operatorId,
			  "agentName" => "'".mysql_real_escape_string($operatorName, $link)."'"), $link);
	mysql_close($link);
}

function reopen_thread($threadid) {
	global $state_queue,$state_loading,$state_waiting,$state_chatting,$state_closed,$state_left,$kind_events;
	$link = connect();
	$thread = thread_by_id_($threadid, $link);

	if( !$thread )
		return FALSE;

	if( $thread['istate'] == $state_closed || $thread['istate'] == $state_left )
		return FALSE;

	if( $thread['istate'] != $state_chatting && $thread['istate'] != $state_queue && $thread['istate'] != $state_loading ) {
		commit_thread( $threadid,
			array("istate" => $state_waiting, "nextagent" => 0), $link);
	}

	post_message_($thread['threadid'], $kind_events, getstring_("chat.status.user.reopenedthread", $thread['locale']), $link);
	mysql_close($link);
	return $thread;
}

function take_thread($thread,$operator) {
	global $state_queue, $state_loading, $state_waiting, $state_chatting, $kind_events, $kind_avatar, $home_locale;

	$state = $thread['istate'];
	$threadid = $thread['threadid'];
	$message_to_post = "";

	$operatorName = ($thread['locale'] == $home_locale) ? $operator['vclocalename'] : $operator['vccommonname'];

	if( $state == $state_queue || $state == $state_waiting || $state == $state_loading) {
		do_take_thread($threadid, $operator['operatorid'], $operatorName);

		if( $state == $state_waiting ) {
			if( $operatorName != $thread['agentName'] ) {
				$message_to_post = getstring2_("chat.status.operator.changed", array($operatorName, $thread['agentName']), $thread['locale']);
			} else {
				$message_to_post = getstring2_("chat.status.operator.returned", array($operatorName), $thread['locale']);
			}
		} else {
			$message_to_post = getstring2_("chat.status.operator.joined", array($operatorName), $thread['locale']);
		}
	} else if( $state == $state_chatting ) {
		if( $operator['operatorid'] != $thread['agentId'] ) {
			do_take_thread($threadid, $operator['operatorid'], $operatorName);
			$message_to_post = getstring2_("chat.status.operator.changed", array($operatorName, $thread['agentName']), $thread['locale']);
		}
	} else {
		die("cannot take thread");
	}

	if( $message_to_post ) {
		post_message($threadid,$kind_events,$message_to_post);
		post_message($threadid,$kind_avatar,$operator['vcavatar'] ? $operator['vcavatar'] : "");
	}
}

function check_for_reassign($thread,$operator) {
	global $state_waiting, $home_locale, $kind_events, $kind_avatar;
	$operatorName = ($thread['locale'] == $home_locale) ? $operator['vclocalename'] : $operator['vccommonname'];
	if( $thread['istate'] == $state_waiting &&
			(  $thread['nextagent'] == $operator['operatorid']
			|| $thread['agentId'] == $operator['operatorid'] )) {
		do_take_thread($thread['threadid'], $operator['operatorid'], $operatorName);
		if( $operatorName != $thread['agentName'] ) {
			$message_to_post = getstring2_("chat.status.operator.changed", array($operatorName, $thread['agentName']), $thread['locale']);
		} else {
			$message_to_post = getstring2_("chat.status.operator.returned", array($operatorName), $thread['locale']);
		}

		post_message($thread['threadid'],$kind_events,$message_to_post);
		post_message($thread['threadid'],$kind_avatar,$operator['vcavatar'] ? $operator['vcavatar'] : "");
	}
}

function check_connections_from_remote($remote,$link) {
	global $settings, $state_closed, $state_left;
	if($settings['max_connections_from_one_host'] == 0) {
		return true;
	}
	$result = select_one_row(
			"select count(*) as opened from chatthread ".
			"where remote = '". mysql_real_escape_string($remote, $link)."' AND istate <> $state_closed AND istate <> $state_left", $link );
	if($result && isset($result['opened'])) {
		return $result['opened'] < $settings['max_connections_from_one_host'];
	}
	return true;
}

function visitor_from_request() {
	global $namecookie, $webim_encoding, $usercookie;
	$defaultName = getstring("chat.default.username");
	$userName = $defaultName;
	if( isset($_COOKIE[$namecookie]) ) {
		$data = base64_decode(strtr($_COOKIE[$namecookie],'-_,', '+/='));
		if( strlen($data) > 0 ) {
			$userName = myiconv("utf-8",$webim_encoding,$data);
		}
	}

	if($userName == $defaultName) {
		$userName = getgetparam('name', $userName);
	}

	$userId = "";
	if (isset($_COOKIE[$usercookie])) {
		$userId = $_COOKIE[$usercookie];
	} else {
		$userId = get_user_id();
		setcookie($usercookie, $userId, time()+60*60*24*365);
	}
	return array( 'id' => $userId, 'name' => $userName );
}

function get_remote_host() {
	$extAddr = $_SERVER['REMOTE_ADDR'];
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
	          $_SERVER['HTTP_X_FORWARDED_FOR'] != $_SERVER['REMOTE_ADDR']) {
		$extAddr = $_SERVER['REMOTE_ADDR'].' ('.$_SERVER['HTTP_X_FORWARDED_FOR'].')';
	}
	return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : $extAddr;
}

?>