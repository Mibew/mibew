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

namespace Mibew\Plugin;

/**
 * Contains a set of utility methods.
 */
class Utils
{
    /**
     * Describes a valid plugin name.
     */
    const PLUGIN_NAME_REG_EXP = "/^([A-Z][0-9A-Za-z]+):([A-Z][0-9A-Za-z]+)$/";

    /**
     * Gets list of plugins existing in File System.
     *
     * @return array List of existing plugins. Each item is a full plugin name
     *   in "Vendor:Name" format.
     */
    public static function discoverPlugins()
    {
        static $plugins = null;

        if (is_null($plugins)) {
            $plugins = array();

            $pattern = MIBEW_FS_ROOT . str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                '/plugins/*/Mibew/Plugin/*/Plugin.php'
            );

            $plugin_files = glob($pattern);
            if ($plugin_files === false) {
                // TODO: Maybe one should raise some exception here
                return $plugins;
            }
            foreach ($plugin_files as $plugin_file) {
                // Build plugin's name and make sure the plugin exists.
                $parts = array_reverse(explode(DIRECTORY_SEPARATOR, $plugin_file));
                $plugin_name = $parts[4] . ':' . $parts[1];
                if (!self::pluginExists($plugin_name)) {
                    continue;
                }
                $plugins[] = $plugin_name;
            }
        }

        return $plugins;
    }

    /**
     * Checks if the specified name is a valid plugin name.
     *
     * @param string $name A string to check.
     * @return boolean
     */
    public static function isValidPluginName($name)
    {
        return (preg_match(self::PLUGIN_NAME_REG_EXP, $name) != 0);
    }

    /**
     * Builds class name for a plugin with the specified name.
     *
     * @param string $plugin_name Plugin's name in "Vendor:Name" format.
     * @return string Fully qualified class name for the plugin.
     * @throws \InvalidArgumentException If the passed in plugin name is
     *   invalid.
     */
    public static function getPluginClassName($plugin_name)
    {
        if (!self::isValidPluginName($plugin_name)) {
            throw new \InvalidArgumentException('Wrong formatted plugin name');
        }
        list($vendor, $short_name) = explode(':', $plugin_name, 2);

        return '\\' . $vendor . '\\Mibew\\Plugin\\' . $short_name . '\\Plugin';
    }

    /**
     * Checks if a plugin with specified name exists.
     *
     * @param string $plugin_name Name of the plugin to check.
     * @return boolean True if the plugin exists and false otherwise.
     */
    public static function pluginExists($plugin_name)
    {
        if (!self::isValidPluginName($plugin_name)) {
            return false;
        }

        // Make sure plugin class exists and represents a real plugin.
        $class_name = self::getPluginClassName($plugin_name);
        if (!class_exists($class_name)) {
            return false;
        }
        if (!in_array('Mibew\\Plugin\\PluginInterface', class_implements($class_name))) {
            return false;
        }

        return true;
    }

    /**
     * Returns list of environment items.
     *
     * @return array List of environment items. Each key is a string with one of
     * the following value:
     *  - "mibew": represents Mibew Messenger Core;
     *  - "php": represents PHP installed in the system;
     *  - "ext-*": represents one of PHP extensions.
     * Each value of the array is version of the item.
     */
    public static function getSystemInfo()
    {
        static $system = null;

        if (is_null($system)) {
            $system = array(
                'php' => phpversion(),
                'mibew' => MIBEW_VERSION,
            );

            foreach (get_loaded_extensions() as $ext) {
                $system['ext-' . str_replace(' ', '-', $ext)] = phpversion($ext) ?: '0.0.0';
            }
        }

        return $system;
    }

    /**
     * This class should not be instantiated
     */
    private function __construct()
    {
    }
}
