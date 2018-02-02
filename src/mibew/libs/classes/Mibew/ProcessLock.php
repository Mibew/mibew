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

namespace Mibew;

use Mibew\Settings;

/**
 * Watch the process is ran only once.
 */
class ProcessLock
{
    /**
     * Name of the lock.
     *
     * @var string
     */
    private $name;

    /**
     * Time to live of the lock.
     *
     * @var int
     */
    private $ttl;

    /**
     * Class constructor.
     *
     * @param string $name Name of the lock.
     * @param int $ttl Time after the lock will be automatically released.
     */
    public function __construct($name, $ttl = 300)
    {
        $this->name = $name;
        $this->ttl = $ttl;
    }

    /**
     * Tries to get the lock.
     *
     * @return boolean True if the lock has been got and false otherwise.
     */
    public function get()
    {
        // Check if we can get the lock
        $lock_timestamp = (int)Settings::get($this->getInternalName(), 0);
        $is_lock_free = !$lock_timestamp
            // Lock cannot be got for more than ttl.
            || (time() - $lock_timestamp) > $this->ttl;

        if (!$is_lock_free) {
            return false;
        }

        // Get the lock
        Settings::set($this->getInternalName(), time());

        return true;
    }

    /**
     * Releases the lock
     */
    public function release()
    {
        Settings::set($this->getInternalName(), '0');
    }

    /**
     * Builds internal name of the lock.
     *
     * @return string
     */
    protected function getInternalName()
    {
        return '_' . $this->name . '_lock_time';
    }
}
