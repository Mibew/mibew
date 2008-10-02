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
require_once('../libs/operator.php');
require_once('../libs/chat.php');

$operator = check_login();

$page = array( 'operator' => topage(get_operator_name($operator)) );

if( isset($_GET['threadid'])) {
        $threadid = verifyparam( "threadid", "/^(\d{1,9})?$/", "");
	$lastid = -1;
	$page['threadMessages'] = get_messages($threadid,"html",false,$lastid);
}

start_html_output();
require('../view/thread_log.php');
?>