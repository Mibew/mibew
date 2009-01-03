<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

function update_settings() {
	global $settings, $settings_in_db;
	$link = connect();
	foreach ($settings as $key => $value) {
		if(!isset($settings_in_db[$key])) {
			perform_query("insert into chatconfig (vckey) values ('$key')",$link);
		}
        $query = sprintf("update chatconfig set vcvalue='%s' where vckey='$key'", mysql_real_escape_string($value));
		perform_query($query,$link);
	}

	mysql_close($link);
}

function setup_settings_tabs($active) {
	global $page, $webimroot;
	$page['tabs'] = array(
		getlocal("page_settings.tab.main") => $active != 0 ? "$webimroot/operator/settings.php" : "",
		getlocal("page_settings.tab.features") => $active != 1 ? "$webimroot/operator/features.php" : "",
		getlocal("page_settings.tab.departments") => $active != 2 ? "$webimroot/operator/departments.php" : "",
	);
}

?>
