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

require_once(dirname(dirname(__FILE__)).'/libs/init.php');

$db = \Mibew\Database::getInstance();

$db->throwExeptions(true);

$update_datetime = array(
	'{chatthread}' => array(
		'dtmcreated',
		'dtmchatstarted',
		'dtmmodified',
		'lastpinguser',
		'lastpingagent'
	),
	'{chatmessage}' => array(
		'dtmcreated'
	),
	'{chatoperator}' => array(
		'dtmlastvisited',
		'dtmrestore'
	),
	'{chatban}' => array(
		'dtmcreated',
		'dtmtill'
	),
	'{chatsitevisitor}' => array(
		'firsttime',
		'lasttime',
		'invitationtime'
	),
	'{visitedpage}' => array(
		'visittime'
	),
	'{visitedpagestatistics}' => array(
		'visittime'
	)
);

foreach($update_datetime as $table => $columns) {
	echo("Table: {$table}<br />");
	foreach($columns as $column) {
		echo("-- Column: {$column}<br />");
		$db->query("ALTER TABLE {$table} CHANGE {$column} {$column}_tmp datetime");
		$db->query("ALTER TABLE {$table} ADD COLUMN {$column} int NOT NULL DEFAULT 0 AFTER {$column}_tmp");
		$db->query("UPDATE {$table} SET {$column} = UNIX_TIMESTAMP({$column}_tmp)");
		$db->query("ALTER TABLE {$table} DROP COLUMN {$column}_tmp");
	}
}


?>