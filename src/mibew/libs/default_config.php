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


/*
 * IMPORTANT: Before install mibew copy this file to config.php and fill
 * it with your own settings!
 */


/*
 *  Application path on server
 */
$mibewroot = "/mibew";

/*
 *  MySQL Database parameters
 */
$mysqlhost = "";
$mysqldb = "";
$mysqllogin = "";
$mysqlpass = "";
$mysqlprefix = "";

$use_persistent_connection = false;

/*
 *  Mailbox
 */
$mibew_mailbox = "mibew@yourdomain.com";

/*
 *  Locales
 */
$home_locale = "en"; /* native name will be used in this locale */
$default_locale = "en"; /* if user does not provide known lang */

/*
 * Plugins
 */
$plugins_list = array();

/* Exapmle of plugins configuration
$plugins_list[] = array(
    'name' => 'plugin_name',
    'config' => array(
        'weight' => 100,
        'some_configurable_value' => 'value'
    )
)
*/
