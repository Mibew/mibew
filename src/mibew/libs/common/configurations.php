<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2018 the original author or authors.
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

use Symfony\Component\Yaml\Parser as YamlParser;

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
        // Make sure that configuration file exists, otherwise initialize empty config
        if (file_exists(MIBEW_FS_ROOT . '/configs/config.yml')) {
            $parser = new YamlParser();
            $configs = $parser->parse(file_get_contents(MIBEW_FS_ROOT . '/configs/config.yml'));
        } else {
            $configs = array();
        }

        // Mailer configs are not necessary and can be omitted but the section
        // should exists anyway. Empty statement is used to make sure null, false
        // and "" will be converted to an empty array.
        if (empty($configs['mailer'])) {
            $configs['mailer'] = array();
        }

        // Cache section should exists too. The logic behind "empty" statement is
        // the same as above.
        if (empty($configs['cache'])) {
            $configs['cache'] = array();
        }

        // Plugins section should exists too. The logic behind "empty" statement is
        // the same as above.
        if (empty($configs['plugins'])) {
            $configs['plugins'] = array();
        }

        // Trusted proxies section should exists too. The logic behind "empty" statement is
        // the same as above.
        if (empty($configs['trusted_proxies'])) {
            $configs['trusted_proxies'] = array();
        }

        // Database section should exists too. Also it should have an appropriate structure.
        if (empty($configs['database'])) {
            $configs['database'] = array();
        }
        foreach (array('host', 'port', 'db', 'login', 'pass', 'tables_prefix', 'use_persistent_connection') as $key) {
            if (!array_key_exists($key, $configs['database'])) {
                $configs['database'][$key] = '';
            }
        }

        // Mailbox value should exists.
        if (!array_key_exists('mailbox', $configs)) {
            $configs['mailbox'] = '';
        }
    }

    return $configs;
}
