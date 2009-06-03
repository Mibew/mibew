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

function get_useragent_version($userAgent) {
    global $knownAgents;
    if (is_array($knownAgents)) {
	$userAgent = strtolower($userAgent);
	foreach( $knownAgents as $agent ) {
		if( strstr($userAgent,$agent) ) {
			if( preg_match( "/".$agent."[\\s\/]?(\\d+(\\.\\d+(\\.\\d+(\\.\\d+)?)?)?)/", $userAgent, $matches ) ) {
				$ver = $matches[1];
				if($agent=='safari') {
					if(preg_match( "/version\/(\\d+(\\.\\d+(\\.\\d+)?)?)/", $userAgent, $matches)) {
						$ver = $matches[1];
					} else {
						$ver = "1 or 2 (build ".$ver.")";
					}
					if(preg_match( "/mobile\/(\\d+(\\.\\d+(\\.\\d+)?)?)/", $userAgent, $matches)) {
						$userAgent = "iPhone ".$matches[1]." ($agent $ver)";
						break;
					}
				}

				$userAgent = ucfirst($agent)." ".$ver;
				break;
			}
		}
	}
    }
    return $userAgent;
}

function get_user_addr($addr) {
	global $settings;
	if($settings['geolink'] && preg_match( "/(\\d+\\.\\d+\\.\\d+\\.\\d+)/", $addr, $matches )) {
		$userip = $matches[1];
		return get_popup(str_replace("{ip}", $userip, $settings['geolink']), '', htmlspecialchars($addr), "GeoLocation", "ip$userip", $settings['geolinkparams']);
	}
	return htmlspecialchars($addr);
}

?>