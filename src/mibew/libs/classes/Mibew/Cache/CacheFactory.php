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

namespace Mibew\Cache;

use Stash\Interfaces\PoolInterface;
use Stash\Driver\Ephemeral as EphemeralDriver;
use Stash\Driver\FileSystem as FileSystemDriver;
use Stash\Driver\Memcache as MemcacheDriver;
use Stash\Pool as CachePool;

/**
 * Creates and configures appropriate instance of Cache pool.
 */
class CacheFactory
{
    /**
     * @var PoolInterface|null
     */
    private $cache = null;

    /**
     * List of factory's options.
     *
     * @var array
     */
    private $options;

    /**
     * Class constructor.
     *
     * @param Array $options Associative array of options that should be used.
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Gets factory's option.
     *
     * @param string $name Name of the option to retrieve.
     * @throws \InvalidArgumentException If the option is unknown.
     */
    public function getOption($name)
    {
        if (!isset($this->options[$name])) {
            throw new \InvalidArgumentException(sprintf('Unknown option "%s"', $name));
        }

        return $this->options[$name];
    }

    /**
     * Sets factory's option.
     *
     * @param string $name Name of the option to set.
     * @param string $value New value.
     * @throws \InvalidArgumentException If the option is unknown.
     */
    public function setOption($name, $value)
    {
        if (!isset($this->options[$name])) {
            throw new \InvalidArgumentException(sprintf('Unknown option "%s"', $name));
        }

        $this->options[$name] = $value;

        // Cached instance of cache is not valid any more. New one should be
        // created.
        $this->cache = null;
    }

    /**
     * Sets factory's options.
     *
     * @param array $options Associative array of options.
     * @throws \InvalidArgumentException If specified array has unknow options.
     */
    public function setOptions($options)
    {
        $defaults = array(
            'storage' => 'file_system',
            'path' => '/tmp',
            'memcached_servers' => array(),
        );

        // Make sure all passed options are known
        $unknown_options = array_diff(array_keys($options), array_keys($defaults));
        if (count($unknown_options) != 0) {
            throw new \InvalidArgumentException(sprintf(
                'These options are unknown: %s',
                implode(', ', $unknown_options)
            ));
        }

        if (empty($this->options)) {
            // The options are set for the first time.
            $this->options = $options + $defaults;
        } else {
            // Update only specified options.
            $this->options = $options + $this->options;
        }

        // Cached instance of cache is not valid any more. New one should be
        // created.
        $this->cache = null;
    }

    /**
     * Builds cache pool instance.
     *
     * @todo Most likely the factory should return Ephemeral cache if a real
     * storage cannot be used by some resons.
     *
     * @return PoolInterface An instance of cache pool.
     * @throws \RuntimeException If at least one of factory's options is
     * invalid.
     */
    public function getCache()
    {
        if (is_null($this->cache)) {
            $storage = $this->getOption('storage');
            if ($storage === 'none') {
                $driver = new EphemeralDriver();
            } elseif ($storage === 'file_system') {
                $driver = new FileSystemDriver();
                $driver->setOptions(array('path' => $this->getOption('path')));
            } elseif ($storage === 'memcached') {
                $servers = $this->getOption('memcached_servers');

                // Make sure memcached servers was described correctly. The next
                // statement will throw Exception if something is wrong so we do
                // not need to check the result.
                $this->validateMemcachedServers($servers);

                // Convert structure from the "memcached_servers" option to the
                // form used in cache driver.
                $formatted_servers = array_map(function ($server) {
                    return array(
                        $server['host'],
                        intval($server['port']),
                        isset($server['weight']) ? intval($server['weight']) : 0,
                    );
                }, $servers);

                $driver = new MemcacheDriver();
                $driver->setOptions(array(
                    'servers' => $formatted_servers,
                    // Use only PHP's "memcached" extension.
                    'extension' => 'memcached'
                ));
            } else {
                throw new \RuntimeException(sprintf(
                    'Wrong value of "storage" option: "%s"',
                    $storage
                ));
            }

            $this->cache = new CachePool($driver);
        }

        return $this->cache;
    }

    /**
     * Checks if the specified array is a valid memcached servers array.
     *
     * @param Array $servers
     * @throws \UnexpectedValueException
     */
    private function validateMemcachedServers($servers)
    {
        foreach ($servers as $server) {
            // The host should be specified.
            if (!isset($server['host']) || !$server['host']) {
                throw new \UnexpectedValueException('Memcached server port was not specified.');
            }

            // The port can be only a positive integer.
            $correct_port = isset($server['port'])
                && (bool)filter_var($server['port'], FILTER_VALIDATE_INT)
                && intval($server['port']) > 0;
            if (!$correct_port) {
                throw new \UnexpectedValueException(sprintf(
                    'Memcached server port can be only a positive integer. "%s" is given.',
                    isset($server['port']) ? $server['port'] : ''
                ));
            }

            if (!isset($server['weight'])) {
                // The weight is optional thus it can be missed.
                continue;
            }

            // The weight can be only a positive integer if specified.
            $correct_weight = (bool)filter_var($server['weight'], FILTER_VALIDATE_INT)
                && intval($server['weight']) > 0;
            if (!$correct_weight) {
                throw new \UnexpectedValueException(sprintf(
                    'Memcached server weight can be only a positive integer. "%s" is given.',
                    $server['weight']
                ));
            }
        }
    }
}
