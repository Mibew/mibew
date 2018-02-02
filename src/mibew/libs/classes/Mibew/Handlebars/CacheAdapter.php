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

namespace Mibew\Handlebars;

use Handlebars\Cache as HandlebarsCacheInterface;
use Mibew\Cache\CacheAwareInterface;
use Stash\Interfaces\PoolInterface;
use Stash\Invalidation;

/**
 * An adapter to use Mibew Messenger cache with Handlebars template engine.
 */
class CacheAdapter implements HandlebarsCacheInterface, CacheAwareInterface
{
    /**
     * @var PoolInterface;
     */
    protected $cache = null;

    /**
     * Class constructor.
     *
     * @param PoolInterface $cache An instance of cache pool
     */
    public function __construct(PoolInterface $cache)
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
     * {@inheritdoc}
     */
    public function setCache(PoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        $item = $this->getCacheItem($name);
        $cached_data = $item->get(Invalidation::NONE);

        if ($item->isMiss()) {
            return false;
        }

        return $cached_data;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        $item = $this->getCacheItem($name);
        $item->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value, $ttl = 0)
    {
        $item = $this->getCacheItem($name);
        // Cache templates for twelve hours. Actually we can use arbitrary value
        // for cache ttl. Handlebars builds cache name according to template
        // content, thus if template is changed another cache item will be used.
        // At the same time ttl must be a finite value to allow garbage
        // collector to remove unused cache items.
        $item->set($value, 12 * 60 * 60);
    }

    /**
     * Gets an item from the cache pool.
     *
     * @param string $name Name of the item.
     * @return \Stash\Interfaces\ItemInterface
     */
    protected function getCacheItem($name)
    {
        return $this->getCache()->getItem('handlebars/template/' . $name);
    }
}
