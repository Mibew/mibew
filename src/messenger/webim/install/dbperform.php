<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
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

function runsql($query,$link) {
	$res = mysql_query($query,$link)
		or show_install_err(' Query failed: '.mysql_error());
	return $res;
}

$act = verifyparam( "act", "/^(silentcreateall|createdb|ct|dt|addcolumns)$/");

$link = @mysql_connect($mysqlhost,$mysqllogin ,$mysqlpass )
	or show_install_err('Could not connect: ' . mysql_error());

if ($act == "silentcreateall") {
	mysql_query("CREATE DATABASE $mysqldb",$link)
		or show_install_err(' Query failed: '.mysql_error());
	foreach($dbtables as $id) {
		create_table($id, $link);
   	}
} else if($act == "createdb") {
	mysql_query("CREATE DATABASE $mysqldb",$link)
		or show_install_err(' Query failed: '.mysql_error());
} else {
	mysql_select_db($mysqldb,$link)
		or show_install_err('Could not select database');
	if( $force_charset_in_connection ) {
		mysql_query("SET character set $dbencoding", $link);
	}

	if( $act == "ct") {
		$curr_tables = get_tables($link);
		if( $curr_tables === false) {
			show_install_err($errors[0]);
		}
		$tocreate = array_diff(array_keys($dbtables), $curr_tables);
		foreach( $tocreate as $id) {
			create_table($id, $link);
		}
	} else if( $act == "dt") {
		foreach( array_keys($dbtables) as $id) {
			mysql_query("DROP TABLE IF EXISTS $id",$link)
				or show_install_err(' Query failed: '.mysql_error());
		}
	} else if( $act == "addcolumns") {
		$absent = array();
		foreach( $dbtables as $id => $columns) {
			$curr_columns = get_columns($id, $link);
			if( $curr_columns === false ) {
				show_install_err($errors[0]);
			}
			$tocreate = array_diff(array_keys($columns), $curr_columns);
			foreach($tocreate as $v) {
				$absent[] = "$id.$v";
			}
		}

		if( in_array("chatmessage.agentId", $absent) ) {
			runsql("ALTER TABLE chatmessage ADD agentId int NOT NULL DEFAULT 0 AFTER ikind", $link);
			runsql("update chatmessage,chatoperator set agentId = operatorid where agentId = 0 AND ikind = 2 AND (vclocalename = tname OR vccommonname = tname)", $link);
		}

		if( in_array("chatthread.agentId", $absent) ) {
			runsql("ALTER TABLE chatthread ADD agentId int NOT NULL DEFAULT 0 AFTER agentName", $link);
			runsql("update chatthread,chatoperator set agentId = operatorid where agentId = 0 AND (vclocalename = agentName OR vccommonname = agentName)", $link);
		}

		if( in_array("chatthread.agentTyping", $absent) ) {
			runsql("ALTER TABLE chatthread ADD agentTyping int DEFAULT 0", $link);
		}

		if( in_array("chatthread.userTyping", $absent) ) {
			runsql("ALTER TABLE chatthread ADD userTyping int DEFAULT 0", $link);
		}

		if( in_array("chatthread.messageCount", $absent) ) {
			runsql("ALTER TABLE chatthread ADD messageCount varchar(16)", $link);
			runsql("ALTER TABLE chatmessage ADD INDEX idx_threadid_ikind (threadid, ikind)", $link);
			runsql("UPDATE chatthread t SET t.messageCount = (SELECT COUNT(*) FROM chatmessage WHERE chatmessage.threadid = t.threadid AND ikind = 1)", $link);
			runsql("ALTER TABLE chatmessage DROP INDEX idx_threadid_ikind", $link);
		}

		if( in_array("chatthread.nextagent", $absent) ) {
			runsql("ALTER TABLE chatthread ADD nextagent int NOT NULL DEFAULT 0", $link);
		}

		if( in_array("chatthread.shownmessageid", $absent) ) {
			runsql("ALTER TABLE chatthread ADD shownmessageid int NOT NULL DEFAULT 0", $link);
		}

		if( in_array("chatthread.userid", $absent) ) {
			runsql("ALTER TABLE chatthread ADD userid varchar(255) DEFAULT \"\"", $link);
		}

		if( in_array("chatoperator.iperm", $absent) ) {
			runsql("ALTER TABLE chatoperator ADD iperm int DEFAULT 65535", $link);
		}

		if( in_array("chatoperator.istatus", $absent) ) {
			runsql("ALTER TABLE chatoperator ADD istatus int DEFAULT 0", $link);
		}
		
		if( in_array("chatoperator.vcavatar", $absent) ) {
			runsql("ALTER TABLE chatoperator ADD vcavatar varchar(255)", $link);
		}

		if( in_array("chatoperator.vcjabbername", $absent) ) {
			runsql("ALTER TABLE chatoperator ADD vcjabbername varchar(255)", $link);
		}

		if( in_array("chatoperator.vcemail", $absent) ) {
			runsql("ALTER TABLE chatoperator ADD vcemail varchar(64)", $link);
		}

		if( in_array("chatoperator.dtmrestore", $absent) ) {
			runsql("ALTER TABLE chatoperator ADD dtmrestore datetime DEFAULT 0", $link);
		}

		if( in_array("chatoperator.vcrestoretoken", $absent) ) {
			runsql("ALTER TABLE chatoperator ADD vcrestoretoken varchar(64)", $link);
		}
		
		if( in_array("chatthread.groupid", $absent) ) {
			runsql("ALTER TABLE chatthread ADD groupid int references chatgroup(groupid)", $link);
		}

		if( in_array("chatthread.userAgent", $absent) ) {
			runsql("ALTER TABLE chatthread ADD userAgent varchar(255)", $link);
		}

		if( in_array("chatgroup.vcemail", $absent) ) {
			runsql("ALTER TABLE chatgroup ADD vcemail varchar(64)", $link);
		}
		
		$res = mysql_query("select null from information_schema.statistics where table_name = 'chatmessage' and index_name = 'idx_agentid'", $link);
		if($res && mysql_num_rows($res) == 0) {
			runsql("ALTER TABLE chatmessage ADD INDEX idx_agentid (agentid)", $link);
		}
	}
}

mysql_close($link);
header("Location: $webimroot/install/index.php");
exit;
?>