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

function invitation_state($visitorid)
{
	$db = Database::getInstance();
	$result = $db->query(
		"select invited, threadid from {chatsitevisitor} where visitorid = ?",
		array($visitorid),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
	if (!$result) {
	    $result['invited'] = 0;
	    $result['threadid'] = 0;
	}
	return $result;
}

function invitation_invite($visitorid, $operatorid)
{
	if (!invitation_check($visitorid)) {
		$db = Database::getInstance();
		$db->query(
			"update {chatsitevisitor} set invited = 1, invitedby = ?, " .
			"invitationtime = now(), invitations = invitations + 1 where visitorid = ?",
			array($operatorid, $visitorid)
		);
		return invitation_check($visitorid);
	} else {
		return FALSE;
	}
}

function invitation_check($visitorid)
{
	$db = Database::getInstance();
	$result = $db->query(
		"select invitedby from {chatsitevisitor} where invited and visitorid = ? " .
		 " and lasttime < invitationtime and threadid is null",
		array($visitorid),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);
	return ($result && isset($result['invitedby']) && $result['invitedby']) ? $result['invitedby'] : FALSE;
}

function invitation_accept($visitorid, $threadid)
{
	$db = Database::getInstance();
	$db->query(
		"update {chatsitevisitor} set threadid = ?, chats = chats + 1 where visitorid = ?",
		array($threadid, $visitorid)
	);

	$result = $db->query(
		"select invitedby from {chatsitevisitor} where visitorid = ?",
		array($visitorid),
		array('return_rows' => Database::RETURN_ONE_ROW)
	);

	if ($result && isset($result['invitedby']) && $result['invitedby']) {
		return $result['invitedby'];
	} else {
		return FALSE;
	}
}

?>