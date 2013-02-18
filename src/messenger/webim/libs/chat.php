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
require_once(dirname(__FILE__).'/classes/mibew_api.php');
require_once(dirname(__FILE__).'/classes/mibew_api_interaction.php');
require_once(dirname(__FILE__).'/classes/mibew_api_chat_interaction.php');
require_once(dirname(__FILE__).'/classes/mibew_api_execution_context.php');
require_once(dirname(__FILE__).'/classes/request_processor.php');
require_once(dirname(__FILE__).'/classes/client_side_processor.php');
require_once(dirname(__FILE__).'/classes/thread_processor.php');

$namecookie = "WEBIM_Data";
$usercookie = "WEBIM_UserID";

function get_user_id()
{
	return (time() + microtime()) . rand(0, 99999999);
}

function message_to_text($msg)
{
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

/**
 * Prepare logo data
 *
 * @param array $group Group info
 * @return array Array of logo data
 */
function setup_logo($group = NULL) {
	$data = array();

	$toplevelgroup = (!$group)?array():get_top_level_group($group);

	$data['company'] = array(
		'name' => topage(empty($toplevelgroup['vctitle'])
			? Settings::get('title')
			: $toplevelgroup['vctitle']),

		'chatLogoURL' => topage(empty($toplevelgroup['vclogo'])
			? Settings::get('logo')
			: $toplevelgroup['vclogo'])
	);

	$data['webimHost'] = topage(empty($toplevelgroup['vchosturl'])
		? Settings::get('hosturl')
		: $toplevelgroup['vchosturl']);

	return $data;
}

/**
 * Prepare data to display leave message form
 *
 * @param string $name User name
 * @param string $email User email
 * @param string $message First message text
 * @param int $groupid Id of selected group
 * @param string $groupname Name of selected group
 * @param string $info User info
 * @param string $referrer URL of referrer page
 * @return array Array of leave message form data
 *
 * @todo Think about $info param. It seems to this param is meaningless.
 */
function setup_leavemessage($name, $email, $message, $groupid, $groupname, $info, $referrer) {
	$data = array();

	$canshowcaptcha = can_show_captcha();
	$data['formname'] = topage($name);
	$data['formemail'] = topage($email);
	$data['formmessage'] = $message ? topage($message) : "";
	$data['showcaptcha'] = Settings::get("enablecaptcha") == "1" && $canshowcaptcha ? "1" : "";
	$data['formgroupid'] = $groupid;
	$data['formgroupname'] = $groupname;
	$data['forminfo'] = topage($info);
	$data['referrer'] = urlencode(topage($referrer));

	if (Settings::get('enablegroups') == '1') {
		$groups = setup_groups_select($groupid, false);
		if ($groups) {
			$data['groups'] = $groups['select'];
			$data['group.descriptions'] = json_encode($groups['descriptions']);
			$data['default.department.description'] = $groups['defaultdescription'];
		}
	}

	return $data;
}

/**
 * Prepare data to dispaly pre-chat survey
 *
 * @param string $name User name
 * @param string $email User email
 * @param int $groupid Id of selected group
 * @param string $info User info
 * @param string $referrer URL of referrer page
 * @return array Array of survey data
 *
 * @todo Think about $info param. It seems to this param is meaningless.
 */
function setup_survey($name, $email, $groupid, $info, $referrer) {
	$data = array();

	$data['formname'] = topage($name);
	$data['formemail'] = topage($email);
	$data['formgroupid'] = $groupid;
	$data['forminfo'] = topage($info);
	$data['referrer'] = urlencode(topage($referrer));

	if (Settings::get('enablegroups') == '1' && Settings::get('surveyaskgroup') == '1') {
		$groups = setup_groups_select($groupid, true);
		if ($groups) {
			$data['groups'] = $groups['select'];
			$data['group.descriptions'] = json_encode($groups['descriptions']);
			$data['default.department.description'] = $groups['defaultdescription'];
		}
	}

	$data['showemail'] = Settings::get("surveyaskmail") == "1" ? "1" : "";
	$data['showmessage'] = Settings::get("surveyaskmessage") == "1" ? "1" : "";
	$data['showname'] = Settings::get('usercanchangename') == "1" ? "1" : "";

	return $data;
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
	$defaultdescription = "";
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

/**
 * Prepare some data for chat for both user and operator
 *
 * @param Thread $thread thread object
 * @return array Array of chat view data
 */
function setup_chatview(Thread $thread) {
	$data = array();

	// Get group info
	if (! empty($thread->groupId)) {
		$group = group_by_id($thread->groupId);
		$group = get_top_level_group($group);
	} else {
		$group = array();
	}

	// Set thread params
	$data['chat.thread.id'] = $thread->id;
	$data['chat.thread.token'] = $thread->lastToken;
	$data['chat.title'] = topage(
		empty($group['vcchattitle'])
			? Settings::get('chattitle')
			: $group['vcchattitle']
	);

	// Setup logo
	$data = array_merge_recursive(
		$data,
		setup_logo($group)
	);

	// Set enter key shortcut
	if (Settings::get('sendmessagekey') == 'enter') {
		$data['send_shortcut'] = "Enter";
		$data['ignorectrl'] = 1;
	} else {
		$data['send_shortcut'] = is_mac_opera()
			? "&#8984;-Enter"
			: "Ctrl-Enter";
		$data['ignorectrl'] = 0;
	}

	// Set some browser info
	$data['isOpera95'] = is_agent_opera95();
	$data['neediframesrc'] = needsFramesrc();

	// Set refresh frequency
	$data['frequency'] = Settings::get('updatefrequency_chat');

	// Load dialogs style options
	$style_config = get_dialogs_style_config(getchatstyle());
	$data['chatStyles.mailWindowParams'] = $style_config['mail']['window_params'];

	// Load core style options
	$style_config = get_core_style_config();
	$data['coreStyles.historyWindowParams'] = $style_config['history']['window_params'];

	return $data;
}

/**
 * Prepare some data for chat for user
 *
 * @global string $webimroot Root URL path for Mibew
 * @param Thread $thread thread object
 * @param string $level Chat level. Indicates ajax or old chat window should
 * be used
 * @return array Array of chat view data
 */
function setup_chatview_for_user(Thread $thread, $level) {
	global $webimroot;

	$data = setup_chatview($thread);

	// Set user info
	$data['agent'] = false;
	$data['user'] = true;
	$data['canpost'] = true;
	$data['level'] = $level;
	$data['chat.user.name'] = htmlspecialchars(topage($thread->userName));
	$data['canChangeName'] = Settings::get('usercanchangename') == "1";

	$params = "thread=" . $thread->id . "&amp;token=" . $thread->lastToken;

	// Set link to send mail page
	$data['mailLink'] = "$webimroot/client.php?" . $params . "&amp;level=$level&amp;act=mailthread";

	// Set SSL link
	if (Settings::get('enablessl') == "1" && !is_secure_request()) {
		$data['sslLink'] = get_app_location(true, true) . "/client.php?" . $params . "&amp;level=$level";
	}

	return $data;
}

/**
 * Prepare some data for chat for operator
 *
 * @global string $webimroot Root URL path for Mibew
 * @global string $webim_encoding Current Mibew encoding
 * @param Thread $thread thread object
 * @param string $level Chat level. Indicates ajax or old chat window should
 * be used
 * @return array Array of chat view data
 */
function setup_chatview_for_operator(Thread $thread, $operator) {
	global $webimroot, $webim_encoding;

	$data = setup_chatview($thread);

	// Set operator info
	$data['agent'] = true;
	$data['user'] = false;
	$data['canpost'] = $thread->agentId == $operator['operatorid'];
	$data['chat.user.name'] = htmlspecialchars(topage(get_user_name($thread->userName, $thread->remote, $thread->userId)));

	// Set SSL link
	if (Settings::get('enablessl') == "1" && !is_secure_request()) {
		$data['sslLink'] = get_app_location(true, true) . "/operator/agent.php?thread=" . $thread->id . "&amp;token=" . $thread->lastToken;
	}

	// Set history window params
	$data['historyParams'] = array("userid" => (string)$thread->userId);
	$data['historyParamsLink'] = add_params(
		$webimroot . "/operator/userhistory.php",
		$data['historyParams']
	);

	// Set tracking params
	if (Settings::get('enabletracking')) {
	    $visitor = track_get_visitor_by_threadid($thread->id);
	    $data['trackedParams'] = array("visitor" => "" . $visitor['visitorid']);
	    $data['trackedParamsLink'] = add_params($webimroot . "/operator/tracked.php", $data['trackedParams']);
	}

	// Get predefined answers
	$canned_messages = load_canned_messages($thread->locale, 0);
	if ($thread->groupId) {
		$canned_messages = array_merge(
			load_canned_messages($thread->locale, $thread->groupId),
			$canned_messages
		);
	};

	$predefined_answers = array();
	foreach ($canned_messages as $answer) {
		$predefined_answers[] = array(
			'short' => htmlspecialchars(
				topage($answer['vctitle']?$answer['vctitle']:cutstring($answer['vcvalue'], 97, '...'))
			),
			'full' => myiconv($webim_encoding, getoutputenc(), $answer['vcvalue'])
		);
	}
	$data['predefinedAnswers'] = json_encode($predefined_answers);

	// Set link to user redirection page
	$params = "thread=" . $thread->id . "&amp;token=" . $thread->lastToken;
	$data['redirectLink'] = "$webimroot/operator/agent.php?" . $params . "&amp;act=redirect";

	$data['namePostfix'] = "";

	return $data;
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