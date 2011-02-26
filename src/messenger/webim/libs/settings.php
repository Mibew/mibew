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

function update_settings()
{
	global $settings, $settings_in_db, $mysqlprefix;
	$link = connect();
	foreach ($settings as $key => $value) {
		if (!isset($settings_in_db[$key])) {
			perform_query("insert into ${mysqlprefix}chatconfig (vckey) values ('$key')", $link);
		}
		$query = sprintf("update ${mysqlprefix}chatconfig set vcvalue='%s' where vckey='$key'", mysql_real_escape_string($value));
		perform_query($query, $link);
	}

	mysql_close($link);
}

function setup_settings_tabs($active)
{
	global $page, $webimroot;
	$page['tabs'] = array(
		getlocal("page_settings.tab.main") => $active != 0 ? "$webimroot/operator/settings.php" : "",
		getlocal("page_settings.tab.features") => $active != 1 ? "$webimroot/operator/features.php" : "",
		getlocal("page_settings.tab.performance") => $active != 2 ? "$webimroot/operator/performance.php" : "",
		getlocal("page_settings.tab.themes") => $active != 3 ? "$webimroot/operator/themes.php" : "",
	);
}

?>