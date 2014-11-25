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
}
