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

function setup_settings_tabs($active)
{
	global $page, $webimroot;
	$page['tabs'] = array(
		getlocal("page_settings.tab.main") => $active != 0 ? "$webimroot/operator/settings.php" : "",
		getlocal("page_settings.tab.features") => $active != 1 ? "$webimroot/operator/features.php" : "",
		getlocal("page_settings.tab.performance") => $active != 2 ? "$webimroot/operator/performance.php" : "",
		getlocal("page_settings.tab.themes") => $active != 3 ? "$webimroot/operator/themes.php" : "",
	);
	if (Settings::get('enabletracking')) {
		$page['tabs'][getlocal("page_settings.tab.invitationthemes")] = ($active != 4 ? "$webimroot/operator/invitationthemes.php" : "");
	}
}

	// Send noindex to avoid bots
	header("X-Robots-Tag: noindex, nofollow", true);

?>