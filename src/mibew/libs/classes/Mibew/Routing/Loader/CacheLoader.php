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

namespace Mibew\Routing\Loader;

use Stash\Interfaces\PoolInterface;
use Stash\Invalidation;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

/**
 * Loads routes from the cache.
 */
class CacheLoader extends Loader
{
    /**
     * @var PoolInterface
     */
    protected $cache;

    /**
     * Maximum time in seconds the cache can be stored.
     *
     * @var int
     */
    protected $cacheMaxAge;

    /**
     * Constructor of the class.
     *
     * @param PoolInterface $cache An instance of cache pool.
     * @param int|null $max_age Maximum time in seconds the cache can be stored.
     * If the null is passed in the cache will never expired but can be
     * invalidated by some other resons.
     */
    public function __construct(PoolInterface $cache, $max_age = null)
    {
        $this->cache = $cache;
        $this->cacheMaxAge = $max_age;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (!is_string($resource)) {
            // Resources which names are not strings cannot be cached.
            return $this->import($resource, $type);
        }

        $key = sprintf('routing/resources/%s/%s/', ($type ?: '-'), $resource);
        $item = $this->cache->getItem($key);
        $data = $item->get(Invalidation::VALUE, null);

        if ($item->isMiss()) {
            // There is no value in the cache. We should read the target file
            // and cache the result.
            $item->lock();
            $collection = $this->import($resource, $type);

            // Store routes and some extra info that needed for cache
            // invalidation.
            $item->set(
                array(
                    'collection' => $this->serializeCollection($collection),
                    // We need current timestamp to invalidate resources
                    // manually if they are changed.
                    'created' => time(),
                ),
                $this->cacheMaxAge
            );

            return $collection;
        }

        if (!$data) {
            // $item->isMiss() returs false but there are no data in the cache.
            // It seems that another script instance already lock the item and
            // generate the data. Just read the target file and return actuall
            // results without any caching.
            return $this->import($resource, $type);
        }

        $collection = $this->unserializeCollection($data['collection']);

        // Check if the cache should be invalidated.
        if (!$this->isCollectionFresh($collection, $data['created'])) {
            // The collection contains stale resources. The cache should be
            // cleared and fresh data should be return to the client. The other
            // call for the "load" method will regenerate the cache.
            $item->clear();

            return $this->import($resource, $type);
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        // The loader itself can load nothing.
        return false;
    }

    /**
     * Serializes a RoutesCollection instance.
     *
     * @param RouteCollection $collection A collection that should be
     * serialized.
     * @return string
     */
    protected function serializeCollection(RouteCollection $collection)
    {
        return serialize(array(
            'routes' => $collection->all(),
            'resources' => $collection->getResources(),
        ));
    }

    /**
     * Unserializes a RoutesCollection instance.
     *
     * @param string $serialized_collection An output of
     * {@link CacheLoader::serializeCollection} method.
     * @return RouteCollection
     */
    protected function unserializeCollection($serialized_collection)
    {
        $data = unserialize($serialized_collection);
        $collection = new RouteCollection();

        foreach ($data['routes'] as $name => $route) {
            $collection->add($name, $route);
        }

        foreach ($data['resources'] as $resource) {
            $collection->addResource($resource);
        }

        return $collection;
    }

    /**
     * Checks if all resources related with the collection are fresh.
     *
     * @param RouteCollection $collection The collection which resources will be
     * checked.
     * @param int $timestamp The last time the collection was loaded.
     * @return boolean True if all resources are fresh and false otherwise.
     */
    protected function isCollectionFresh(RouteCollection $collection, $timestamp)
    {
        foreach ($collection->getResources() as $resource) {
            if (!$resource->isFresh($timestamp)) {
                return false;
            }
        }

        return true;
    }
}
