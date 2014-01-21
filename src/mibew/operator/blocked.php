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
use Mibew\Database;
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(MIBEW_FS_ROOT.'/libs/chat.php');
require_once(MIBEW_FS_ROOT.'/libs/operator.php');
require_once(MIBEW_FS_ROOT.'/libs/pagination.php');

$operator = check_login();
csrfchecktoken();

$page = array(
	'errors' => array(),
);

setlocale(LC_TIME, getstring("time.locale"));

$db = Database::getInstance();

if (isset($_GET['act']) && $_GET['act'] == 'del') {
	$banId = isset($_GET['id']) ? $_GET['id'] : "";

	if (!preg_match("/^\d+$/", $banId)) {
		$page['errors'][] = "Cannot delete: wrong argument";
	}

	if (count($page['errors']) == 0) {
		$db->query("delete from {chatban} where banid = ?", array($banId));
		header("Location: " . MIBEW_WEB_ROOT . "/operator/blocked.php");
		exit;
	}
}

$blockedList = $db->query(
	"select banid, dtmtill as till,address,comment from {chatban}",
	NULL,
	array('return_rows' => Database::RETURN_ALL_ROWS)
);

$page['title'] = getlocal("page_bans.title");
$page['menuid'] = "blocked";

$pagination = setup_pagination($blockedList);
$page['pagination'] = $pagination['info'];
$page['pagination.items'] = $pagination['items'];

$page = array_merge(
	$page,
	prepare_menu($operator)
);

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('blocked_visitors', $page);

?>