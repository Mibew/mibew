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
 *    Fedor Fetisov - tracking and inviting implementation
 */

require_once('../libs/chat.php');
require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('../libs/track.php');

$operator = check_login();

loadsettings();
setlocale(LC_TIME, getstring("time.locale"));

if ($settings['enabletracking'] == "0") {
    die("Tracking disabled!");
}

if (isset($_GET['thread'])) {
    $threadid = verifyparam("thread", "/^\d{1,8}$/");
}
else {
    $visitorid = verifyparam("visitor", "/^\d{1,8}$/");
}

$link = connect();
if (isset($threadid)) {
    $visitor = track_get_visitor_by_threadid($threadid, $link);
    if (!$visitor) {
	die("Wrong thread!");
    }
}
else {
    $visitor = track_get_visitor_by_id($visitorid, $link);
    if (!$visitor) {
	die("Wrong visitor!");
    }
}
$path = track_get_path($visitor, $link);
close_connection($link);

$page['entry'] = htmlspecialchars($visitor['entry']);
$page['history'] = array();
ksort($path);
foreach ($path as $k => $v) {
    $page['history'][] = array( 'date' => date_to_text($k),
				'link' => htmlspecialchars($v) );
}
start_html_output();
require('../view/tracked.php');
?>