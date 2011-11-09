<?php
/*
 * This file is part of Mibew Messenger project.
 *
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 *
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
 *
 * Contributors:
 *    Fedor Fetisov - tracking and inviting implementation
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
	    perform_query(sprintf("update ${mysqlprefix}chatsitevisitor set lasttime = CURRENT_TIMESTAMP, path = '%s' where visitorid=" . $visitor['visitorid'],
		db_escape_string(track_build_path($referer, $visitor['path']))), $link);
	    return $visitor['visitorid'];
	}
}

function track_visitor_start($entry, $referer, $link)
{
	global $mysqlprefix;

	$visitor = visitor_from_request();

	perform_query(sprintf("insert into ${mysqlprefix}chatsitevisitor (userid, username, firsttime, lasttime, entry, path, details) values ('%s', '%s', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '%s', '%s', '%s')",
			db_escape_string($visitor['id']),
			db_escape_string($visitor['name']),
			db_escape_string($entry),
			db_escape_string(track_build_path($referer, '')),
			db_escape_string(track_build_details())), $link);

	$id = mysql_insert_id($link);
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


function track_build_path($referer, $path)
{
    if ($path !== '') {
	$path = unserialize($path);
	krsort($path);

	list($lasttime, $lastpage) = each($path);

	if ($referer != $lastpage) {
	    $path[time()] = $referer;
	}
    }
    else {
	$path[time()] = $referer;
    }

    $path = serialize($path);
    return $path;
}

function track_retrieve_path($visitor)
{
    return unserialize($visitor['path']);
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
