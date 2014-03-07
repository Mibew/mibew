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

function update_settings()
{
	global $settings, $settings_in_db, $low_level_settings, $mysqlprefix;
	$link = connect();
	foreach ($settings as $key => $value) {

// Don't store low level settings in the database to prevent them from being
// unchangeable
		if (in_array($key, $low_level_settings)) {
			continue;
		}

		if (!isset($settings_in_db[$key])) {
			perform_query("insert into ${mysqlprefix}chatconfig (vckey) values ('" . mysql_real_escape_string($key, $link) . "')", $link);
		}
		$query = sprintf("update ${mysqlprefix}chatconfig set vcvalue='%s' where vckey='%s'", mysql_real_escape_string($value, $link), mysql_real_escape_string($key, $link));
		perform_query($query, $link);
	}

	mysql_close($link);
}

function setup_settings_tabs($active)
{
	global $page, $mibewroot;
	$page['tabselected'] = $active;
	$page['tabs'] = array(
		array('title' => getlocal("page_settings.tab.main"), 'link' => "$mibewroot/operator/settings.php"),
		array('title' => getlocal("page_settings.tab.features"), 'link' => "$mibewroot/operator/features.php"),
		array('title' => getlocal("page_settings.tab.performance"), 'link' => "$mibewroot/operator/performance.php"),
		array('title' => getlocal("page_settings.tab.themes"), 'link' => "$mibewroot/operator/themes.php"),
	);
}

?>