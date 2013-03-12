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

function invitation_state($visitorid, $link)
{
	global $mysqlprefix;
	$query = "select invited, threadid from ${mysqlprefix}chatsitevisitor where visitorid = '" . db_escape_string($visitorid) . "'";
	$result = select_one_row($query, $link);
	if (!$result) {
	    $result['invited'] = 0;
	    $result['threadid'] = 0;
	}
	return $result;
}

function invitation_invite($visitorid, $operatorid, $link)
{
	global $mysqlprefix;

	if (!invitation_check($visitorid, $link)) {
	    $query = "update ${mysqlprefix}chatsitevisitor set invited = 1, invitedby = '" . db_escape_string($operatorid) . "', invitationtime = now(), invitations = invitations + 1 where visitorid = '" . db_escape_string($visitorid) . "'";
	    perform_query($query, $link);
	    return invitation_check($visitorid, $link);
	}
	else {
	    return FALSE;
	}
}

function invitation_check($visitorid, $link)
{
	global $mysqlprefix;

	$query = "select invitedby from ${mysqlprefix}chatsitevisitor where invited and visitorid = '" . db_escape_string($visitorid) . "'" .
		 " and lasttime < invitationtime and threadid is null";
	$result = select_one_row($query, $link);

	return ($result && isset($result['invitedby']) && $result['invitedby']) ? $result['invitedby'] : FALSE;
}

function invitation_accept($visitorid, $threadid, $link)
{
	global $mysqlprefix;

	$query = "update ${mysqlprefix}chatsitevisitor set threadid = " . $threadid . ", chats = chats + 1 where visitorid = " . db_escape_string($visitorid) . "";
	perform_query($query, $link);

	$query = "select invitedby from ${mysqlprefix}chatsitevisitor where visitorid = '" . db_escape_string($visitorid) . "'";
	$result = select_one_row($query, $link);

	if ($result && isset($result['invitedby']) && $result['invitedby']) {
	    return $result['invitedby'];
	}
	else {
	    return FALSE;
	}
}

?>