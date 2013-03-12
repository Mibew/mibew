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

require_once(dirname(__FILE__).'/chat.php');

function track_visitor($visitorid, $entry, $referer, $link)
{
	global $mysqlprefix;

	$visitor = track_get_visitor_by_id($visitorid, $link);

	if (FALSE === $visitor) {
	    $visitor = track_visitor_start($entry, $referer, $link);
	    return $visitor;
	}
	else {
	    perform_query("update ${mysqlprefix}chatsitevisitor set lasttime = CURRENT_TIMESTAMP where visitorid=" . $visitor['visitorid'], $link);
	    track_visit_page($visitor['visitorid'], $referer, $link);
	    return $visitor['visitorid'];
	}
}

function track_visitor_start($entry, $referer, $link)
{
	global $mysqlprefix;

	$visitor = visitor_from_request();

	perform_query(sprintf("insert into ${mysqlprefix}chatsitevisitor (userid, username, firsttime, lasttime, entry, details) values ('%s', '%s', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '%s', '%s')",
			db_escape_string($visitor['id']),
			db_escape_string($visitor['name']),
			db_escape_string($entry),
			db_escape_string(track_build_details())), $link);
	$id = db_insert_id($link);

	if ($id) {
		track_visit_page($id, $referer, $link);
	}

	return $id ? $id : 0;
}

function track_get_visitor_by_id($visitorid, $link)
{
	global $mysqlprefix;

	$visitor = select_one_row(
		"select * from ${mysqlprefix}chatsitevisitor where visitorid = $visitorid", $link);

	return $visitor;
}

function track_get_visitor_by_threadid($threadid, $link)
{
	global $mysqlprefix;

	$visitor = select_one_row(
		"select * from ${mysqlprefix}chatsitevisitor where threadid = $threadid", $link);

	return $visitor;
}

function track_visit_page($visitorid, $page, $link)
{
	global $mysqlprefix;
	
	if (empty($page)) {
		return;
	}
	$lastpage = select_one_row(sprintf("select address from ${mysqlprefix}visitedpage where visitorid = '%s' order by visittime desc limit 1",
				db_escape_string($visitorid)), $link);
	if ( $lastpage['address'] != $page ) {
		perform_query(sprintf("insert into ${mysqlprefix}visitedpage (visitorid, address, visittime) values ('%s', '%s', CURRENT_TIMESTAMP)",
					db_escape_string($visitorid),
					db_escape_string($page)), $link);
		perform_query(sprintf("insert into ${mysqlprefix}visitedpagestatistics (address, visittime) values ('%s', CURRENT_TIMESTAMP)",
					db_escape_string($page)), $link);
	}
}

function track_get_path($visitor, $link)
{
	global $mysqlprefix;
	$query_result = perform_query(sprintf("select address, UNIX_TIMESTAMP(visittime) as visittime from ${mysqlprefix}visitedpage where visitorid = '%s'",
				db_escape_string($visitor['visitorid'])), $link);
	$result = array();
	while( $page = db_fetch_assoc($query_result) ){
		$result[$page['visittime']] = $page['address'];
	}
	return $result;
}

function track_build_details()
{
    $result = array(
	'user_agent' => $_SERVER['HTTP_USER_AGENT'],
	'remote_host' => get_remote_host()
    );

    return serialize($result);

}

function track_retrieve_details($visitor)
{
    return unserialize($visitor['details']);
}

?>