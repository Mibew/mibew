<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
 * 
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

session_start();

require_once(dirname(__FILE__).'/converter.php');
require_once(dirname(__FILE__).'/config.php');

$version = '1.6.3';
$jsver = "163";

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

$locale_pattern = "/^[\w-]{2,5}$/";

function locale_exists($locale) {
	return file_exists(dirname(__FILE__)."/../locales/$locale/properties");
}

function get_available_locales() {
	global $locale_pattern;
	$list = array();
	$folder = dirname(__FILE__)."/../locales";
	if($handle = opendir($folder)) {
		while (false !== ($file = readdir($handle))) {
			if (preg_match($locale_pattern, $file) && $file != 'names' && is_dir("$folder/$file")) {
				$list[] = $file;
			}
		}
		closedir($handle);
	}
	sort($list);
	return $list;
}

function get_user_locale() {
	global $default_locale;

	if( isset($_COOKIE['webim_locale']) ) {
		$requested_lang = $_COOKIE['webim_locale'];
		if( locale_exists($requested_lang) )
			return $requested_lang;
	}

	if( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
		$requested_langs = explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
		foreach( $requested_langs as $requested_lang) {
			if( strlen($requested_lang) > 2 )
				$requested_lang = substr($requested_lang,0,2);

			if( locale_exists($requested_lang) )
				return $requested_lang;
		}
	}

	if( locale_exists($default_locale) )
		return $default_locale;

	return 'en';
}

function get_locale() {
	global $webimroot, $locale_pattern;

	$locale = verifyparam("locale", $locale_pattern, "");

	if( $locale && locale_exists($locale) ) {
		$_SESSION['locale'] = $locale;
		setcookie('webim_locale', $locale, time()+60*60*24*1000, "$webimroot/");
	} else if( isset($_SESSION['locale']) ){
		$locale = $_SESSION['locale'];
	}

	if( !$locale || !locale_exists($locale) )
		$locale = get_user_locale();
	return $locale;
}

$current_locale = get_locale();
$messages = array();
$output_encoding = array();

if(function_exists("date_default_timezone_set")) {
	// TODO try to get timezone from config.php/session etc.
	// autodetect timezone
	@date_default_timezone_set(function_exists("date_default_timezone_get") ? @date_default_timezone_get() : "GMT");
}

function get_locale_links($href) {
	global $current_locale;
	$localeLinks = array();
	$allLocales = get_available_locales();
	if(count($allLocales) < 2) {
		return null;
	}
	foreach($allLocales as $k) {
		$localeLinks[$k] = getlocal_($k, "names");
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
		$keyval = preg_split("/=/", $line, 2 );
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
	if( $locale != 'en' ) {
		return getstring_($text,'en');
	}

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

/* prepares for Javascript string */
function getlocalforJS($text,$params) {
	global $current_locale, $webim_encoding;
	$string = myiconv($webim_encoding,getoutputenc(), getstring_($text,$current_locale));
	$string = str_replace("\"", "\\\"", str_replace("\n", "\\n", $string)); 
	for( $i = 0; $i < count($params); $i++ ) {
		$string = str_replace("{".$i."}", $params[$i], $string);
	}
	return $string;
}

/* ajax server actions use utf-8 */
function getrawparam( $name ) {
	global $webim_encoding;
	if( isset($_POST[$name]) ) {
		$value = myiconv("utf-8",$webim_encoding,$_POST[$name]);
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		return $value;
	}
	die("no ".$name." parameter");
}

/* form processors use current Output encoding */
function getparam( $name ) {
	global $webim_encoding;
	if( isset($_POST[$name]) ) {
		$value = myiconv(getoutputenc(), $webim_encoding, $_POST[$name]);
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		return $value;
	}
	die("no ".$name." parameter");
}

function unicode_urldecode($url) {
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
        $url = str_replace('%u'.$uniord, $utf, $url);
    }
    return urldecode($url);
}

function getgetparam($name,$default='') {
	global $webim_encoding;
	if( !isset($_GET[$name]) || !$_GET[$name] ) {
		return $default;
	}
	$value = myiconv("utf-8", $webim_encoding, unicode_urldecode($_GET[$name]));
	if (get_magic_quotes_gpc()) {
		$value = stripslashes($value);
	}
	return $value;
}

function connect() {
	global $mysqlhost, $mysqllogin, $mysqlpass, $mysqldb, $dbencoding, $force_charset_in_connection;
	if(!extension_loaded("mysql")) {
		die('Mysql extension is not loaded');
	}
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

function rows_count($link,$table,$whereclause="") {
	$result = mysql_query("SELECT count(*) FROM $table $whereclause",$link)
			or die(' Count query failed: '.mysql_error());
	$line = mysql_fetch_array($result, MYSQL_NUM);
	mysql_free_result($result);
	return $line[0];
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
		return htmlspecialchars($page["form$key"]);
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

function get_popup($href,$jshref,$message,$title,$wndName,$options) {
	if(!$jshref) { $jshref = "'$href'"; }
	return "<a href=\"$href\" target=\"_blank\" ".($title?"title=\"$title\" ":"")."onclick=\"if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 &amp;&amp; window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open($jshref, '$wndName', '$options');this.newWindow.focus();this.newWindow.opener=window;return false;\">$message</a>";
}

function get_image($href,$width,$height) {
	if( $width != 0 && $height != 0 )
		return "<img src=\"$href\" border=\"0\" width=\"$width\" height=\"$height\" alt=\"\"/>";
	return "<img src=\"$href\" border=\"0\" alt=\"\"/>";
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
		$infix = '&amp;';
	foreach($params as $k => $v) {
		$servlet .= $infix.$k."=".$v;
		$infix = '&amp;';
	}
	return $servlet;
}

function div($a,$b) {
	return ($a-($a % $b)) / $b;
}

function date_diff_to_text($seconds) {
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

function get_app_location($showhost,$issecure) {
	global $webimroot;
	if( $showhost ) {
		return ($issecure?"https://":"http://").$_SERVER['HTTP_HOST'].$webimroot;
	} else {
		return $webimroot;
	}
}

function is_secure_request() {
	return
		   isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443'
		|| isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on"
		|| isset($_SERVER["HTTP_HTTPS"]) && $_SERVER["HTTP_HTTPS"] == "on";
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

function date_to_text($unixtime) {
	if ($unixtime < 60*60*24*30)
		return getlocal("time.never");
		
	$then = getdate($unixtime);
	$now = getdate();

	if ($then['yday'] == $now['yday'] && $then['year'] == $now['year']) {
		$date_format = getlocal("time.today.at");
	} else if (($then['yday']+1) == $now['yday'] && $then['year'] == $now['year']) {
		$date_format = getlocal("time.yesterday.at");
	} else {
		$date_format = getlocal("time.dateformat");
	}
	
	return strftime($date_format." ".getlocal("time.timeformat"), $unixtime);
}

function webim_mail($toaddr, $reply_to, $subject, $body) {
	global $webim_encoding, $webim_mailbox, $mail_encoding;

	$headers = "From: $webim_mailbox\r\n"
	   ."Reply-To: ".myiconv($webim_encoding, $mail_encoding, $reply_to)."\r\n"
	   ."Content-Type: text/plain; charset=$mail_encoding\r\n"
	   .'X-Mailer: PHP/'.phpversion();

	$real_subject = "=?".$mail_encoding."?B?".base64_encode(myiconv($webim_encoding,$mail_encoding,$subject))."?=";

	$body = preg_replace("/\n/","\r\n", $body);
	
	@mail($toaddr, $real_subject, wordwrap(myiconv($webim_encoding, $mail_encoding, $body),70), $headers);
}

$dbversion = '1.6.3';

$settings = array(
	'dbversion' => 0,
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

	'email' => '',				/* inbox for left messages */
	'left_messages_locale' => $home_locale,
	'sendmessagekey' => 'center',

	'enableban' => '0',
	'enablessl' => '0',
		'forcessl' => '0',
	'usercanchangename' => '1',
	'enablegroups' => '0',
	'enablestatistics' => '1',
	'enablepresurvey' => '1',
		'surveyaskmail' => '0',
		'surveyaskgroup' => '1',
		'surveyaskmessage' => '0',
	'enablepopupnotification' => '0',
	'enablecaptcha' => '0',

	'online_timeout' => 30,		/* Timeout (in seconds) when online operator becomes offline */
	'updatefrequency_operator' => 2,
	'updatefrequency_chat' => 2,
	'updatefrequency_oldchat' => 7,
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

function jspath() {
	global $jsver;
	return "js/$jsver";	
}

?>