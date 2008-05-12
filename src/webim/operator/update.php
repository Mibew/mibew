<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Internet Services Ltd.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require('../libs/common.php');
require('../libs/chat.php');
require('../libs/operator.php');

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
	$state_closed => "closed"
);

$threadstate_key = array(
	$state_queue => "chat.thread.state_wait",
	$state_waiting => "chat.thread.state_wait_for_another_agent",
	$state_chatting => "chat.thread.state_chatting_with_agent",
	$state_closed => "chat.thread.state_closed"
);

function thread_to_xml($thread) {
	global $threadstate_to_string, $threadstate_key, $webim_encoding;
	$state = $threadstate_to_string[$thread['istate']];
	$result = "<thread id=\"".$thread['threadid']."\" stateid=\"$state\"";
	if( $state == "closed" )
		return $result."/>";

	$state = getstring($threadstate_key[$thread['istate']]);
	$threadoperator = ($thread['agentName'] ? $thread['agentName'] : "-");

	$result .= " canopen=\"true\"";

	$result .= " state=\"$state\" typing=\"".$thread['userTyping']."\">";
	$result .= "<name>".htmlspecialchars(htmlspecialchars(get_user_name($thread['userName'])))."</name>";
	$result .= "<addr>".htmlspecialchars(htmlspecialchars($thread['remote']))."</addr>";
	$result .= "<agent>".htmlspecialchars(htmlspecialchars($threadoperator))."</agent>";
	$result .= "<time>".$thread['unix_timestamp(dtmcreated)']."000</time>";
	$result .= "<modified>".$thread['unix_timestamp(dtmmodified)']."000</modified>";
	
	$result .= "</thread>";
	return $result;
}

function print_pending_threads($since) {
	global $webim_encoding;
	$link = connect();

	$revision = $since;
	$output = array();
	$query = "select threadid, userName, agentName, unix_timestamp(dtmcreated), userTyping, ".
			 "unix_timestamp(dtmmodified), lrevision, istate, remote ".
			 "from chatthread where lrevision > $since ORDER BY threadid";
	$result = mysql_query($query,$link) or die(' Query failed: ' .mysql_error().": ".$query);

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$thread = thread_to_xml($row);
		$output[] = $thread;
		if( $row['lrevision'] > $revision )
			$revision = $row['lrevision'];
	}

	mysql_free_result($result);
	mysql_close($link);

	start_xml_output();
	echo "<threads revision=\"$revision\" time=\"".time()."000\">";
	foreach( $output as $thr ) {
		print myiconv($webim_encoding,"utf-8",$thr);
	}
	echo "</threads>";
}

////////

$since = verifyparam( "since", "/^\d{1,9}$/", 0);

print_pending_threads($since);
notify_operator_alive($operator['operatorid']);
exit;

?>
