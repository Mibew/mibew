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

require_once('../libs/init.php');
require_once('../libs/chat.php');
require_once('../libs/userinfo.php');
require_once('../libs/operator.php');
require_once('../libs/groups.php');
require_once('../libs/track.php');

$operator = get_logged_in();
if (!$operator) {
	start_xml_output();
	echo "<error><descr>" . myiconv($webim_encoding, "utf-8", escape_with_cdata(getstring("agent.not_logged_in"))) . "</descr></error>";
	exit;
}

$threadstate_to_string = array(
	$state_queue => "wait",
	$state_waiting => "prio",
	$state_chatting => "chat",
	$state_closed => "closed",
	$state_loading => "wait",
	$state_left => "closed"
);

$threadstate_key = array(
	$state_queue => "chat.thread.state_wait",
	$state_waiting => "chat.thread.state_wait_for_another_agent",
	$state_chatting => "chat.thread.state_chatting_with_agent",
	$state_closed => "chat.thread.state_closed",
	$state_loading => "chat.thread.state_loading"
);

function thread_to_xml($thread)
{
	global $state_chatting, $threadstate_to_string, $threadstate_key,
		$webim_encoding, $operator, $can_viewthreads, $can_takeover;
	$state = $threadstate_to_string[$thread['istate']];
	$result = "<thread id=\"" . $thread['threadid'] . "\" stateid=\"$state\"";
	if ($state == "closed")
		return $result . "/>";

	$state = getstring($threadstate_key[$thread['istate']]);
	$nextagent = $thread['nextagent'] != 0 ? operator_by_id($thread['nextagent']) : null;
	$threadoperator = $nextagent ? get_operator_name($nextagent)
			: ($thread['agentName'] ? $thread['agentName'] : "-");

	if ($threadoperator == "-" && $thread['groupname']) {
		$threadoperator = "- " . $thread['groupname'] . " -";
	}

	if (!($thread['istate'] == $state_chatting && $thread['agentId'] != $operator['operatorid'] && !is_capable($can_takeover, $operator))) {
		$result .= " canopen=\"true\"";
	}
	if ($thread['agentId'] != $operator['operatorid'] && $thread['nextagent'] != $operator['operatorid']
		&& is_capable($can_viewthreads, $operator)) {
		$result .= " canview=\"true\"";
	}
	if (Settings::get('enableban') == "1") {
		$result .= " canban=\"true\"";
	}

	$banForThread = Settings::get('enableban') == "1" ? ban_for_addr($thread['remote']) : false;
	if ($banForThread) {
		$result .= " ban=\"blocked\" banid=\"" . $banForThread['banid'] . "\"";
	}

	$result .= " state=\"$state\" typing=\"" . $thread['userTyping'] . "\">";
	$result .= "<name>";
	if ($banForThread) {
		$result .= htmlspecialchars(getstring('chat.client.spam.prefix'));
	}
	$result .= htmlspecialchars(htmlspecialchars(get_user_name($thread['userName'], $thread['remote'], $thread['userid']))) . "</name>";
	$result .= "<addr>" . htmlspecialchars(get_user_addr($thread['remote'])) . "</addr>";
	$result .= "<agent>" . htmlspecialchars(htmlspecialchars($threadoperator)) . "</agent>";
	$result .= "<time>" . $thread['dtmcreated'] . "000</time>";
	$result .= "<modified>" . $thread['dtmmodified'] . "000</modified>";

	if ($banForThread) {
		$result .= "<reason>" . $banForThread['comment'] . "</reason>";
	}

	$userAgent = get_useragent_version($thread['userAgent']);
	$result .= "<useragent>" . $userAgent . "</useragent>";
	if ($thread["shownmessageid"] != 0) {
		$db = Database::getInstance();
		$line = $db->query(
			"select tmessage from {chatmessage} where messageid = ?",
			array($thread["shownmessageid"]),
			array('return_rows' => Database::RETURN_ONE_ROW)
		);
		if ($line) {
			$message = preg_replace("/[\r\n\t]+/", " ", $line["tmessage"]);
			$result .= "<message>" . htmlspecialchars(htmlspecialchars($message)) . "</message>";
		}
	}
	$result .= "</thread>";
	return $result;
}

function print_pending_threads($groupids, $since)
{
	global $webim_encoding, $state_closed, $state_left;
	$db = Database::getInstance();

	$revision = $since;
	$query = "select threadid, userName, agentName, dtmcreated, userTyping, " .
		"dtmmodified, lrevision, istate, remote, nextagent, agentId, " .
		"userid, shownmessageid, userAgent, (select vclocalname from {chatgroup} where {chatgroup}.groupid = {chatthread}.groupid) as groupname " .
		"from {chatthread} where lrevision > :since " .
		($since <= 0
			? "AND istate <> {$state_closed} AND istate <> {$state_left} "
			: "") .
		(Settings::get('enablegroups') == '1'
			? "AND (groupid is NULL" . ($groupids
				? " OR groupid IN ($groupids) OR groupid IN (SELECT parent FROM {chatgroup} WHERE groupid IN ($groupids)) "
				: "") .
			") "
			: "") .
		"ORDER BY threadid";
	$rows = $db->query(
		$query,
		array(':since' => $since),
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);

	$output = array();
	foreach ($rows as $row) {
		$thread = thread_to_xml($row);
		$output[] = $thread;
		if ($row['lrevision'] > $revision)
			$revision = $row['lrevision'];
	}

	echo "<threads revision=\"$revision\" time=\"" . time() . "000\">";
	foreach ($output as $thr) {
		print myiconv($webim_encoding, "utf-8", $thr);
	}
	echo "</threads>";
}

function print_operators($operator)
{
	global $webim_encoding;
	echo "<operators>";

	$list_options = in_isolation($operator)?array('isolated_operator_id' => $operator['operatorid']):array();
	$operators = get_operators_list($list_options);

	foreach ($operators as $operator) {
		if (!operator_is_online($operator))
			continue;

		$name = myiconv($webim_encoding, "utf-8", htmlspecialchars(htmlspecialchars($operator['vclocalename'])));
		$away = operator_is_away($operator) ? " away=\"1\"" : "";

		echo "<operator name=\"$name\"$away/>";
	}
	echo "</operators>";
}

function visitor_to_xml($visitor)
{
    $result = "<visitor id=\"" . $visitor['visitorid'] . "\">";

//    $result .= "<userid>" . htmlspecialchars($visitor['userid']) . "</userid>";
    $result .= "<username>" . htmlspecialchars($visitor['username']) . "</username>";

    $result .= "<time>" . $visitor['firsttime'] . "000</time>";
    $result .= "<modified>" . $visitor['lasttime'] . "000</modified>";
//    $result .= "<entry>" . htmlspecialchars($visitor['entry']) . "</entry>";

//    $result .= "<path>";
//    $path = track_retrieve_path($visitor);
//    ksort($path);
//    foreach ($path as $k => $v) {
//	$result .= "<url visited=\"" . $k . "000\">" . htmlspecialchars($v) . "</url>";
//    }
//    $result .= "</path>";

    $details = track_retrieve_details($visitor);
    $userAgent = get_useragent_version($details['user_agent']);
    $result .= "<useragent>" . $userAgent . "</useragent>";
    $result .= "<addr>" . htmlspecialchars(get_user_addr($details['remote_host'])) . "</addr>";

    $result .= "<invitations>" . $visitor['invitations'] . "</invitations>";
    $result .= "<chats>" . $visitor['chats'] . "</chats>";

    $result .= "<invitation>";
    if ($visitor['invited']) {
	$result .= "<invitationtime>" . $visitor['invitationtime'] . "000</invitationtime>";
	$operator = get_operator_name(operator_by_id($visitor['invitedby']));
	$result .= "<operator>" . htmlspecialchars(htmlspecialchars($operator)) . "</operator>";
    }
    $result .= "</invitation>";

    $result .= "</visitor>";
    return $result;
}

function print_visitors()
{
	global $webim_encoding, $state_closed, $state_left;

	$db = Database::getInstance();

// Remove old visitors
	$db->query(
		"DELETE FROM {chatsitevisitor} " .
		"WHERE (:now - lasttime) > :lifetime ".
		"AND (threadid IS NULL OR " .
		"(SELECT count(*) FROM {chatthread} WHERE threadid = {chatsitevisitor}.threadid " .
		"AND istate <> {$state_closed} AND istate <> {$state_left}) = 0)",
		array(
			':lifetime' => Settings::get('tracking_lifetime'),
			':now' => time()
		)
	);

// Remove old invitations
	$db->query(
		"UPDATE {chatsitevisitor} SET invited = 0, invitationtime = NULL, invitedby = NULL".
		" WHERE threadid IS NULL AND (:now - invitationtime) > :lifetime",
		array(
			':lifetime' => Settings::get('invitation_lifetime'),
			':now' => time()
		)
	);

// Remove associations of visitors with closed threads
	$db->query(
		"UPDATE {chatsitevisitor} SET threadid = NULL WHERE threadid IS NOT NULL AND" .
		" (SELECT count(*) FROM {chatthread} WHERE threadid = {chatsitevisitor}.threadid" .
		" AND istate <> {$state_closed} AND istate <> {$state_left}) = 0"
	);

// Remove old visitors' tracks
	$db->query(
		"DELETE FROM {visitedpage} WHERE (:now - visittime) > :lifetime " .
		" AND visitorid NOT IN (SELECT visitorid FROM {chatsitevisitor})",
		array(
			':lifetime' => Settings::get('tracking_lifetime'),
			':now' => time()
		)
	);

	$query = "SELECT visitorid, userid, username, firsttime, lasttime, " .
			 "entry, details, invited, invitationtime, invitedby, invitations, chats " .
			 "FROM {chatsitevisitor} " .
			 "WHERE threadid IS NULL " .
			 "ORDER BY invited, lasttime DESC, invitations";
	$query .= (Settings::get('visitors_limit') == '0') ? "" : " LIMIT " . Settings::get('visitors_limit');
	
	$rows = $db->query($query, NULL, array('return_rows' => Database::RETURN_ALL_ROWS));
	
	$output = array();
	foreach ($rows as $row) {
		$visitor = visitor_to_xml($row);
		$output[] = $visitor;
	}

	echo "<visitors>";
	foreach ($output as $thr) {
		print myiconv($webim_encoding, "utf-8", $thr);
	}
	echo "</visitors>";
}

$since = verifyparam("since", "/^\d{1,9}$/", 0);
$status = verifyparam("status", "/^\d{1,2}$/", 0);
$showonline = verifyparam("showonline", "/^1$/", 0);
$showvisitors = verifyparam("showvisitors", "/^1$/", 0);

if (!isset($_SESSION["${mysqlprefix}operatorgroups"])) {
	$_SESSION["${mysqlprefix}operatorgroups"] = get_operator_groupslist($operator['operatorid']);
}
close_old_threads();
$groupids = $_SESSION["${mysqlprefix}operatorgroups"];

start_xml_output();
echo '<update>';
if ($showonline) {
	print_operators($operator);
}
print_pending_threads($groupids, $since);
if ($showvisitors) {
	print_visitors();
}
echo '</update>';
notify_operator_alive($operator['operatorid'], $status);
exit;

?>