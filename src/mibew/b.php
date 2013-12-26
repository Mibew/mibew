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

require_once(dirname(__FILE__).'/libs/init.php');
require_once(MIBEW_FS_ROOT.'/libs/chat.php');
require_once(MIBEW_FS_ROOT.'/libs/operator.php');
require_once(MIBEW_FS_ROOT.'/libs/groups.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/thread.php');

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
if($referer && isset($_SESSION['threadid'])) {
	$thread = Thread::load($_SESSION['threadid']);
    if ($thread && $thread->state != Thread::STATE_CLOSED) {
        $msg = getstring2_("chat.client.visited.page", array($referer), $thread->locale);
		$thread->postMessage(Thread::KIND_FOR_AGENT, $msg);
    }
}

$image = verifyparam(isset($_GET['image']) ? "image" : "i", "/^\w+$/", "mibew");
$lang = verifyparam(isset($_GET['language']) ? "language" : "lang", "/^[\w-]{2,5}$/", "");
if(!$lang || !locale_exists($lang)) {
	$lang = $current_locale;
}

$groupid = verifyparam( "group", "/^\d{1,8}$/", "");
if($groupid) {
	if(Settings::get('enablegroups') == '1') {
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