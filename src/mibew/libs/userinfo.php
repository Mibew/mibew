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

function get_useragent_version($userAgent)
{
	global $knownAgents;
	if (is_array($knownAgents)) {
		$userAgent = strtolower($userAgent);
		foreach ($knownAgents as $agent) {
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
	global $settings;
	if ($settings['geolink'] && preg_match("/(\\d+\\.\\d+\\.\\d+\\.\\d+)/", $addr, $matches)) {
		$userip = $matches[1];
		return get_popup(safe_htmlspecialchars(str_replace("{ip}", $userip, $settings['geolink'])), '', safe_htmlspecialchars($addr), "GeoLocation", safe_htmlspecialchars("ip$userip"), safe_htmlspecialchars($settings['geolinkparams']));
	}
	return safe_htmlspecialchars($addr);
}

?>