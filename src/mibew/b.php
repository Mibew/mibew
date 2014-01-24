<?php
/*
 * Copyright 2005-2014 the original author or authors.
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

require_once('libs/common.php');
require_once('libs/chat.php');
require_once('libs/operator.php');
require_once('libs/groups.php');

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
if($referer && isset($_SESSION['threadid'])) {
	$link = connect();
	$thread = thread_by_id_($_SESSION['threadid'], $link);
    if ($thread && $thread['istate'] != $state_closed) {
        $msg = getstring2_("chat.client.visited.page", array($referer), $thread['locale'], true);
        post_message_($thread['threadid'], $kind_for_agent,$msg,$link);
    }
    mysql_close($link);
}

$image = verifyparam(isset($_GET['image']) ? "image" : "i", "/^\w+$/", "mibew");
$lang = verifyparam(isset($_GET['language']) ? "language" : "lang", "/^[\w-]{2,5}$/", "");
if(!$lang || !locale_pattern_check($lang) || !locale_exists($lang)) {
	$lang = $current_locale;
}

$groupid = verifyparam( "group", "/^\d{1,10}$/", "");
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
$filename = dirname(__FILE__) . "/locales/${lang}/button/${image}_${image_postfix}.gif";
if (!file_exists($filename)) {
	die("no image");
}
$fp = fopen($filename, 'rb') or die("unable to get image");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
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