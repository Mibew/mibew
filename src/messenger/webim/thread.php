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
 */

require_once('libs/common.php');
require_once('libs/chat.php');
require_once('libs/operator.php');

$act = verifyparam( "act", "/^(refresh|post|rename|close|ping)$/");
$token = verifyparam( "token", "/^\d{1,9}$/");
$threadid = verifyparam( "thread", "/^\d{1,9}$/");
$isuser = verifyparam( "user", "/^true$/", "false") == 'true';
$outformat = ((verifyparam( "html", "/^on$/", "off") == 'on') ? "html" : "xml");
$istyping = verifyparam( "typed", "/^1$/", "") == '1';

if($threadid == 0 && ($token == 123 || $token == 124)) {
	require_once('libs/demothread.php');
	$lastid = verifyparam( "lastid", "/^\d{1,9}$/", 0);
	demo_process_thread($act,$outformat,$lastid,$isuser,$token == 123,$istyping,$act=="post"?getrawparam('message') : "");
	exit;
}

$thread = thread_by_id($threadid);
if( !$thread || !isset($thread['ltoken']) || $token != $thread['ltoken'] ) {
	die("wrong thread");
}

function show_ok_result($resid) {
	start_xml_output();
	echo "<$resid></$resid>";
	exit;
}

function show_error($message) {
	start_xml_output();
	echo "<error><descr>$message</descr></error>";
	exit;
}

ping_thread($thread, $isuser,$istyping);

if( !$isuser && $act != "rename" ) {
	$operator = check_login();
	check_for_reassign($thread,$operator);
}

if( $act == "refresh" ) {
	$lastid = verifyparam( "lastid", "/^\d{1,9}$/", -1);
	print_thread_messages($thread, $token, $lastid, $isuser,$outformat, $isuser ? null : $operator['operatorid']);
	exit;

} else if( $act == "post" ) {
	$lastid = verifyparam( "lastid", "/^\d{1,9}$/", -1);
	$message = getrawparam('message');

	$kind = $isuser ? $kind_user : $kind_agent;
	$from = $isuser ? $thread['userName'] : $thread['agentName'];

	if(!$isuser && $operator['operatorid'] != $thread['agentId']) {
		show_error("cannot send");
	}

	$link = connect();
	$postedid = post_message_($threadid,$kind,$message,$link,$from,null,$isuser ? null : $operator['operatorid'] );
	if($isuser && $thread["shownmessageid"] == 0) {
		commit_thread( $thread['threadid'], array('shownmessageid' => $postedid), $link);
	}
	mysql_close($link);
	print_thread_messages($thread, $token, $lastid, $isuser, $outformat, $isuser ? null : $operator['operatorid']);
	exit;

} else if( $act == "rename" ) {

	loadsettings();
	if( $settings['usercanchangename'] != "1" ) {
		show_error("server: forbidden to change name");
	}

	$newname = getrawparam('name');

	rename_user($thread, $newname);
	$data = strtr(base64_encode(myiconv($webim_encoding,"utf-8",$newname)), '+/=', '-_,');
	setcookie($namecookie, $data, time()+60*60*24*365);
	show_ok_result("rename");

} else if( $act == "ping" ) {
	show_ok_result("ping");

} else if( $act == "close" ) {

	if( $isuser || $thread['agentId'] == $operator['operatorid']) {
		close_thread($thread, $isuser);
	}
	show_ok_result("closed");

}

?>