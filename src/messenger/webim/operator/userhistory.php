<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
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
 *    Pavel Petroshenko - initial API and implementation
 */

require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('../libs/chat.php');
require_once('../libs/userinfo.php');
require_once('../libs/pagination.php');

$operator = check_login();
loadsettings();

$page = array();

setlocale(LC_TIME, getstring("time.locale"));

$userid = "";
if (isset($_GET['userid'])) {
	$userid = verifyparam("userid", "/^.{0,63}$/", "");
}

function threads_by_userid($userid)
{
	global $mysqlprefix;
	if ($userid == "") {
		return null;
	}
	$link = connect();

	$query = sprintf("select unix_timestamp(dtmcreated) as created, unix_timestamp(dtmmodified) as modified, " .
					 " threadid, remote, agentName, userName " .
					 "from ${mysqlprefix}chatthread " .
					 "where userid=\"$userid\" order by created DESC", $userid);

	$result = mysql_query($query, $link) or die(' Query failed: ' . mysql_error($link) /*.": ".$query*/);

	$foundThreads = array();
	while ($thread = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$foundThreads[] = $thread;
	}

	mysql_free_result($result);
	mysql_close($link);
	return $foundThreads;
}

$found = threads_by_userid($userid);

prepare_menu($operator);
setup_pagination($found, 6);
start_html_output();
require('../view/userhistory.php');
?>