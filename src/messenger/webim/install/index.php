<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
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

require_once('../libs/common.php');
require_once('../libs/settings.php');
require_once('dbinfo.php');

$page = array(
	'version' => $version,
	'localeLinks' => get_locale_links("$webimroot/install/index.php")
);

$page['done'] = array();
$page['nextstep'] = false;
$page['nextnotice'] = false;
$errors = array();

function check_connection() {
	global $mysqlhost,$mysqllogin,$mysqlpass, $page, $errors, $webimroot;
	$link = @mysql_connect($mysqlhost,$mysqllogin,$mysqlpass);
	if ($link) {
		$result = mysql_query("SELECT VERSION() as c", $link);
		if( $result && $ver = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$page['done'][] = getlocal2("install.1.connected", array($ver['c']));
			mysql_free_result($result);
		} else {
			$errors[] = "Version of your SQL server is unknown. Please check. Error: ".mysql_error();
			mysql_close($link);
			return null;
		}
		return $link;
	} else {
		$errors[] = getlocal2("install.connection.error", array(mysql_error()));
		return null;
	}
}

function check_database($link) {
	global $mysqldb, $force_charset_in_connection, $dbencoding, $page, $webimroot;
	if(mysql_select_db($mysqldb,$link)) {
		$page['done'][] = getlocal2("install.2.db_exists", array($mysqldb));
		if( $force_charset_in_connection ) {
			mysql_query("SET character set $dbencoding", $link);
		}
		return true;
	} else {
		$page['nextstep'] = getlocal2("install.2.create", array($mysqldb));
		$page['nextnotice'] = getlocal("install.2.notice");
		$page['nextstepurl'] = "$webimroot/install/dbperform.php?act=createdb";
	}
	return false;
}

function check_tables($link) {
	global $dbtables, $page, $webimroot;
	$curr_tables = get_tables($link);
	if( $curr_tables !== false) {
		$tocreate = array_diff(array_keys($dbtables), $curr_tables);
		if( count($tocreate) == 0 ) {
			$page['done'][] = getlocal("install.3.tables_exist");
			return true;
		} else {
			$page['nextstep'] = getlocal("install.3.create");
			$page['nextstepurl'] = "$webimroot/install/dbperform.php?act=ct";
		}
	}
	return false;
}

function check_columns($link) {
	global $dbtables, $dbtables_can_update, $errors, $page, $webimroot;

	$need_to_create_columns = false;
	foreach( $dbtables as $id => $columns) {
		$curr_columns = get_columns($id, $link);
		if( $curr_columns === false ) {
			return false;
		}
		$tocreate = array_diff(array_keys($columns), $curr_columns);
		if( count($tocreate) != 0 ) {
			$cannot_update = array_diff($tocreate, $dbtables_can_update[$id]);
			if( count($cannot_update) != 0) {
				$errors[] = "Key columns are absent in table `$id'. Unable to continue installation.";
				$page['nextstep'] = getlocal("install.kill_tables");
				$page['nextstepurl'] = "$webimroot/install/dbperform.php?act=dt";
				$page['nextnotice'] = getlocal("install.kill_tables.notice");
				return false;
			}
			$need_to_create_columns = true;
		}
	}

	if( $need_to_create_columns ) {
		$page['nextstep'] = getlocal("install.4.create");
		$page['nextstepurl'] = "$webimroot/install/dbperform.php?act=addcolumns";
		$page['nextnotice'] = getlocal("install.4.notice");
		return false;
	}

	$page['done'][] = getlocal("install.4.done");
	return true;
}

function check_status() {
	global $page, $webimroot, $settings, $dbversion;
	$link = check_connection();
	if(!$link) {
		return;
	}

	if( !check_database($link)) {
		mysql_close($link);
		return;
	}

	if( !check_tables($link)) {
		mysql_close($link);
		return;
	}

	if( !check_columns($link)) {
		mysql_close($link);
		return;
	}

	$page['done'][] = getlocal("installed.message");

	$page['nextstep'] = getlocal("installed.login_link");
	$page['nextnotice'] = getlocal("installed.notice");
	$page['nextstepurl'] = "$webimroot/";
	
	$page['show_small_login'] = true;

	mysql_close($link);

	loadsettings();
	$settings['dbversion'] = $dbversion;
	update_settings();
}

check_status();

start_html_output();
require('../view/install_index.php');
?>