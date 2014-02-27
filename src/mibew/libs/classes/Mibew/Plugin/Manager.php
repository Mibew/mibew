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

namespace Mibew\Plugin;

/**
 * Manage plugins
 */
class Manager
{
    /**
     * Contains all loaded plugins
     *
     * @var array
     */
    protected static $loadedPlugins = array();

    /**
     * Returns plugin object
     *
     * @param string $plugin_name
     * @return \Mibew\Plugin
     */
    public static function getPlugin($plugin_name)
    {
        if (empty(self::$loadedPlugins[$plugin_name])) {
            trigger_error(
                "Plugin '{$plugin_name}' does not initialized!",
                E_USER_WARNING
            );
        }

        return self::$loadedPlugins[$plugin_name];
    }

    /**
     * Returns associative array of loaded plugins.
     *
     * Key represents plugin's name and value contains Plugin object
     *
     * @return array
     */
    public static function getAllPlugins()
    {
        return self::$loadedPlugins;
    }

    /**
     * Loads plugins.
     *
     * The method checks dependences and plugin avaiulability before loading and
     * invokes PluginInterface::run() after loading.
     *
     * @param array $plugins_list List of plugins' names and configurations.
     *   For example:
     * <code>
     * $plugins_list = array();
     * $plugins_list[] = array(
     *   'name' => 'vendor:plugin_name',     // Obligatory value
     *   'config' => array(                  // Pass to plugin constructor
     *     'weight' => 100,
     *     'some_configurable_value' => 'value'
     *   )
     * )
     * </code>
     *
     * @see \Mibew\Plugin\PluginInterface::run()
     */
    public static function loadPlugins($plugins_list)
    {
        // Load plugins one by one
        $loading_queue = array();
        $offset = 0;
        foreach ($plugins_list as $plugin) {
            if (empty($plugin['name'])) {
                trigger_error("Plugin name is undefined!", E_USER_WARNING);
                continue;
            }
            $plugin_name = $plugin['name'];

            // Split full name to vendor name and short name
            list($vendor_name, $plugin_short_name) = explode(':', $plugin_name, 2);
            if (empty($vendor_name) || empty($plugin_short_name)) {
                trigger_error(
                    "Wrong formated plugin name '" . $plugin_name . "'!",
                    E_USER_WARNING
                );
                continue;
            }

            $plugin_config = isset($plugin['config']) ? $plugin['config'] : array();

            // Build name of the plugin class
            $plugin_name_parts = explode('_', $plugin_short_name);
            $plugin_name_parts = array_map('ucfirst', $plugin_name_parts);
            $plugin_classname = '\\' . ucfirst($vendor_name)
                . '\\Mibew\\Plugin\\' . implode('', $plugin_name_parts) . '\\Plugin';

            // Check plugin class name
            if (!class_exists($plugin_classname)) {
                trigger_error(
                    "Plugin class '{$plugin_classname}' is undefined!",
                    E_USER_WARNING
                );
                continue;
            }
            // Check if plugin extends abstract 'Plugin' class
            if (!in_array('Mibew\\Plugin\\PluginInterface', class_implements($plugin_classname))) {
                $error_message = "Plugin class '{$plugin_classname}' does not "
                    . "implement '\\Mibew\\Plugin\\PluginInterface' interface!";
                trigger_error($error_message, E_USER_WARNING);
                continue;
            }

            // Check plugin dependencies
            $plugin_dependencies = call_user_func(array(
                $plugin_classname,
                'getDependencies',
            ));
            foreach ($plugin_dependencies as $dependency) {
                if (empty(self::$loadedPlugins[$dependency])) {
                    $error_message = "Plugin '{$dependency}' was not loaded "
                        . "yet, but exists in '{$plugin_name}' dependencies list!";
                    trigger_error($error_message, E_USER_WARNING);
                    continue 2;
                }
            }

            // Add plugin to loading queue
            $plugin_instance = new $plugin_classname($plugin_config);
            if ($plugin_instance->initialized()) {
                // Store plugin instance
                self::$loadedPlugins[$plugin_name] = $plugin_instance;
                $loading_queue[$plugin_instance->getWeight() . "_" . $offset] = $plugin_instance;
                $offset++;
            } else {
                trigger_error(
                    "Plugin '{$plugin_name}' was not initialized correctly!",
                    E_USER_WARNING
                );
            }
        }
        // Sort queue in order to plugins' weights and run plugins one by one
        uksort($loading_queue, 'strnatcmp');
        foreach ($loading_queue as $plugin) {
            $plugin->run();
        }
    }
}
