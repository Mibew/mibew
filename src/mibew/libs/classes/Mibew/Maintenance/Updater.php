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

namespace Mibew\Maintenance;

use Mibew\Database;
use Stash\Interfaces\PoolInterface;

/**
 * Encapsulates update process.
 */
class Updater
{
    /**
     * A minimum version Mibew can be updated from.
     */
    const MIN_VERSION = '2.0.0-beta.1';

    /**
     * Database instance.
     *
     * @var Database
     */
    protected $db = null;

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
     * An instance of cache pool.
     *
     * @var PoolInterface|null
     */
    protected $cache = null;

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
     * Retuns list of all errors that took place during update process.
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
     * Performs all actions that are needed to update database structure.
     *
     * @return boolean True if the system updated successfully and false
     *   otherwise.
     */
    public function run()
    {
        $current_version = $this->getDatabaseVersion();

        if (!preg_match("/^([0-9]{1,2}\.){2}[0-9]{1,2}(-beta\.[0-9]+)?$/", $current_version)) {
            $this->errors[] = getlocal(
                'The current version ({0}) is unknown or wrong formated',
                array($current_version)
            );

            return false;
        }

        if (version_compare($current_version, self::MIN_VERSION) < 0) {
            $this->errors[] = getlocal(
                'You can update the system only from {0} and later versions. The current version is {1}',
                array(
                    self::MIN_VERSION,
                    $current_version
                )
            );

            return false;
        }

        // Get list of all available updates
        $updates = $this->getUpdates();

        // Check if updates should be performed
        $versions = array_keys($updates);
        $last_version = end($versions);
        if (version_compare($current_version, $last_version) >= 0) {
            $this->log[] = getlocal('The database is already up to date');

            return true;
        }

        try {
            // Perform incremental updates
            foreach ($updates as $version => $method) {
                if (version_compare($version, $current_version) <= 0) {
                    // Skip updates to lower versions.
                    continue;
                }

                // Run the update
                if (!$this->{$method}()) {
                    $this->errors[] = getlocal('Cannot update to {0}', array($version));

                    return false;
                }

                // Store new version number in the database. With this info
                // we can rerun the updating process if one of pending
                // updates fails.
                if (!$this->setDatabaseVersion($version)) {
                    $this->errors[] = getlocal('Cannot store new version number');

                    return false;
                } else {
                    $this->log[] = getlocal('Updated to {0}', array($version));
                }

                $current_version = $version;
            }
        } catch (\Exception $e) {
            // Something went wrong
            $this->errors[] = getlocal(
                'Update failed: {0}',
                array($e->getMessage())
            );

            return false;
        }

        // Clean up the cache
        $this->cache->flush();

        return true;
    }

    /**
     * Returns initialized database object.
     *
     * @return \Mibew\Database|boolean A database class instance or boolean
     *   false if something went wrong.
     */
    protected function getDatabase()
    {
        try {
            $db = Database::getInstance();
            $db->throwExeptions(true);

            return $db;
        } catch (\Exception $e) {
            $this->errors[] = getlocal(
                "Could not retrieve database instance. Error: {0}",
                array($e->getMessage())
            );

            return false;
        }
    }

    /**
     * Gets version of existing database structure.
     *
     * If Mibew is not installed yet boolean false will be returned.
     *
     * @return int|boolean Database structure version or boolean false if the
     *   version cannot be determined.
     */
    protected function getDatabaseVersion()
    {
        if (!($db = $this->getDatabase())) {
            return false;
        }

        try {
            $result = $db->query(
                "SELECT vcvalue AS version FROM {config} WHERE vckey = :key LIMIT 1",
                array(':key' => 'dbversion'),
                array('return_rows' => Database::RETURN_ONE_ROW)
            );
        } catch (\Exception $e) {
            return false;
        }

        if (!$result) {
            // It seems that database structure version isn't stored in the
            // database.
            return '0.0.0';
        }

        return $result['version'];
    }

    /**
     * Updates version of database tables tructure.
     *
     * @param string $version Current version
     * @return boolean True if the version is set and false otherwise.
     */
    protected function setDatabaseVersion($version)
    {
        if (!($db = $this->getDatabase())) {
            return false;
        }

        try {
            return $db->query(
                'UPDATE {config} SET vcvalue = :version WHERE vckey = :key LIMIT 1',
                array(
                    ':version' => $version,
                    ':key' => 'dbversion',
                )
            );
        } catch (\Exception $e) {
            // The query fails by some reason.
            return false;
        }
    }

    /**
     * Gets list of all available updates.
     *
     * @return array The keys of this array are version numbers and values are
     *   methods of the {@link \Mibew\Maintenance\Updater} class that should be
     *   performed.
     */
    protected function getUpdates()
    {
        $updates = array();

        $self_reflection = new \ReflectionClass($this);
        foreach ($self_reflection->getMethods() as $method_reflection) {
            // Filter update methods
            $name = $method_reflection->getName();
            if (preg_match("/^update([0-9]+)(?:Beta([0-9]+))?$/", $name, $matches)) {
                $version = Utils::formatVersionId($matches[1]);
                // Check if a beta version is defined.
                if (!empty($matches[2])) {
                    $version .= '-beta.' . $matches[2];
                }

                $updates[$version] = $name;
            }
        }

        uksort($updates, 'version_compare');

        return $updates;
    }
}
