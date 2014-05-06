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

namespace Mibew\Routing;

use Mibew\Plugin\Manager as PluginManager;
use Mibew\EventDispatcher;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\FileLocatorInterface;

/**
 * Encapsulates routes loading logic.
 */
class RouteCollectionLoader
{
    /**
     * Indicates that only routes of the core should be loaded.
     */
    const ROUTES_CORE = 1;

    /**
     * Indicates that only plugins' routes should be loaded.
     */
    const ROUTES_PLUGINS = 2;

    /**
     * Indicates that all available routes should be loaded.
     */
    const ROUTES_ALL = 3;

    /**
     * @var YamlFileLoader|null
     */
    protected $loader = null;

    /**
     * Class constructor.
     */
    public function __construct(FileLocatorInterface $locator)
    {
        $this->loader = new YamlFileLoader($locator);
    }

    /**
     * Load routes of specified type.
     *
     * @param int $type Type of routes to load. Can be one of
     * RouteCollectionLoader::ROUTES_* constants.
     * @return RouteCollection
     */
    public function load($type = self::ROUTES_ALL)
    {
        $collection = new RouteCollection();

        // Load core routes if needed
        if ($type & self::ROUTES_CORE) {
            $collection->addCollection($this->loadCoreRoutes());
        }

        // Load plugins routes if needed
        if ($type & self::ROUTES_PLUGINS) {
            $collection->addCollection($this->loadPluginRoutes());
        }

        // Add an ability for plugins to alter routes list
        $arguments = array('routes' => $collection);
        EventDispatcher::getInstance()->triggerEvent('routesAlter', $arguments);

        return $arguments['routes'];
    }

    /**
     * Loads routes of the core.
     *
     * @return RouteCollection
     * @throws \RuntimeException If core routing file is not found.
     */
    protected function loadCoreRoutes()
    {
        return $this->loader->load('libs/routing.yml');
    }

    /**
     * Loads plugins' routes.
     *
     * @return RouteCollection
     */
    protected function loadPluginRoutes()
    {
        $collection = new RouteCollection();
        foreach (PluginManager::getAllPlugins() as $plugin) {
            $file = $plugin->getFilesPath() . '/routing.yml';
            if (!file_exists($file)) {
                continue;
            }
            $collection->addCollection($this->loader->load($file));
        }

        return $collection;
    }
}
