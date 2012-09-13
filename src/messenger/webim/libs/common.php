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

session_start();

require_once(dirname(__FILE__) . '/config.php');
require_once(dirname(__FILE__) . '/plugins.php');
require_once(dirname(__FILE__) . '/classes/database.php');
require_once(dirname(__FILE__) . '/classes/settings.php');

// Include common libs
require_once(dirname(__FILE__) . '/common/constants.php');
require_once(dirname(__FILE__) . '/common/csrf.php');
require_once(dirname(__FILE__) . '/common/datetime.php');
require_once(dirname(__FILE__) . '/common/forms.php');
require_once(dirname(__FILE__) . '/common/verification.php');
require_once(dirname(__FILE__) . '/common/locale.php');
require_once(dirname(__FILE__) . '/common/misc.php');
require_once(dirname(__FILE__) . '/common/request.php');
require_once(dirname(__FILE__) . '/common/response.php');
require_once(dirname(__FILE__) . '/common/string.php');


// Initialize the database
Database::initialize(
	$mysqlhost,
	$mysqllogin,
	$mysqlpass,
	$use_persistent_connection,
	$mysqldb,
	$mysqlprefix,
	$force_charset_in_connection,
	$dbencoding
);

if (function_exists("date_default_timezone_set")) {
	// TODO try to get timezone from config.php/session etc.
	// autodetect timezone
	@date_default_timezone_set(function_exists("date_default_timezone_get") ? @date_default_timezone_get() : "GMT");
}

?>