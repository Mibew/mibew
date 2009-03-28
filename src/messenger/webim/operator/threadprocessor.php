<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2009 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('../libs/chat.php');
require_once('../libs/userinfo.php');

$operator = check_login();

$page = array();

function thread_info($id) {
	$link = connect();
	$thread = select_one_row("select userName,agentName,remote,userAgent,".
			"unix_timestamp(dtmmodified) as modified, unix_timestamp(dtmcreated) as created,".
			"vclocalname as groupName ".
			"from chatthread left join chatgroup on chatthread.groupid = chatgroup.groupid ".
			"where threadid = ". $id, $link );
	mysql_close($link);
	return $thread;
}


if( isset($_GET['threadid'])) {
        $threadid = verifyparam( "threadid", "/^(\d{1,9})?$/", "");
	$lastid = -1;
	$page['threadMessages'] = get_messages($threadid,"html",false,$lastid);
	$page['thread'] = thread_info($threadid);
}

prepare_menu($operator, false);
start_html_output();
require('../view/thread_log.php');
?>