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

use vierbergenlars\SemVer\version as Version;
use vierbergenlars\SemVer\expression as VersionExpression;

/**
 * Manage plugins.
 *
 * Implements singleton pattern.
 */
class PluginManager
{
    /**
     * An instance of Plugin Manager class.
     * @var PluginManager
     */
    protected static $instance = null;

    /**
     * Contains all loaded plugins
     *
     * @var array
     */
    protected $loadedPlugins = array();

    /**
     * Get instance of PluginManager class.
     *
     * @return PluginManager
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Returns plugin instance.
     *
     * @param string $plugin_name Name of the plugin to retrieve.
     * @return \Mibew\Plugin\PluginInterface|boolean Instance of the plugin or
     *   boolean false if there is no plugin with such name.
     */
    public function getPlugin($plugin_name)
    {
        if (empty($this->loadedPlugins[$plugin_name])) {
            trigger_error(
                "Plugin '{$plugin_name}' does not initialized!",
                E_USER_WARNING
            );

            return false;
        }

        return $this->loadedPlugins[$plugin_name];
    }

    /**
     * Returns associative array of loaded plugins.
     *
     * Key represents plugin's name and value contains Plugin object
     *
     * @return array
     */
    public function getAllPlugins()
    {
        return $this->loadedPlugins;
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
    public function loadPlugins($plugins_list)
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

            // Get vendor name and short name from plugin's name
            if (!Utils::isValidPluginName($plugin_name)) {
                trigger_error(
                    "Wrong formated plugin name '" . $plugin_name . "'!",
                    E_USER_WARNING
                );
                continue;
            }
            list($vendor_name, $plugin_short_name) = explode(':', $plugin_name, 2);

            $plugin_config = isset($plugin['config']) ? $plugin['config'] : array();

            // Build name of the plugin class
            $plugin_classname = '\\' . $vendor_name
                . '\\Mibew\\Plugin\\' . $plugin_short_name . '\\Plugin';

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
            foreach ($plugin_dependencies as $dependency => $required_version) {
                if (empty($this->loadedPlugins[$dependency])) {
                    $error_message = "Plugin '{$dependency}' was not loaded "
                        . "yet, but exists in '{$plugin_name}' dependencies list!";
                    trigger_error($error_message, E_USER_WARNING);
                    continue 2;
                }

                $version_constrain = new VersionExpression($required_version);
                $dependency_version = call_user_func(array(
                    $this->loadedPlugins[$dependency],
                    'getVersion'
                ));

                if (!$version_constrain->satisfiedBy(new Version($dependency_version))) {
                    $error_message = "Plugin '{$dependency}' has version "
                        . "incompatible with '{$plugin_name}' requirements!";
                    trigger_error($error_message, E_USER_WARNING);
                    continue 2;
                }
            }

            // Add plugin to loading queue
            $plugin_instance = new $plugin_classname($plugin_config);
            if ($plugin_instance->initialized()) {
                // Store plugin instance
                $this->loadedPlugins[$plugin_name] = $plugin_instance;
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
