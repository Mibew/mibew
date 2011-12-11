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
