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

use Mibew\Cache\CacheAwareInterface;
use Mibew\Plugin\Utils as PluginUtils;
use Mibew\Maintenance\Utils as MaintenanceUtils;
use Stash\Interfaces\PoolInterface;

/**
 * Manage plugins.
 *
 * Implements singleton pattern.
 */
class PluginManager implements
    CacheAwareInterface
{
    /**
     * An instance of Plugin Manager class.
     * @var PluginManager
     */
    protected static $instance = null;

    /**
     * @var PoolInterface|null
     */
    protected $cache = null;

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
     * {@inheritdoc}
     */
    public function setCache(PoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getCache()
    {
        return $this->cache;
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
     * Checks if there is an instance of the plugin in the manager.
     *
     * @param string $plugin_name Name of the plugin to check
     * @return boolean True if the plugin exists in the manager and false
     *   otherwise.
     */
    public function hasPlugin($plugin_name)
    {
        return isset($this->loadedPlugins[$plugin_name]);
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
     * The method checks dependences and plugin availability before loading and
     * invokes PluginInterface::run() after loading.
     *
     * @param array $configs List of plugins' configurations. Each key is a
     * plugin name and each value is a configurations array.
     *
     * @see \Mibew\Plugin\PluginInterface::run()
     */
    public function loadPlugins($configs)
    {
        // Builds Dependency graph with available plugins.
        $graph = new DependencyGraph();
        foreach (State::loadAllEnabled() as $plugin_state) {
            if (!PluginUtils::pluginExists($plugin_state->pluginName)) {
                trigger_error(
                    sprintf(
                        'Plugin "%s" exists in database base but is not found in file system!',
                        $plugin_state->pluginName
                    ),
                    E_USER_WARNING
                );
                continue;
            }

            $plugin_info = PluginInfo::fromState($plugin_state);
            if ($plugin_info->needsUpdate()) {
                trigger_error(
                    sprintf(
                        'Update "%s" plugin before use it!',
                        $plugin_info->getName()
                    ),
                    E_USER_WARNING
                );
                continue;
            }

            if ($plugin_info->hasUnsatisfiedSystemRequirements()) {
                trigger_error(
                    sprintf(
                        'Plugin "%s" has unsatisfied system requirements!',
                        $plugin_info->getName()
                    ),
                    E_USER_WARNING
                );
                continue;
            }

            $graph->addPlugin($plugin_info);
        }

        $offset = 0;
        $running_queue = array();
        foreach ($graph->getLoadingQueue() as $plugin_info) {
            // Make sure all depedendencies are loaded
            foreach (array_keys($plugin_info->getDependencies()) as $dependency) {
                if (!isset($this->loadedPlugins[$dependency])) {
                    trigger_error(
                        sprintf(
                            'Plugin "%s" was not loaded yet, but exists in "%s" dependencies list!',
                            $dependency,
                            $plugin_info->getName()
                        ),
                        E_USER_WARNING
                    );
                    continue 2;
                }
            }

            // Try to load the plugin.
            $name = $plugin_info->getName();
            $config = isset($configs[$name]) ? $configs[$name] : array();
            $instance = $plugin_info->getInstance($config);
            if ($instance->initialized()) {
                // Store the plugin and add it to running queue
                $this->loadedPlugins[$name] = $instance;
                $running_queue[$instance->getWeight() . "_" . $offset] = $instance;
                $offset++;
                if (!$plugin_info->isInitialized()) {
                    $plugin_info->getState()->initialized = true;
                    $plugin_info->getState()->save();
                    // Plugins can have own routing files and when the plugin becomes
                    // initialized after non-initialized state its routes should be
                    // reset. So the cache is cleared to make sure the routes set is up to date.
                    $this->getCache()->getItem('routing/resources')->clear();
                }
            } else {
                // The plugin cannot be loaded. Just skip it.
                trigger_error(
                    "Plugin '{$name}' was not initialized correctly!",
                    E_USER_WARNING
                );
                if ($plugin_info->isInitialized()) {
                    $plugin_info->getState()->initialized = false;
                    $plugin_info->getState()->save();
                    // Plugins can have own routing files and when the plugin becomes
                    // non-initialized after initialized state its routes should be
                    // removed. So the cache is cleared to make sure the routes set is up to date.
                    $this->getCache()->getItem('routing/resources')->clear();
                }
            }
        }

        // Sort queue in order to plugins' weights and run plugins one by one
        uksort($running_queue, 'strnatcmp');
        foreach ($running_queue as $plugin) {
            $plugin->run();
        }
    }

    /**
     * Tries to enable a plugin.
     *
     * @param string $plugin_name Name of the plugin to enable.
     * @return boolean Indicates if the plugin has been enabled or not.
     */
    public function enable($plugin_name)
    {
        $plugin = new PluginInfo($plugin_name);

        if ($plugin->isEnabled()) {
            // The plugin is already enabled. There is nothing we can do.
            return true;
        }

        if (!$plugin->canBeEnabled()) {
            // The plugin cannot be enabled.
            return false;
        }

        if (!$plugin->isInstalled()) {
            // Try to install the plugin.
            $plugin_class = $plugin->getClass();
            if (!$plugin_class::install()) {
                return false;
            }

            // Plugin installed successfully. Update the state
            $plugin->getState()->version = $plugin->getVersion();
            $plugin->getState()->installed = true;
        }

        $plugin->getState()->enabled = true;
        $plugin->getState()->save();

        return true;
    }

    /**
     * Tries to disable a plugin.
     *
     * @param string $plugin_name Name of the plugin to disable.
     * @return boolean Indicates if the plugin has been disabled or not.
     */
    public function disable($plugin_name)
    {
        $plugin = new PluginInfo($plugin_name);

        if (!$plugin->isEnabled()) {
            // The plugin is not enabled
            return true;
        }

        if (!$plugin->canBeDisabled()) {
            // The plugin cannot be disabled
            return false;
        }

        $plugin->getState()->enabled = false;
        $plugin->getState()->initialized = false;
        $plugin->getState()->save();

        return true;
    }

    /**
     * Tries to uninstall a plugin.
     *
     * @param string $plugin_name Name of the plugin to uninstall.
     * @return boolean Indicates if the plugin has been uninstalled or not.
     */
    public function uninstall($plugin_name)
    {
        $plugin = new PluginInfo($plugin_name);

        if (!$plugin->isInstalled()) {
            // The plugin was not installed
            return true;
        }

        if (!$plugin->canBeUninstalled()) {
            // The plugin cannot be uninstalled.
            return false;
        }

        // Try to uninstall the plugin.
        $plugin_class = $plugin->getClass();
        if (!$plugin_class::uninstall()) {
            // Something went wrong. The plugin cannot be uninstalled.
            return false;
        }

        // The plugin state is not needed anymore.
        $plugin->clearState();

        return true;
    }

    /**
     * Tries to update a plugin.
     *
     * @param string $plugin_name Name of the plugin to update.
     * @return boolean Indicates if the plugin has been updated or not.
     */
    public function update($plugin_name)
    {
        $plugin = new PluginInfo($plugin_name);

        if (!$plugin->needsUpdate()) {
            // There is no need to update the plugin
            return true;
        }

        if (!$plugin->canBeUpdated()) {
            // The plugin cannot be updated.
            return false;
        }

        try {
            // Perform incremental updates
            $updates = MaintenanceUtils::getUpdates($plugin->getClass());
            foreach ($updates as $version => $method) {
                // Skip updates to lower versions.
                if (version_compare($version, $plugin->getInstalledVersion()) <= 0) {
                    continue;
                }

                // Skip updates to versions that greater then the current plugin
                // version.
                if (version_compare($version, $plugin->getVersion()) > 0) {
                    break;
                }

                // Run the update
                if (!$method()) {
                    // By some reasons we cannot update to the next version.
                    // Stop the process here.
                    return false;
                }

                // Store new version number in the database. With this info
                // we can rerun the updating process if one of pending
                // updates fails.
                $plugin->getState()->version = $version;
                $plugin->getState()->save();
            }
        } catch (\Exception $e) {
            // Something went wrong
            trigger_error(
                sprintf(
                    'Update of "%s" plugin failed: %s',
                    $plugin->getName(),
                    $e->getMessage()
                ),
                E_USER_WARNING
            );

            return false;
        }

        // All updates are done. Make sure the state of the plugin contains
        // current plugin version.
        $plugin->getState()->version = $plugin->getVersion();
        $plugin->getState()->save();

        return true;
    }
}
