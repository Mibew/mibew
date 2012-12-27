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
require_once(dirname(__FILE__).'/classes/thread.php');

$namecookie = "WEBIM_Data";
$usercookie = "WEBIM_UserID";

function get_user_id()
{
	return (time() + microtime()) . rand(0, 99999999);
}

function message_to_text($msg)
{
	if ($msg['kind'] == Thread::KIND_AVATAR) {
		return "";
	}
	$message_time = date("H:i:s ", $msg['created']);
	if ($msg['kind'] == Thread::KIND_USER || $msg['kind'] == Thread::KIND_AGENT) {
		if ($msg['name'])
			return $message_time . $msg['name'] . ": " . $msg['message'] . "\n";
		else
			return $message_time . $msg['message'] . "\n";
	} else if ($msg['kind'] == Thread::KIND_INFO) {
		return $message_time . $msg['message'] . "\n";
	} else {
		return $message_time . "[" . $msg['message'] . "]\n";
	}
}

function get_user_name($username, $addr, $id)
{
	return str_replace(
		"{addr}", $addr,
		str_replace(
			"{id}", $id,
			str_replace("{name}", $username, Settings::get('usernamepattern'))
		)
	);
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

$knownAgents = array("opera", "msie", "chrome", "safari", "firefox", "netscape", "mozilla");

function get_remote_level($useragent)
{
	global $knownAgents;
	$useragent = strtolower($useragent);
	foreach ($knownAgents as $agent) {
		if (strstr($useragent, $agent)) {
			if (preg_match("/" . $agent . "[\\s\/]?(\\d+(\\.\\d+)?)/", $useragent, $matches)) {
				$ver = $matches[1];

				if (is_ajax_browser($agent, $ver, $useragent)) {
					return "ajaxed";
				} else {
					return "old";
				}

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
	global $page;
	$toplevelgroup = (!$group)?array():get_top_level_group($group);
	$page['ct.company.name'] = topage(empty($toplevelgroup['vctitle'])?Settings::get('title'):$toplevelgroup['vctitle']);
	$page['ct.company.chatLogoURL'] = topage(empty($toplevelgroup['vclogo'])?Settings::get('logo'):$toplevelgroup['vclogo']);
	$page['webimHost'] = topage(empty($toplevelgroup['vchosturl'])?Settings::get('hosturl'):$toplevelgroup['vchosturl']);
}

function setup_leavemessage($name, $email, $message, $groupid, $groupname, $info, $referrer, $canshowcaptcha)
{
	global $page;
	$page['formname'] = topage($name);
	$page['formemail'] = topage($email);
	$page['formmessage'] = $message ? topage($message) : "";
	$page['showcaptcha'] = Settings::get("enablecaptcha") == "1" && $canshowcaptcha ? "1" : "";
	$page['formgroupid'] = $groupid;
	$page['formgroupname'] = $groupname;
	$page['forminfo'] = topage($info);
	$page['referrer'] = urlencode(topage($referrer));

	if (Settings::get('enablegroups') == '1') {
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
	global $page;

	$page['formname'] = topage($name);
	$page['formemail'] = topage($email);
	$page['formgroupid'] = $groupid;
	$page['forminfo'] = topage($info);
	$page['referrer'] = urlencode(topage($referrer));

	if (Settings::get('enablegroups') == '1' && Settings::get('surveyaskgroup') == '1') {
		$groups = setup_groups_select($groupid, true);
		if ($groups) {
			$page['groups'] = $groups['select'];
			$page['group.descriptions'] = json_encode($groups['descriptions']);
			$page['default.department.description'] = $groups['defaultdescription'];
		}
	}

	$page['showemail'] = Settings::get("surveyaskmail") == "1" ? "1" : "";
	$page['showmessage'] = Settings::get("surveyaskmessage") == "1" ? "1" : "";
	$page['showname'] = Settings::get('usercanchangename') == "1" ? "1" : "";
}

function setup_groups_select($groupid, $markoffline)
{
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
		if ($k['ilastseen'] !== NULL && $k['ilastseen'] < Settings::get('online_timeout')) {
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
	global $page, $webimroot;
	$page = array();
	if (! empty($thread->groupId)) {
		$group = group_by_id($thread->groupId);
		$group = get_top_level_group($group);
	} else {
		$group = array();
	}
	$page['agent'] = false;
	$page['user'] = true;
	$page['canpost'] = true;
	$nameisset = getstring("chat.default.username") != $thread->userName;
	$page['displ1'] = $nameisset ? "none" : "inline";
	$page['displ2'] = $nameisset ? "inline" : "none";
	$page['level'] = $level;
	$page['ct.chatThreadId'] = $thread->id;
	$page['ct.token'] = $thread->lastToken;
	$page['ct.user.name'] = htmlspecialchars(topage($thread->userName));
	$page['canChangeName'] = Settings::get('usercanchangename') == "1";
	$page['chat.title'] = topage(empty($group['vcchattitle'])?Settings::get('chattitle'):$group['vcchattitle']);
	$page['chat.close.confirmation'] = getlocal('chat.close.confirmation');

	setup_logo($group);
	if (Settings::get('sendmessagekey') == 'enter') {
		$page['send_shortcut'] = "Enter";
		$page['ignorectrl'] = 1;
	} else {
		$page['send_shortcut'] = is_mac_opera() ? "&#8984;-Enter" : "Ctrl-Enter";
		$page['ignorectrl'] = 0;
	}

	$params = "thread=" . $thread->id . "&amp;token=" . $thread->lastToken;
	$page['mailLink'] = "$webimroot/client.php?" . $params . "&amp;level=$level&amp;act=mailthread";

	if (Settings::get('enablessl') == "1" && !is_secure_request()) {
		$page['sslLink'] = get_app_location(true, true) . "/client.php?" . $params . "&amp;level=$level";
	}

	$page['isOpera95'] = is_agent_opera95();
	$page['neediframesrc'] = needsFramesrc();

	$page['frequency'] = Settings::get('updatefrequency_chat');
}

function setup_chatview_for_operator($thread, $operator)
{
	global $page, $webimroot, $company_logo_link, $webim_encoding, $company_name;
	$page = array();
	if (! is_null($thread->groupId)) {
		$group = group_by_id($thread->groupId);
		$group = get_top_level_group($group);
	} else {
		$group = array();
	}
	$page['agent'] = true;
	$page['user'] = false;
	$page['canpost'] = $thread->agentId == $operator['operatorid'];
	$page['ct.chatThreadId'] = $thread->id;
	$page['ct.token'] = $thread->lastToken;
	$page['ct.user.name'] = htmlspecialchars(topage(get_user_name($thread->userName, $thread->remote, $thread->userId)));
	$page['chat.title'] = topage(empty($group['vcchattitle'])?Settings::get('chattitle'):$group['vcchattitle']);
	$page['chat.close.confirmation'] = getlocal('chat.close.confirmation');

	setup_logo($group);
	if (Settings::get('sendmessagekey') == 'enter') {
		$page['send_shortcut'] = "Enter";
		$page['ignorectrl'] = 1;
	} else {
		$page['send_shortcut'] = is_mac_opera() ? "&#8984;-Enter" : "Ctrl-Enter";
		$page['ignorectrl'] = 0;
	}

	if (Settings::get('enablessl') == "1" && !is_secure_request()) {
		$page['sslLink'] = get_app_location(true, true) . "/operator/agent.php?thread=" . $thread->id . "&amp;token=" . $thread->lastToken;
	}
	$page['isOpera95'] = is_agent_opera95();
	$page['neediframesrc'] = needsFramesrc();
	$page['historyParams'] = array("userid" => "" . $thread->userId);
	$page['historyParamsLink'] = add_params($webimroot . "/operator/userhistory.php", $page['historyParams']);
	if (Settings::get('enabletracking')) {
	    $visitor = track_get_visitor_by_threadid($thread->id);
	    $page['trackedParams'] = array("visitor" => "" . $visitor['visitorid']);
	    $page['trackedParamsLink'] = add_params($webimroot . "/operator/tracked.php", $page['trackedParams']);
	}
	$canned_messages = load_canned_messages($thread->locale, 0);
	if ($thread->groupId) {
		$canned_messages = array_merge(
			load_canned_messages($thread->locale, $thread->groupId),
			$canned_messages
		);
	};

	// Get predefined answers
	$predefined_answers = array();
	foreach ($canned_messages as $answer) {
		$predefined_answers[] = array(
			'short' => htmlspecialchars(
				topage($answer['vctitle']?$answer['vctitle']:cutstring($answer['vcvalue'], 97, '...'))
			),
			'full' => myiconv($webim_encoding, getoutputenc(), $answer['vcvalue'])
		);
	}
	$page['predefinedAnswers'] = json_encode($predefined_answers);

	$params = "thread=" . $thread->id . "&amp;token=" . $thread->lastToken;
	$page['redirectLink'] = "$webimroot/operator/agent.php?" . $params . "&amp;act=redirect";

	$page['namePostfix'] = "";
	$page['frequency'] = Settings::get('updatefrequency_chat');
}

function ban_for_addr($addr)
{
	$db = Database::getInstance();
	return $db->query(
		"select banid,comment from {chatban} " .
		"where dtmtill > :now AND address = :addr",
		array(
			':addr' => $addr,
			':now' => time()
		),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
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