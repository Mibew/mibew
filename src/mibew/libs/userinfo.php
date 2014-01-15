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

function get_useragent_version($userAgent)
{
	$known_agents = get_known_user_agents();
	if (is_array($known_agents)) {
		$userAgent = strtolower($userAgent);
		foreach ($known_agents as $agent) {
			if (strstr($userAgent, $agent)) {
				if (preg_match("/" . $agent . "[\\s\/]?(\\d+(\\.\\d+(\\.\\d+(\\.\\d+)?)?)?)/", $userAgent, $matches)) {
					$ver = $matches[1];
					if ($agent == 'safari') {
						if (preg_match("/version\/(\\d+(\\.\\d+(\\.\\d+)?)?)/", $userAgent, $matches)) {
							$ver = $matches[1];
						} else {
							$ver = "1 or 2 (build " . $ver . ")";
						}
						if (preg_match("/mobile\/(\\d+(\\.\\d+(\\.\\d+)?)?)/", $userAgent, $matches)) {
							$userAgent = "iPhone " . $matches[1] . " ($agent $ver)";
							break;
						}
					}

					$userAgent = ucfirst($agent) . " " . $ver;
					break;
				}
			}
		}
	}
	return $userAgent;
}

function get_user_addr($addr)
{
	if (Settings::get('geolink') && preg_match("/(\\d+\\.\\d+\\.\\d+\\.\\d+)/", $addr, $matches)) {
		$userip = $matches[1];
		return get_popup(
			str_replace("{ip}", $userip, Settings::get('geolink')),
			'',
			htmlspecialchars($addr),
			"GeoLocation",
			"ip$userip",
			Settings::get('geolinkparams')
		);
	}
	return htmlspecialchars($addr);
}

?>