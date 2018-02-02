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

use Mibew\Database;

/**
 * Represents plugin's state that is stored in database.
 */
class State
{
    /**
     * ID of the plugin's record in database.
     * @var int|boolean
     */
    public $id;

    /**
     * Name of the plugin.
     * @var string
     */
    public $pluginName;

    /**
     * Version of the plugin.
     * @var string
     */
    public $version;

    /**
     * Indicates if the plugin is installed or not.
     * @var boolean
     */
    public $installed;

    /**
     * Indicates if the plugin is enabled or not.
     * @var boolean
     */
    public $enabled;

    /**
     * Loads state by its ID.
     *
     * @param int $id ID of the state.
     * @return State|boolean An instance of state or boolean false on failure.
     */
    public static function load($id)
    {
        // Check $id
        if (empty($id)) {
            return false;
        }

        // Load plugin state
        $info = Database::getInstance()->query(
            'SELECT * FROM {plugin} WHERE id = :id',
            array(':id' => $id),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        // There is no such record in database
        if (!$info) {
            return false;
        }

        // Create and populate object
        $state = new self();
        $state->populateFromDbFields($info);

        return $state;
    }

    /**
     * Loads state by plugin's name.
     *
     * @param string $name Name of the plugin which state should be loaded.
     * @return State|boolean An instance of state or boolean false on failure.
     */
    public static function loadByName($name)
    {
        if (!Utils::isValidPluginName($name)) {
            return false;
        }

        // Load plugin state
        $info = Database::getInstance()->query(
            'SELECT * FROM {plugin} WHERE name = :name',
            array(':name' => $name),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        // There is no such record in database
        if (!$info) {
            return false;
        }

        // Create and populate object
        $state = new self();
        $state->populateFromDbFields($info);

        return $state;
    }

    /**
     * Loads all states from database.
     *
     * @return State[] List of state objects.
     * @throws \RuntimeException If the data cannot be retrieved because of a
     *   failure.
     */
    public static function loadAll()
    {
        $rows = Database::getInstance()->query(
            "SELECT * FROM {plugin}",
            null,
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        if ($rows === false) {
            throw new \RuntimeException('Plugins list cannot be retrieved.');
        }

        $states = array();
        foreach ($rows as $row) {
            $state = new self();
            $state->populateFromDbFields($row);
            $states[] = $state;
        }

        return $states;
    }

    /**
     * Loads state for all enabled plugins.
     *
     * @return State[] List of state objects.
     * @throws \RuntimeException If the data cannot be retrieved because of a
     *   failure.
     */
    public static function loadAllEnabled()
    {
        $rows = Database::getInstance()->query(
            'SELECT * FROM {plugin} WHERE enabled = :enabled AND installed = :installed',
            array(
                ':enabled' => 1,
                ':installed' => 1,
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        if ($rows === false) {
            throw new \RuntimeException('Plugins list cannot be retrieved.');
        }

        $states = array();
        foreach ($rows as $row) {
            $state = new self();
            $state->populateFromDbFields($row);
            $states[] = $state;
        }

        return $states;
    }

    /**
     * Class constructor.
     *
     * @param string $plugin_name Name of the plugin the state belongs to.
     */
    public function __construct()
    {
        // Set default values
        $this->id = false;
        $this->pluginName = null;
        $this->version = null;
        $this->installed = false;
        $this->enabled = false;
        $this->initialized = false;
    }

    /**
     * Saves the state to the database.
     */
    public function save()
    {
        $db = Database::getInstance();

        if (!$this->id) {
            // This state is new.
            $db->query(
                ("INSERT INTO {plugin} (name, version, installed, enabled, initialized) "
                    . "VALUES (:name, :version, :installed, :enabled, :initialized)"),
                array(
                    ':name' => $this->pluginName,
                    ':version' => $this->version,
                    ':installed' => (int)$this->installed,
                    ':enabled' => (int)$this->enabled,
                    ':initialized' => (int)$this->initialized,
                )
            );
            $this->id = $db->insertedId();
        } else {
            // Update existing state
            $db->query(
                ("UPDATE {plugin} SET name = :name, version = :version, "
                    . "installed = :installed, enabled = :enabled, initialized = :initialized WHERE id = :id"),
                array(
                    ':id' => $this->id,
                    ':name' => $this->pluginName,
                    ':version' => $this->version,
                    ':installed' => (int)$this->installed,
                    ':enabled' => (int)$this->enabled,
                    ':initialized' => (int)$this->initialized,
                )
            );
        }
    }

    /**
     * Deletes a state from the database.
     *
     * @throws \RuntimeException If the state is not stored in the database.
     */
    public function delete()
    {
        if (!$this->id) {
            throw new \RuntimeException('You cannot delete a plugin state without ID');
        }

        Database::getInstance()->query(
            "DELETE FROM {plugin} WHERE id = :id LIMIT 1",
            array(':id' => $this->id)
        );
    }

    /**
     * Populates fields of the instance with values from database row.
     *
     * @param array $db_fields Associative array of database fields for the
     *   state.
     */
    protected function populateFromDbFields($db_fields)
    {
        $this->id = $db_fields['id'];
        $this->pluginName = $db_fields['name'];
        $this->version = $db_fields['version'];
        $this->enabled = (bool)$db_fields['enabled'];
        $this->installed = (bool)$db_fields['installed'];
        $this->initialized = (bool)$db_fields['initialized'];
    }
}
