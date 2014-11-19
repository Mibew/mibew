<?php
/*
 * This file is a part of Mibew Messenger.
 *
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

namespace Mibew\Plugin;

/**
 * Contains a set of utility methods.
 */
class Utils
{
    /**
     * Gets list of plugins existing in File System.
     *
     * @return array List of existing plugins. Each item is a full plugin name
     *   in "Vendor:Name" format.
     */
    public static function discoverPlugins()
    {
        $pattern = MIBEW_FS_ROOT . str_replace(
            '/',
            DIRECTORY_SEPARATOR,
            '/plugins/*/Mibew/Plugin/*/Plugin.php'
        );

        $plugins = array();
        foreach (glob($pattern) as $plugin_file) {
            $parts = array_reverse(explode(DIRECTORY_SEPARATOR, $plugin_file));
            $plugin = $parts[1];
            $vendor = $parts[4];

            $class_name = '\\' . $vendor . '\\Mibew\\Plugin\\' . $plugin . '\\Plugin';

            // Check plugin class name
            if (!class_exists($class_name)) {
                continue;
            }
            // Check if plugin implements 'Plugin' interface
            if (!in_array('Mibew\\Plugin\\PluginInterface', class_implements($class_name))) {
                continue;
            }

            $plugins[] = $vendor . ':' . $plugin;
        }

        return $plugins;
    }

    /**
     * This class should not be instantiated
     */
    private function __construct()
    {
    }
}
