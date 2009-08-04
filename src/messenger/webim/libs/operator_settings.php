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