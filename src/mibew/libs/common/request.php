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

require_once(dirname(__FILE__).'/locale.php');

/* ajax server actions use utf-8 */
function getrawparam($name)
{
	global $mibew_encoding;
	if (isset($_POST[$name])) {
		$value = myiconv("utf-8", $mibew_encoding, $_POST[$name]);
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		return $value;
	}
	die("no " . $name . " parameter");
}

/* form processors use current Output encoding */
function getparam($name)
{
	global $mibew_encoding;
	if (isset($_POST[$name])) {
		$value = myiconv(getoutputenc(), $mibew_encoding, $_POST[$name]);
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		return $value;
	}
	die("no " . $name . " parameter");
}

function getgetparam($name, $default = '')
{
	global $mibew_encoding;
	if (!isset($_GET[$name]) || !$_GET[$name]) {
		return $default;
	}
	$value = myiconv("utf-8", $mibew_encoding, unicode_urldecode($_GET[$name]));
	if (get_magic_quotes_gpc()) {
		$value = stripslashes($value);
	}
	return $value;
}

function get_app_location($showhost, $issecure)
{
	global $mibewroot;
	if ($showhost) {
		return ($issecure ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $mibewroot;
	} else {
		return $mibewroot;
	}
}

function is_secure_request()
{
	return
			isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443'
			|| isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on"
			|| isset($_SERVER["HTTP_HTTPS"]) && $_SERVER["HTTP_HTTPS"] == "on";
}

/**
 * Returns name of the current operator pages style
 *
 * @return string
 */
function get_operator_pages_style() {
	return Settings::get('operator_pages_style');
}

?>