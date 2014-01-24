<?php
/*
 * Copyright 2005-2014 the original author or authors.
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

function setup_operator_settings_tabs($opId, $active)
{
	global $page, $mibewroot, $settings;
	loadsettings();

	if ($opId) {
		if ($settings['enablegroups'] == '1') {
			$page['tabs'] = array(
				array('title' => getlocal("page_agent.tab.main"), 'link' => "$mibewroot/operator/operator.php?op=$opId"),
				array('title' => getlocal("page_agent.tab.avatar"), 'link' => "$mibewroot/operator/avatar.php?op=$opId"),
				array('title' => getlocal("page_agent.tab.groups"), 'link' => "$mibewroot/operator/opgroups.php?op=$opId"),
				array('title' => getlocal("page_agent.tab.permissions"), 'link' => "$mibewroot/operator/permissions.php?op=$opId"),
			);
		} else {
			$page['tabs'] = array(
				array('title' => getlocal("page_agent.tab.main"), 'link' => "$mibewroot/operator/operator.php?op=$opId"),
				array('title' => getlocal("page_agent.tab.avatar"), 'link' => "$mibewroot/operator/avatar.php?op=$opId"),
				array('title' => getlocal("page_agent.tab.permissions"), 'link' => "$mibewroot/operator/permissions.php?op=$opId"),
			);
			if ($active == 3) $active--;
		}
		$page['tabselected'] = $active;
	} else {
		$page['tabs'] = array();
	}
}

?>