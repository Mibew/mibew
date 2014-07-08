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
 * Current version of Mibew Messenger
 */
define('MIBEW_VERSION', '2.0');

/**
 * Current version of database structure
 */
define('DB_VERSION', 20000);

/**
 * Current version of implemented features
 */
define('FEATURES_VERSION', '2.0');

/**
 * Prefix for session variables.
 * Provide an ability to instal several mibew instances on one server.
 */
define('SESSION_PREFIX', md5(
    $configs['database']['host'] . '##'
    . $configs['database']['db']. '##'
    . $configs['database']['tables_prefix']
) . '_');

/**
 * Default value for cron security key.
 * Another value can be set at operator/settings page.
 */
define('DEFAULT_CRON_KEY', md5(
    $configs['database']['host'] . '##' . $configs['database']['db'] . '##'
    . $configs['database']['login'] . '##' . $configs['database']['pass'] . '##'
    . $configs['database']['tables_prefix'] . '##'
));

/**
 * Name for cookie to track visitor
 */
define('VISITOR_COOKIE_NAME', 'MIBEW_VisitorID');

/**
 * Names for chat-related cookies
 */
define('USERID_COOKIE_NAME', 'MIBEW_UserID');
define('USERNAME_COOKIE_NAME', 'MIBEW_Data');

/**
 * Mailbox of the current installation
 */
define('MIBEW_MAILBOX', $configs['mailbox']);
