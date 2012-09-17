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

function track_visitor($visitorid, $entry, $referer)
{
	$visitor = track_get_visitor_by_id($visitorid);

	if (FALSE === $visitor) {
		$visitor = track_visitor_start($entry, $referer);
		return $visitor;
	} else {
		$db = Database::getInstance();
		$db->query(
			"update {chatsitevisitor} set lasttime = :now " .
			"where visitorid = :visitorid",
			array(
				':visitorid' => $visitor['visitorid'],
				':now' => time()
			)
		);
		track_visit_page($visitor['visitorid'], $referer);
		return $visitor['visitorid'];
	}
}

function track_visitor_start($entry, $referer)
{
	$visitor = visitor_from_request();

	$db = Database::getInstance();
	$db->query(
		"insert into {chatsitevisitor} (userid,username,firsttime,lasttime,entry,details) ".
		"values (:userid, :username, :now, :now, :entry, :details)",
		array(
			':userid' => $visitor['id'],
			':username' => $visitor['name'],
			':now' => time(),
			':entry' => $entry,
			':details' => track_build_details()
		)
	);

	$id = $db->insertedId();

	if ($id) {
		track_visit_page($id, $referer);
	}

	return $id ? $id : 0;
}

function track_get_visitor_by_id($visitorid)
{
	$db = Database::getInstance();
	return $db->query(
		"select * from {chatsitevisitor} where visitorid = ?",
		array($visitorid),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
}

function track_get_visitor_by_threadid($threadid)
{
	$db = Database::getInstance();
	return $db->query(
		"select * from {chatsitevisitor} where threadid = ?",
		array($threadid),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
}

function track_visit_page($visitorid, $page)
{
	$db = Database::getInstance();

	if (empty($page)) {
		return;
	}
	$lastpage = $db->query(
		"select address from {visitedpage} where visitorid = ? " .
		"order by visittime desc limit 1",
		array($visitorid),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
	if ( $lastpage['address'] != $page ) {
		$db->query(
			"insert into {visitedpage} (visitorid, address, visittime) " .
			"values (:visitorid, :page, :now)",
			array(
				':visitorid' => $visitorid,
				':page' => $page,
				':now' => time()
			)
		);
		$db->query(
			"insert into {visitedpagestatistics} (address, visittime) " .
			"values (:page, :now)",
			array(
				':page' =>  $page,
				':now' => time()
			)
		);
	}
}

function track_get_path($visitor)
{
	$db = Database::getInstance();
	$query_result = $db->query(
		"select address, visittime from {visitedpage} " .
		"where visitorid = ?",
		array($visitor['visitorid']),
		array('return_rows' => Database::RETURN_ALL_ROWS)
	);
	$result = array();
	foreach ($query_result as $page) {
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