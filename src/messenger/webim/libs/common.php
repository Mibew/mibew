<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

session_start();

require_once(dirname(__FILE__).'/converter.php');
require_once(dirname(__FILE__).'/config.php');

$version = '1.5.0 beta 2';

function myiconv($in_enc, $out_enc, $string) {
	global $_utf8win1251, $_win1251utf8;
	if($in_enc == $out_enc ) {
		return $string;
	}
	if( function_exists('iconv') ) {
		$converted = @iconv($in_enc, $out_enc, $string);
		if( $converted !== FALSE ) {
			return $converted;
		}
	}
	if( $in_enc == "cp1251" && $out_enc == "utf-8" )
		return strtr($string, $_win1251utf8);
	if( $in_enc == "utf-8" && $out_enc == "cp1251" )
		return strtr($string, $_utf8win1251);

	return $string; // do not know how to convert
}

function verifyparam( $name, $regexp, $default = null ) {
	if( isset( $_GET[$name] ) ) {
		$val = $_GET[$name];
		if( preg_match( $regexp, $val ) )
			return $val;

	} else if( isset( $_POST[$name] ) ) {
		$val = $_POST[$name];
		if( preg_match( $regexp, $val ) )
			return $val;

	} else {
		if( isset( $default ) )
			return $default;
	}
	echo "<html><head></head><body>Wrong parameter used or absent: ".$name."</body></html>";
	exit;
}

function debugexit_print( $var ) {
	echo "<html><body><pre>";
	print_r( $var );
	echo "</pre></body></html>";
	exit;
}

function get_user_locale() {
	global $available_locales, $default_locale;

	if( isset($_COOKIE['webim_locale']) ) {
		$requested_lang = $_COOKIE['webim_locale'];
		if( in_array($requested_lang,$available_locales) )
			return $requested_lang;
	}

	if( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
		$requested_langs = explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
		foreach( $requested_langs as $requested_lang) {
			if( strlen($requested_lang) > 2 )
				$requested_lang = substr($requested_lang,0,2);

			if( in_array($requested_lang,$available_locales) )
				return $requested_lang;
		}
	}

	if( in_array($default_locale,$available_locales) )
		return $default_locale;

	return 'en';
}

function get_locale() {
	global $available_locales, $webimroot;

	$locale = verifyparam("locale", "/^[\w-]{2,5}$/", "");

	if( $locale && in_array($locale,$available_locales) ) {
		$_SESSION['locale'] = $locale;
		setcookie('webim_locale', $locale, time()+60*60*24*1000, "$webimroot/");
	} else if( isset($_SESSION['locale']) ){
		$locale = $_SESSION['locale'];
	}

	if( !$locale || !in_array($locale,$available_locales) )
		$locale = get_user_locale();
	return $locale;
}

$current_locale = get_locale();
setlocale(LC_TIME, $current_locale);
$messages = array();
$output_encoding = array();

function get_locale_links($href) {
	global $available_locales, $current_locale;
	$localeLinks = "";
	foreach($available_locales as $k) {
		if( strlen($localeLinks) > 0 )
			$localeLinks .= " &bull; ";
		if( $k == $current_locale )
			$localeLinks .= getlocal_($k, "names");
		else
			$localeLinks .= "<a href=\"$href?locale=$k\">".getlocal_($k, "names")."</a>";
	}
	return $localeLinks;
}

function load_messages($locale) {
	global $messages, $webim_encoding, $output_encoding;
	$hash = array();
	$current_encoding = $webim_encoding;
	$fp = fopen(dirname(__FILE__)."/../locales/$locale/properties", "r");
	while (!feof($fp)) {
		$line = fgets($fp, 4096);
		$keyval = split("=", $line, 2 );
		if( isset($keyval[1]) ) {
			if($keyval[0] == 'encoding') {
				$current_encoding = trim($keyval[1]);
			} else if($keyval[0] == 'output_encoding') {
				$output_encoding[$locale] = trim($keyval[1]);
			} else if( $current_encoding == $webim_encoding ) {
				$hash[$keyval[0]] = str_replace("\\n", "\n",trim($keyval[1]));
			} else {
				$hash[$keyval[0]] = myiconv($current_encoding, $webim_encoding, str_replace("\\n", "\n",trim($keyval[1])));
			}
		}
	}
	fclose($fp);
	$messages[$locale] = $hash;
}

function getoutputenc() {
	global $current_locale, $output_encoding, $webim_encoding, $messages;
	if(!isset($messages[$current_locale]))
		load_messages($current_locale);
	return isset($output_encoding[$current_locale]) ? $output_encoding[$current_locale] : $webim_encoding;
}

function getstring_($text,$locale) {
	global $messages;
	if(!isset($messages[$locale]))
		load_messages($locale);

	$localized = $messages[$locale];
	if( isset($localized[$text]) )
		return $localized[$text];

	return "!".$text;
}

function getstring($text) {
	global $current_locale;
	return getstring_($text,$current_locale);
}

function getlocal($text) {
	global $current_locale, $webim_encoding;
	return myiconv($webim_encoding,getoutputenc(), getstring_($text,$current_locale));
}

function getlocal_($text,$locale) {
	global $webim_encoding;
	return myiconv($webim_encoding,getoutputenc(), getstring_($text,$locale));
}

function topage($text) {
	global $webim_encoding;
	return myiconv($webim_encoding,getoutputenc(), $text);
}

function getstring2_($text,$params,$locale) {
	$string = getstring_($text,$locale);
	for( $i = 0; $i < count($params); $i++ ) {
		$string = str_replace("{".$i."}", $params[$i], $string);
	}
	return $string;
}

function getstring2($text,$params) {
	global $current_locale;
	return getstring2_($text,$params,$current_locale);
}

function getlocal2($text,$params) {
	global $current_locale, $webim_encoding;
	$string = myiconv($webim_encoding,getoutputenc(), getstring_($text,$current_locale));
	for( $i = 0; $i < count($params); $i++ ) {
		$string = str_replace("{".$i."}", $params[$i], $string);
	}
	return $string;
}

/* ajax server actions use utf-8 */
function getrawparam( $name ) {
	global $webim_encoding;
	if( isset($_POST[$name]) )
		return myiconv("utf-8",$webim_encoding,$_POST[$name]);
	die("no ".$name." parameter");
}

/* form processors use current Output encoding */
function getparam( $name ) {
	global $webim_encoding;
	if( isset($_POST[$name]) )
		return myiconv(getoutputenc(), $webim_encoding, $_POST[$name]);
	die("no ".$name." parameter");
}

function connect() {
	global $mysqlhost, $mysqllogin, $mysqlpass, $mysqldb, $dbencoding, $force_charset_in_connection;
	$link = @mysql_connect($mysqlhost,$mysqllogin ,$mysqlpass )
		or die('Could not connect: ' . mysql_error());
	mysql_select_db($mysqldb,$link) or die('Could not select database');
	if( $force_charset_in_connection ) {
		mysql_query("SET NAMES '$dbencoding'", $link);
	}
	return $link;
}

function perform_query($query,$link) {
	mysql_query($query,$link)
		or die(' Query failed: '.mysql_error()/*.": ".$query*/);
}

function select_one_row($query,$link) {
	$result = mysql_query($query,$link) or die(' Query failed: ' .
		mysql_error().": ".$query);
	$line = mysql_fetch_array($result, MYSQL_ASSOC);
	mysql_free_result($result);
	return $line;
}

function select_multi_assoc($query, $link) {
	$sqlresult = mysql_query($query,$link) or die(' Query failed: ' .
		mysql_error().": ".$query);

	$result = array();
	while ($row = mysql_fetch_array($sqlresult, MYSQL_ASSOC)) {
		$result[] = $row;
	}
	mysql_free_result($sqlresult);
	return $result;
}

function start_xml_output() {
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Content-type: text/xml; charset=utf-8");
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
}

function start_html_output() {
	$charset = getstring("output_charset");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Content-type: text/html".(isset($charset)?"; charset=".$charset:""));
}

function escape_with_cdata($text) {
	return "<![CDATA[" . str_replace("]]>", "]]>]]&gt;<![CDATA[",$text) . "]]>";
}

function form_value($key) {
	global $page;
	if( isset($page) && isset($page["form$key"]) )
		return $page["form$key"];
	return "";
}

function form_value_cb($key) {
	global $page;
	if( isset($page) && isset($page["form$key"]) )
		return $page["form$key"] === true;
	return false;
}

function form_value_mb($key,$id) {
	global $page;
	if( isset($page) && isset($page["form$key"]) && is_array($page["form$key"]) ) {
		return in_array($id, $page["form$key"]);
	}
	return false;
}

function no_field($key) {
	return getlocal2("errors.required",array(getlocal($key)));
}

function failed_uploading_file($filename, $key) {
	return getlocal2("errors.failed.uploading.file",
					  array($filename, getlocal($key)));
}

function wrong_field($key) {
	return getlocal2("errors.wrong_field",array(getlocal($key)));
}

function get_popup($href,$message,$title,$wndName,$options) {
	return "<a href=\"$href\" target=\"_blank\" ".($title?"title=\"$title\" ":"")."onclick=\"if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('$href', '$wndName', '$options');this.newWindow.focus();this.newWindow.opener=window;return false;\">$message</a>";
}

function get_image($href,$width,$height) {
	if( $width != 0 && $height != 0 )
		return "<img src=\"$href\" border=\"0\" width=\"$width\" height=\"$height\"/>";
	return "<img src=\"$href\" border=\"0\"/>";
}

function get_gifimage_size($filename) {
	if( function_exists('gd_info')) {
		$info = gd_info();
		if( isset($info['GIF Read Support']) && $info['GIF Read Support'] ) {
			$img = @imagecreatefromgif($filename);
			if($img) {
				$height = imagesy($img);
				$width = imagesx($img);
				imagedestroy($img);
				return array($width,$height);
			}
		}
	}
	return array(0,0);
}

function add_params($servlet, $params) {
	$infix = '?';
	if( strstr($servlet,$infix) !== FALSE )
		$infix = '&';
	foreach($params as $k => $v) {
		$servlet .= $infix.$k."=".$v;
		$infix = '&';
	}
	return $servlet;
}

function div($a,$b) {
	return ($a-($a % $b)) / $b;
}

function date_diff($seconds) {
	$minutes = div($seconds,60);
	$seconds = $seconds % 60;
	if( $minutes < 60 ) {
		return sprintf("%02d:%02d",$minutes, $seconds);
	} else {
		$hours = div($minutes,60);
		$minutes = $minutes % 60;
		return sprintf("%02d:%02d:%02d",$hours, $minutes, $seconds);
	}
}

function is_valid_email($email) {
	return preg_match("/^[^@]+@[^\.]+(\.[^\.]+)*$/", $email);
}

function quote_smart($value,$link) {
	if (get_magic_quotes_gpc()) {
		$value = stripslashes($value);
	}
	return mysql_real_escape_string($value,$link);
}

function get_app_location($showhost,$issecure) {
	global $webimroot;
	if( $showhost ) {
		return ($issecure?"https://":"http://").$_SERVER['HTTP_HOST'].$webimroot;
	} else {
		return $webimroot;
	}
}

function get_month_selection($fromtime,$totime) {
	$start = getdate($fromtime);
	$month = $start['mon'];
	$year = $start['year'];
	$result = array();
	do {
		$current = mktime(0,0,0,$month,1,$year);
		$result[date("m.y",$current)] = strftime("%B, %Y",$current);
		$month++;
		if( $month > 12 ) {
			$month = 1;
			$year++;
		}
	} while( $current < $totime );
	return $result;
}

function get_form_date($day,$month) {
	if( preg_match('/^(\d{2}).(\d{2})$/', $month, $matches)) {
		return mktime(0,0,0,$matches[1],$day,$matches[2]);
	}
	return 0;
}

function set_form_date($utime,$prefix) {
	global $page;
	$page["form${prefix}day"] = date("d", $utime);
	$page["form${prefix}month"] = date("m.y", $utime);
}

function webim_mail($toaddr, $reply_to, $subject, $body) {
	global $webim_encoding, $webim_from_email, $mail_encoding;

	$headers = "From: $webim_from_email\r\n"
	   ."Reply-To: ".myiconv($webim_encoding, $mail_encoding, $reply_to)."\r\n"
	   ."Content-Type: text/plain; charset=$mail_encoding\r\n"
	   .'X-Mailer: PHP/'.phpversion();

	$real_subject = "=?".$mail_encoding."?B?".base64_encode(myiconv($webim_encoding,$mail_encoding,$subject))."?=";

	mail($toaddr, $real_subject, wordwrap(myiconv($webim_encoding, $mail_encoding, $body),70), $headers);
}

$settings = array(
	'email' => '',				/* inbox for left messages */
	'title' => 'Your Company',
	'hosturl' => 'http://webim.sourceforge.net',
	'logo' => '',
	'enableban' => '0',
	'usernamepattern' => '{name}',
	'usercanchangename' => '1',
	'chatstyle' => 'default',
	'chattitle' => 'Live Support',
	'geolink' => 'http://api.hostip.info/get_html.php?ip={ip}',
	'geolinkparams' => 'width=440,height=100,toolbar=0,scrollbars=0,location=0,status=1,menubar=0,resizable=1',
);
$settingsloaded = false;
$settings_in_db = array();

function loadsettings() {
	global $settingsloaded, $settings_in_db, $settings;
	if($settingsloaded) {
		return;
	}
	$settingsloaded = true;

	$link = connect();
	$sqlresult = mysql_query('select vckey,vcvalue from chatconfig',$link) or die(' Query failed: '.mysql_error().": ".$query);

	while ($row = mysql_fetch_array($sqlresult, MYSQL_ASSOC)) {
		$name = $row['vckey'];
		$settings[$name] = $row['vcvalue'];
		$settings_in_db[$name] = true;
	}
	mysql_free_result($sqlresult);
	mysql_close($link);
}

function getchatstyle() {
	global $settings;
	$chatstyle = verifyparam( "style", "/^\w+$/", "");
	if($chatstyle) {
		return $chatstyle;
	}
	loadsettings();
	return $settings['chatstyle'];
}

?>