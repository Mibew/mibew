<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
 * 
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('dbinfo.php');

function runsql($query, $link)
{
	$res = mysql_query($query, $link) or show_install_err(' Query failed: ' . mysql_error($link));
	return $res;
}

$act = verifyparam("act", "/^(silentcreateall|createdb|ct|dt|addcolumns)$/");

$link = @mysql_connect($mysqlhost, $mysqllogin, $mysqlpass)
		 or show_install_err('Could not connect: ' . mysql_error());

if ($act == "silentcreateall") {
	mysql_query("CREATE DATABASE $mysqldb", $link) or show_install_err(' Query failed: ' . mysql_error($link));
	foreach ($dbtables as $id) {
		create_table($id, $link);
	}
} else if ($act == "createdb") {
	mysql_query("CREATE DATABASE $mysqldb", $link) or show_install_err(' Query failed: ' . mysql_error($link));
} else {
	mysql_select_db($mysqldb, $link) or show_install_err('Could not select database');
	if ($force_charset_in_connection) {
		mysql_query("SET character set $dbencoding", $link);
	}

	if ($act == "ct") {
		$curr_tables = get_tables($link);
		if ($curr_tables === false) {
			show_install_err($errors[0]);
		}
		$tocreate = array_diff(array_keys($dbtables), $curr_tables);
		foreach ($tocreate as $id) {
			create_table($id, $link);
		}
	} else if ($act == "dt") {

		# comment this line to be able to drop tables
		show_install_err("For security reasons, removing tables is disabled by default");

		foreach (array_keys($dbtables) as $id) {
			mysql_query("DROP TABLE IF EXISTS $id", $link) or show_install_err(' Query failed: ' . mysql_error($link));
		}
	} else if ($act == "addcolumns") {

// Add absent columns
		$absent_columns = array();
		foreach ($dbtables as $id => $columns) {
			$curr_columns = get_columns($id, $link);
			if ($curr_columns === false) {
				show_install_err($errors[0]);
			}
			$tocreate = array_diff(array_keys($columns), $curr_columns);
			foreach ($tocreate as $v) {
				$absent_columns[] = "$id.$v";
			}
		}

		if (in_array("${mysqlprefix}chatmessage.agentId", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatmessage ADD agentId int NOT NULL DEFAULT 0 AFTER ikind", $link);
			runsql("update ${mysqlprefix}chatmessage, ${mysqlprefix}chatoperator set agentId = operatorid where agentId = 0 AND ikind = 2 AND (vclocalename = tname OR vccommonname = tname)", $link);
		}

		if (in_array("${mysqlprefix}chatthread.agentId", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthread ADD agentId int NOT NULL DEFAULT 0 AFTER agentName", $link);
			runsql("update ${mysqlprefix}chatthread, ${mysqlprefix}chatoperator set agentId = operatorid where agentId = 0 AND (vclocalename = agentName OR vccommonname = agentName)", $link);
		}

		if (in_array("${mysqlprefix}chatthread.dtmchatstarted", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthread ADD dtmchatstarted datetime DEFAULT 0 AFTER dtmcreated", $link);
			runsql("update ${mysqlprefix}chatthread set dtmchatstarted = dtmcreated", $link);
		}

		if (in_array("${mysqlprefix}chatthread.agentTyping", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthread ADD agentTyping int DEFAULT 0", $link);
		}

		if (in_array("${mysqlprefix}chatthread.userTyping", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthread ADD userTyping int DEFAULT 0", $link);
		}

		if (in_array("${mysqlprefix}chatthread.messageCount", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthread ADD messageCount varchar(16)", $link);
			runsql("ALTER TABLE ${mysqlprefix}chatmessage ADD INDEX idx_threadid_ikind (threadid, ikind)", $link);
			runsql("UPDATE ${mysqlprefix}chatthread t SET t.messageCount = (SELECT COUNT(*) FROM ${mysqlprefix}chatmessage WHERE ${mysqlprefix}chatmessage.threadid = t.threadid AND ikind = 1)", $link);
			runsql("ALTER TABLE ${mysqlprefix}chatmessage DROP INDEX idx_threadid_ikind", $link);
		}

		if (in_array("${mysqlprefix}chatthread.nextagent", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthread ADD nextagent int NOT NULL DEFAULT 0", $link);
		}

		if (in_array("${mysqlprefix}chatthread.shownmessageid", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthread ADD shownmessageid int NOT NULL DEFAULT 0", $link);
		}

		if (in_array("${mysqlprefix}chatthread.userid", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthread ADD userid varchar(255) DEFAULT \"\"", $link);
		}

		if (in_array("${mysqlprefix}chatoperator.iperm", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatoperator ADD iperm int DEFAULT 65535", $link);
		}

		if (in_array("${mysqlprefix}chatoperator.istatus", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatoperator ADD istatus int DEFAULT 0", $link);
		}

		if (in_array("${mysqlprefix}chatoperator.idisabled", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatoperator ADD idisabled int DEFAULT 0 AFTER istatus", $link);
		}

		if (in_array("${mysqlprefix}chatoperator.vcavatar", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatoperator ADD vcavatar varchar(255)", $link);
		}

		if (in_array("${mysqlprefix}chatoperator.vcjabbername", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatoperator ADD vcjabbername varchar(255)", $link);
		}

		if (in_array("${mysqlprefix}chatoperator.vcemail", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatoperator ADD vcemail varchar(64)", $link);
		}

		if (in_array("${mysqlprefix}chatoperator.dtmrestore", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatoperator ADD dtmrestore datetime DEFAULT 0", $link);
		}

		if (in_array("${mysqlprefix}chatoperator.vcrestoretoken", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatoperator ADD vcrestoretoken varchar(64)", $link);
		}

		if (in_array("${mysqlprefix}chatresponses.vctitle", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatresponses ADD vctitle varchar(100) NOT NULL DEFAULT '' AFTER groupid", $link);
		}

		if (in_array("${mysqlprefix}chatthread.groupid", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthread ADD groupid int references ${mysqlprefix}chatgroup(groupid)", $link);
		}

		if (in_array("${mysqlprefix}chatthread.userAgent", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthread ADD userAgent varchar(255)", $link);
		}

		if (in_array("${mysqlprefix}chatgroup.vcemail", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatgroup ADD vcemail varchar(64)", $link);
		}

		if (in_array("${mysqlprefix}chatgroup.iweight", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatgroup ADD iweight int DEFAULT 0", $link);
		}

		if (in_array("${mysqlprefix}chatgroup.parent", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatgroup ADD parent int DEFAULT NULL AFTER groupid", $link);
		}

		if (in_array("${mysqlprefix}chatgroup.vctitle", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatgroup ADD vctitle varchar(255) DEFAULT ''", $link);
		}

		if (in_array("${mysqlprefix}chatgroup.vcchattitle", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatgroup ADD vcchattitle varchar(255) DEFAULT ''", $link);
		}

		if (in_array("${mysqlprefix}chatgroup.vclogo", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatgroup ADD vclogo varchar(255) DEFAULT ''", $link);
		}

		if (in_array("${mysqlprefix}chatgroup.vchosturl", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatgroup ADD vchosturl varchar(255) DEFAULT ''", $link);
		}

// Add absent indexes
		$absent_indexes = array();
		foreach ($dbtables_indexes as $id => $indexes) {
			$curr_indexes = get_indexes($id, $link);
			if ($curr_indexes === false) {
				show_install_err($errors[0]);
			}
			$tocreate = array_diff(array_keys($indexes), $curr_indexes);
			foreach ($tocreate as $i) {
				$absent_indexes[] = "$id.$i";
			}
		}

		if (in_array("${mysqlprefix}chatgroup.parent", $absent_indexes)) {
			runsql("ALTER TABLE ${mysqlprefix}chatgroup ADD INDEX (parent)", $link);
		}

		if (in_array("${mysqlprefix}chatgroupoperator.groupid", $absent_indexes)) {
			runsql("ALTER TABLE ${mysqlprefix}chatgroupoperator ADD INDEX (groupid)", $link);
		}

		if (in_array("${mysqlprefix}chatgroupoperator.operatorid", $absent_indexes)) {
			runsql("ALTER TABLE ${mysqlprefix}chatgroupoperator ADD INDEX (operatorid)", $link);
		}

		if (in_array("${mysqlprefix}chatmessage.idx_agentid", $absent_indexes)) {
			runsql("ALTER TABLE ${mysqlprefix}chatmessage ADD INDEX idx_agentid (agentid)", $link);
		}

		if (in_array("${mysqlprefix}chatsitevisitor.threadid", $absent_indexes)) {
			runsql("ALTER TABLE ${mysqlprefix}chatsitevisitor ADD INDEX (threadid)", $link);
		}

		if (in_array("${mysqlprefix}visitedpage.visitorid", $absent_indexes)) {
			runsql("ALTER TABLE ${mysqlprefix}visitedpage ADD INDEX (visitorid)", $link);
		}

	}
}

mysql_close($link);
header("Location: $webimroot/install/index.php");
exit;
?>
