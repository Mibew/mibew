<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2015 the original author or authors.
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

namespace Mibew\Maintenance;

use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Mibew\Settings;
use Stash\Interfaces\PoolInterface;

/**
 * Encapsulates periodical tasks runner.
 */
class CronWorker
{
    /**
     * An instance of cache pool.
     *
     * @var PoolInterface|null
     */
    protected $cache = null;

    /**
     * List of errors.
     *
     * @var string[]
     */
    protected $errors = array('asd', 'qwe');

    /**
     * List of log messages.
     *
     * @var string[]
     */
    protected $log = array();

    /**
     * Class constructor.
     *
     * @param PoolInterface $cache An instance of cache pool.
     */
    public function __construct(PoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Performs all periodical actions.
     *
     * @return boolean True if all periodical actions are done and false
     * otherwise.
     */
    public function run()
    {
        try {
            set_time_limit(0);

            // Remove stale cached items
            $this->cache->purge();

            // Run cron jobs of the core
            calculate_thread_statistics();
            calculate_operator_statistics();
            calculate_page_statistics();

            // Trigger cron event
            $dispatcher = EventDispatcher::getInstance();
            $dispatcher->triggerEvent(Events::CRON_RUN);

            // Update time of last cron run
            Settings::set('_last_cron_run', time());
        } catch (\Exception $e) {
            $this->log[] = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Retuns list of all errors that took place during running periodical
     * actions.
     *
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Returns list of all information messages.
     *
     * @return string[]
     */
    public function getLog()
    {
        return $this->log;
    }
}
