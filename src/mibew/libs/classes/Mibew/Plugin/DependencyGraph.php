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
 * Represents a dependency graph.
 *
 * The main aim of the class is to validate dependencies and build loading queue
 * based on them.
 */
class DependencyGraph
{
    /**
     * Indicates that a plugin was not visited by depth-first search algorithm.
     */
    const DFS_NOT_VISITED = 'not_visited';

    /**
     * Indicates that depth-first search algorithm is processing the plugin.
     */
    const DFS_IN_PROGRESS = 'in_progress';

    /**
     * Indicates that a plugin was visited by depth-first search algorithm.
     */
    const DFS_VISITED = 'visited';

    /**
     * List of all plugins attached to the graph.
     * @var PluginInfo[]
     */
    protected $plugins = array();

    /**
     * Contains plugins states related with depth-first search algorithm.
     *
     * Each key of the array is plugin name and each value is one of
     * DependencyGraph::DFS_* constants.
     *
     * @var array
     */
    protected $dfsState = array();

    /**
     * Plugins loading queue.
     * @var PluginInfo[]
     */
    private $loadingQueue = array();

    /**
     * List of plugins with fully satisfied dependencies.
     * @var PluginInfo[]
     */
    private $loadablePlugins = array();

    /**
     * Class constructor.
     *
     * @param PluginInfo[]|null $plugins List of plugins that should be added to
     *   the graph.
     * @throws \InvalidArgumentException
     */
    public function __construct($plugins = null)
    {
        if (!is_null($plugins)) {
            if (!is_array($plugins)) {
                throw new \InvalidArgumentException('The first argument must be an array or null');
            }
            foreach ($plugins as $plugin) {
                $this->addPlugin($plugin);
            }
        }
    }

    /**
     * Adds a plugin to the graph.
     *
     * Notice that this method accepts an instance of
     * {@link \Mibew\Plugin\PluginInfo} class. It's done intentionally because
     * we do not need an instance of {@link \Mibew\Plugin\PluginInterface} to
     * analize its dependencies.
     *
     * @param PluginInfo $plugin A plugin that should be added.
     */
    public function addPlugin(PluginInfo $plugin)
    {
        $this->plugins[$plugin->getName()] = $plugin;
    }

    /**
     * Checks if the plugin with specified name was attached to the graph.
     *
     * @param string $name Name of the plugin.
     * @return boolean
     */
    public function hasPlugin($name)
    {
        return isset($this->plugins[$name]);
    }

    /**
     * Remove the plugin from the graph.
     *
     * @param string $name Name of the plugin.
     * @throws \RuntimeException If the plugin with such name was not attached
     *   to the graph.
     */
    public function removePlugin($name)
    {
        if (!isset($this->plugins[$name])) {
            throw new \RuntimeException(sprintf(
                'There is no "%s" plugin in dependency graph',
                $name
            ));
        }

        unset($this->plugins[$name]);
    }

    /**
     * Gets a plugin attached to the graph.
     *
     * Notice that this method accepts an instance of
     * {@link \Mibew\Plugin\PluginInfo} class. It's done intentionally because
     * we do not need an instance of {@link \Mibew\Plugin\PluginInterface} to
     * analize its dependencies.
     *
     * @param string $name Name of the plugin.
     * @return PluginInfo
     * @throws \RuntimeException If the plugin with such name was not attached
     *   to the graph.
     */
    public function getPlugin($name)
    {
        if (!isset($this->plugins[$name])) {
            throw new \RuntimeException(sprintf(
                'There is no "%s" plugin in dependency graph',
                $name
            ));
        }

        return $this->plugins[$name];
    }

    /**
     * Builds plugins loading queue.
     *
     * The method filters plugins that cannot be loaded because of unsatisfied
     * dependencies and sorts the others by loading turn.
     *
     * Together with {@link \Mibew\Plugin\DependencyGraph::doSortStep()} method
     * it implements topological sorting algorithm. The only deference from the
     * topological sorting the results are in reverse order.
     *
     * @return PluginInfo[]
     */
    public function getLoadingQueue()
    {
        $this->loadingQueue = array();

        $this->clearDfsState();
        foreach ($this->getLoadablePlugins() as $plugin) {
            if ($this->getDfsState($plugin) != self::DFS_VISITED) {
                $this->doSortStep($plugin);
            }
        }
        $this->clearDfsState();

        return $this->loadingQueue;
    }

    /**
     * Performs a step of sorting algorithm.
     *
     * Actually this method represents a step of depth-first serach algorithm
     * with some additions related with sorting.
     *
     * @param PluginInfo $plugin A plugin that should be sorted.
     * @throws \LogicException If cyclic dependencies are found.
     */
    protected function doSortStep(PluginInfo $plugin)
    {
        if ($this->getDfsState($plugin) == self::DFS_IN_PROGRESS) {
            throw new \LogicException(sprintf(
                'Cyclic dependencies found for plugin "%s"',
                $plugin->getName()
            ));
        }

        $this->setDfsState($plugin, self::DFS_IN_PROGRESS);
        foreach (array_keys($plugin->getDependencies()) as $dependency_name) {
            $dependency = $this->getPlugin($dependency_name);
            if ($this->getDfsState($dependency) != self::DFS_VISITED) {
                $this->doSortStep($dependency);
            }
        }
        $this->setDfsState($plugin, self::DFS_VISITED);
        $this->loadingQueue[] = $plugin;
    }

    /**
     * Returns a list of plugins that can be loaded.
     *
     * This method together with
     * {@link \Mibew\Plugin\DependencyGraph::doVerificationStep()} method
     * implements depth-first search algorithm to filter plugins with
     * unsatisfied dependencies.
     *
     * @return PluginInfo[]
     */
    protected function getLoadablePlugins()
    {
        $this->loadablePlugins = array();

        $this->clearDfsState();
        foreach ($this->plugins as $plugin) {
            if ($this->getDfsState($plugin) != self::DFS_VISITED) {
                $this->doVerificationStep($plugin);
            }
        }
        $this->clearDfsState();

        return array_values($this->loadablePlugins);
    }

    /**
     * Performs a step of plugin's verification algorithm.
     *
     * Actually this method represents a step of depth-first search algorithm
     * with additions related with plugin's verification.
     *
     * @param PluginInfo $plugin A plugin that should be verified.
     * @throws \LogicException If cyclic dependencies are found.
     */
    protected function doVerificationStep(PluginInfo $plugin)
    {
        if ($this->getDfsState($plugin) == self::DFS_IN_PROGRESS) {
            throw new \LogicException(sprintf(
                'Cyclic dependencies found for plugin "%s"',
                $plugin->getName()
            ));
        }

        $this->setDfsState($plugin, self::DFS_IN_PROGRESS);
        $can_be_loaded = true;
        foreach ($plugin->getDependencies() as $dependency_name => $required_version) {
            // Make sure the dependency exist
            if (!$this->hasPlugin($dependency_name)) {
                trigger_error(
                    sprintf(
                        'Plugin "%s" does not exist but is listed in "%s" dependencies!',
                        $dependency_name,
                        $plugin->getName()
                    ),
                    E_USER_WARNING
                );
                $can_be_loaded = false;
                break;
            }

            // Check that version of the dependency satisfied requirements
            $version_constrain = new VersionExpression($required_version);
            $dependency = $this->getPlugin($dependency_name);
            if (!$version_constrain->satisfiedBy(new Version($dependency->getInstalledVersion()))) {
                trigger_error(
                    sprintf(
                        'Plugin "%s" has version incompatible with "%s" requirements!',
                        $dependency_name,
                        $plugin->getName()
                    ),
                    E_USER_WARNING
                );
                $can_be_loaded = false;
                break;
            }

            // Check that dependencies of the current dependency are satisfied
            if ($this->getDfsState($dependency) != self::DFS_VISITED) {
                $this->doVerificationStep($dependency);
            }
            if (!isset($this->loadablePlugins[$dependency_name])) {
                trigger_error(
                    sprintf(
                        'Not all dependencies of "%s" plugin are satisfied!',
                        $plugin->getName()
                    ),
                    E_USER_WARNING
                );
                $can_be_loaded = false;
                break;
            }
        }
        $this->setDfsState($plugin, self::DFS_VISITED);

        if ($can_be_loaded) {
            $this->loadablePlugins[$plugin->getName()] = $plugin;
        }
    }

    /**
     * Clear state related with depth-first search algorithm for all plugins.
     */
    protected function clearDfsState()
    {
        $this->dfsState = array();
    }

    /**
     * Get state related with depth-first search algorithm for a plugin.
     *
     * @param PluginInfo $plugin A plugin for which the state should be
     *   retrieved.
     * @return string One of DependencyGraph::DFS_* constant.
     */
    protected function getDfsState(PluginInfo $plugin)
    {
        return isset($this->dfsState[$plugin->getName()])
            ? $this->dfsState[$plugin->getName()]
            : self::DFS_NOT_VISITED;
    }

    /**
     * Sets state related with depth-first search algorithm for a plugin.
     *
     * @param PluginInfo $plugin A plugin for which the state should be set.
     * @param string $state One of DependencyGraph::DFS_* constants.
     */
    protected function setDfsState(PluginInfo $plugin, $state)
    {
        $this->dfsState[$plugin->getName()] = $state;
    }
}
