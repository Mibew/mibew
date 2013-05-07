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

require_once('../libs/init.php');
require_once('../libs/chat.php');
require_once('../libs/operator.php');
require_once('../libs/statistics.php');
require_once('../libs/cron.php');

$operator = check_login();
force_password($operator);

setlocale(LC_TIME, getstring("time.locale"));

$page = array();
$page['operator'] = topage(get_operator_name($operator));
$page['availableDays'] = range(1, 31);
$page['availableMonth'] = get_month_selection(time() - 400 * 24 * 60 * 60, time() + 50 * 24 * 60 * 60);
$page['showresults'] = false;
$statisticstype = verifyparam("type", "/^(bydate|byagent|bypage)$/", "bydate");
$page['type'] = $statisticstype;
$page['showbydate'] = ($statisticstype == 'bydate');
$page['showbyagent'] = ($statisticstype == 'byagent');
$page['showbypage'] = ($statisticstype == 'bypage');

$page['cron_path'] = cron_get_uri(Settings::get('cron_key'));
$page['last_cron_run'] = Settings::get('_last_cron_run');

$errors = array();

if (isset($_GET['startday'])) {
	$startday = verifyparam("startday", "/^\d+$/");
	$startmonth = verifyparam("startmonth", "/^\d{2}.\d{2}$/");
	$endday = verifyparam("endday", "/^\d+$/");
	$endmonth = verifyparam("endmonth", "/^\d{2}.\d{2}$/");
	$start = get_form_date($startday, $startmonth);
	$end = get_form_date($endday, $endmonth) + 24 * 60 * 60;

} else {
	$curr = getdate(time());
	if ($curr['mday'] < 7) {
		// previous month
		if ($curr['mon'] == 1) {
			$month = 12;
			$year = $curr['year'] - 1;
		} else {
			$month = $curr['mon'] - 1;
			$year = $curr['year'];
		}
		$start = mktime(0, 0, 0, $month, 1, $year);
		$end = mktime(0, 0, 0, $month, date("t", $start), $year) + 24 * 60 * 60;
	} else {
		$start = mktime(0, 0, 0, $curr['mon'], 1, $curr['year']);
		$end = time() + 24 * 60 * 60;
	}
}
set_form_date($start, "start");
set_form_date($end - 24 * 60 * 60, "end");

if ($start > $end) {
	$errors[] = getlocal("statistics.wrong.dates");
}

$activetab = 0;
$db = Database::getInstance();
if ($statisticstype == 'bydate') {
	$page['reportByDate'] = $db->query(
		"SELECT DATE(FROM_UNIXTIME(date)) AS date, " .
			"threads, " .
			"operatormessages AS agents, " .
			"usermessages AS users, " .
			"averagewaitingtime AS avgwaitingtime, " .
			"averagechattime AS avgchattime " .
		"FROM {chatthreadstatistics} s " .
		"WHERE s.date >= :start " .
			"AND s.date < :end " .
		"ORDER BY s.date DESC",
		array(
			':start' => $start,
			':end' => $end
		),
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);

	$page['reportByDateTotal'] = $db->query(
		"SELECT DATE(FROM_UNIXTIME(date)) AS date, " .
			"SUM(threads) AS threads, " .
			"SUM(operatormessages) AS agents, " .
			"SUM(usermessages) AS users, " .
			"ROUND(SUM(averagewaitingtime * s.threads) / SUM(s.threads),1) AS avgwaitingtime, " .
			"ROUND(SUM(averagechattime * s.threads) / SUM(s.threads),1) AS avgchattime " .
		"FROM {chatthreadstatistics} s " .
		"WHERE s.date >= :start " .
			"AND s.date < :end",
		array(
			':start' => $start,
			':end' => $end
		),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);

	$activetab = 0;
} elseif($statisticstype == 'byagent') {
	$page['reportByAgent'] = $db->query(
		"SELECT o.vclocalename AS name, " .
			"s.threads AS threads, " .
			"s.messages AS msgs, " .
			"s.averagelength AS avglen " .
		"FROM {chatoperatorstatistics} s, {chatoperator} o " .
		"WHERE s.operatorid = o.operatorid " .
			"AND s.date >= :start " .
			"AND s.date < :end " .
		"GROUP BY s.operatorid",
		array(
			':start' => $start,
			':end' => $end
		),
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
	$activetab = 1;
} elseif($statisticstype == 'bypage') {
	$page['reportByPage'] = $db->query(
		"SELECT SUM(visits) as visittimes, " .
			"address, " .
			"SUM(chats) as chattimes " .
		"FROM {visitedpagestatistics} " .
		"WHERE date >= :start " .
			"AND date < :end " .
		"GROUP BY address",
		array(':start' => $start, ':end' => $end),
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
	$activetab = 2;
}
$page['showresults'] = count($errors) == 0;

prepare_menu($operator);
setup_statistics_tabs($activetab);
start_html_output();
require('../view/statistics.php');
?>