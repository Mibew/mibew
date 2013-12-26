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

// Import namespaces and classes of the core
use Mibew\Settings;

function setup_settings_tabs($active)
{
	global $page, $mibewroot;
	$page['tabs'] = array(
		getlocal("page_settings.tab.main") => $active != 0 ? "$mibewroot/operator/settings.php" : "",
		getlocal("page_settings.tab.features") => $active != 1 ? "$mibewroot/operator/features.php" : "",
		getlocal("page_settings.tab.performance") => $active != 2 ? "$mibewroot/operator/performance.php" : "",
		getlocal("page_settings.tab.page_themes") => $active != 3 ? "$mibewroot/operator/page_themes.php" : "",
		getlocal("page_settings.tab.themes") => $active != 4 ? "$mibewroot/operator/themes.php" : "",
	);
	if (Settings::get('enabletracking')) {
		$page['tabs'][getlocal("page_settings.tab.invitationthemes")] = ($active != 5 ? "$mibewroot/operator/invitationthemes.php" : "");
	}
}

?>