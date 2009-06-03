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

function setup_operator_settings_tabs($opId, $active) {
	global $page, $webimroot, $settings;
	loadsettings();
	
	if($opId) {
		if($settings['enablegroups'] == '1') {
			$page['tabs'] = array(
				getlocal("page_agent.tab.main") => $active != 0 ? "$webimroot/operator/operator.php?op=$opId" : "",
				getlocal("page_agent.tab.avatar") => $active != 1 ? "$webimroot/operator/avatar.php?op=$opId" : "",
				getlocal("page_agent.tab.groups") => $active != 2 ? "$webimroot/operator/opgroups.php?op=$opId" : "",
				getlocal("page_agent.tab.permissions") => $active != 3 ? "$webimroot/operator/permissions.php?op=$opId" : ""
			);
		} else {
			$page['tabs'] = array(
				getlocal("page_agent.tab.main") => $active != 0 ? "$webimroot/operator/operator.php?op=$opId" : "",
				getlocal("page_agent.tab.avatar") => $active != 1 ? "$webimroot/operator/avatar.php?op=$opId" : "",
				getlocal("page_agent.tab.permissions") => $active != 3 ? "$webimroot/operator/permissions.php?op=$opId" : ""
			);
		}
	} else {
		$page['tabs'] = array();
	}
}

?>
