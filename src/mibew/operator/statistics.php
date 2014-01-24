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

require_once('../libs/common.php');
require_once('../libs/chat.php');
require_once('../libs/operator.php');

$operator = check_login();

setlocale(LC_TIME, getstring("time.locale"));

$page = array();
$page['operator'] = topage(get_operator_name($operator));
$page['availableDays'] = range(1, 31);
$page['availableMonth'] = get_month_selection(time() - 400 * 24 * 60 * 60, time() + 50 * 24 * 60 * 60);
$page['showresults'] = false;
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

$link = connect();

$page['reportByDate'] = select_multi_assoc("select DATE(dtmcreated) as date, COUNT(distinct threadid) as threads, SUM(${mysqlprefix}chatmessage.ikind = " . intval($kind_agent) . ") as agents, SUM(${mysqlprefix}chatmessage.ikind = " . intval($kind_user) . ") as users " .
										   "from ${mysqlprefix}chatmessage where unix_timestamp(dtmcreated) >= " . intval($start) . " AND unix_timestamp(dtmcreated) < " . intval($end) . " group by DATE(dtmcreated) order by dtmcreated desc", $link);

$page['reportByDateTotal'] = select_one_row("select COUNT(distinct threadid) as threads, SUM(${mysqlprefix}chatmessage.ikind = " . intval($kind_agent) . ") as agents, SUM(${mysqlprefix}chatmessage.ikind = " . intval($kind_user) . ") as users " .
											"from ${mysqlprefix}chatmessage where unix_timestamp(dtmcreated) >= " . intval($start) . " AND unix_timestamp(dtmcreated) < " . intval($end), $link);

$page['reportByAgent'] = select_multi_assoc("select vclocalename as name, COUNT(distinct threadid) as threads, SUM(ikind = " . intval($kind_agent) . ") as msgs, AVG(CHAR_LENGTH(tmessage)) as avglen " .
											"from ${mysqlprefix}chatmessage, ${mysqlprefix}chatoperator " .
											"where agentId = operatorid AND unix_timestamp(dtmcreated) >= " . intval($start) . " AND unix_timestamp(dtmcreated) < " . intval($end) . " group by operatorid", $link);

$page['showresults'] = count($errors) == 0;

mysql_close($link);

prepare_menu($operator);
start_html_output();
require('../view/statistics.php');
?>