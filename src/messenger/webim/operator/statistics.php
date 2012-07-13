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

require_once('../libs/common.php');
require_once('../libs/chat.php');
require_once('../libs/operator.php');
require_once('../libs/statistics.php');

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
		"select DATE(t.dtmcreated) as date, COUNT(distinct t.threadid) as threads, SUM(m.ikind = :kind_agent) as agents, SUM(m.ikind = :kind_user) as users, ROUND(AVG(unix_timestamp(t.dtmchatstarted)-unix_timestamp(t.dtmcreated)),1) as avgwaitingtime, ROUND(AVG(tmp.lastmsgtime - unix_timestamp(t.dtmchatstarted)),1) as avgchattime " .
		"from {chatmessage} m, {chatthread} t, (SELECT i.threadid, unix_timestamp(MAX(i.dtmcreated)) AS lastmsgtime  FROM {chatmessage} i WHERE (ikind = :kind_user OR ikind = :kind_agent) GROUP BY i.threadid) tmp " .
		"where m.threadid = t.threadid AND tmp.threadid = t.threadid AND unix_timestamp(t.dtmchatstarted) <> 0 AND unix_timestamp(m.dtmcreated) >= :start AND unix_timestamp(m.dtmcreated) < :end group by DATE(m.dtmcreated) order by m.dtmcreated desc",
		array(
			':kind_agent' => $kind_agent,
			':kind_user' => $kind_user,
			':start' => $start,
			':end' => $end
		),
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
	
	$page['reportByDateTotal'] = $db->query(
		"select DATE(t.dtmcreated) as date, COUNT(distinct t.threadid) as threads, SUM(m.ikind = :kind_agent) as agents, SUM(m.ikind = :kind_user) as users, ROUND(AVG(unix_timestamp(t.dtmchatstarted)-unix_timestamp(t.dtmcreated)),1) as avgwaitingtime, ROUND(AVG(tmp.lastmsgtime - unix_timestamp(t.dtmchatstarted)),1) as avgchattime " .
		"from {chatmessage} m, {chatthread} t, (SELECT i.threadid, unix_timestamp(MAX(i.dtmcreated)) AS lastmsgtime FROM {chatmessage} i WHERE (ikind = :kind_user OR ikind = :kind_agent) GROUP BY i.threadid) tmp " .
		"where m.threadid = t.threadid AND tmp.threadid = t.threadid AND unix_timestamp(t.dtmchatstarted) <> 0 AND unix_timestamp(m.dtmcreated) >= :start AND unix_timestamp(m.dtmcreated) < :end",
		array(
			':kind_agent' => $kind_agent,
			':kind_user' => $kind_user,
			':start' => $start,
			':end' => $end
		),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
	$activetab = 0;
} elseif($statisticstype == 'byagent') {
	$page['reportByAgent'] = $db->query(
		"select vclocalename as name, COUNT(distinct threadid) as threads, " .
		"SUM(ikind = :kind_agent) as msgs, AVG(CHAR_LENGTH(tmessage)) as avglen " .
		"from {chatmessage}, {chatoperator} " .
		"where agentId = operatorid AND unix_timestamp(dtmcreated) >= :start " .
		"AND unix_timestamp(dtmcreated) < :end group by operatorid",
		array(
			':kind_agent' => $kind_agent,
			':start' => $start,
			':end' => $end
		),
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
	$activetab = 1;
} elseif($statisticstype == 'bypage') {
	$page['reportByPage'] = $db->query(
		"SELECT COUNT(DISTINCT p.pageid) as visittimes, p.address, COUNT(DISTINCT t.threadid) as chattimes " .
		"FROM {visitedpagestatistics} p LEFT OUTER JOIN {chatthread} t ON (p.address = t.referer AND DATE(p.visittime) = DATE(t.dtmcreated)) " .
		"WHERE unix_timestamp(p.visittime) >= :start AND unix_timestamp(p.visittime) < :end GROUP BY p.address",
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