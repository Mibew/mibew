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

/**
 * Indicate that installation in progress
 */
define('INSTALLATION_IN_PROGRESS', TRUE);

/**
 * File system root directory of the Mibew installations
 */
define('MIBEW_FS_ROOT', dirname(dirname(__FILE__)));

session_start();

require_once(MIBEW_FS_ROOT.'/libs/config.php');

/**
 * Value of $mibewroot varaible from config.php
 */
define('MIBEW_CONFIG_WEB_ROOT', $mibewroot);

// Initialize external dependencies
require_once(MIBEW_FS_ROOT . '/vendor/autoload.php');

// Try to get actual base URL of the Mibew
$requestUri = $_SERVER["REQUEST_URI"];
if (!preg_match('/^(.*)\\/install(\\/[^\\/\\\\]*)?$/', $requestUri, $matches)) {
	die("Cannot detect application location: $requestUri");
}
$base_url = $matches[1];

/**
 * Base URL of the Mibew installation
 */
define('MIBEW_WEB_ROOT', $base_url);

// Include common functions
require_once(MIBEW_FS_ROOT.'/libs/common/constants.php');
require_once(MIBEW_FS_ROOT.'/libs/common/verification.php');
require_once(MIBEW_FS_ROOT.'/libs/common/locale.php');
require_once(MIBEW_FS_ROOT.'/libs/common/misc.php');
require_once(MIBEW_FS_ROOT.'/libs/common/response.php');
require_once(MIBEW_FS_ROOT.'/libs/common/string.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/Mibew/Style/StyleInterface.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/Mibew/Style/AbstractStyle.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/Mibew/Style/PageStyle.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/Mibew/Handlebars/HelpersSet.php');
// Include database structure
require_once(MIBEW_FS_ROOT.'/install/dbinfo.php');

$page = array(
	'version' => MIBEW_VERSION,
	'localeLinks' => get_locale_links()
);

$page['done'] = array();
$page['nextstep'] = false;
$page['nextnotice'] = false;
$page['soundcheck'] = false;
$errors = array();

function check_mibewroot()
{
	global $page, $errors;

	if (MIBEW_CONFIG_WEB_ROOT != MIBEW_WEB_ROOT) {
		$errors[] = "Please, check file " . MIBEW_WEB_ROOT . "/libs/config.php<br/>Wrong value of \$mibewroot variable, should be \"" . MIBEW_WEB_ROOT . "\"";
		return false;
	}

	$page['done'][] = getlocal2("install.0.app", array(MIBEW_WEB_ROOT));
	return true;
}

function fpermissions($file)
{
	$perms = fileperms($file);
	if (($perms & 0x8000) == 0x8000) {
		$info = '-';
	} elseif (($perms & 0x4000) == 0x4000) {
		$info = 'd';
	} else {
		$info = '?';
	}

	// Owner
	$info .= (($perms & 0x0100) ? 'r' : '-');
	$info .= (($perms & 0x0080) ? 'w' : '-');
	$info .= (($perms & 0x0040) ?
			(($perms & 0x0800) ? 's' : 'x') :
			(($perms & 0x0800) ? 'S' : '-'));

	// Group
	$info .= (($perms & 0x0020) ? 'r' : '-');
	$info .= (($perms & 0x0010) ? 'w' : '-');
	$info .= (($perms & 0x0008) ?
			(($perms & 0x0400) ? 's' : 'x') :
			(($perms & 0x0400) ? 'S' : '-'));

	// World
	$info .= (($perms & 0x0004) ? 'r' : '-');
	$info .= (($perms & 0x0002) ? 'w' : '-');
	$info .= (($perms & 0x0001) ?
			(($perms & 0x0200) ? 't' : 'x') :
			(($perms & 0x0200) ? 'T' : '-'));

	return $info;
}

function check_files()
{
	global $page, $errors;

	$packageFile = MIBEW_FS_ROOT . "/install/package";
	$fp = @fopen($packageFile, "r");
	if ($fp === FALSE) {
		$errors[] = getlocal2("install.cannot_read", array(MIBEW_WEB_ROOT . "/install/package"));
		if (file_exists($packageFile)) {
			$errors[] = getlocal2("install.check_permissions", array(fpermissions($packageFile)));
		}
		return false;
	}

	$knownFiles = array();
	while (!feof($fp)) {
		$line = fgets($fp, 4096);
		$keyval = preg_split("/ /", $line, 2);
		if (isset($keyval[1])) {
			$knownFiles[$keyval[0]] = trim($keyval[1]);
		}
	}
	fclose($fp);

	foreach ($knownFiles as $file => $sum) {
		$relativeName = MIBEW_FS_ROOT . "/$file";
		if (!is_readable($relativeName)) {
			if (file_exists($relativeName)) {
				$errors[] = getlocal2("install.cannot_read", array(MIBEW_WEB_ROOT . "/$file"));
				$errors[] = getlocal2("install.check_permissions", array(fpermissions($relativeName)));
			} else {
				$errors[] = getlocal2("install.no_file", array(MIBEW_WEB_ROOT . "/$file"));
			}
			return false;
		}
		if ($sum != "-") {
			$result = md5_file($relativeName);
			if ($result != $sum) {
				// try without \r
				$result = md5(str_replace("\r", "", file_get_contents($relativeName)));
			}
			if ($result != $sum) {
				$errors[] = getlocal2("install.bad_checksum", array(MIBEW_WEB_ROOT . "/$file"));
				$errors[] = getlocal("install.check_files");
				return false;
			}
		}
	}

	$page['done'][] = getlocal("install.0.package");
	return true;
}

function check_connection()
{
	global $mysqlhost, $mysqllogin, $mysqlpass, $page, $errors;
	$link = @mysql_connect($mysqlhost, $mysqllogin, $mysqlpass);
	if ($link) {
		$result = mysql_query("SELECT VERSION() as c", $link);
		if ($result && $ver = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$page['done'][] = getlocal2("install.1.connected", array($ver['c']));
			mysql_free_result($result);
		} else {
			$errors[] = "Version of your SQL server is unknown. Please check. Error: " . mysql_error($link);
			mysql_close($link);
			return null;
		}
		return $link;
	} else {
		$errors[] = getlocal2("install.connection.error", array(mysql_error()));
		return null;
	}
}

function check_database($link)
{
	global $mysqldb, $page;
	if (mysql_select_db($mysqldb, $link)) {
		$page['done'][] = getlocal2("install.2.db_exists", array($mysqldb));
		mysql_query("SET character set utf8", $link);

		return true;
	} else {
		$page['nextstep'] = getlocal2("install.2.create", array($mysqldb));
		$page['nextnotice'] = getlocal("install.2.notice");
		$page['nextstepurl'] = MIBEW_WEB_ROOT . "/install/dbperform.php?act=createdb";
	}
	return false;
}

function check_tables($link)
{
	global $dbtables, $page;
	$curr_tables = get_tables($link);
	if ($curr_tables !== false) {
		$tocreate = array_diff(array_keys($dbtables), $curr_tables);
		if (count($tocreate) == 0) {
			$page['done'][] = getlocal("install.3.tables_exist");
			return true;
		} else {
			$page['nextstep'] = getlocal("install.3.create");
			$page['nextstepurl'] = MIBEW_WEB_ROOT . "/install/dbperform.php?act=ct";
		}
	}
	return false;
}

function check_columns($link)
{
	global $dbtables, $dbtables_can_update, $dbtables_indexes, $errors, $page;

	$need_to_create_columns = false;
	foreach ($dbtables as $id => $columns) {
		$curr_columns = get_columns($id, $link);
		if ($curr_columns === false) {
			return false;
		}
		$tocreate = array_diff(array_keys($columns), $curr_columns);
		if (count($tocreate) != 0) {
			$cannot_update = array_diff($tocreate, $dbtables_can_update[$id]);
			if (count($cannot_update) != 0) {
				$errors[] = "Key columns are absent in table `$id'. Unable to continue installation.";
				$page['nextstep'] = getlocal("install.kill_tables");
				$page['nextstepurl'] = MIBEW_WEB_ROOT . "/install/dbperform.php?act=dt";
				$page['nextnotice'] = getlocal("install.kill_tables.notice");
				return false;
			}
			$need_to_create_columns = true;
		}
	}

	$need_to_create_indexes = false;
	foreach ($dbtables_indexes as $id => $indexes) {
		$curr_indexes = get_indexes($id, $link);
		if ($curr_indexes === false) {
			return false;
		}
		$tocreate = array_diff(array_keys($indexes), $curr_indexes);
		if (count($tocreate) != 0) {
			$need_to_create_indexes = true;
		}
	}

	if ($need_to_create_columns || $need_to_create_indexes) {
		$page['nextstep'] = getlocal("install.4.create");
		$page['nextstepurl'] = MIBEW_WEB_ROOT . "/install/dbperform.php?act=addcolumns";
		$page['nextnotice'] = getlocal("install.4.notice");
		return false;
	}

	$page['done'][] = getlocal("install.4.done");
	return true;
}

function check_sound()
{
	global $page;

	$page['soundcheck'] = true;
	$page['done'][] = getlocal2("install.5.text", array(
													   "<a id='check-nv' href='javascript:void(0)'>" . getlocal("install.5.newvisitor") . "</a>",
													   "<a id='check-nm' href='javascript:void(0)'>" . getlocal("install.5.newmessage") . "</a>"
												  ));
}

function check_admin($link)
{
	global $mysqlprefix;
	$result = mysql_query("select * from ${mysqlprefix}chatoperator where vclogin = 'admin'", $link);
	if ($result) {
		$line = mysql_fetch_array($result, MYSQL_ASSOC);
		mysql_free_result($result);
		return $line['vcpassword'] != md5('');
	}

	return false;
}

function add_canned_messages($link){
	global $mysqlprefix;
	$localesresult = mysql_query("select locale from ${mysqlprefix}chatresponses", $link);
	$existlocales = array();
	for ($i = 0; $i < mysql_num_rows($localesresult); $i++) {
		$existlocales[] = mysql_result($localesresult, $i, 'locale');
	}
	$result = array();
	foreach (get_available_locales() as $locale) {
		if (! in_array($locale, $existlocales)) {
			foreach (explode("\n", getstring_('chat.predefined_answers', $locale)) as $answer) {
				$result[] = array('locale' => $locale, 'vctitle' => cut_string($answer, 97, '...'), 'vcvalue' => $answer);
			}
		}
	}
	if (count($result) > 0) {
		$updatequery = "insert into ${mysqlprefix}chatresponses (vctitle,vcvalue,locale,groupid) values ";
		for ($i = 0; $i < count($result); $i++) {
			if ($i > 0) {
				$updatequery .= ", ";
			}
			$updatequery .= "('" . mysql_real_escape_string($result[$i]['vctitle'], $link) . "', "
				. "'" . mysql_real_escape_string($result[$i]['vcvalue'], $link) . "', "
				. "'" . mysql_real_escape_string($result[$i]['locale'], $link) . "', NULL)";
		}
		mysql_query($updatequery, $link);
	}
}

function check_status()
{
	global $page, $mysqlprefix;

	$page['done'][] = getlocal2("install.0.php", array(phpversion()));

	if (!check_mibewroot()) {
		return;
	}

	if (!check_files()) {
		return;
	}

	$link = check_connection();
	if (!$link) {
		return;
	}

	if (!check_database($link)) {
		mysql_close($link);
		return;
	}

	if (!check_tables($link)) {
		mysql_close($link);
		return;
	}

	if (!check_columns($link)) {
		mysql_close($link);
		return;
	}

	add_canned_messages($link);

	check_sound();

	$page['done'][] = getlocal("installed.message");

	if (!check_admin($link)) {
		$page['nextstep'] = getlocal("installed.login_link");
		$page['nextnotice'] = getlocal2("installed.notice", array(MIBEW_WEB_ROOT . "/install/"));
		$page['nextstepurl'] = MIBEW_WEB_ROOT . "/operator/login.php?login=admin";
	}

	$page['show_small_login'] = true;

	// Update current dbversion
	$res = mysql_query("select COUNT(*) as count from ${mysqlprefix}chatconfig where vckey = 'dbversion'", $link);
	if(mysql_result($res, 0, 'count') == 0) {
		mysql_query("insert into ${mysqlprefix}chatconfig (vckey) values ('dbversion')", $link);
	}

	mysql_query("update ${mysqlprefix}chatconfig set vcvalue = '" . DB_VERSION . "' where vckey='dbversion'", $link);
	mysql_close($link);

}

check_status();

$page['title'] = getlocal("install.title");
$page['fixedwrap'] = true;
$page['errors'] = $errors;

$page_style = new \Mibew\Style\PageStyle('default');
$page_style->render('install_index', $page);

?>