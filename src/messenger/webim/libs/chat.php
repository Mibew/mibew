<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 *    Pavel Petroshenko - history search
 */

$connection_timeout = 30; // sec

$simplenamecookie = "WEBIM_Name";   // 1.0.8 and earlier
$namecookie = "WEBIM_Data";   // 1.0.9+
$usercookie = "WEBIM_UserID";

$state_queue = 0;
$state_waiting = 1;
$state_chatting = 2;
$state_closed = 3;
$state_loading = 4;

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
			quote_smart($message,$link),
			$from ? "'".quote_smart($from,$link)."'" : "null",
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
	global $webim_encoding, $webimroot, $connection_timeout;
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
		$output = get_messages($threadid,"html",$isuser,$lastid);

		start_html_output();
		$url = "$webimroot/thread.php?act=refresh&thread=$threadid&token=$token&html=on&user=".($isuser?"true":"false");

		print("<html><head>\n".
			"<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"$webimroot/chat.css\" />\n".
			"<meta http-equiv=\"Refresh\" content=\"7; URL=$url&sn=11\">\n".
			"<meta http-equiv=\"Pragma\" content=\"no-cache\">\n".
			"</head>".
			"<body bgcolor='#FFFFFF' text='#000000' link='#C28400' vlink='#C28400' alink='#C28400' marginwidth='0' marginheight='0' leftmargin='0' rightmargin='0' topmargin='0' bottommargin='0' onload=\"if( location.hash != '#aend' ){location.hash='#aend';}\">".
			"<table width='100%' cellspacing='0' cellpadding='0' border='0'><tr><td valign='top' class='message'>" );

		foreach( $output as $msg ) {
			print $msg;
		}

		print(
			"</td></tr></table><a name='aend'>".
			"</body></html>" );
	}
}

function get_user_name($username, $addr, $id) {
	global $presentable_name_pattern;
	return str_replace("{addr}", $addr,
			str_replace("{id}", $id,
			str_replace("{name}", $username, $presentable_name_pattern)));
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

	return false;
}

function is_old_browser($browserid,$ver) {
	if( $browserid == "opera" )
		return $ver < 7.0;
	if( $browserid == "msie" )
		return $ver < 5.0;
	return false;
}

$knownAgents = array("opera","msie","safari","firefox","netscape","mozilla");

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

function needsFramesrc() {
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	return strstr($useragent,"safari/");
}

function setup_chatview_for_user($thread,$level) {
	global $page, $webimroot, $user_can_change_name, $settings;
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
	$page['ct.user.name'] = topage($thread['userName']);
	$page['canChangeName'] = $user_can_change_name;

	$page['ct.company.name'] = topage($settings['title']);
	$page['ct.company.chatLogoURL'] = topage($settings['logo']);
	$page['webimHost'] = topage($settings['hosturl']);
	$page['send_shortcut'] = "Ctrl-Enter";

	$params = "thread=".$thread['threadid']."&token=".$thread['ltoken'];
	$page['selfLink'] = "$webimroot/client.php?".$params."&level=".$level;
	$page['isOpera95'] = is_agent_opera95();
	$page['neediframesrc'] = needsFramesrc();

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
	$page['ct.user.name'] = topage(get_user_name($thread['userName'],$thread['remote'],$thread['userid']));

	$page['ct.company.name'] = topage($settings['title']);
	$page['ct.company.chatLogoURL'] = topage($settings['logo']);
	$page['webimHost'] = topage($settings['hosturl']);
	$page['send_shortcut'] = "Ctrl-Enter";
	$page['isOpera95'] = is_agent_opera95();
	$page['neediframesrc'] = needsFramesrc();
	$page['historyParams'] = array("userid" => "".$thread['userid']);
	$page['historyParamsLink'] = add_params($webimroot."/operator/userhistory.php",$page['historyParams']);
	$predefinedres = "";
	foreach(explode("\n", getlocal_('chat.predefined_answers', $thread['locale'])) as $answer) {
		$predefinedres .= "<option>$answer</option>";
	}
	$page['predefinedAnswers'] = $predefinedres;
	$params = "thread=".$thread['threadid']."&token=".$thread['ltoken'];
	$page['selfLink'] = "$webimroot/operator/agent.php?".$params;

	$page['namePostfix'] = "";
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
	commit_thread( $thread['threadid'], array('userName' => "'".mysql_real_escape_string($newname)."'"), $link);
	mysql_close($link);

	if( $thread['userName'] != $newname ) {
		post_message($thread['threadid'],$kind_events,
			getstring2_("chat.status.user.changedname",array($thread['userName'], $newname), $thread['locale']));
	}
}

function close_thread($thread,$isuser) {
	global $state_closed, $kind_events;

	if( $thread['istate'] != $state_closed ) {
		$link = connect();
		commit_thread( $thread['threadid'], array('istate' => $state_closed,
			'messageCount' => '(SELECT COUNT(*) FROM chatmessage WHERE chatmessage.threadid = t.threadid AND ikind = 1)'), $link);
		mysql_close($link);
	}

	$message =  $isuser ? getstring2_("chat.status.user.left", array($thread['userName']), $thread['locale'])
					: getstring2_("chat.status.operator.left", array($thread['agentName']), $thread['locale']);
	post_message($thread['threadid'], $kind_events, $message);
}

function thread_by_id_($id,$link) {
	return select_one_row("select threadid,userName,agentName,agentId,lrevision,istate,ltoken,userTyping,agentTyping".
			",remote,referer,locale,unix_timestamp(lastpinguser) as lpuser,unix_timestamp(lastpingagent) as lpagent, unix_timestamp(CURRENT_TIMESTAMP) as current,nextagent,shownmessageid,userid,userAgent".
			" from chatthread where threadid = ". $id, $link );
}

function ban_for_addr_($addr,$link) {
	return select_one_row("select banid,comment from chatban where unix_timestamp(dtmtill) > unix_timestamp(CURRENT_TIMESTAMP) AND address = '".quote_smart($addr,$link)."'", $link );
}

function thread_by_id($id) {
	$link = connect();
	$thread = thread_by_id_($id,$link);
	mysql_close($link);
	return $thread;
}

function create_thread($username,$remoteHost,$referer,$lang,$userid,$userbrowser) {
	global $state_loading;
	$link = connect();

	$query = sprintf(
		 "insert into chatthread (userName,userid,ltoken,remote,referer,lrevision,locale,userAgent,dtmcreated,dtmmodified,istate) values ".
								 "('%s','%s',%s,'%s','%s',%s,'%s','%s',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,$state_loading)",
			mysql_real_escape_string($username),
			mysql_real_escape_string($userid),
			next_token(),
			mysql_real_escape_string($remoteHost),
			mysql_real_escape_string($referer),
			next_revision($link),
			mysql_real_escape_string($lang),
			mysql_real_escape_string($userbrowser));

	perform_query($query,$link);
	$id = mysql_insert_id($link);

	$newthread = thread_by_id_($id,$link);
	mysql_close($link);
	return $newthread;
}

function do_take_thread($threadid,$operatorId,$operatorName) {
	global $state_chatting;
	$link = connect();
	commit_thread( $threadid,
		array("istate" => $state_chatting,
			  "nextagent" => 0,
			  "agentId" => $operatorId,
			  "agentName" => "'".mysql_real_escape_string($operatorName)."'"), $link);
	mysql_close($link);
}

function reopen_thread($threadid) {
	global $state_queue,$state_loading,$state_waiting,$state_chatting,$state_closed,$kind_events;
	$thread = thread_by_id($threadid);

	if( !$thread )
		return FALSE;

	if( $thread['istate'] == $state_closed )
		return FALSE;

	if( $thread['istate'] != $state_chatting && $thread['istate'] != $state_queue && $thread['istate'] != $state_loading ) {
		$link = connect();
		commit_thread( $threadid,
			array("istate" => $state_waiting, "nextagent" => 0), $link);
		mysql_close($link);
	}

	post_message($thread['threadid'], $kind_events, getstring_("chat.status.user.reopenedthread", $thread['locale']));
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

function visitor_from_request() {
	global $namecookie, $simplenamecookie, $compatibility_encoding, $webim_encoding, $usercookie;
	$userName = getstring("chat.default.username");
	if( isset($_COOKIE[$namecookie]) ) {
		$data = base64_decode(strtr($_COOKIE[$namecookie],'-_,', '+/='));
		if( strlen($data) > 0 ) {
			$userName = myiconv("utf-8",$webim_encoding,$data);
		}
	} else if( isset($_COOKIE[$simplenamecookie]) && isset($compatibility_encoding) ) {
		$userName = myiconv($compatibility_encoding,$webim_encoding,$_COOKIE[$simplenamecookie]);
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

?>
