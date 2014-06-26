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

	$page['done'][] = getlocal("Application path is {0}", array(MIBEW_WEB_ROOT));
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
		$errors[] = getlocal("Cannot read file {0}", array(MIBEW_WEB_ROOT . "/install/package"));
		if (file_exists($packageFile)) {
			$errors[] = getlocal("Insufficient file permissions {0}", array(fpermissions($packageFile)));
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
				$errors[] = getlocal("Cannot read file {0}", array(MIBEW_WEB_ROOT . "/$file"));
				$errors[] = getlocal("Insufficient file permissions {0}", array(fpermissions($relativeName)));
			} else {
				$errors[] = getlocal("File is absent: {0}", array(MIBEW_WEB_ROOT . "/$file"));
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
				$errors[] = getlocal("Checksum differs for {0}", array(MIBEW_WEB_ROOT . "/$file"));
				$errors[] = getlocal("Please, re-upload files to the server.");
				return false;
			}
		}
	}

	$page['done'][] = getlocal("Mibew package is valid.");
	return true;
}

function check_connection()
{
	global $mysqlhost, $mysqllogin, $mysqlpass, $page, $errors;
	$link = @mysql_connect($mysqlhost, $mysqllogin, $mysqlpass);
	if ($link) {
		$result = mysql_query("SELECT VERSION() as c", $link);
		if ($result && $ver = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$page['done'][] = getlocal("You are connected to MySQL server version {0}", array($ver['c']));
			mysql_free_result($result);
		} else {
			$errors[] = "Version of your SQL server is unknown. Please check. Error: " . mysql_error($link);
			mysql_close($link);
			return null;
		}
		return $link;
	} else {
		$errors[] = getlocal("Could not connect. Please check server settings in config.php. Error: {0}", array(mysql_error()));
		return null;
	}
}

function check_database($link)
{
	global $mysqldb, $page;
	if (mysql_select_db($mysqldb, $link)) {
		$page['done'][] = getlocal("Database \"{0}\" is created.", array($mysqldb));
		mysql_query("SET character set utf8", $link);

		return true;
	} else {
		$page['nextstep'] = getlocal("Create database \"{0}\"", array($mysqldb));
		$page['nextnotice'] = getlocal("The database was not found on the server. If you have permissions to create it now, click on the following link.");
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
			$page['done'][] = getlocal("Required tables are created.");
			return true;
		} else {
			$page['nextstep'] = getlocal("Create required tables.");
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
				$page['nextstep'] = getlocal("Drop existing tables from database");
				$page['nextstepurl'] = MIBEW_WEB_ROOT . "/install/dbperform.php?act=dt";
				$page['nextnotice'] = getlocal("Impossible to update tables structure. Try to do it manually or recreate all tables (warning: all your data will be lost).");
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
		$page['nextstep'] = getlocal("Update tables");
		$page['nextstepurl'] = MIBEW_WEB_ROOT . "/install/dbperform.php?act=addcolumns";
		$page['nextnotice'] = getlocal("Structure of your tables should be adjusted for new version of Messenger.");
		return false;
	}

	$page['done'][] = getlocal("Tables structure is up to date.");
	return true;
}

function check_sound()
{
	global $page;

	$page['soundcheck'] = true;
	$page['done'][] = getlocal("Click to check the sound: {0} and {1}", array(
													   "<a id='check-nv' href='javascript:void(0)'>" . getlocal("New Visitor") . "</a>",
													   "<a id='check-nm' href='javascript:void(0)'>" . getlocal("New Message") . "</a>"
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
		if (in_array($locale, $existlocales)) {
			// Do not export canned messages for existing locales
			continue;
		}

		$file_path = MIBEW_FS_ROOT . '/locales/' . $locale . '/canned_messages.yml';
		if (!is_readable($file_path)) {
			// Export canned messages only for locales which have canned messages
			continue;
		}

		$canned_messages = get_yml_file_content($file_path);
		foreach ($canned_messages as $answer) {
			$result[] = array('locale' => $locale, 'vctitle' => cut_string($answer, 97, '...'), 'vcvalue' => $answer);
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

function add_mail_templates($link){
	global $mysqlprefix;
	$localesresult = mysql_query("select distinct locale from ${mysqlprefix}mailtemplate", $link);
	$existlocales = array();
	for ($i = 0; $i < mysql_num_rows($localesresult); $i++) {
		$existlocales[] = mysql_result($localesresult, $i, 'locale');
	}
	$result = array();
	foreach (get_available_locales() as $locale) {
		if (in_array($locale, $existlocales)) {
			// Do not export mail templates for existing locales
			continue;
		}

        $file_path = MIBEW_FS_ROOT . '/locales/' . $locale . '/mail_templates.yml';
		if (!is_readable($file_path)) {
			// Export templates only for locales which have templates
			continue;
		}

		$templates = get_yml_file_content($file_path);
		if (isset($templates['user_history'])) {
			$result[] = array(
				'locale' => $locale,
				'name' => 'user_history',
				'subject' => $templates['user_history']['subject'],
				'body' => $templates['user_history']['body'],
			);
		}

		if (isset($templates['password_recovery'])) {
			$result[] = array(
				'locale' => $locale,
				'name' => 'password_recovery',
				'subject' => $templates['password_recovery']['subject'],
				'body' => $templates['password_recovery']['body'],
			);
		}

		if (isset($templates['leave_message'])) {
			$result[] = array(
				'locale' => $locale,
				'name' => 'leave_message',
				'subject' => $templates['leave_message']['subject'],
				'body' => $templates['leave_message']['body'],
			);
		}
	}
	if (count($result) > 0) {
		$updatequery = "insert into ${mysqlprefix}mailtemplate (name,locale,subject,body) values ";
		for ($i = 0; $i < count($result); $i++) {
			if ($i > 0) {
				$updatequery .= ", ";
			}
			$updatequery .= "('" . mysql_real_escape_string($result[$i]['name'], $link) . "', "
				. "'" . mysql_real_escape_string($result[$i]['locale'], $link) . "', "
				. "'" . mysql_real_escape_string($result[$i]['subject'], $link) . "', "
				. "'" . mysql_real_escape_string($result[$i]['body'], $link) . "')";
		}
		mysql_query($updatequery, $link);
	}
}

function add_locales($link)
{
	global $mysqlprefix;

	$localesresult = mysql_query("select code from ${mysqlprefix}locale", $link);
	$existlocales = array();
	for ($i = 0; $i < mysql_num_rows($localesresult); $i++) {
		$existlocales[] = mysql_result($localesresult, $i, 'code');
	}
	$locales = discover_locales();
	foreach ($locales as $locale) {
		if (in_array($locale, $existlocales)) {
			// Do not add locales twice.
			continue;
		}
		$query = "insert into ${mysqlprefix}locale (code, enabled) values ('"
			. mysql_real_escape_string($locale, $link) . "', 1)";
		mysql_query($query, $link);
	}
}

function get_yml_file_content($file)
{
	$yaml = new \Symfony\Component\Yaml\Parser();

	return $yaml->parse(file_get_contents($file));
}

function check_status()
{
	global $page, $mysqlprefix;

	$page['done'][] = getlocal("PHP version {0}", array(phpversion()));

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

	add_locales($link);
	add_canned_messages($link);
	add_mail_templates($link);

	check_sound();

	$page['done'][] = getlocal("<b>Application installed successfully.</b>");

	if (!check_admin($link)) {
		$page['nextstep'] = getlocal("Proceed to the login page");
		$page['nextnotice'] = getlocal("You can logon as <b>admin</b> with empty password.<br/><br/><span class=\"warning\">!!! For security reasons please change your password immediately and remove the {0} folder from your server.</span>", array(MIBEW_WEB_ROOT . "/install/"));
		$page['nextstepurl'] = MIBEW_WEB_ROOT . "/operator/login?login=admin";
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

$page['title'] = getlocal("Installation");
$page['fixedwrap'] = true;
$page['errors'] = $errors;

$page_style = new \Mibew\Style\PageStyle('default');

start_html_output();
echo($page_style->render('install_index', $page));

?>