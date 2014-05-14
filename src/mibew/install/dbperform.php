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

/**
 * Indicate that installation in progress
 */
define('INSTALLATION_IN_PROGRESS', TRUE);

/**
 * File system root directory of the Mibew installations
 */
define('MIBEW_FS_ROOT', dirname(dirname(__FILE__)));

session_start();

require_once(MIBEW_FS_ROOT.'/libs/config.php');

/**
 * Base URL of the Mibew installation
 */
define('MIBEW_WEB_ROOT', $mibewroot);

// Include common functions
require_once(MIBEW_FS_ROOT.'/libs/common/constants.php');
require_once(MIBEW_FS_ROOT.'/libs/common/verification.php');
require_once(MIBEW_FS_ROOT.'/libs/common/locale.php');
require_once(MIBEW_FS_ROOT.'/libs/common/misc.php');
require_once(MIBEW_FS_ROOT.'/libs/common/response.php');
// Include database structure
require_once(MIBEW_FS_ROOT.'/install/dbinfo.php');

function runsql($query, $link)
{
	$res = mysql_query($query, $link) or show_install_err(' Query failed: ' . mysql_error($link));
	return $res;
}

$act = verify_param("act", "/^(silentcreateall|createdb|ct|dt|addcolumns)$/");

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
	mysql_query("SET character set utf8", $link);

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

		if (in_array("${mysqlprefix}chatmessage.plugin", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatmessage ADD plugin varchar(256) NOT NULL DEFAULT '' AFTER tmessage", $link);
		}

		if (in_array("${mysqlprefix}chatmessage.data", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatmessage ADD data text AFTER plugin", $link);
		}

		if (in_array("${mysqlprefix}chatthread.agentId", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthread ADD agentId int NOT NULL DEFAULT 0 AFTER agentName", $link);
			runsql("update ${mysqlprefix}chatthread, ${mysqlprefix}chatoperator set agentId = operatorid where agentId = 0 AND (vclocalename = agentName OR vccommonname = agentName)", $link);
		}

		if (in_array("${mysqlprefix}chatthread.dtmchatstarted", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthread ADD dtmchatstarted int NOT NULL DEFAULT 0 AFTER dtmcreated", $link);
			runsql("update ${mysqlprefix}chatthread set dtmchatstarted = dtmcreated", $link);
		}

		if (in_array("${mysqlprefix}chatthread.dtmclosed", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthread ADD dtmclosed int NOT NULL DEFAULT 0 AFTER dtmmodified", $link);
			runsql("update ${mysqlprefix}chatthread set dtmclosed = dtmmodified", $link);
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

		if (in_array("${mysqlprefix}chatthread.invitationstate", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthread ADD invitationstate int NOT NULL DEFAULT 0 AFTER istate", $link);
		}

		if (in_array("${mysqlprefix}chatthreadstatistics.missedthreads", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthreadstatistics ADD missedthreads int NOT NULL DEFAULT 0 AFTER threads", $link);
		}

		if (in_array("${mysqlprefix}chatthreadstatistics.sentinvitations", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthreadstatistics ADD sentinvitations int NOT NULL DEFAULT 0 AFTER missedthreads", $link);
		}

		if (in_array("${mysqlprefix}chatthreadstatistics.acceptedinvitations", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthreadstatistics ADD acceptedinvitations int NOT NULL DEFAULT 0 AFTER sentinvitations", $link);
		}

		if (in_array("${mysqlprefix}chatthreadstatistics.rejectedinvitations", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthreadstatistics ADD rejectedinvitations int NOT NULL DEFAULT 0 AFTER acceptedinvitations", $link);
		}

		if (in_array("${mysqlprefix}chatthreadstatistics.ignoredinvitations", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatthreadstatistics ADD ignoredinvitations int NOT NULL DEFAULT 0 AFTER rejectedinvitations", $link);
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
			runsql("ALTER TABLE ${mysqlprefix}chatoperator ADD dtmrestore int NOT NULL DEFAULT 0", $link);
		}

		if (in_array("${mysqlprefix}chatoperator.vcrestoretoken", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatoperator ADD vcrestoretoken varchar(64)", $link);
		}

		if (in_array("${mysqlprefix}chatoperator.code", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatoperator ADD code varchar(64) DEFAULT ''", $link);
		}

		if (in_array("${mysqlprefix}chatoperatorstatistics.sentinvitations", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatoperatorstatistics ADD sentinvitations int NOT NULL DEFAULT 0", $link);
		}

		if (in_array("${mysqlprefix}chatoperatorstatistics.acceptedinvitations", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatoperatorstatistics ADD acceptedinvitations int NOT NULL DEFAULT 0", $link);
		}

		if (in_array("${mysqlprefix}chatoperatorstatistics.rejectedinvitations", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatoperatorstatistics ADD rejectedinvitations int NOT NULL DEFAULT 0", $link);
		}

		if (in_array("${mysqlprefix}chatoperatorstatistics.ignoredinvitations", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}chatoperatorstatistics ADD ignoredinvitations int NOT NULL DEFAULT 0", $link);
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

		if (in_array("${mysqlprefix}visitedpagestatistics.sentinvitations", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}visitedpagestatistics ADD sentinvitations int NOT NULL DEFAULT 0", $link);
		}

		if (in_array("${mysqlprefix}visitedpagestatistics.acceptedinvitations", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}visitedpagestatistics ADD acceptedinvitations int NOT NULL DEFAULT 0", $link);
		}

		if (in_array("${mysqlprefix}visitedpagestatistics.rejectedinvitations", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}visitedpagestatistics ADD rejectedinvitations int NOT NULL DEFAULT 0", $link);
		}

		if (in_array("${mysqlprefix}visitedpagestatistics.ignoredinvitations", $absent_columns)) {
			runsql("ALTER TABLE ${mysqlprefix}visitedpagestatistics ADD ignoredinvitations int NOT NULL DEFAULT 0", $link);
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

		if (in_array("${mysqlprefix}chatoperatorstatistics.operatorid", $absent_indexes)) {
			runsql("ALTER TABLE ${mysqlprefix}chatoperatorstatistics ADD INDEX (operatorid)", $link);
		}
	}
}

mysql_close($link);
header("Location: " . MIBEW_WEB_ROOT . "/install/index.php");
exit;
?>