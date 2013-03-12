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

function load_canned_messages($locale, $groupid)
{
	global $mysqlprefix;
	$link = connect();
	$query = "select id, vctitle, vcvalue from ${mysqlprefix}chatresponses " .
			 "where locale = '" . $locale . "' AND (" .
			 ($groupid
					 ? "groupid = $groupid"
					 : "groupid is NULL OR groupid = 0") .
			 ") order by vcvalue";
	$result = select_multi_assoc($query, $link);
	close_connection($link);
	return $result;
}

function load_canned_message($key)
{
	global $mysqlprefix;
	$link = connect();
	$result = select_one_row("select vctitle, vcvalue from ${mysqlprefix}chatresponses where id = $key", $link);
	close_connection($link);
	return $result ? $result : null;
}

function save_canned_message($key, $title, $message)
{
	global $mysqlprefix;
	$link = connect();
	perform_query("update ${mysqlprefix}chatresponses set vcvalue = '" . db_escape_string($message, $link) . "', " .
				"vctitle = '" . db_escape_string($title, $link) . "' " .
				"where id = $key", $link);
	close_connection($link);
}

function add_canned_message($locale, $groupid, $title, $message)
{
	global $mysqlprefix;
	$link = connect();
	perform_query("insert into ${mysqlprefix}chatresponses (locale,groupid,vctitle,vcvalue) values ('$locale'," .
				($groupid ? "$groupid, " : "null, ") .
				"'" . db_escape_string($title, $link) . "', " .
				"'" . db_escape_string($message, $link) . "')", $link);
	close_connection($link);
}

?>