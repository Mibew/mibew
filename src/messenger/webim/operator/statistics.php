<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2009 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once('../libs/common.php');
require_once('../libs/chat.php');
require_once('../libs/operator.php');

$operator = check_login();

$page = array();
$page['operator'] = topage(get_operator_name($operator));
$page['availableDays'] = range(1,31);
$page['availableMonth'] = get_month_selection(time()-400*24*60*60,time()+50*24*60*60 );
$page['showresults'] = false;
$errors = array();

if(isset($_GET['startday'])) {
	$startday = verifyparam("startday","/^\d+$/");
	$startmonth = verifyparam("startmonth","/^\d{2}.\d{2}$/");
	$endday = verifyparam("endday","/^\d+$/");
	$endmonth = verifyparam("endmonth","/^\d{2}.\d{2}$/");
	$start = get_form_date($startday,$startmonth);
	$end = get_form_date($endday, $endmonth)+24*60*60;

} else {
	$curr = getdate(time());
	if( $curr['mday'] < 7 ) {
		// previous month
		if($curr['mon'] == 1) {
			$month = 12; 
			$year = $curr['year']-1;
		} else {
			$month = $curr['mon']-1; 
			$year = $curr['year'];
		}
		$start = mktime(0,0,0,$month,1,$year);
		$end = mktime(0,0,0,$month, date("t",$start),$year)+24*60*60;
	} else {
		$start = mktime(0,0,0,$curr['mon'],1,$curr['year']);
		$end = time()+24*60*60;
	}
}
set_form_date($start, "start");
set_form_date($end-24*60*60, "end");

if( $start > $end ) {
	$errors[] = getlocal("statistics.wrong.dates");
}

$link = connect();

$page['reportByDate'] = select_multi_assoc("select DATE(dtmcreated) as date, COUNT(distinct threadid) as threads, SUM(chatmessage.ikind = $kind_agent) as agents, SUM(chatmessage.ikind = $kind_user) as users ".
	 "from chatmessage where unix_timestamp(dtmcreated) >= $start AND unix_timestamp(dtmcreated) < $end group by DATE(dtmcreated) order by dtmcreated desc", $link);

$page['reportByDateTotal'] = select_one_row("select COUNT(distinct threadid) as threads, SUM(chatmessage.ikind = $kind_agent) as agents, SUM(chatmessage.ikind = $kind_user) as users ".
	 "from chatmessage where unix_timestamp(dtmcreated) >= $start AND unix_timestamp(dtmcreated) < $end", $link);

$page['reportByAgent'] = select_multi_assoc("select vclocalename as name, COUNT(distinct threadid) as threads, SUM(ikind = $kind_agent) as msgs, AVG(CHAR_LENGTH(tmessage)) as avglen ".
	 "from chatmessage, chatoperator ".
         "where agentId = operatorid AND unix_timestamp(dtmcreated) >= $start AND unix_timestamp(dtmcreated) < $end group by operatorid", $link);

$page['showresults'] = count($errors) == 0;

mysql_close($link);

prepare_menu($operator);
start_html_output();
require('../view/statistics.php');
?>