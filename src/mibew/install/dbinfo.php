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

	// Chat threads
	"${mysqlprefix}chatthread" => array(
		// ID of the thread.
		"threadid" => "int NOT NULL auto_increment PRIMARY KEY",
		// Name of the user in chat.
		"userName" => "varchar(64) NOT NULL",
		// ID of the user. This field is foreign key for
		// {chatsitevisitor}.userid
		"userid" => "varchar(255)",
		// Name of the operator who took place in the chat.
		"agentName" => "varchar(64)",
		// ID of the operator who took place in the chat.
		"agentId" => "int NOT NULL DEFAULT 0",
		// Unix timestamp of the moment when the thread was created.
		"dtmcreated" => "int NOT NULL DEFAULT 0",
		// Unix timestamp of the moment when chat actually started.
		"dtmchatstarted" => "int NOT NULL DEFAULT 0",
		// Unix timestamp of the last thread modification.
		"dtmmodified" => "int NOT NULL DEFAULT 0",
		// Unix timestamp of the moment when the thread was closed.
		"dtmclosed" => "int NOT NULL DEFAULT 0",
		// ID of the last thread revision.
		"lrevision" => "int NOT NULL DEFAULT 0",
		// State of the thread. It is one of Thread::STATE_* constants.
		"istate" => "int NOT NULL DEFAULT 0",
		// State of invitation related with the thread. It is one of
		// Thread::INVITATION_* constants.
		"invitationstate" => "int NOT NULL DEFAULT 0",
		// Last token of the thread.
		"ltoken" => "int NOT NULL",
		// IP address of the user.
		"remote" => "varchar(255)",
		// Page from which chat thread was started.
		"referer" => "text",
		// ID of the operator who will next in the chat.
		"nextagent" => "int NOT NULL DEFAULT 0",
		// Code of chat locale.
		"locale" => "varchar(8)",
		// Unix timestamp of the last request from user's window to server.
		"lastpinguser" => "int NOT NULL DEFAULT 0",
		// Unix timestamp of the last request from operator's window to server.
		"lastpingagent" => "int NOT NULL DEFAULT 0",
		// Indicates if user typing or not. It can take two values 0 and 1.
		"userTyping" => "int DEFAULT 0",
		// Indicates if operator typing or not. It can take two values 0 and 1.
		"agentTyping" => "int DEFAULT 0",
		// ID of shown message in the chat.
		"shownmessageid" => "int NOT NULL DEFAULT 0",
		// User agent description that took from 'User-Agent' HTTP header.
		"userAgent" => "varchar(255)",
		// Total count of user's messages related with the thread.
		"messageCount" => "varchar(16)",
		// ID of the group at Mibew side related with the thread.
		"groupid" => "int references ${mysqlprefix}chatgroup(groupid)",
	),

	"${mysqlprefix}chatthreadstatistics" => array(
		"statid" => "int NOT NULL auto_increment PRIMARY KEY",
		"date" => "int NOT NULL DEFAULT 0",
		"threads" => "int NOT NULL DEFAULT 0",
		"missedthreads" => "int NOT NULL DEFAULT 0",
		"sentinvitations" => "int NOT NULL DEFAULT 0",
		"acceptedinvitations" => "int NOT NULL DEFAULT 0",
		"rejectedinvitations" => "int NOT NULL DEFAULT 0",
		"ignoredinvitations" => "int NOT NULL DEFAULT 0",
		"operatormessages" => "int NOT NULL DEFAULT 0",
		"usermessages" => "int NOT NULL DEFAULT 0",
		"averagewaitingtime" => "FLOAT(10, 1) NOT NULL DEFAULT 0",
		"averagechattime" => "FLOAT(10, 1) NOT NULL DEFAULT 0"
	),

	"${mysqlprefix}requestbuffer" => array(
		"requestid" => "int NOT NULL auto_increment PRIMARY KEY",
		// Use MD5 hashes as keys
		"requestkey" => "char(32) NOT NULL",
		"request" => "text NOT NULL"
	),

	"${mysqlprefix}requestcallback" => array(
		"callbackid" => "int NOT NULL auto_increment PRIMARY KEY",
		"token" => "varchar(64) NOT NULL DEFAULT ''",
		"function" => "varchar(64) NOT NULL",
		"arguments" => "varchar(1024)"
	),

	// Store chat thread messages
	"${mysqlprefix}chatmessage" => array(
		// Message ID.
		"messageid" => "int NOT NULL auto_increment PRIMARY KEY",
		// ID of the thread related with the message.
		"threadid" => "int NOT NULL references ${mysqlprefix}chatthread(threadid)",
		// Message kind. It is one of Thread::KIND_* constants.
		"ikind" => "int NOT NULL",
		// ID of operator who sent the message. This value will be ignored for
		// system messages and messages which sent by users.
		"agentId" => "int NOT NULL DEFAULT 0",
		// Message text body.
		"tmessage" => "text NOT NULL",
		// Name of the plugin which sent the message. If message was not sent by
		// a plugin this field equals to an empty string.
		"plugin" => "varchar(256) NOT NULL DEFAULT ''",
		// Arbitrary serialized data related with message.
		"data" => "text",
		// Unix timestamp when message was created.
		"dtmcreated" => "int NOT NULL DEFAULT 0",
		// Name of the message sender.
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
		"iperm" => "int DEFAULT 0", /* Do not grant all privileges by default */
		"dtmrestore" => "int NOT NULL DEFAULT 0",
		"vcrestoretoken" => "varchar(64)",
		// Use to start chat with specified operator
		"code" => "varchar(64) DEFAULT ''"
	),

	"${mysqlprefix}chatoperatorstatistics" => array(
		"statid" => "int NOT NULL auto_increment PRIMARY KEY",
		"date" => "int NOT NULL DEFAULT 0",
		"operatorid" => "int NOT NULL",
		"threads" => "int NOT NULL DEFAULT 0",
		"messages" => "int NOT NULL DEFAULT 0",
		"averagelength" => "FLOAT(10, 1) NOT NULL DEFAULT 0",
		"sentinvitations" => "int NOT NULL DEFAULT 0",
		"acceptedinvitations" => "int NOT NULL DEFAULT 0",
		"rejectedinvitations" => "int NOT NULL DEFAULT 0",
		"ignoredinvitations" => "int NOT NULL DEFAULT 0"
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
		"userid" => "varchar(255) NOT NULL",
		"username" => "varchar(64)",
		"firsttime" => "int NOT NULL DEFAULT 0",
		"lasttime" => "int NOT NULL DEFAULT 0",
		"entry" => "text NOT NULL",
		"details" => "text NOT NULL",
		"invitations" => "INT NOT NULL DEFAULT 0",
		"chats" => "INT NOT NULL DEFAULT 0",
		"threadid" => "INT references ${mysqlprefix}chatthread(threadid) on delete set null"
	),

	"${mysqlprefix}visitedpage" => array(
		"pageid" => "INT NOT NULL auto_increment PRIMARY KEY",
		"address" => "varchar(1024)",
		"visittime" => "int NOT NULL DEFAULT 0",
		"visitorid" => "INT",
		// Indicates if path included in 'by page' statistics
		"calculated" => "tinyint NOT NULL DEFAULT 0"
	),

	"${mysqlprefix}visitedpagestatistics" => array(
		"pageid" => "INT NOT NULL auto_increment PRIMARY KEY",
		"date" => "int NOT NULL DEFAULT 0",
		"address" => "varchar(1024)",
		"visits" => "int NOT NULL DEFAULT 0",
		"chats" => "int NOT NULL DEFAULT 0",
		"sentinvitations" => "int NOT NULL DEFAULT 0",
		"acceptedinvitations" => "int NOT NULL DEFAULT 0",
		"rejectedinvitations" => "int NOT NULL DEFAULT 0",
		"ignoredinvitations" => "int NOT NULL DEFAULT 0"
	),
);

$dbtables_indexes = array(
	"${mysqlprefix}chatgroup" => array(
		"parent" => "parent"
	),
	"${mysqlprefix}chatoperatorstatistics" => array(
		"operatorid" => "operatorid"
	),
	"${mysqlprefix}chatgroupoperator" => array(
		"groupid" => "groupid",
		"operatorid" => "operatorid"
	),
	"${mysqlprefix}requestbuffer" => array(
		"requestkey" => "requestkey"
	),
	"${mysqlprefix}chatmessage" => array(
		"idx_agentid" => "agentid"
	),
	"${mysqlprefix}chatsitevisitor" => array(
		"threadid" => "threadid"
	),
	"${mysqlprefix}requestcallback" => array(
		"token" => "token"
	),
	"${mysqlprefix}visitedpage" => array(
		"visitorid" => "visitorid"
	)
);

$memtables = array();

$dbtables_can_update = array(
	"${mysqlprefix}chatthread" => array("agentId", "userTyping", "agentTyping", "messageCount", "nextagent", "shownmessageid", "userid", "userAgent", "groupid", "dtmchatstarted", "dtmclosed", "invitationstate"),
	"${mysqlprefix}chatthreadstatistics" => array("missedthreads", "sentinvitations", "acceptedinvitations", "rejectedinvitations", "ignoredinvitations"),
	"${mysqlprefix}requestbuffer" => array("requestid", "requestkey", "request"),
	"${mysqlprefix}chatmessage" => array("agentId", "plugin", "data"),
	"${mysqlprefix}chatoperator" => array("vcavatar", "vcjabbername", "iperm", "istatus", "idisabled", "vcemail", "dtmrestore", "vcrestoretoken", "code"),
	"${mysqlprefix}chatoperatorstatistics" => array("sentinvitations", "acceptedinvitations", "rejectedinvitations", "ignoredinvitations"),
	"${mysqlprefix}chatban" => array(),
	"${mysqlprefix}chatgroup" => array("vcemail", "iweight", "parent", "vctitle", "vcchattitle", "vclogo", "vchosturl"),
	"${mysqlprefix}chatgroupoperator" => array(),
	"${mysqlprefix}chatresponses" => array("vctitle"),
	"${mysqlprefix}chatsitevisitor" => array(),
	"${mysqlprefix}requestcallback" => array("callbackid", "token", "function", "arguments"),
	"${mysqlprefix}visitedpage" => array(),
	"${mysqlprefix}visitedpagestatistics" => array("sentinvitations", "acceptedinvitations", "rejectedinvitations", "ignoredinvitations"),
);

function show_install_err($text)
{
	global $page;
	$page = array(
		'version' => MIBEW_VERSION,
		'localeLinks' => get_locale_links(),
		'title' => getlocal("install.err.title"),
		'no_right_menu' => true,
		'fixedwrap' => true,
		'errors' => array($text),
	);
	$page_style = new \Mibew\Style\PageStyle('default');
	$page_style->render('install_err', $page);
	exit;
}

function create_table($id, $link)
{
	global $dbtables, $dbtables_indexes, $memtables, $mysqlprefix;

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
	$query .= ") charset utf8";
	if (in_array($id, $memtables)) {
		$query .= " ENGINE=MEMORY";
	} else {
		$query .= " ENGINE=InnoDb";
	}

	mysql_query($query, $link) or show_install_err(' Query failed: ' . mysql_error($link));

	if ($id == "${mysqlprefix}chatoperator") {
		// Create First Administrator
		// Grant all privileges by default only for First Administrator
		mysql_query(
			"INSERT INTO ${mysqlprefix}chatoperator ( " .
				"vclogin, " .
				"vcpassword, " .
				"vclocalename, " .
				"vccommonname, " .
				"vcavatar, " .
				"vcemail, " .
				"iperm " .
			") values ( " .
				"'admin', " .
				"MD5(''), " .
				"'', " .
				"'Administrator', " .
				"'Administrator', " .
				"'', " .
				"65535" .
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