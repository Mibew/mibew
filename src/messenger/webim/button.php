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
require_once('libs/groups.php');

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
if($referer && isset($_SESSION['threadid'])) {
	$link = connect();
	$thread = thread_by_id_($_SESSION['threadid'], $link);
    if ($thread && $thread['istate'] != $state_closed) {
        $msg = getstring2_("chat.client.visited.page", array($referer), $thread['locale']);
        post_message_($thread['threadid'], $kind_for_agent,$msg,$link);
    }
    mysql_close($link);
}

$image = verifyparam(isset($_GET['image']) ? "image" : "i", "/^\w+$/", "webim");
$lang = verifyparam(isset($_GET['language']) ? "language" : "lang", "/^[\w-]{2,5}$/", "");
if(!$lang || !locale_exists($lang)) {
	$lang = $current_locale;
}

$groupid = verifyparam( "group", "/^\d{1,8}$/", "");
if($groupid) {
	loadsettings();
	if($settings['enablegroups'] == '1') {
		$group = group_by_id($groupid);
		if(!$group) {
			$groupid = "";
		}
	} else {
		$groupid = "";
	}
}

$image_postfix = has_online_operators($groupid) ? "on" : "off";
$filename = "locales/${lang}/button/${image}_${image_postfix}.gif";

$fp = fopen($filename, 'rb') or die("no image");
header("Content-Type: image/gif");
header("Content-Length: ".filesize($filename));
if(function_exists('fpassthru')){
	@fpassthru($fp);
} else {
	while( (!feof($fp)) && (connection_status()==0)){
		print(fread($fp, 1024*8));
		flush();
	}
	fclose($fp);
}
exit;
?>