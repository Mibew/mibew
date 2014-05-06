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

// Import namespaces and classes of the core
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');

$operator = check_login();
csrf_check_token();

$string_id = verify_param("key", "/^\d{0,9}$/", "");

$page = array(
    'errors' => array(),
);

$page_style = new PageStyle(PageStyle::getCurrentStyle());

if ($string_id) {
    $canned_message = load_canned_message($string_id);
    if (!$canned_message) {
        $page['errors'][] = getlocal("cannededit.no_such");
        $string_id = "";
    } else {
        $title = $canned_message['vctitle'];
        $message = $canned_message['vcvalue'];
    }
} else {
    $message = '';
    $title = '';
    $page['locale'] = verify_param("lang", "/^[\w-]{2,5}$/", "");
    $page['groupid'] = "";
    $page['groupid'] = verify_param("group", "/^\d{0,8}$/");
}

if (isset($_POST['message']) && isset($_POST['title'])) {
    $title = get_param('title');
    if (!$title) {
        $page['errors'][] = no_field("form.field.title");
    }

    $message = get_param('message');
    if (!$message) {
        $page['errors'][] = no_field("form.field.message");
    }

    if (count($page['errors']) == 0) {
        if ($string_id) {
            save_canned_message($string_id, $title, $message);
        } else {
            add_canned_message($page['locale'], $page['groupid'], $title, $message);
        }
        $page['saved'] = true;
        $page = array_merge($page, prepare_menu($operator, false));
        $page_style->render('canned_edit', $page);
        exit;
    }
}

$page['saved'] = false;
$page['key'] = $string_id;
$page['formtitle'] = to_page($title);
$page['formmessage'] = to_page($message);
$page['title'] = empty($string_id) ? getlocal("cannednew.title") : getlocal("cannededit.title");

$page = array_merge($page, prepare_menu($operator, false));

$page_style->render('canned_edit', $page);
