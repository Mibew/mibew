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

// Import namespaces and classes of the core
use Mibew\Settings;
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');
require_once(MIBEW_FS_ROOT . '/libs/chat.php');
require_once(MIBEW_FS_ROOT . '/libs/operator.php');
require_once(MIBEW_FS_ROOT . '/libs/track.php');

$operator = check_login();

setlocale(LC_TIME, getstring("time.locale"));

if (Settings::get('enabletracking') == "0") {
    die("Tracking disabled!");
}

if (isset($_GET['thread'])) {
    $thread_id = verify_param("thread", "/^\d{1,8}$/");
} else {
    $visitor_id = verify_param("visitor", "/^\d{1,8}$/");
}

if (isset($thread_id)) {
    $visitor = track_get_visitor_by_thread_id($thread_id);
    if (!$visitor) {
        die("Wrong thread!");
    }
} else {
    $visitor = track_get_visitor_by_id($visitor_id);
    if (!$visitor) {
        die("Wrong visitor!");
    }
}
$path = track_get_path($visitor);

$page['entry'] = htmlspecialchars($visitor['entry']);
$page['history'] = array();
ksort($path);
foreach ($path as $k => $v) {
    $page['history'][] = array(
        'date' => date_to_text($k),
        'link' => htmlspecialchars($v),
    );
}

$page['title'] = getlocal("tracked.path");
$page['show_small_login'] = false;

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('tracked', $page);
