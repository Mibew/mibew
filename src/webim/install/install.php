<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2007 Internet Services Ltd.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require('../libs/common.php');
require('../libs/operator.php');

function drop_tables() {
	$link = connect();
	perform_query("DROP TABLE IF EXISTS chatthread",$link);
	perform_query("DROP TABLE IF EXISTS chatmessage",$link);
	perform_query("DROP TABLE IF EXISTS chatrevision",$link);
	perform_query("DROP TABLE IF EXISTS chatoperator",$link);
	mysql_close($link);
}

function create_tables() {
	global $dbencoding;
	$link = connect();

	// to update from v1
	// ALTER TABLE chatthread ADD agentId int NOT NULL DEFAULT 0 AFTER agentName
	// update chatthread,chatoperator set agentId = operatorid where agentId = 0 AND (vclocalename = agentName OR vccommonname = agentName) 
	// ALTER TABLE chatmessage ADD agentId int NOT NULL DEFAULT 0 AFTER ikind
	// update chatmessage,chatoperator set agentId = operatorid where agentId = 0 AND ikind = 2 AND (vclocalename = tname OR vccommonname = tname) 
	
	$query = 
		"CREATE TABLE chatthread (\n".
		"	threadid int NOT NULL auto_increment PRIMARY KEY ,\n".
		"	userName varchar(64) NOT NULL,\n".
		"	agentName varchar(64),\n".
		"   agentId int NOT NULL DEFAULT 0,\n".
		"	dtmcreated datetime DEFAULT 0,\n".
		"	dtmmodified datetime DEFAULT 0,\n".
		"	lrevision int NOT NULL DEFAULT 0,\n".
		"	istate int NOT NULL DEFAULT 0,\n".
		"	ltoken int NOT NULL,\n".
		"	remote varchar(255),\n".
		"	referer text,\n".
		"	locale varchar(8),\n".
		"	lastpinguser datetime DEFAULT 0,\n".
		"	lastpingagent datetime DEFAULT 0\n".
		") charset $dbencoding\n";

	perform_query($query,$link);	

	$query = 
		"CREATE TABLE chatmessage\n".
		"(\n".
		"	messageid int NOT NULL auto_increment PRIMARY KEY,\n".
		"	threadid int NOT NULL references chatthread(threadid),\n".
		"	ikind int NOT NULL,\n".
		"   agentId int NOT NULL DEFAULT 0,\n".
		"	tmessage text NOT NULL,\n".
		"	dtmcreated datetime DEFAULT 0,\n".
		"	tname varchar(64)\n".
		") charset $dbencoding\n";

	perform_query($query,$link);	

	perform_query("CREATE TABLE chatrevision (id INT NOT NULL)",$link);	
	perform_query("INSERT INTO chatrevision VALUES (1)",$link);	

	$query = 
		"CREATE TABLE chatoperator\n".
		"(\n".
		"	operatorid int NOT NULL auto_increment PRIMARY KEY,\n".
		"	vclogin varchar(64) NOT NULL,\n".
		"	vcpassword varchar(64) NOT NULL,\n".
		"	vclocalename varchar(64) NOT NULL,\n".
		"	vccommonname varchar(64) NOT NULL,\n".
		"	dtmlastvisited datetime DEFAULT 0\n".
		") charset $dbencoding\n";

	perform_query($query,$link);	

	mysql_close($link);

	create_operator("admin", "", "Administrator", "Administrator");
}

drop_tables();
create_tables();

start_html_output();
require('view_install.php');

?>