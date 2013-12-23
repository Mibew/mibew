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

require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(dirname(dirname(__FILE__)).'/libs/chat.php');
require_once(dirname(dirname(__FILE__)).'/libs/operator.php');
require_once(dirname(dirname(__FILE__)).'/libs/track.php');
require_once(dirname(dirname(__FILE__)).'/libs/interfaces/style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/page_style.php');

$operator = check_login();

setlocale(LC_TIME, getstring("time.locale"));

if (Settings::get('enabletracking') == "0") {
    die("Tracking disabled!");
}

if (isset($_GET['thread'])) {
    $threadid = verifyparam("thread", "/^\d{1,8}$/");
}
else {
    $visitorid = verifyparam("visitor", "/^\d{1,8}$/");
}

if (isset($threadid)) {
    $visitor = track_get_visitor_by_threadid($threadid);
    if (!$visitor) {
	die("Wrong thread!");
    }
}
else {
    $visitor = track_get_visitor_by_id($visitorid);
    if (!$visitor) {
	die("Wrong visitor!");
    }
}
$path = track_get_path($visitor);

$page['entry'] = htmlspecialchars($visitor['entry']);
$page['history'] = array();
ksort($path);
foreach ($path as $k => $v) {
    $page['history'][] = array( 'date' => date_to_text($k),
				'link' => htmlspecialchars($v) );
}

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('tracked');

?>