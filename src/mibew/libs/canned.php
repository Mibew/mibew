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

// Import namespaces and classes of the core
use Mibew\Database;

function load_canned_messages($locale, $groupid)
{
	$db = Database::getInstance();
	$values = array(':locale' => $locale);
	if ($groupid) {
		$values[':groupid'] = $groupid;
	}
	return $db->query(
		"select id, vctitle, vcvalue from {chatresponses} " .
		"where locale = :locale AND (" .
		($groupid ? "groupid = :groupid" : "groupid is NULL OR groupid = 0") .
		") order by vcvalue",
		$values,
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
}

function load_canned_message($key)
{
	$db = Database::getInstance();
	$result = $db->query(
		"select vctitle, vcvalue from {chatresponses} where id = ?",
		array($key),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
	return $result ? $result : null;
}

function save_canned_message($key, $title, $message)
{
	$db = Database::getInstance();
	$db->query(
		"update {chatresponses} set vcvalue = ?, vctitle = ? where id = ?",
		array($message, $title, $key)
	);
}

function add_canned_message($locale, $groupid, $title, $message)
{
	$db = Database::getInstance();
	$db->query(
		"insert into {chatresponses} (locale,groupid,vctitle,vcvalue) " .
		"values (?, ?, ?, ?)",
		array(
			$locale,
			($groupid ? $groupid : "null"),
			$title,
			$message
		)
	);
}

?>