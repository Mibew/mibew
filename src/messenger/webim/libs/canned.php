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
 *    Evgeny Gryaznov - initial API and implementation
 */

function load_canned_messages($locale, $groupid)
{
	global $mysqlprefix;
	$link = connect();
	$query = "select id, vcvalue from ${mysqlprefix}chatresponses " .
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
	$result = select_one_row("select vcvalue from ${mysqlprefix}chatresponses where id = $key", $link);
	close_connection($link);
	return $result ? $result['vcvalue'] : null;
}

function save_canned_message($key, $message)
{
	global $mysqlprefix;
	$link = connect();
	perform_query("update ${mysqlprefix}chatresponses set vcvalue = '" . db_escape_string($message, $link) . "' " .
				  "where id = $key", $link);
	close_connection($link);
}

function add_canned_message($locale, $groupid, $message)
{
	global $mysqlprefix;
	$link = connect();
	perform_query("insert into ${mysqlprefix}chatresponses (locale,groupid,vcvalue) values ('$locale'," .
				  ($groupid ? "$groupid, " : "null, ") .
				  "'" . db_escape_string($message, $link) . "')", $link);
	close_connection($link);
}

?>