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

require_once(dirname(__FILE__).'/track.php');

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

$kind_to_string = array($kind_user => "user", $kind_agent => "agent", $kind_for_agent => "hidden",
						$kind_info => "inf", $kind_conn => "conn", $kind_events => "event", $kind_avatar => "avatar");

function get_user_id()
{
	return (time() + microtime()) . rand(0, 99999999);
}

function next_token()
{
	return rand(99999, 99999999);
}

function next_revision()
{
	$db = Database::getInstance();
	$db->query("update {chatrevision} set id=LAST_INSERT_ID(id+1)");
	$val = $db->insertedId();
	return $val;
}

/**
 * @todo Think about post_message_ and post_message diffrence
 */
function post_message_($threadid, $kind, $message, $from = null, $utime = null, $opid = null)
{
	$db = Database::getInstance();
	$query = "insert into {chatmessage} " .
		"(threadid,ikind,tmessage,tname,agentId,dtmcreated) " .
		"values (?,?,?,?,?,".($utime?"FROM_UNIXTIME(?)":"CURRENT_TIMESTAMP").")";
	 $values = array(
		$threadid,
		$kind,
		$message,
		($from ? $from : "null"),
		($opid ? $opid : 0)
	);
	if ($utime) {
		$values[] = $utime;
	}
	 $db->query($query, $values);
	return $db->insertedId();
}

function post_message($threadid, $kind, $message, $from = null, $agentid = null)
{
	return post_message_($threadid, $kind, $message, $from, null, $agentid);
}

function prepare_html_message($text, $allow_formating)
{
	$escaped_text = htmlspecialchars($text);
	$text_w_links = preg_replace('/(https?|ftp):\/\/\S*/', '<a href="$0" target="_blank">$0</a>', $escaped_text);
	$multiline = str_replace("\n", "<br/>", $text_w_links);
	if (! $allow_formating) {
		return $multiline;
	}
	$formated = preg_replace('/&lt;(span|strong)&gt;(.*)&lt;\/\1&gt;/U', '<$1>$2</$1>', $multiline);
	$formated = preg_replace('/&lt;span class=&quot;(.*)&quot;&gt;(.*)&lt;\/span&gt;/U', '<span class="$1">$2</span>', $formated);
	return $formated;
}

function message_to_html($msg)
{
	global $kind_to_string, $kind_user, $kind_agent, $kind_avatar;
	if ($msg['ikind'] == $kind_avatar) return "";
	$message = "<span>" . date("H:i:s", $msg['created']) . "</span> ";
	$kind = $kind_to_string{$msg['ikind']};
	if ($msg['tname'])
		$message .= "<span class='n$kind'>" . htmlspecialchars($msg['tname']) . "</span>: ";
	$allow_formating = ($msg['ikind'] != $kind_user && $msg['ikind'] != $kind_agent);
	$message .= "<span class='m$kind'>" . prepare_html_message($msg['tmessage'], $allow_formating) . "</span><br/>";
	return $message;
}

function message_to_text($msg)
{
	global $kind_user, $kind_agent, $kind_info, $kind_avatar;
	if ($msg['ikind'] == $kind_avatar) return "";
	$message_time = date("H:i:s ", $msg['created']);
	if ($msg['ikind'] == $kind_user || $msg['ikind'] == $kind_agent) {
		if ($msg['tname'])
			return $message_time . $msg['tname'] . ": " . $msg['tmessage'] . "\n";
		else
			return $message_time . $msg['tmessage'] . "\n";
	} else if ($msg['ikind'] == $kind_info) {
		return $message_time . $msg['tmessage'] . "\n";
	} else {
		return $message_time . "[" . $msg['tmessage'] . "]\n";
	}
}

function get_messages($threadid, $meth, $isuser, &$lastid)
{
	global $kind_for_agent, $kind_avatar, $webim_encoding;
	$db = Database::getInstance();

	$msgs = $db->query(
		"select messageid,ikind,unix_timestamp(dtmcreated) as created,tname,tmessage from {chatmessage} " .
		"where threadid = :threadid and messageid > :lastid " .
		($isuser ? "and ikind <> {$kind_for_agent} " : "") .
		"order by messageid",
		array(
			':threadid' => $threadid,
			':lastid' => $lastid,
		),
		array('return_rows' => Database::RETURN_ALL_ROWS)
		
	);

	$messages = array();
	foreach ($msgs as $msg) {
		$message = "";
		if ($meth == 'xml') {
			switch ($msg['ikind']) {
				case $kind_avatar:
					$message = "<avatar>" . myiconv($webim_encoding, "utf-8", escape_with_cdata($msg['tmessage'])) . "</avatar>";
					break;
				default:
					$message = "<message>" . myiconv($webim_encoding, "utf-8", escape_with_cdata(message_to_html($msg))) . "</message>\n";
			}
		} else {
			if ($msg['ikind'] != $kind_avatar) {
				$message = (($meth == 'text') ? message_to_text($msg) : topage(message_to_html($msg)));
			}
		}

		$messages[] = $message;
		if ($msg['messageid'] > $lastid) {
			$lastid = $msg['messageid'];
		}
	}

	return $messages;
}

function print_thread_messages($thread, $token, $lastid, $isuser, $format, $agentid = null)
{
	global $webim_encoding, $webimroot, $connection_timeout, $settings;
	$threadid = $thread['threadid'];
	$istyping = abs($thread['current'] - $thread[$isuser ? "lpagent" : "lpuser"]) < $connection_timeout
				&& $thread[$isuser ? "agentTyping" : "userTyping"] == "1" ? "1" : "0";

	if ($format == "xml") {
		$output = get_messages($threadid, "xml", $isuser, $lastid);

		start_xml_output();
		print("<thread lastid=\"$lastid\" typing=\"" . $istyping . "\" canpost=\"" . (($isuser || $agentid != null && $agentid == $thread['agentId']) ? 1 : 0) . "\">");
		foreach ($output as $msg) {
			print $msg;
		}
		print("</thread>");
	} else if ($format == "html") {
		loadsettings();
		$output = get_messages($threadid, "html", $isuser, $lastid);

		start_html_output();
		$url = "$webimroot/thread.php?act=refresh&amp;thread=$threadid&amp;token=$token&amp;html=on&amp;user=" . ($isuser ? "true" : "false");

		print(
				"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">" .
				"<html>\n<head>\n" .
				"<link href=\"$webimroot/styles/default/chat.css\" rel=\"stylesheet\" type=\"text/css\">\n" .
				"<meta http-equiv=\"Refresh\" content=\"" . $settings['updatefrequency_oldchat'] . "; URL=$url&amp;sn=11\">\n" .
				"<meta http-equiv=\"Pragma\" content=\"no-cache\">\n" .
				"<title>chat</title>\n" .
				"</head>\n" .
				"<body bgcolor='#FFFFFF' text='#000000' link='#C28400' vlink='#C28400' alink='#C28400' onload=\"if( location.hash != '#aend' ){location.hash='#aend';}\">" .
				"<table width='100%' cellspacing='0' cellpadding='0' border='0'><tr><td valign='top' class='message'>");

		foreach ($output as $msg) {
			print $msg;
		}

		print(
				"</td></tr></table><a name='aend'></a>" .
				"</body></html>");
	}
}

function get_user_name($username, $addr, $id)
{
	global $settings;
	loadsettings();
	return str_replace("{addr}", $addr,
					   str_replace("{id}", $id,
								   str_replace("{name}", $username, $settings['usernamepattern'])));
}

function is_ajax_browser($browserid, $ver, $useragent)
{
	if ($browserid == "opera")
		return $ver >= 8.02;
	if ($browserid == "safari")
		return $ver >= 125;
	if ($browserid == "msie")
		return $ver >= 5.5 && !strstr($useragent, "powerpc");
	if ($browserid == "netscape")
		return $ver >= 7.1;
	if ($browserid == "mozilla")
		return $ver >= 1.4;
	if ($browserid == "firefox")
		return $ver >= 1.0;
	if ($browserid == "chrome")
		return true;

	return false;
}

function is_old_browser($browserid, $ver)
{
	if ($browserid == "opera")
		return $ver < 7.0;
	if ($browserid == "msie")
		return $ver < 5.0;
	return false;
}

$knownAgents = array("opera", "msie", "chrome", "safari", "firefox", "netscape", "mozilla");

function get_remote_level($useragent)
{
	global $knownAgents;
	$useragent = strtolower($useragent);
	foreach ($knownAgents as $agent) {
		if (strstr($useragent, $agent)) {
			if (preg_match("/" . $agent . "[\\s\/]?(\\d+(\\.\\d+)?)/", $useragent, $matches)) {
				$ver = $matches[1];

				if (is_ajax_browser($agent, $ver, $useragent))
					return "ajaxed";
				else if (is_old_browser($agent, $ver))
					return "old";

				return "simple";
			}
		}
	}
	return "simple";
}

function is_agent_opera95()
{
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if (strstr($useragent, "opera")) {
		if (preg_match("/opera[\\s\/]?(\\d+(\\.\\d+)?)/", $useragent, $matches)) {
			$ver = $matches[1];

			if ($ver >= "9.5")
				return true;
		}
	}
	return false;
}

function is_mac_opera()
{
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	return strstr($useragent, "opera") && strstr($useragent, "mac");
}

function needsFramesrc()
{
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	return strstr($useragent, "safari/");
}

function setup_logo($group = NULL)
{
	global $page, $settings;
	loadsettings();
	$toplevelgroup = (!$group)?array():get_top_level_group($group);
	$page['ct.company.name'] = topage(empty($toplevelgroup['vctitle'])?$settings['title']:$toplevelgroup['vctitle']);
	$page['ct.company.chatLogoURL'] = topage(empty($toplevelgroup['vclogo'])?$settings['logo']:$toplevelgroup['vclogo']);
	$page['webimHost'] = topage(empty($toplevelgroup['vchosturl'])?$settings['hosturl']:$toplevelgroup['vchosturl']);
}

function setup_leavemessage($name, $email, $message, $groupid, $groupname, $info, $referrer, $canshowcaptcha)
{
	global $settings, $page;
	$page['formname'] = topage($name);
	$page['formemail'] = topage($email);
	$page['formmessage'] = $message ? topage($message) : "";
	$page['showcaptcha'] = $settings["enablecaptcha"] == "1" && $canshowcaptcha ? "1" : "";
	$page['formgroupid'] = $groupid;
	$page['formgroupname'] = $groupname;
	$page['forminfo'] = topage($info);
	$page['referrer'] = urlencode(topage($referrer));

	if ($settings['enablegroups'] == '1') {
		$groups = setup_groups_select($groupid, false);
		if ($groups) {
			$page['groups'] = $groups['select'];
			$page['group.descriptions'] = json_encode($groups['descriptions']);
			$page['default.department.description'] = $groups['defaultdescription'];
		}
	}

}

function setup_survey($name, $email, $groupid, $info, $referrer)
{
	global $settings, $page;

	$page['formname'] = topage($name);
	$page['formemail'] = topage($email);
	$page['formgroupid'] = $groupid;
	$page['forminfo'] = topage($info);
	$page['referrer'] = urlencode(topage($referrer));

	if ($settings['enablegroups'] == '1' && $settings["surveyaskgroup"] == "1") {
		$groups = setup_groups_select($groupid, true);
		if ($groups) {
			$page['groups'] = $groups['select'];
			$page['group.descriptions'] = json_encode($groups['descriptions']);
			$page['default.department.description'] = $groups['defaultdescription'];
		}
	}

	$page['showemail'] = $settings["surveyaskmail"] == "1" ? "1" : "";
	$page['showmessage'] = $settings["surveyaskmessage"] == "1" ? "1" : "";
	$page['showname'] = $settings['usercanchangename'] == "1" ? "1" : "";
}

function setup_groups_select($groupid, $markoffline)
{
	global $settings;

	$showgroups = ($groupid == '')?true:group_has_children($groupid);
	if (!$showgroups) {
		return false;
	}

	$allgroups = get_groups(false);

	if (empty($allgroups)) {
		return false;
	}

	$val = "";
	$selectedgroupid = $groupid;
	$groupdescriptions = array();
	foreach ($allgroups as $k) {
		$groupname = $k['vclocalname'];
		if ($k['inumofagents'] == 0 || ($groupid && $k['parent'] != $groupid && $k['groupid'] != $groupid )) {
			continue;
		}
		if ($k['ilastseen'] !== NULL && $k['ilastseen'] < $settings['online_timeout']) {
			if (!$selectedgroupid) {
				$selectedgroupid = $k['groupid']; // select first online group
			}
		} else {
			$groupname .= $markoffline?" (offline)":"";
		}
		$isselected = $k['groupid'] == $selectedgroupid;
		if ($isselected) {
			$defaultdescription = $k['vclocaldescription'];
		}
		$val .= "<option value=\"" . $k['groupid'] . "\"" . ($isselected ? " selected=\"selected\"" : "") . ">$groupname</option>";
		$groupdescriptions[] = $k['vclocaldescription'];
	}

	return array(
		'select' => $val,
		'descriptions' => $groupdescriptions,
		'defaultdescription' => $defaultdescription
	);
}

function setup_chatview_for_user($thread, $level)
{
	global $page, $webimroot, $settings;
	loadsettings();
	$page = array();
	if (! empty($thread['groupid'])) {
		$group = group_by_id($thread['groupid']);
		$group = get_top_level_group($group);
	} else {
		$group = array();
	}
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
	$page['chat.title'] = topage(empty($group['vcchattitle'])?$settings['chattitle']:$group['vcchattitle']);
	$page['chat.close.confirmation'] = getlocal('chat.close.confirmation');

	setup_logo($group);
	if ($settings['sendmessagekey'] == 'enter') {
		$page['send_shortcut'] = "Enter";
		$page['ignorectrl'] = 1;
	} else {
		$page['send_shortcut'] = is_mac_opera() ? "&#8984;-Enter" : "Ctrl-Enter";
		$page['ignorectrl'] = 0;
	}

	$params = "thread=" . $thread['threadid'] . "&amp;token=" . $thread['ltoken'];
	$page['mailLink'] = "$webimroot/client.php?" . $params . "&amp;level=$level&amp;act=mailthread";

	if ($settings['enablessl'] == "1" && !is_secure_request()) {
		$page['sslLink'] = get_app_location(true, true) . "/client.php?" . $params . "&amp;level=$level";
	}

	$page['isOpera95'] = is_agent_opera95();
	$page['neediframesrc'] = needsFramesrc();

	$page['frequency'] = $settings['updatefrequency_chat'];
}

function setup_chatview_for_operator($thread, $operator)
{
	global $page, $webimroot, $company_logo_link, $webim_encoding, $company_name, $settings;
	loadsettings();
	$page = array();
	if (! is_null($thread['groupid'])) {
		$group = group_by_id($thread['groupid']);
		$group = get_top_level_group($group);
	} else {
		$group = array();
	}
	$page['agent'] = true;
	$page['user'] = false;
	$page['canpost'] = $thread['agentId'] == $operator['operatorid'];
	$page['ct.chatThreadId'] = $thread['threadid'];
	$page['ct.token'] = $thread['ltoken'];
	$page['ct.user.name'] = htmlspecialchars(topage(get_user_name($thread['userName'], $thread['remote'], $thread['userid'])));
	$page['chat.title'] = topage(empty($group['vcchattitle'])?$settings['chattitle']:$group['vcchattitle']);
	$page['chat.close.confirmation'] = getlocal('chat.close.confirmation');

	setup_logo($group);
	if ($settings['sendmessagekey'] == 'enter') {
		$page['send_shortcut'] = "Enter";
		$page['ignorectrl'] = 1;
	} else {
		$page['send_shortcut'] = is_mac_opera() ? "&#8984;-Enter" : "Ctrl-Enter";
		$page['ignorectrl'] = 0;
	}

	if ($settings['enablessl'] == "1" && !is_secure_request()) {
		$page['sslLink'] = get_app_location(true, true) . "/operator/agent.php?thread=" . $thread['threadid'] . "&amp;token=" . $thread['ltoken'];
	}
	$page['isOpera95'] = is_agent_opera95();
	$page['neediframesrc'] = needsFramesrc();
	$page['historyParams'] = array("userid" => "" . $thread['userid']);
	$page['historyParamsLink'] = add_params($webimroot . "/operator/userhistory.php", $page['historyParams']);
	if ($settings['enabletracking']) {
	    $visitor = track_get_visitor_by_threadid($thread['threadid']);
	    $page['trackedParams'] = array("visitor" => "" . $visitor['visitorid']);
	    $page['trackedParamsLink'] = add_params($webimroot . "/operator/tracked.php", $page['trackedParams']);
	}
	$predefinedres = "";
	$canned_messages = load_canned_messages($thread['locale'], 0);
	if ($thread['groupid']) {
		$canned_messages = array_merge(
			load_canned_messages($thread['locale'], $thread['groupid']),
			$canned_messages
		);
	};
	foreach ($canned_messages as $answer) {
		$predefinedres .= "<option>" . htmlspecialchars(topage($answer['vctitle']?$answer['vctitle']:cutstring($answer['vcvalue'], 97, '...'))) . "</option>";
		$fullAnswers[] = myiconv($webim_encoding, getoutputenc(), $answer['vcvalue']);
	}
	$page['predefinedAnswers'] = $predefinedres;
	$page['fullPredefinedAnswers'] = json_encode($fullAnswers);
	$params = "thread=" . $thread['threadid'] . "&amp;token=" . $thread['ltoken'];
	$page['redirectLink'] = "$webimroot/operator/agent.php?" . $params . "&amp;act=redirect";

	$page['namePostfix'] = "";
	$page['frequency'] = $settings['updatefrequency_chat'];
}

function update_thread_access($threadid, $params)
{
	$db = Database::getInstance();
	$clause = "";
	$values = array();
	foreach ($params as $k => $v) {
		if (strlen($clause) > 0)
			$clause .= ", ";
		$clause .= $k . "=?";
		$values[] = $v;
	}
	$values[] = $threadid;

	$db->query(
		"update {chatthread} set {$clause} where threadid = ?",
		$values
	);
}

function ping_thread($thread, $isuser, $istyping)
{
	global $kind_for_agent, $state_queue, $state_loading, $state_chatting, $state_waiting, $kind_conn, $connection_timeout;

	$params = array(($isuser ? "lastpinguser" : "lastpingagent") => "CURRENT_TIMESTAMP",
					($isuser ? "userTyping" : "agentTyping") => ($istyping ? "1" : "0"));

	$lastping = $thread[$isuser ? "lpagent" : "lpuser"];
	$current = $thread['current'];

	if ($thread['istate'] == $state_loading && $isuser) {
		$params['istate'] = $state_queue;
		commit_thread($thread['threadid'], $params);
		return;
	}

	if ($lastping > 0 && abs($current - $lastping) > $connection_timeout) {
		$params[$isuser ? "lastpingagent" : "lastpinguser"] = "0";
		if (!$isuser) {
			$message_to_post = getstring_("chat.status.user.dead", $thread['locale']);
			post_message_($thread['threadid'], $kind_for_agent, $message_to_post, null, $lastping + $connection_timeout);
		} else if ($thread['istate'] == $state_chatting) {

			$message_to_post = getstring_("chat.status.operator.dead", $thread['locale']);
			post_message_($thread['threadid'], $kind_conn, $message_to_post, null, $lastping + $connection_timeout);
			$params['istate'] = $state_waiting;
			$params['nextagent'] = 0;
			commit_thread($thread['threadid'], $params);
			return;
		}
	}

	update_thread_access($thread['threadid'], $params);
}

function commit_thread($threadid, $params)
{
	$db = Database::getInstance();

	$query = "update {chatthread} t " .
		"set lrevision = ?, dtmmodified = CURRENT_TIMESTAMP";
		$values = array(next_revision());
	foreach ($params as $k => $v) {
		$query .= ", " . $k . "=?";
		$values[] = $v;
	}
	$query .= " where threadid = ?";
	$values[] = $threadid;

	$db->query($query, $values);
}

function rename_user($thread, $newname)
{
	global $kind_events;

	commit_thread($thread['threadid'], array('userName' => $newname));

	if ($thread['userName'] != $newname) {
		post_message_($thread['threadid'], $kind_events,
					  getstring2_("chat.status.user.changedname", array($thread['userName'], $newname), $thread['locale']));
	}
}

function close_thread($thread, $isuser)
{
	global $state_closed, $kind_events;

	if ($thread['istate'] != $state_closed) {
		commit_thread(
			$thread['threadid'],
			array(
				'istate' => $state_closed,
				'messageCount' => "(SELECT COUNT(*) FROM {chatmessage} WHERE {chatmessage}.threadid = t.threadid AND ikind = 1)"
			)
		);
	}

	$message = $isuser ? getstring2_("chat.status.user.left", array($thread['userName']), $thread['locale'])
			: getstring2_("chat.status.operator.left", array($thread['agentName']), $thread['locale']);
	post_message_($thread['threadid'], $kind_events, $message);
}

function close_old_threads()
{
	global $state_closed, $state_left, $state_chatting, $settings;
	if ($settings['thread_lifetime'] == 0) {
		return;
	}

	$db = Database::getInstance();

	$query = "update {chatthread} set lrevision = :next_revision, " .
		"dtmmodified = CURRENT_TIMESTAMP, istate = :state_closed " .
		"where istate <> :state_closed and istate <> :state_left " .
		"and lastpingagent <> 0 and lastpinguser <> 0 and " .
		"(ABS(UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(lastpinguser)) > ".
		":thread_lifetime and " .
		"ABS(UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(lastpingagent)) > ".
		":thread_lifetime)";

	$db->query(
		$query,
		array(
			':next_revision' => next_revision(),
			':state_closed' => $state_closed,
			':state_left' => $state_left,
			':thread_lifetime' => $settings['thread_lifetime']
		)
	);
}

function thread_by_id($id)
{
	$db = Database::getInstance();
	return $db->query(
		"select threadid,userName,agentName,agentId,lrevision,istate,ltoken,userTyping, " .
		"agentTyping,unix_timestamp(dtmmodified) as modified, " .
		"unix_timestamp(dtmcreated) as created, " .
		"unix_timestamp(dtmchatstarted) as chatstarted,remote,referer,locale," .
		"unix_timestamp(lastpinguser) as lpuser,unix_timestamp(lastpingagent) as lpagent," .
		"unix_timestamp(CURRENT_TIMESTAMP) as current,nextagent,shownmessageid,userid, " .
		"userAgent,groupid from {chatthread} where threadid = ?",
		array($id),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
}

function ban_for_addr($addr)
{
	$db = Database::getInstance();
	return $db->query(
		"select banid,comment from {chatban} " .
		"where unix_timestamp(dtmtill) > unix_timestamp(CURRENT_TIMESTAMP) AND address = ?",
		array($addr),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
}

function create_thread($groupid, $username, $remoteHost, $referer, $lang, $userid, $userbrowser, $initialState)
{
	$db = Database::getInstance();

	$query = "insert into {chatthread} (userName,userid,ltoken,remote,referer, " .
		"lrevision,locale,userAgent,dtmcreated,dtmmodified,istate" .
		($groupid ? ",groupid" : "") . ") values " .
		"(?,?,?,?,?,?,?,?,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,?" .
		($groupid ? ", ?" : "") . ")";

	$values = array(
		$username,
		$userid,
		next_token(),
		$remoteHost,
		$referer,
		next_revision(),
		$lang,
		$userbrowser,
		$initialState
	);

	if ($groupid) {
		$values[] = $groupid;
	}

	$db->query($query, $values);
	$id = $db->insertedId();

	$newthread = thread_by_id($id);
	return $newthread;
}

function do_take_thread($threadid, $operatorId, $operatorName, $chatstart = false)
{
	global $state_chatting;
	$params = array("istate" => $state_chatting,
			"nextagent" => 0,
			"agentId" => $operatorId,
			"agentName" => $operatorName);
	if ($chatstart){
		$params['dtmchatstarted'] = "CURRENT_TIMESTAMP";
	}
	commit_thread($threadid, $params);
}

function reopen_thread($threadid)
{
	global $state_queue, $state_loading, $state_waiting, $state_chatting, $state_closed, $state_left, $kind_events, $settings;

	$thread = thread_by_id($threadid);

	if (!$thread)
		return FALSE;

	if ($settings['thread_lifetime'] != 0 && abs($thread['lpuser'] - time()) > $settings['thread_lifetime'] && abs($thread['lpagent'] - time()) > $settings['thread_lifetime']) {
		return FALSE;
	}

	if ($thread['istate'] == $state_closed || $thread['istate'] == $state_left)
		return FALSE;

	if ($thread['istate'] != $state_chatting && $thread['istate'] != $state_queue && $thread['istate'] != $state_loading) {
		commit_thread(
			$threadid,
			array("istate" => $state_waiting, "nextagent" => 0)
		);
	}

	post_message_($thread['threadid'], $kind_events, getstring_("chat.status.user.reopenedthread", $thread['locale']));
	return $thread;
}

function take_thread($thread, $operator)
{
	global $state_queue, $state_loading, $state_waiting, $state_chatting, $kind_events, $kind_avatar, $home_locale;

	$state = $thread['istate'];
	$threadid = $thread['threadid'];
	$message_to_post = "";
	$chatstart = $thread['chatstarted'] == 0;

	$operatorName = ($thread['locale'] == $home_locale) ? $operator['vclocalename'] : $operator['vccommonname'];

	if ($state == $state_queue || $state == $state_waiting || $state == $state_loading) {
		do_take_thread($threadid, $operator['operatorid'], $operatorName, $chatstart);

		if ($state == $state_waiting) {
			if ($operatorName != $thread['agentName']) {
				$message_to_post = getstring2_("chat.status.operator.changed", array($operatorName, $thread['agentName']), $thread['locale']);
			} else {
				$message_to_post = getstring2_("chat.status.operator.returned", array($operatorName), $thread['locale']);
			}
		} else {
			$message_to_post = getstring2_("chat.status.operator.joined", array($operatorName), $thread['locale']);
		}
	} else if ($state == $state_chatting) {
		if ($operator['operatorid'] != $thread['agentId']) {
			do_take_thread($threadid, $operator['operatorid'], $operatorName, $chatstart);
			$message_to_post = getstring2_("chat.status.operator.changed", array($operatorName, $thread['agentName']), $thread['locale']);
		}
	} else {
		return false;
	}

	if ($message_to_post) {
		post_message($threadid, $kind_events, $message_to_post);
		post_message($threadid, $kind_avatar, $operator['vcavatar'] ? $operator['vcavatar'] : "");
	}
	return true;
}

function check_for_reassign($thread, $operator)
{
	global $state_waiting, $home_locale, $kind_events, $kind_avatar;
	$operatorName = ($thread['locale'] == $home_locale) ? $operator['vclocalename'] : $operator['vccommonname'];
	if ($thread['istate'] == $state_waiting &&
		($thread['nextagent'] == $operator['operatorid']
		 || $thread['agentId'] == $operator['operatorid'])) {
		do_take_thread($thread['threadid'], $operator['operatorid'], $operatorName);
		if ($operatorName != $thread['agentName']) {
			$message_to_post = getstring2_("chat.status.operator.changed", array($operatorName, $thread['agentName']), $thread['locale']);
		} else {
			$message_to_post = getstring2_("chat.status.operator.returned", array($operatorName), $thread['locale']);
		}

		post_message($thread['threadid'], $kind_events, $message_to_post);
		post_message($thread['threadid'], $kind_avatar, $operator['vcavatar'] ? $operator['vcavatar'] : "");
	}
}

function check_connections_from_remote($remote)
{
	global $settings, $state_closed, $state_left;
	if ($settings['max_connections_from_one_host'] == 0) {
		return true;
	}

	$db = Database::getInstance();
	$result = $db->query(
		"select count(*) as opened from {chatthread} " .
		"where remote = ? AND istate <> ? AND istate <> ?",
		array($remote, $state_closed, $state_left),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);

	if ($result && isset($result['opened'])) {
		return $result['opened'] < $settings['max_connections_from_one_host'];
	}
	return true;
}

function visitor_from_request()
{
	global $namecookie, $webim_encoding, $usercookie;
	$defaultName = getstring("chat.default.username");
	$userName = $defaultName;
	if (isset($_COOKIE[$namecookie])) {
		$data = base64_decode(strtr($_COOKIE[$namecookie], '-_,', '+/='));
		if (strlen($data) > 0) {
			$userName = myiconv("utf-8", $webim_encoding, $data);
		}
	}

	if ($userName == $defaultName) {
		$userName = getgetparam('name', $userName);
	}

	if (isset($_COOKIE[$usercookie])) {
		$userId = $_COOKIE[$usercookie];
	} else {
		$userId = get_user_id();
		setcookie($usercookie, $userId, time() + 60 * 60 * 24 * 365);
	}
	return array('id' => $userId, 'name' => $userName);
}

function get_remote_host()
{
	$extAddr = $_SERVER['REMOTE_ADDR'];
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
		$_SERVER['HTTP_X_FORWARDED_FOR'] != $_SERVER['REMOTE_ADDR']) {
		$extAddr = $_SERVER['REMOTE_ADDR'] . ' (' . $_SERVER['HTTP_X_FORWARDED_FOR'] . ')';
	}
	return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : $extAddr;
}

?>