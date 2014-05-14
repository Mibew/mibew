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
use Mibew\Database;
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');

$operator = check_login();
csrf_check_token();

$page = array(
    'errors' => array(),
);

setlocale(LC_TIME, getstring("time.locale"));

$db = Database::getInstance();

if (isset($_GET['act']) && $_GET['act'] == 'del') {
    $ban_id = isset($_GET['id']) ? $_GET['id'] : "";

    if (!preg_match("/^\d+$/", $ban_id)) {
        $page['errors'][] = "Cannot delete: wrong argument";
    }

    if (count($page['errors']) == 0) {
        $db->query("DELETE FROM {chatban} WHERE banid = ?", array($ban_id));
        header("Location: " . MIBEW_WEB_ROOT . "/operator/blocked.php");
        exit;
    }
}

$blocked_list = $db->query(
    "SELECT banid, dtmtill AS till,address,comment FROM {chatban}",
    null,
    array('return_rows' => Database::RETURN_ALL_ROWS)
);

foreach ($blocked_list as &$item) {
    $item['comment'] = $item['comment'];
}
unset($item);

$page['title'] = getlocal("page_bans.title");
$page['menuid'] = "blocked";

$pagination = setup_pagination($blocked_list);
$page['pagination'] = $pagination['info'];
$page['pagination.items'] = $pagination['items'];

$page = array_merge($page, prepare_menu($operator));

$page_style = new PageStyle(PageStyle::getCurrentStyle());
$page_style->render('blocked_visitors', $page);
