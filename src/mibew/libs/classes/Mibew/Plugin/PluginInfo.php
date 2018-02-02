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

use vierbergenlars\SemVer\version as Version;
use vierbergenlars\SemVer\expression as VersionExpression;

/**
 * Provides a handy wrapper for plugin info.
 */
class PluginInfo
{
    /**
     * Name of the plugin.
     * @var string
     */
    protected $pluginName;

    /**
     * Name of the plugin's class.
     * @var string|null
     */
    protected $pluginClass = null;

    /**
     * The current state of the plugin.
     * @var State|null
     */
    protected $pluginState = null;

    /**
     * Class constructor.
     *
     * @param string $plugin_name Name of the plguin.
     * @throws \InvalidArgumentException If the plugin name isn't correct.
     * @throws \RuntimeException If the plugin does not exist.
     */
    public function __construct($plugin_name)
    {
        if (!Utils::isValidPluginName($plugin_name)) {
            throw new \InvalidArgumentException('Wrong plugin name');
        }

        if (!Utils::pluginExists($plugin_name)) {
            throw new \RuntimeException('Plugin is not found');
        }

        $this->pluginName = $plugin_name;
    }

    /**
     * Returns current state of the plugin.
     *
     * @return State
     */
    public function getState()
    {
        if (is_null($this->pluginState)) {
            $state = State::loadByName($this->pluginName);
            if (!$state) {
                // There is no appropriate state in the database. Use a new one.
                $state = new State();
                $state->pluginName = $this->pluginName;
                $state->version = false;
                $state->installed = false;
                $state->enabled = false;
                $state->initialized = false;
            }
            $this->pluginState = $state;
        }

        return $this->pluginState;
    }

    /**
     * Clears state of the plugin attached to the info object.
     *
     * Also the method deletes state from database but only if it's stored
     * where.
     */
    public function clearState()
    {
        if (!is_null($this->pluginState)) {
            if ($this->pluginState->id) {
                // Remove state only if it's in the database.
                $this->pluginState->delete();
            }
            $this->pluginState = null;
        }
    }

    /**
     * Returns fully qualified plugin's class.
     *
     * @return string
     */
    public function getClass()
    {
        if (is_null($this->pluginClass)) {
            $this->pluginClass = Utils::getPluginClassName($this->pluginName);
        }

        return $this->pluginClass;
    }

    /**
     * Returns name of the plugin.
     *
     * @return string
     */
    public function getName()
    {
        return $this->pluginName;
    }

    /**
     * Returns current version of the plugin.
     *
     * @return string
     */
    public function getVersion()
    {
        return call_user_func(array($this->getClass(), 'getVersion'));
    }

    /**
     * Returns installed version of the plugin.
     *
     * Notice that in can differs from
     * {@link \Mibew\Plugin\PluginInfo::getVersion()} results if the plugin's
     * files are updated without database changes.
     *
     * @return string
     */
    public function getInstalledVersion()
    {
        return $this->getState()->version;
    }

    /**
     * Returns dependencies of the plugin.
     *
     * @return array Dependencies list. See
     *   {@link \Mibew\Plugin\PluginInterface::getDependencies()} for details of
     *   the array structure.
     */
    public function getDependencies()
    {
        return call_user_func(array($this->getClass(), 'getDependencies'));
    }

    /**
     * Returns system requirements of the plugin.
     *
     * @return array Requirements list. See
     *   {@link \Mibew\Plugin\PluginInterface::getSystemRequirements()} for
     *   details of the array structure.
     */
    public function getSystemRequirements()
    {
        return call_user_func(array($this->getClass(), 'getSystemRequirements'));
    }

    /**
     * Returns list of dependent plugins.
     *
     * @return array List of plugins names.
     */
    public function getDependentPlugins()
    {
        $dependent_plugins = array();
        foreach (Utils::discoverPlugins() as $plugin_name) {
            $plugin = new PluginInfo($plugin_name);
            if (array_key_exists($this->getName(), $plugin->getDependencies())) {
                $dependent_plugins[] = $plugin_name;
            }
        }

        return $dependent_plugins;
    }

    /**
     * Creates an instance of the plugin.
     *
     * @param array $configs Configurations array that will be passed to
     *   plugin's constructor.
     * @return \Mibew\Plugin\PluginInterface
     */
    public function getInstance($configs = array())
    {
        $plugin_class = $this->getClass();

        return new $plugin_class($configs);
    }

    /**
     * Checks if the plugin is initialized.
     *
     * @return bool
     */
    public function isInitialized()
    {
        return $this->getState()->initialized;
    }

    /**
     * Checks if the plugin is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getState()->enabled;
    }

    /**
     * Checks if the plugin is installed.
     *
     * @return bool
     */
    public function isInstalled()
    {
        return $this->getState()->installed;
    }

    /**
     * Checks if the plugin needs to be updated.
     *
     * @return bool
     */
    public function needsUpdate()
    {
        return $this->isInstalled()
            && (version_compare($this->getVersion(), $this->getInstalledVersion()) > 0);
    }

    /**
     * Checks if the plugin has unsatisfied system requirements.
     *
     * @return bool
     */
    public function hasUnsatisfiedSystemRequirements()
    {
        $system_info = Utils::getSystemInfo();

        foreach ($this->getSystemRequirements() as $lib => $required_version) {
            // Check if the library exists
            if (!isset($system_info[$lib])) {
                return true;
            }

            // Check exact version of the library
            $version_constrain = new VersionExpression($required_version);
            if (!$version_constrain->satisfiedBy(new Version($system_info[$lib]))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the plugin can be enabled.
     *
     * @return boolean
     */
    public function canBeEnabled()
    {
        if ($this->isEnabled()) {
            // The plugin cannot be enabled twice
            return false;
        }

        if ($this->hasUnsatisfiedSystemRequirements()) {
            return false;
        }

        // Make sure all plugin's dependencies exist, are enabled and have
        // appropriate versions
        foreach ($this->getDependencies() as $plugin_name => $required_version) {
            if (!Utils::pluginExists($plugin_name)) {
                return false;
            }
            $plugin = new PluginInfo($plugin_name);
            if (!$plugin->isInstalled() || !$plugin->isEnabled()) {
                return false;
            }
            $version_constrain = new VersionExpression($required_version);
            if (!$version_constrain->satisfiedBy(new Version($plugin->getInstalledVersion()))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if the plugin can be disabled.
     *
     * @return boolean
     */
    public function canBeDisabled()
    {
        if (!$this->isEnabled()) {
            // The plugin was not enabled thus it cannot be disabled
            return false;
        }

        // Make sure that the plugin has no enabled dependent plugins.
        foreach ($this->getDependentPlugins() as $plugin_name) {
            $plugin = new PluginInfo($plugin_name);
            if ($plugin->isEnabled()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if the plugin can be uninstalled.
     *
     * @return boolean
     */
    public function canBeUninstalled()
    {
        if ($this->isEnabled()) {
            // Enabled plugin cannot be uninstalled
            return false;
        }

        // Make sure that the plugin has no installed dependent plugins.
        foreach ($this->getDependentPlugins() as $plugin_name) {
            $plugin = new PluginInfo($plugin_name);
            if ($plugin->isInstalled()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if the plugin can be updated.
     *
     * @return boolean
     */
    public function canBeUpdated()
    {
        if (!$this->needsUpdate()) {
            return false;
        }

        foreach (array_keys($this->getDependencies()) as $dependency_name) {
            $dependency = new PluginInfo($dependency_name);
            if ($dependency->needsUpdate()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Creates plugin info object based on a state object.
     *
     * @param State $state A state of the plugin.
     * @return PluginInfo
     */
    public static function fromState(State $state)
    {
        $info = new self($state->pluginName);
        $info->pluginState = $state;

        return $info;
    }
}
