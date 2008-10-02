<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
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

$act = verifyparam( "act", "/^(silentcreateall|createdb|createtables|droptables|addcolumns)$/");
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

	if( $act == "createtables") {
		$curr_tables = get_tables($link);
		if( $curr_tables === false) {
			show_install_err($errors[0]);
		}
		$tocreate = array_diff(array_keys($dbtables), $curr_tables);
		foreach( $tocreate as $id) {
			create_table($id, $link);
		}
	} else if( $act == "droptables") {
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

		$res = runsql("select null from information_schema.statistics where table_name = 'chatmessage' and index_name = 'idx_agentid'", $link);
		if(mysql_num_rows($res) == 0) {
			runsql("ALTER TABLE chatmessage ADD INDEX idx_agentid (agentid)", $link);

		}
	}
}

mysql_close($link);
header("Location: $webimroot/install/index.php");
exit;
?>