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
 * Loads system configurations.
 *
 * The configs are cached inside the function.
 *
 * @return array Associative array of system configs.
 */
function load_system_configs()
{
    static $configs = null;

    if (is_null($configs)) {
        // Load and "parse" configs file. While configs are written in a php
        // file include is the only option to load and parse them.
        include(MIBEW_FS_ROOT . "/libs/config.php");

        $configs = array(
            'mibew_root' => $mibewroot,
            'database' => array(
                'host' => $mysqlhost,
                'db' => $mysqldb,
                'login' => $mysqllogin,
                'pass' => $mysqlpass,
                'tables_prefix' => $mysqlprefix,
                'use_persistent_connection' => $use_persistent_connection,
            ),
            'mailbox' => $mibew_mailbox,
            'home_locale' => $home_locale,
            'default_locale' => $default_locale,
            'plugins' => $plugins_list,
        );
    }

    return $configs;
}
