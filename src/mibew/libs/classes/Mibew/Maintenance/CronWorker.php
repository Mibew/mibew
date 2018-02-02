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
     * An instance of update checker.
     *
     * @var UpdateChecker|null
     */
    protected $updateChecker = null;

    /**
     * List of errors.
     *
     * @var string[]
     */
    protected $errors = array();

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
     * @param UpdateChecker $update_checker An instance of update checker.
     */
    public function __construct(PoolInterface $cache, UpdateChecker $update_checker = null)
    {
        $this->cache = $cache;

        if (!is_null($update_checker)) {
            $this->updateChecker = $update_checker;
        }
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

            // Update time of last cron run
            Settings::set('_last_cron_run', time());

            // Remove stale cached items
            $this->cache->purge();

            // Run cron jobs of the core
            calculate_thread_statistics();
            calculate_operator_statistics();
            calculate_page_statistics();

            // Trigger cron event
            $dispatcher = EventDispatcher::getInstance();
            $dispatcher->triggerEvent(Events::CRON_RUN);

            if (Settings::get('autocheckupdates') == '1') {
                // Run the update checker
                $update_checker = $this->getUpdateChecker();
                if (!$update_checker->run()) {
                    $this->errors = array_merge(
                        $this->errors,
                        $update_checker->getErrors()
                    );

                    return false;
                }
            }
        } catch (\Exception $e) {
            $this->log[] = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Returns list of all errors that took place during running periodical
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

    /**
     * Retrives an instance of Update Checker attached to the worker.
     *
     * If there was no attached checker it creates a new one.
     *
     * @return UpdateChecker
     */
    protected function getUpdateChecker()
    {
        if (is_null($this->updateChecker)) {
            $this->updateChecker = new UpdateChecker();
            $id = Settings::get('_instance_id');
            if ($id) {
                $this->updateChecker->setInstanceId($id);
            }
        }

        return $this->updateChecker;
    }
}
