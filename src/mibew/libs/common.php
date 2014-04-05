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

require_once(dirname(__FILE__) . '/converter.php');
require_once(dirname(__FILE__) . '/config.php');

if (isset($use_open_basedir_protection) && $use_open_basedir_protection) {
// Prevent Mibew from access to files outside the installation
    @ini_set('open_basedir', dirname(dirname(__FILE__)));
}

// Sanitize path to application and remove extra slashes
$mibewroot = join("/", array_map("rawurlencode", preg_split('/\//', preg_replace('/\/+$/', '', preg_replace('/\/{2,}/', '/', '/' . $mibewroot)))));

// Sanitize database tables prefix
$mysqlprefix = preg_replace('/[^A-Za-z0-9_$]/', '', $mysqlprefix);

// test and set default locales
$default_locale = locale_pattern_check($default_locale) && locale_exists($default_locale) ? $default_locale : 'en';
$home_locale = locale_pattern_check($home_locale) && locale_exists($home_locale) ? $home_locale : 'en';

$locale_cookie_name = 'mibew_locale';

$version = '1.6.11';
$jsver = "1611";

// Make session cookie more secure
@ini_set('session.cookie_httponly', TRUE);
if (is_secure_request()) {
    @ini_set('session.cookie_secure', TRUE);
}
@ini_set('session.cookie_path', "$mibewroot/");
@ini_set('session.name', 'MibewSessionID');

session_start();

function myiconv($in_enc, $out_enc, $string)
{
	global $_utf8win1251, $_win1251utf8;
	if ($in_enc == $out_enc) {
		return $string;
	}
	if (function_exists('iconv')) {
		$converted = @iconv($in_enc, $out_enc, $string);
		if ($converted !== FALSE) {
			return $converted;
		}
	}
	if ($in_enc == "cp1251" && $out_enc == "utf-8")
		return strtr($string, $_win1251utf8);
	if ($in_enc == "utf-8" && $out_enc == "cp1251")
		return strtr($string, $_utf8win1251);

	return $string; // do not know how to convert
}

function verifyparam($name, $regexp, $default = null)
{
	if (isset($_GET[$name]) && is_scalar($_GET[$name])) {
		$val = $_GET[$name];
		if (preg_match($regexp, $val))
			return $val;

	} else if (isset($_POST[$name]) && is_scalar($_POST[$name])) {
		$val = $_POST[$name];
		if (preg_match($regexp, $val))
			return $val;

	} else {
		if (isset($default))
			return $default;
	}
	echo "<html><head></head><body>Wrong parameter used or absent: " . safe_htmlspecialchars($name) . "</body></html>";
	exit;
}

function debugexit_print($var)
{
	echo "<html><body><pre>";
	print_r($var);
	echo "</pre></body></html>";
	exit;
}

function locale_exists($locale)
{
	return file_exists(dirname(__FILE__) . "/../locales/$locale/properties");
}

function locale_pattern_check($locale)
{
	$locale_pattern = "/^[\w-]{2,5}$/";
	return preg_match($locale_pattern, $locale) && $locale != 'names';
}

function get_available_locales()
{
	$list = array();
	$folder = dirname(__FILE__) . "/../locales";
	if ($handle = opendir($folder)) {
		while (false !== ($file = readdir($handle))) {
			if (locale_pattern_check($file) && is_dir("$folder/$file")) {
				$list[] = $file;
			}
		}
		closedir($handle);
	}
	sort($list);
	return $list;
}

function get_user_locale()
{
	global $default_locale, $locale_cookie_name;

	if (isset($_COOKIE[$locale_cookie_name])) {
		$requested_lang = $_COOKIE[$locale_cookie_name];
		if (locale_pattern_check($requested_lang) && locale_exists($requested_lang))
			return $requested_lang;
	}

	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$requested_langs = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		foreach ($requested_langs as $requested_lang) {
			if (strlen($requested_lang) > 2)
				$requested_lang = substr($requested_lang, 0, 2);

			if (locale_pattern_check($requested_lang) && locale_exists($requested_lang))
				return $requested_lang;
		}
	}

	if (locale_pattern_check($default_locale) && locale_exists($default_locale))
		return $default_locale;

	return 'en';
}

function get_locale()
{
	global $mibewroot, $locale_cookie_name;

	$locale = verifyparam("locale", "/./", "");

	if ($locale && locale_pattern_check($locale) && locale_exists($locale)) {
		$_SESSION['locale'] = $locale;
	}
	else if (isset($_SESSION['locale']) && locale_pattern_check($_SESSION['locale']) && locale_exists($_SESSION['locale'])) {
		$locale =  $_SESSION['locale'];
	}
	else {
		$locale = get_user_locale();
	}

	setcookie($locale_cookie_name, $locale, time() + 60 * 60 * 24 * 1000, "$mibewroot/");
	return $locale;
}

$current_locale = get_locale();
$messages = array();
$output_encoding = array();

if (function_exists("date_default_timezone_set")) {
	// TODO try to get timezone from config.php/session etc.
	// autodetect timezone
	@date_default_timezone_set(function_exists("date_default_timezone_get") ? @date_default_timezone_get() : "GMT");
}

function get_locale_links($href)
{
	global $current_locale;
	$localeLinks = array();
	$allLocales = get_available_locales();
	if (count($allLocales) < 2) {
		return null;
	}
	foreach ($allLocales as $k) {
		$localeLinks[$k] = getlocal_($k, "names");
	}
	return $localeLinks;
}

function load_messages($locale)
{
	global $messages, $mibew_encoding, $output_encoding;
	$hash = array();
	$current_encoding = $mibew_encoding;
	
	$fp = fopen(dirname(__FILE__) . "/../locales/$locale/properties", "r");
	if ($fp === FALSE) {
		die("unable to open properties for locale $locale");
	}
	while (!feof($fp)) {
		$line = fgets($fp, 4096);
		$keyval = preg_split("/=/", $line, 2);
		if (isset($keyval[1])) {
			if ($keyval[0] == 'encoding') {
				$current_encoding = trim($keyval[1]);
			} else if ($keyval[0] == 'output_encoding') {
				$output_encoding[$locale] = trim($keyval[1]);
			} else if ($current_encoding == $mibew_encoding) {
				$hash[$keyval[0]] = str_replace("\\n", "\n", trim($keyval[1]));
			} else {
				$hash[$keyval[0]] = myiconv($current_encoding, $mibew_encoding, str_replace("\\n", "\n", trim($keyval[1])));
			}
		}
	}
	fclose($fp);
	$messages[$locale] = $hash;
}

function getoutputenc()
{
	global $current_locale, $output_encoding, $mibew_encoding, $messages;
	if (!isset($messages[$current_locale]))
		load_messages($current_locale);
	return isset($output_encoding[$current_locale]) ? $output_encoding[$current_locale] : $mibew_encoding;
}

function getstring_($text, $locale, $raw = false)
{
	global $messages;
	if (!isset($messages[$locale]))
		load_messages($locale);

	$localized = $messages[$locale];
	if (isset($localized[$text]))
		return $raw ? $localized[$text] : sanitize_string($localized[$text], 'low', 'moderate');
	if ($locale != 'en') {
		return getstring_($text, 'en', $raw);
	}

	return "!" . ($raw ? $text : sanitize_string($text, 'low', 'moderate'));
}

function getstring($text, $raw = false)
{
	global $current_locale;
	$string = getstring_($text, $current_locale, true);
	return $raw ? $string : sanitize_string($string, 'low', 'moderate');
}

function getlocal($text, $raw = false)
{
	global $current_locale, $mibew_encoding;
	$string = myiconv($mibew_encoding, getoutputenc(), getstring_($text, $current_locale, true));
	return $raw ? $string : sanitize_string($string, 'low', 'moderate');
}

function getlocal_($text, $locale, $raw = false)
{
	global $mibew_encoding;
	$string = myiconv($mibew_encoding, getoutputenc(), getstring_($text, $locale, true));
	return $raw ? $string : sanitize_string($string, 'low', 'moderate');
}

function topage($text)
{
	global $mibew_encoding;
	return myiconv($mibew_encoding, getoutputenc(), $text);
}

function getstring2_($text, $params, $locale, $raw = false)
{
	$string = getstring_($text, $locale, true);
	for ($i = 0; $i < count($params); $i++) {
		$string = str_replace("{" . $i . "}", $params[$i], $string);
	}
	return $raw ? $string : sanitize_string($string, 'low', 'moderate');
}

function getstring2($text, $params, $raw = false)
{
	global $current_locale;
	$string = getstring2_($text, $params, $current_locale, true);
	return $raw ? $string : sanitize_string($string, 'low', 'moderate');
}

function getlocal2($text, $params, $raw = false)
{
	global $current_locale, $mibew_encoding;
	$string = myiconv($mibew_encoding, getoutputenc(), getstring_($text, $current_locale, true));
	for ($i = 0; $i < count($params); $i++) {
		$string = str_replace("{" . $i . "}", $params[$i], $string);
	}
	return $raw ? $string : sanitize_string($string, 'low', 'moderate');
}

/* prepares for Javascript string */
function getlocalforJS($text, $params)
{
	global $current_locale, $mibew_encoding;
	$string = myiconv($mibew_encoding, getoutputenc(), getstring_($text, $current_locale, true));
	$string = str_replace("\"", "\\\"", str_replace("\n", "\\n", $string));
	for ($i = 0; $i < count($params); $i++) {
		$string = str_replace("{" . $i . "}", $params[$i], $string);
	}
	return sanitize_string($string, 'low', 'moderate');
}

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

function unicode_urldecode($url)
{
	preg_match_all('/%u([[:alnum:]]{4})/', $url, $a);

	foreach ($a[1] as $uniord) {
		$dec = hexdec($uniord);
		$utf = '';

		if ($dec < 128) {
			$utf = chr($dec);
		} else if ($dec < 2048) {
			$utf = chr(192 + (($dec - ($dec % 64)) / 64));
			$utf .= chr(128 + ($dec % 64));
		} else {
			$utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
			$utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
			$utf .= chr(128 + ($dec % 64));
		}
		$url = str_replace('%u' . $uniord, $utf, $url);
	}
	return urldecode($url);
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

function connect()
{
	global $mysqlhost, $mysqllogin, $mysqlpass, $mysqldb, $dbencoding, $force_charset_in_connection;
	if (!extension_loaded("mysql")) {
		die('Mysql extension is not loaded');
	}
	$link = @mysql_connect($mysqlhost, $mysqllogin, $mysqlpass)
		or die('Could not connect: ' . mysql_error());
	mysql_select_db($mysqldb, $link) or die('Could not select database');
	if ($force_charset_in_connection) {
		mysql_query("SET NAMES '" . mysql_real_escape_string($dbencoding, $link) . "'", $link);
	}
	return $link;
}

function perform_query($query, $link)
{
	mysql_query($query, $link) or die(' Query failed: ' . mysql_error($link));
}

function select_one_row($query, $link)
{
	$result = mysql_query($query, $link) or die(' Query failed: ' . mysql_error($link));
	$line = mysql_fetch_array($result, MYSQL_ASSOC);
	mysql_free_result($result);
	return $line;
}

function select_multi_assoc($query, $link)
{
	$sqlresult = mysql_query($query, $link) or die(' Query failed: ' . mysql_error($link));

	$result = array();
	while ($row = mysql_fetch_array($sqlresult, MYSQL_ASSOC)) {
		$result[] = $row;
	}
	mysql_free_result($sqlresult);
	return $result;
}

function db_build_select($fields, $table, $conditions, $orderandgroup)
{
	$condition = count($conditions) > 0 ? " where " . implode(" and ", $conditions) : "";
	if ($orderandgroup) $orderandgroup = " " . $orderandgroup;
	return "select $fields from $table$condition$orderandgroup";
}

function db_rows_count($table, $conditions, $countfields, $link)
{
	$result = mysql_query(db_build_select("count(" . ($countfields ? $countfields : "*") . ")", $table, $conditions, ""), $link)
		or die(' Count query failed: ' . mysql_error($link));
	$line = mysql_fetch_array($result, MYSQL_NUM);
	mysql_free_result($result);
	return $line[0];
}

function start_xml_output()
{
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Content-type: text/xml; charset=utf-8");
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
}

function start_html_output()
{
	$charset = getstring("output_charset");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Content-type: text/html" . (isset($charset) ? "; charset=" . $charset : ""));
}

function escape_with_cdata($text)
{
	return "<![CDATA[" . str_replace("]]>", "]]>]]&gt;<![CDATA[", $text) . "]]>";
}

function form_value($key)
{
	global $page;
	if (isset($page) && isset($page["form$key"]))
		return safe_htmlspecialchars($page["form$key"]);
	return "";
}

function form_value_cb($key)
{
	global $page;
	if (isset($page) && isset($page["form$key"]))
		return $page["form$key"] === true;
	return false;
}

function form_value_mb($key, $id)
{
	global $page;
	if (isset($page) && isset($page["form$key"]) && is_array($page["form$key"])) {
		return in_array($id, $page["form$key"]);
	}
	return false;
}

function no_field($key)
{
	return getlocal2("errors.required", array(getlocal($key)));
}

function failed_uploading_file($filename, $key)
{
	return getlocal2("errors.failed.uploading.file",
		array(safe_htmlspecialchars($filename), getlocal($key)));
}

function wrong_field($key)
{
	return getlocal2("errors.wrong_field", array(getlocal($key)));
}

function get_popup($href, $jshref, $message, $title, $wndName, $options)
{
	if (!$jshref) {
		$jshref = "'$href'";
	}
	return "<a href=\"$href\" target=\"_blank\" " . ($title ? "title=\"$title\" " : "") . "onclick=\"if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 &amp;&amp; window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open($jshref, '$wndName', '$options');this.newWindow.focus();this.newWindow.opener=window;return false;\">$message</a>";
}

function get_image($href, $width, $height)
{
	if ($width != 0 && $height != 0)
		return "<img src=\"" . safe_htmlspecialchars($href) . "\" border=\"0\" width=\"" . safe_htmlspecialchars($width) . "\" height=\"" . safe_htmlspecialchars($height) . "\" alt=\"\"/>";
	return "<img src=\"" . safe_htmlspecialchars($href) . "\" border=\"0\" alt=\"\"/>";
}

function get_gifimage_size($filename)
{
	if (function_exists('gd_info')) {
		$info = gd_info();
		if (isset($info['GIF Read Support']) && $info['GIF Read Support']) {
			$img = @imagecreatefromgif($filename);
			if ($img) {
				$height = imagesy($img);
				$width = imagesx($img);
				imagedestroy($img);
				return array($width, $height);
			}
		}
	}
	return array(0, 0);
}

function add_params($servlet, $params)
{
	$infix = '?';
	if (strstr($servlet, $infix) !== FALSE)
		$infix = '&amp;';
	foreach ($params as $k => $v) {
		$servlet .= $infix . $k . "=" . $v;
		$infix = '&amp;';
	}
	return $servlet;
}

function div($a, $b)
{
	return ($a - ($a % $b)) / $b;
}

function date_diff_to_text($seconds)
{
	$minutes = div($seconds, 60);
	$seconds = $seconds % 60;
	if ($minutes < 60) {
		return sprintf("%02d:%02d", $minutes, $seconds);
	} else {
		$hours = div($minutes, 60);
		$minutes = $minutes % 60;
		return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
	}
}

function is_valid_email($email)
{
	return preg_match("/^[^@]+@[^\.]+(\.[^\.]+)*$/", $email);
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

function get_month_selection($fromtime, $totime)
{
	$start = getdate($fromtime);
	$month = $start['mon'];
	$year = $start['year'];
	$result = array();
	do {
		$current = mktime(0, 0, 0, $month, 1, $year);
		$result[date("m.y", $current)] = strftime("%B, %Y", $current);
		$month++;
		if ($month > 12) {
			$month = 1;
			$year++;
		}
	} while ($current < $totime);
	return $result;
}

function get_form_date($day, $month)
{
	if (preg_match('/^(\d{2}).(\d{2})$/', $month, $matches)) {
		return mktime(0, 0, 0, $matches[1], $day, $matches[2]);
	}
	return 0;
}

function set_form_date($utime, $prefix)
{
	global $page;
	$page["form${prefix}day"] = date("d", $utime);
	$page["form${prefix}month"] = date("m.y", $utime);
}

function date_to_text($unixtime)
{
	if ($unixtime < 60 * 60 * 24 * 30)
		return getlocal("time.never");

	$then = getdate($unixtime);
	$now = getdate();

	if ($then['yday'] == $now['yday'] && $then['year'] == $now['year']) {
		$date_format = getlocal("time.today.at");
	} else if (($then['yday'] + 1) == $now['yday'] && $then['year'] == $now['year']) {
		$date_format = getlocal("time.yesterday.at");
	} else {
		$date_format = getlocal("time.dateformat");
	}

	return strftime($date_format . " " . getlocal("time.timeformat"), $unixtime);
}

$dbversion = '1.6.10';
$featuresversion = '1.6.6';

$settings = array(
	'dbversion' => 0,
	'featuresversion' => 0,
	'title' => 'Your Company',
	'hosturl' => 'http://mibew.org',
	'logo' => '',
	'usernamepattern' => '{name}',
	'chatstyle' => 'default',
	'chattitle' => 'Live Support',
	'geolink' => 'http://api.hostip.info/get_html.php?ip={ip}',
	'geolinkparams' => 'width=440,height=100,toolbar=0,scrollbars=0,location=0,status=1,menubar=0,resizable=1',
	'max_uploaded_file_size' => 100000,
	'max_connections_from_one_host' => 10,
	'thread_lifetime' => 600,

	'email' => '', /* inbox for left messages */
	'left_messages_locale' => $home_locale,
	'sendmessagekey' => 'center',

	'enableban' => '0',
	'enablessl' => '0',
	'forcessl' => '0',
	'usercanchangename' => '1',
	'enablegroups' => '0',
	'enablestatistics' => '1',
	'enablejabber' => '0',
	'enablepresurvey' => '1',
	'surveyaskmail' => '0',
	'surveyaskgroup' => '1',
	'surveyaskmessage' => '0',
	'surveyaskcaptcha' => '0',
	'enablepopupnotification' => '0',
	'showonlineoperators' => '0',
	'enablecaptcha' => '0',

	'online_timeout' => 30, /* Timeout (in seconds) when online operator becomes offline */
	'updatefrequency_operator' => 2,
	'updatefrequency_chat' => 2,
	'updatefrequency_oldchat' => 7,
);
$settingsloaded = false;

// List of low level settings that can't be changed from the UI
$low_level_settings = array(
    'left_messages_locale',
    'max_uploaded_file_size'
);

$settings_in_db = array();

function loadsettings_($link)
{
	global $settingsloaded, $settings_in_db, $settings, $mysqlprefix;
	if ($settingsloaded) {
		return;
	}
	$settingsloaded = true;

	$sqlresult = mysql_query("select vckey,vcvalue from ${mysqlprefix}chatconfig", $link) or die(' Query failed: ' . mysql_error($link));

	while ($row = mysql_fetch_array($sqlresult, MYSQL_ASSOC)) {
		$name = $row['vckey'];
		$settings[$name] = $row['vcvalue'];
		$settings_in_db[$name] = true;
	}
	mysql_free_result($sqlresult);
}

function loadsettings()
{
	global $settingsloaded;
	if (!$settingsloaded) {
		$link = connect();
		loadsettings_($link);
		mysql_close($link);
	}
}

function getchatstyle()
{
	global $settings;
	$chatstyle = verifyparam("style", "/^\w+$/", "");
	if ($chatstyle) {
		return $chatstyle;
	}
	loadsettings();
	return $settings['chatstyle'];
}

function jspath()
{
	global $jsver;
	return "js/$jsver";
}

/* authorization token check for CSRF attack */
function csrfchecktoken()
{
	setcsrftoken();

	// check the turing code for post requests and del requests
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		//if token match
		if (!isset($_POST['csrf_token']) || ($_POST['csrf_token'] != $_SESSION['csrf_token'])) {

			die("CSRF failure");
		}
	} else if (isset($_GET['act'])) {
		if (($_GET['act'] == 'del' || $_GET['act'] == 'delete') && $_GET['csrf_token'] != $_SESSION['csrf_token']) {

			die("CSRF failure");
		}
	}
}

/* print csrf token as a hidden field*/
function print_csrf_token_input()
{
	setcsrftoken();

	echo "<input name=\"csrf_token\" type=\"hidden\" value=\"" . $_SESSION['csrf_token'] . "\" />\n";
}

/* print csrf token in url format */
function print_csrf_token_in_url()
{
	setcsrftoken();

	echo "&amp;csrf_token=" . $_SESSION['csrf_token'];
}

/* set csrf token */
function setcsrftoken()
{
	if (!isset($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = sha1(session_id() . (function_exists('openssl_random_pseudo_bytes') ? openssl_random_pseudo_bytes(32) : (time() + microtime()) . mt_rand(0, 99999999)));
	}
}

/* simple HTML sanitation
 *
 * includes some code from the PHP Strip Attributes Class For XML and HTML
 * Copyright 2009 David (semlabs.co.uk)
 * Available under the MIT License.
 *
 * http://semlabs.co.uk/journal/php-strip-attributes-class-for-xml-and-html
 *
 */

function sanitize_string($string, $tags_level = 'high', $attr_level = 'high')
{
	$sanitize_tags = array(
		'high' => '',
		'moderate' => '<span><em><strong><b><i><br>',
		'low' => '<span><em><strong><b><i><br><p><ul><ol><li><a><font><style>'
	);

	$sanitize_attributes = array(
		'high' => array(),
		'moderate' => array('class', 'style', 'href', 'rel', 'id'),
		'low' => false
	);

	$tags_level = array_key_exists($tags_level, $sanitize_tags) ? $tags_level : 'high';
	$string = strip_tags($string, $sanitize_tags[$tags_level]);

	$attr_level = array_key_exists($attr_level, $sanitize_attributes) ? $attr_level : 'high';
	if ($sanitize_attributes[$attr_level]) {

		preg_match_all("/<([^ !\/\>\n]+)([^>]*)>/i", $string, $elements);
		foreach ($elements[1] as $key => $element) {
			if ($elements[2][$key]) {

				$new_attributes = '';
				preg_match_all("/([^ =]+)\s*=\s*[\"|']{0,1}([^\"']*)[\"|']{0,1}/i", $elements[2][$key], $attributes );

				if ($attributes[1]) {
					foreach ($attributes[1] as $attr_key => $attr) {
						if (in_array($attributes[1][$attr_key], $sanitize_attributes[$attr_level])) {
							$new_attributes .= ' ' . $attributes[1][$attr_key] . '="' . $attributes[2][$attr_key] . '"';
						}
					}
				}

				$replacement = '<' . $elements[1][$key] . $new_attributes . '>';
				$string = preg_replace( '/' . sanitize_reg_escape($elements[0][$key]) . '/', $replacement, $string );

			}
		}

	}

	return $string;
}

function sanitize_reg_escape($string)
{

	$conversions = array(	"^" => "\^",
				"[" => "\[",
				"." => "\.",
				"$" => "\$",
				"{" => "\{",
				"*" => "\*",
				"(" => "\(",
				"\\" => "\\\\",
				"/" => "\/",
				"+" => "\+",
				")" => "\)",
				"|" => "\|",
				"?" => "\?",
				"<" => "\<",
				">" => "\>"
	);

	return strtr($string, $conversions);
}

/* wrapper for htmlspecialchars with single quotes conversion enabled
   by default */

function safe_htmlspecialchars($string)
{
	$string = preg_replace('/[\x00-\x08\x10-\x1f\x0b]/', '', $string);
	return htmlspecialchars($string, ENT_QUOTES);
}

?>