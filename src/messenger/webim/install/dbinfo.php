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

$dbtables = array(
	"chatthread" => array(
		"threadid" => "int NOT NULL auto_increment PRIMARY KEY",
		"userName" => "varchar(64) NOT NULL",
		"userid" => "varchar(255)",
		"agentName" => "varchar(64)",
		"agentId" => "int NOT NULL DEFAULT 0",
		"dtmcreated" => "datetime DEFAULT 0",
		"dtmmodified" => "datetime DEFAULT 0",
		"lrevision" => "int NOT NULL DEFAULT 0",
		"istate" => "int NOT NULL DEFAULT 0",
		"ltoken" => "int NOT NULL",
		"remote" => "varchar(255)",
		"referer" => "text",
		"nextagent" => "int NOT NULL DEFAULT 0",
		"locale" => "varchar(8)",
		"lastpinguser" => "datetime DEFAULT 0",
		"lastpingagent" => "datetime DEFAULT 0",
		"userTyping" => "int DEFAULT 0",
		"agentTyping" => "int DEFAULT 0",
		"shownmessageid" => "int NOT NULL DEFAULT 0",
		"userAgent" => "varchar(255)",
		"messageCount" => "varchar(16)"
	),

	"chatmessage" => array(
		"messageid" => "int NOT NULL auto_increment PRIMARY KEY",
		"threadid" => "int NOT NULL references chatthread(threadid)",
		"ikind" => "int NOT NULL",
		"agentId" => "int NOT NULL DEFAULT 0",
		"tmessage" => "text NOT NULL",
		"dtmcreated" => "datetime DEFAULT 0",
		"tname" => "varchar(64)"
	),

	"chatoperator" => array(
		"operatorid" => "int NOT NULL auto_increment PRIMARY KEY",
		"vclogin" => "varchar(64) NOT NULL",
		"vcpassword" => "varchar(64) NOT NULL",
		"vclocalename" => "varchar(64) NOT NULL",
		"vccommonname" => "varchar(64) NOT NULL",
		"dtmlastvisited" => "datetime DEFAULT 0",
	),

	"chatrevision" => array(
		"id" => "INT NOT NULL"
	),

	"chatconfig" => array (
		"id" => "INT NOT NULL auto_increment PRIMARY KEY",
		"vckey" => "varchar(255)",
		"vcvalue" => "varchar(255)",
	)
);

$memtables = array();

$dbtables_can_update = array(
	"chatthread" => array("agentId", "userTyping", "agentTyping", "messageCount", "nextagent", "shownmessageid", "userid", "userAgent"),
	"chatmessage" => array("agentId"),
);

function show_install_err($text) {
	global $page, $version, $errors, $webimroot;
	$page = array(
		'version' => $version,
		'localeLinks' => get_locale_links("$webimroot/install/index.php")
	);
	$errors = array($text);
	start_html_output();
	require('view_installerr.php');
	exit;
}

function create_table($id,$link) {
	global $dbtables, $memtables, $dbencoding;

	if(!isset($dbtables[$id])) {
		show_install_err("Unknown table: $id, ".mysql_error());
	}

	$query =
		"CREATE TABLE $id\n".
		"(\n";
	foreach( $dbtables[$id] as $k => $v ) {
		$query .= "	$k $v,\n";
	}

	$query = preg_replace("/,\n$/", "", $query);
	$query .= ") charset $dbencoding";
	if (in_array($id, $memtables)) {
		$query .= " ENGINE=MEMORY";
	} else {
		$query .= " TYPE=InnoDb";
	}

	mysql_query($query,$link) or show_install_err(' Query failed: '.mysql_error());

	if( $id == 'chatoperator' ) {
		create_operator_("admin", "", "Administrator", "Administrator", $link);
	} else if( $id == 'chatrevision' ) {
		perform_query("INSERT INTO chatrevision VALUES (1)",$link);
	}
}

function get_tables($link) {
	global $mysqldb, $errors;
	$result = mysql_query("SHOW TABLES FROM `$mysqldb`");
	if( $result ) {
		$arr = array();
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$arr[] = $row[0];
		}
		mysql_free_result($result);
		return $arr;

	} else {
		$errors[] = "Cannot get tables from database. Error: ".mysql_error();
		return false;
	}
}

function get_columns($tablename,$link) {
	global $errors;
	$result = mysql_query("SHOW COLUMNS FROM $tablename");
	if( $result ) {
		$arr = array();
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$arr[] = $row[0];
		}
		mysql_free_result($result);
		return $arr;

	} else {
		$errors[] = "Cannot get columns from table \"$tablename\". Error: ".mysql_error();
		return false;
	}
}

?>