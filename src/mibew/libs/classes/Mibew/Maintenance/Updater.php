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

use Mibew\Database;
use Stash\Interfaces\PoolInterface;

/**
 * Encapsulates update process.
 */
class Updater
{
    /**
     * A minimum version Mibew Messenger can be updated from.
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
     * Returns list of all errors that took place during update process.
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

        if (!preg_match("/^([0-9]{1,2}\.){2}[0-9]{1,2}(-(alpha|beta|rc)\.[0-9]+)?$/", $current_version)) {
            $this->errors[] = getlocal(
                'The current version ({0}) is unknown or wrongly formatted',
                array($current_version)
            );

            return false;
        }

        if (version_compare($current_version, MIBEW_VERSION) == 0) {
            $this->log[] = getlocal('The database is already up to date');

            return true;
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

        try {
            // Perform incremental updates
            foreach (Utils::getUpdates($this) as $version => $method) {
                if (version_compare($version, $current_version) <= 0) {
                    // Skip updates to lower versions.
                    continue;
                }

                // Run the update
                if (!$method()) {
                    $this->errors[] = getlocal('Cannot update to {0}', array($version));

                    return false;
                }

                // Store new version number in the database. With this info
                // we can rerun the updating process if one of pending
                // updates fails.
                if (!$this->setDatabaseVersion($version)) {
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

        // Use the version from the PHP's constant as the current database
        // version.
        if ($this->setDatabaseVersion(MIBEW_VERSION)) {
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
            $db->throwExceptions(true);

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
     * If Mibew Messenger is not installed yet boolean false will be returned.
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
            $this->errors[] = getlocal('Cannot store new version number');

            return false;
        }
    }

    /**
     * Performs all database updates needed for 2.0.0-beta.4.
     *
     * @return boolean True if the updates have been applied successfully and
     * false otherwise.
     */
    protected function update20000Beta4()
    {
        $db = $this->getDatabase();

        if (!$db) {
            return false;
        }

        $db->query('START TRANSACTION');
        try {
            $operators = $db->query(
                'SELECT operatorid AS id, vcavatar AS avatar FROM {operator}',
                null,
                array('return_rows' => Database::RETURN_ALL_ROWS)
            );

            // Mibew Messenger base path should not be included in operators'
            // avatars which stored in the database. Remove the prefixes one
            // by one.
            foreach ($operators as $operator) {
                if (empty($operator['avatar'])) {
                    // The operator has no avatar.
                    continue;
                }

                if (!preg_match("/^.*(files\/avatar\/[^\/]+)$/", $operator['avatar'], $matches)) {
                    // Avatar's path has an unknown format.
                    continue;
                }

                // Remove Mibew's web root from avatar's path
                $db->query(
                    'UPDATE {operator} SET vcavatar = :avatar WHERE operatorid = :id',
                    array(
                        ':id' => $operator['id'],
                        ':avatar' => $matches[1],
                    )
                );
            }
        } catch (\Exception $e) {
            // Something went wrong. We actually cannot update the database.
            $this->errors[] = getlocal('Cannot update content: {0}', $e->getMessage());
            // The database changes should be discarded.
            $db->query('ROLLBACK');

            return false;
        }

        // All needed data has been updated.
        $db->query('COMMIT');

        return true;
    }

    /**
     * Performs all database updates needed for 2.1.0.
     *
     * @return boolean True if the updates have been applied successfully and
     * false otherwise.
     */
    protected function update20100()
    {
        $db = $this->getDatabase();

        if (!$db) {
            return false;
        }

        try {
            // Alter locale table.
            $db->query('ALTER TABLE {locale} ADD COLUMN name varchar(128) NOT NULL DEFAULT "" AFTER code');
            $db->query('ALTER TABLE {locale} ADD COLUMN rtl tinyint NOT NULL DEFAULT 0');
            $db->query('ALTER TABLE {locale} ADD COLUMN time_locale varchar(128) NOT NULL DEFAULT "en_US"');
            $db->query('ALTER TABLE {locale} ADD COLUMN date_format text');

            $db->query('ALTER TABLE {locale} ADD UNIQUE KEY code (code)');

            // Create a table for available updates.
            $db->query('CREATE TABLE {availableupdate} ( '
                . 'id INT NOT NULL auto_increment PRIMARY KEY, '
                . 'target varchar(255) NOT NULL, '
                . 'version varchar(255) NOT NULL, '
                . 'url text, '
                . 'description text, '
                . 'UNIQUE KEY target (target) '
                . ') charset utf8 ENGINE=InnoDb');

            // Generate Unique ID of Mibew Messenger instance.
            $db->query(
                'INSERT INTO {config} (vckey, vcvalue) VALUES (:key, :value)',
                array(
                    ':key' => '_instance_id',
                    ':value' => Utils::generateInstanceId(),
                )
            );
        } catch (\Exception $e) {
            $this->errors[] = getlocal('Cannot update tables: {0}', $e->getMessage());

            return false;
        }

        try {
            // Store configs for available locales in the database.
            $locales = $db->query(
                'SELECT localeid as id, code from {locale}',
                null,
                array('return_rows' => Database::RETURN_ALL_ROWS)
            );

            $locales_info = get_locales();
            foreach ($locales as $row) {
                $id = $row['id'];
                $code = $row['code'];
                $info = (isset($locales_info[$code]) ? $locales_info[$code] : array())
                    // Default info
                    + array(
                        'name' => $code,
                        'rtl' => false,
                        'time_locale' => 'en_US',
                        'date_format' => array(
                            'full' => '%d %B %Y, %H:%M',
                            'date' => '%d %B %Y',
                            'time' => '%H:%M',
                        ),
                    );

                $db->query(
                    ('UPDATE {locale} SET '
                        . 'name = :name, rtl = :rtl, time_locale = :time_locale, '
                        . 'date_format = :date_format '
                    . 'WHERE localeid = :id'),
                    array(
                        ':id' => $id,
                        ':name' => $info['name'],
                        ':rtl' => $info['rtl'] ? 1 : 0,
                        ':time_locale' => $info['time_locale'],
                        ':date_format' => serialize($info['date_format']),
                    )
                );
            }
        } catch (\Exception $e) {
            $this->errors[] = getlocal('Cannot update content: {0}', $e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Performs all database updates needed for 2.2.0.
     *
     * @return boolean True if the updates have been applied successfully and
     * false otherwise.
     */
    protected function update20200()
    {
        $db = $this->getDatabase();

        if (!$db) {
            return false;
        }

        try {
            // Alter plugin table.
            $db->query('ALTER TABLE {plugin} ADD COLUMN initialized tinyint NOT NULL DEFAULT 0 AFTER enabled');
        } catch (\Exception $e) {
            $this->errors[] = getlocal('Cannot update tables: {0}', $e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Performs all database updates needed for 3.1.0.
     *
     * @return boolean True if the updates have been applied successfully and
     * false otherwise.
     */
    protected function update30100()
    {
        $db = $this->getDatabase();

        if (!$db) {
            return false;
        }

        try {
            // Add primary key to the revision table.
            $db->query('ALTER TABLE {revision} ADD PRIMARY KEY (id)');
        } catch (\Exception $e) {
            $this->errors[] = getlocal('Cannot update tables: {0}', $e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Performs all database updates needed for 3.2.0.
     *
     * @return boolean True if the updates have been applied successfully and
     * false otherwise.
     */
    protected function update30200()
    {
        $db = $this->getDatabase();

        if (!$db) {
            return false;
        }

        try {
            // Alter requestcallback table: replace column with an invalid name.
            $db->query('ALTER TABLE {requestcallback} ADD COLUMN `func` VARCHAR(64) NOT NULL AFTER `function`');
            $db->query('UPDATE {requestcallback} SET `func` = `function`');
            $db->query('ALTER TABLE {requestcallback} DROP COLUMN `function`');
        } catch (\Exception $e) {
            $this->errors[] = getlocal('Cannot update tables: {0}', $e->getMessage());

            return false;
        }

        return true;
    }
}
