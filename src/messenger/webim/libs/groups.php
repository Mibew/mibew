<?php
/*
 * This file is part of Mibew Messenger project.
 *
 * Copyright (c) 2005-2009 Mibew Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

function group_by_id($id) {
	$link = connect();
	$group = select_one_row(
		 "select * from chatgroup where groupid = $id", $link );
	mysql_close($link);
	return $group;
}

function get_group_name($group) {
	global $home_locale, $current_locale;
	if( $home_locale == $current_locale || !$group['vccommonname'] )
		return $group['vclocalname'];
	else
		return $group['vccommonname'];
}

function setup_group_settings_tabs($gid, $active) {
	global $page, $webimroot, $settings;
	if($gid) {
		$page['tabs'] = array(
			getlocal("page_group.tab.main") => $active != 0 ? "$webimroot/operator/group.php?gid=$gid" : "",
			getlocal("page_group.tab.members") => $active != 1 ? "$webimroot/operator/groupmembers.php?gid=$gid" : "",
		);
	} else {
		$page['tabs'] = array();
	}
}

?>
