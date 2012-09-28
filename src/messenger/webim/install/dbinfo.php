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

$dbtables = array(
	"${mysqlprefix}chatgroup" => array(
		"groupid" => "int NOT NULL auto_increment PRIMARY KEY",
		"parent" => "int DEFAULT NULL",
		"vcemail" => "varchar(64)",
		"vclocalname" => "varchar(64) NOT NULL",
		"vccommonname" => "varchar(64) NOT NULL",
		"vclocaldescription" => "varchar(1024) NOT NULL",
		"vccommondescription" => "varchar(1024) NOT NULL",
		"iweight" => "int NOT NULL DEFAULT 0",
		"vctitle" => "varchar(255) DEFAULT ''",
		"vcchattitle" => "varchar(255) DEFAULT ''",
		"vclogo" => "varchar(255) DEFAULT ''",
		"vchosturl" => "varchar(255) DEFAULT ''",
	),

	"${mysqlprefix}chatthread" => array(
		"threadid" => "int NOT NULL auto_increment PRIMARY KEY",
		"userName" => "varchar(64) NOT NULL",
		"userid" => "varchar(255)",
		"agentName" => "varchar(64)",
		"agentId" => "int NOT NULL DEFAULT 0",
		"dtmcreated" => "int NOT NULL DEFAULT 0",
		"dtmchatstarted" => "int NOT NULL DEFAULT 0",
		"dtmmodified" => "int NOT NULL DEFAULT 0",
		"lrevision" => "int NOT NULL DEFAULT 0",
		"istate" => "int NOT NULL DEFAULT 0",
		"ltoken" => "int NOT NULL",
		"remote" => "varchar(255)",
		"referer" => "text",
		"nextagent" => "int NOT NULL DEFAULT 0",
		"locale" => "varchar(8)",
		"lastpinguser" => "int NOT NULL DEFAULT 0",
		"lastpingagent" => "int NOT NULL DEFAULT 0",
		"userTyping" => "int DEFAULT 0",
		"agentTyping" => "int DEFAULT 0",
		"shownmessageid" => "int NOT NULL DEFAULT 0",
		"userAgent" => "varchar(255)",
		"messageCount" => "varchar(16)",
		"groupid" => "int references ${mysqlprefix}chatgroup(groupid)",
	),

	"${mysqlprefix}chatrequestbuffer" => array(
		"requestid" => "int NOT NULL auto_increment PRIMARY KEY",
		"threadid" => "int NOT NULL references ${mysqlprefix}chatthread(threadid)",
		"request" => "text NOT NULL"
	),

	"${mysqlprefix}chatrequestcallback" => array(
		"callbackid" => "int NOT NULL auto_increment PRIMARY KEY",
		"token" => "varchar(64) NOT NULL DEFAULT ''",
		"function" => "varchar(64) NOT NULL",
		"arguments" => "varchar(64)"
	),

	"${mysqlprefix}chatmessage" => array(
		"messageid" => "int NOT NULL auto_increment PRIMARY KEY",
		"threadid" => "int NOT NULL references ${mysqlprefix}chatthread(threadid)",
		"ikind" => "int NOT NULL",
		"agentId" => "int NOT NULL DEFAULT 0",
		"tmessage" => "text NOT NULL",
		"dtmcreated" => "int NOT NULL DEFAULT 0",
		"tname" => "varchar(64)"
	),

	"${mysqlprefix}chatoperator" => array(
		"operatorid" => "int NOT NULL auto_increment PRIMARY KEY",
		"vclogin" => "varchar(64) NOT NULL",
		"vcpassword" => "varchar(64) NOT NULL",
		"vclocalename" => "varchar(64) NOT NULL",
		"vccommonname" => "varchar(64) NOT NULL",
		"vcemail" => "varchar(64)",
		"dtmlastvisited" => "int NOT NULL DEFAULT 0",
		"istatus" => "int DEFAULT 0", /* 0 - online, 1 - away */
		"idisabled" => "int DEFAULT 0",
		"vcavatar" => "varchar(255)",
		"vcjabbername" => "varchar(255)",
		"iperm" => "int DEFAULT 65535",
		"dtmrestore" => "int NOT NULL DEFAULT 0",
		"vcrestoretoken" => "varchar(64)",
	),

	"${mysqlprefix}chatrevision" => array(
		"id" => "INT NOT NULL"
	),

	"${mysqlprefix}chatgroupoperator" => array(
		"groupid" => "int NOT NULL references ${mysqlprefix}chatgroup(groupid)",
		"operatorid" => "int NOT NULL references ${mysqlprefix}chatoperator(operatorid)",
	),

	"${mysqlprefix}chatban" => array(
		"banid" => "INT NOT NULL auto_increment PRIMARY KEY",
		"dtmcreated" => "int NOT NULL DEFAULT 0",
		"dtmtill" => "int NOT NULL DEFAULT 0",
		"address" => "varchar(255)",
		"comment" => "varchar(255)",
		"blockedCount" => "int DEFAULT 0"
	),

	"${mysqlprefix}chatconfig" => array(
		"id" => "INT NOT NULL auto_increment PRIMARY KEY",
		"vckey" => "varchar(255)",
		"vcvalue" => "varchar(255)",
	),

	"${mysqlprefix}chatresponses" => array(
		"id" => "INT NOT NULL auto_increment PRIMARY KEY",
		"locale" => "varchar(8)",
		"groupid" => "int references ${mysqlprefix}chatgroup(groupid)",
		"vctitle" => "varchar(100) NOT NULL DEFAULT ''",
		"vcvalue" => "varchar(1024) NOT NULL",
	),

	"${mysqlprefix}chatsitevisitor" => array(
		"visitorid" => "INT NOT NULL auto_increment PRIMARY KEY",
		"userid" => "varchar(64) NOT NULL",
		"username" => "varchar(255)",
		"firsttime" => "int NOT NULL DEFAULT 0",
		"lasttime" => "int NOT NULL DEFAULT 0",
		"entry" => "text NOT NULL",
		"details" => "text NOT NULL",
		"invited" => "tinyint(1) NOT NULL DEFAULT 0",
		"invitationtime" => "int NOT NULL DEFAULT 0",
		"invitedby" => "INT references ${mysqlprefix}chatoperator(operatorid) on delete set null",
		"invitations" => "INT NOT NULL DEFAULT 0",
		"chats" => "INT NOT NULL DEFAULT 0",
		"threadid" => "INT references ${mysqlprefix}chatthread(threadid) on delete set null"
	),

	"${mysqlprefix}visitedpage" => array(
		"pageid" => "INT NOT NULL auto_increment PRIMARY KEY",
		"address" => "varchar(1024)",
		"visittime" => "int NOT NULL DEFAULT 0",
		"visitorid" => "INT",
	),

	"${mysqlprefix}visitedpagestatistics" => array(
		"pageid" => "INT NOT NULL auto_increment PRIMARY KEY",
		"address" => "varchar(1024)",
		"visittime" => "int NOT NULL DEFAULT 0"
	),
);

$dbtables_indexes = array(
	"${mysqlprefix}chatgroup" => array(
		"parent" => "parent"
	),
	"${mysqlprefix}chatgroupoperator" => array(
		"groupid" => "groupid",
		"operatorid" => "operatorid"
	),
	"${mysqlprefix}chatrequestbuffer" => array(
		"threadid" => "threadid"
	),
	"${mysqlprefix}chatrequestcallback" => array(
		"token" => "token"
	),
	"${mysqlprefix}chatmessage" => array(
		"idx_agentid" => "agentid"
	),
	"${mysqlprefix}chatsitevisitor" => array(
		"threadid" => "threadid"
	),
	"${mysqlprefix}visitedpage" => array(
		"visitorid" => "visitorid"
	)
);

$dbtables_indexes = array(
	"${mysqlprefix}chatmessage" => array(
		"idx_agentid" => "agentid"
	)
);

$memtables = array();

$dbtables_can_update = array(
	"${mysqlprefix}chatthread" => array("agentId", "userTyping", "agentTyping", "messageCount", "nextagent", "shownmessageid", "userid", "userAgent", "groupid", "dtmchatstarted"),
	"${mysqlprefix}chatrequestbuffer" => array("requestid", "threadid", "request"),
	"${mysqlprefix}chatrequestcallback" => array("callbackid", "token", "function", "arguments"),
	"${mysqlprefix}chatmessage" => array("agentId"),
	"${mysqlprefix}chatoperator" => array("vcavatar", "vcjabbername", "iperm", "istatus", "idisabled", "vcemail", "dtmrestore", "vcrestoretoken"),
	"${mysqlprefix}chatban" => array(),
	"${mysqlprefix}chatgroup" => array("vcemail", "iweight", "parent", "vctitle", "vcchattitle", "vclogo", "vchosturl"),
	"${mysqlprefix}chatgroupoperator" => array(),
	"${mysqlprefix}chatresponses" => array("vctitle"),
	"${mysqlprefix}chatsitevisitor" => array(),
	"${mysqlprefix}visitedpage" => array(),
	"${mysqlprefix}visitedpagestatistics" => array(),
);

function show_install_err($text)
{
	global $page, $version, $errors, $webimroot;
	$page = array(
		'version' => $version,
		'localeLinks' => get_locale_links("$webimroot/install/index.php")
	);
	$errors = array($text);
	start_html_output();
	require('../view/install_err.php');
	exit;
}

function create_table($id, $link)
{
	global $dbtables, $dbtables_indexes, $memtables, $dbencoding, $mysqlprefix;

	if (!isset($dbtables[$id])) {
		show_install_err("Unknown table: $id, " . mysql_error($link));
	}

	$query =
			"CREATE TABLE $id\n" .
			"(\n";
	foreach ($dbtables[$id] as $k => $v) {
		$query .= "	$k $v,\n";
	}

	if (isset($dbtables_indexes[$id])) {
	    foreach ($dbtables_indexes[$id] as $k => $v) {
		    $query .= "	INDEX $k ($v),\n";
	    }
	}

	$query = preg_replace("/,\n$/", "", $query);
	$query .= ") charset $dbencoding";
	if (in_array($id, $memtables)) {
		$query .= " ENGINE=MEMORY";
	} else {
		$query .= " ENGINE=InnoDb";
	}

	mysql_query($query, $link) or show_install_err(' Query failed: ' . mysql_error($link));

	if ($id == "${mysqlprefix}chatoperator") {
		// Create First Administrator
		mysql_query(
			"INSERT INTO ${mysqlprefix}chatoperator ( " .
				"vclogin, " .
				"vcpassword, " .
				"vclocalename, " .
				"vccommonname, " .
				"vcavatar, " .
				"vcemail " .
			") values ( " .
				"'admin', " .
				"MD5(''), " .
				"'', " .
				"'Administrator', " .
				"'Administrator', " .
				"''" .
			")",
			$link
		);
	} else if ($id == "${mysqlprefix}chatrevision") {
		$result = mysql_query("INSERT INTO ${mysqlprefix}chatrevision VALUES (1)", $link);
		if (! $result) {
			die(' Query failed: ' . mysql_error($link));
		}
	}
}

function get_tables($link)
{
	global $mysqldb, $errors;
	$result = mysql_query("SHOW TABLES FROM `$mysqldb`", $link);
	if ($result) {
		$arr = array();
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$arr[] = $row[0];
		}
		mysql_free_result($result);
		return $arr;

	} else {
		$errors[] = "Cannot get tables from database. Error: " . mysql_error($link);
		return false;
	}
}

function get_columns($tablename, $link)
{
	global $errors;
	$result = mysql_query("SHOW COLUMNS FROM $tablename", $link);
	if ($result) {
		$arr = array();
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$arr[] = $row[0];
		}
		mysql_free_result($result);
		return $arr;

	} else {
		$errors[] = "Cannot get columns from table \"$tablename\". Error: " . mysql_error($link);
		return false;
	}
}

function get_indexes($tablename, $link)
{
	global $mysqldb, $errors;
	$result = mysql_query("SELECT index_name FROM information_schema.statistics where table_schema = '$mysqldb' and table_name = '$tablename' and index_name != 'PRIMARY'", $link);
	if ($result) {
		$arr = array();
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$arr[] = $row[0];
		}
		mysql_free_result($result);
		return $arr;

	} else {
		$errors[] = "Cannot get indexes for table \"$tablename\". Error: " . mysql_error($link);
		return false;
	}
}

?>