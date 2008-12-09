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
 */

require_once('../libs/common.php');
require_once('../libs/chat.php');
require_once('../libs/operator.php');

$operator = get_logged_in();
if( !$operator ) {
	start_xml_output();
	echo "<error><descr>".myiconv($webim_encoding,"utf-8",escape_with_cdata(getstring("agent.not_logged_in")))."</descr></error>";
	exit;
}

$threadstate_to_string = array(
	$state_queue => "wait",
	$state_waiting => "prio",
	$state_chatting => "chat",
	$state_closed => "closed",
	$state_loading => "wait"
);

$threadstate_key = array(
	$state_queue => "chat.thread.state_wait",
	$state_waiting => "chat.thread.state_wait_for_another_agent",
	$state_chatting => "chat.thread.state_chatting_with_agent",
	$state_closed => "chat.thread.state_closed",
	$state_loading => "chat.thread.state_loading"
);

function get_useragent_version($userAgent) {
    global $knownAgents;
    if (is_array($knownAgents)) {
	$userAgent = strtolower($userAgent);
	foreach( $knownAgents as $agent ) {
		if( strstr($userAgent,$agent) ) {
			if( preg_match( "/".$agent."[\\s\/]?(\\d+(\\.\\d+(\\.\\d+)?)?)/", $userAgent, $matches ) ) {
				$ver = $matches[1];
				if($agent=='safari') {
					if(preg_match( "/version\/(\\d+(\\.\\d+(\\.\\d+)?)?)/", $userAgent, $matches)) {
						$ver = $matches[1];
					} else {
						$ver = "1 or 2 (build ".$ver.")";
					}
					if(preg_match( "/mobile\/(\\d+(\\.\\d+(\\.\\d+)?)?)/", $userAgent, $matches)) {
						$userAgent = "iPhone ".$matches[1]." ($agent $ver)";
						break;
					}
				}

				$userAgent = ucfirst($agent)." ".$ver;
				break;
			}
		}
	}
    }
    return $userAgent;
}

function get_user_addr($addr) {
	global $settings;
	if(preg_match( "/(\\d+\\.\\d+\\.\\d+\\.\\d+)/", $addr, $matches )) {
		$userip = $matches[1];
		return get_popup(str_replace("{ip}", $userip, $settings['geolink']), htmlspecialchars($addr), "GeoLocation", "ip$userip", $settings['geolinkparams']);
	}
	return htmlspecialchars($addr);
}

function thread_to_xml($thread,$link) {
	global $state_chatting, $threadstate_to_string, $threadstate_key,
			$webim_encoding, $operator, $settings,
			$can_viewthreads, $can_takeover;
	$state = $threadstate_to_string[$thread['istate']];
	$result = "<thread id=\"".$thread['threadid']."\" stateid=\"$state\"";
	if( $state == "closed" )
		return $result."/>";

	$state = getstring($threadstate_key[$thread['istate']]);
	$nextagent = $thread['nextagent'] != 0 ? operator_by_id_($thread['nextagent'],$link) : null;
	$threadoperator = $nextagent ? get_operator_name($nextagent)
						: ($thread['agentName'] ? $thread['agentName'] : "-");

	if(!($thread['istate'] == $state_chatting && $thread['agentId'] != $operator['operatorid'] && !is_capable($can_takeover,$operator))) {
		$result .= " canopen=\"true\"";
	}
	if ($thread['agentId'] != $operator['operatorid'] && $thread['nextagent'] != $operator['operatorid']
			&& is_capable($can_viewthreads, $operator)) {
		$result .= " canview=\"true\"";
	}
	if ($settings['enableban'] == "1") {
		$result .= " canban=\"true\"";
	}

	$banForThread = $settings['enableban'] == "1" ? ban_for_addr_($thread['remote'],$link) : false;
	if($banForThread) {
		$result .= " ban=\"blocked\" banid=\"".$banForThread['banid']."\"";
	}

	$result .= " state=\"$state\" typing=\"".$thread['userTyping']."\">";
	$result .= "<name>".htmlspecialchars(htmlspecialchars(get_user_name($thread['userName'],$thread['remote'], $thread['userid'])))."</name>";
	$result .= "<addr>".htmlspecialchars(get_user_addr($thread['remote']))."</addr>";
	$result .= "<agent>".htmlspecialchars(htmlspecialchars($threadoperator))."</agent>";
	$result .= "<time>".$thread['unix_timestamp(dtmcreated)']."000</time>";
	$result .= "<modified>".$thread['unix_timestamp(dtmmodified)']."000</modified>";

	if($banForThread) {
		$result .= "<reason>".$banForThread['comment']."</reason>";
	}

	$userAgent = get_useragent_version($thread['userAgent']);
	$result .= "<useragent>".$userAgent."</useragent>";
	if( $thread["shownmessageid"] != 0 ) {
		$query = "select tmessage from chatmessage where messageid = ".$thread["shownmessageid"];
		$line = select_one_row($query, $link);
		if( $line ) {
			$message = preg_replace("/[\r\n\t]+/", " ", $line["tmessage"]);
			$result .= "<message>".htmlspecialchars(htmlspecialchars($message))."</message>";
		}
	}
	$result .= "</thread>";
	return $result;
}

function print_pending_threads($since) {
	global $webim_encoding;
	$link = connect();

	$revision = $since;
	$output = array();
	$query = "select threadid, userName, agentName, unix_timestamp(dtmcreated), userTyping, ".
			 "unix_timestamp(dtmmodified), lrevision, istate, remote, nextagent, agentId, userid, shownmessageid, userAgent ".
			 "from chatthread where lrevision > $since ORDER BY threadid";
	$rows = select_multi_assoc($query, $link);
	foreach ($rows as $row) {
		$thread = thread_to_xml($row,$link);
		$output[] = $thread;
		if( $row['lrevision'] > $revision )
			$revision = $row['lrevision'];
	}

	mysql_close($link);

	start_xml_output();
	echo "<threads revision=\"$revision\" time=\"".time()."000\">";
	foreach( $output as $thr ) {
		print myiconv($webim_encoding,"utf-8",$thr);
	}
	echo "</threads>";
}

$since = verifyparam( "since", "/^\d{1,9}$/", 0);

loadsettings();
print_pending_threads($since);
notify_operator_alive($operator['operatorid']);
exit;

?>
