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

/**
 * Represents a record about available update with all necessary info.
 */
class AvailableUpdate
{
    /**
     * Unique (for the current Mibew Messenger instance) update ID.
     *
     * @type int
     */
    public $id;

    /**
     * String representing update target.
     *
     * It can be equal to either "core" or fully qualified plugin's name.
     *
     * @var string
     */
    public $target;

    /**
     * The latest version the core/plugin can be updated to.
     *
     * @type string
     */
    public $version;

    /**
     * The URL the update can be downloaded from.
     *
     * @type string
     */
    public $url;

    /**
     * Arbitrary description of the update.
     *
     * @type string
     */
    public $description;

    /**
     * Loads update by its ID.
     *
     * @param int $id ID of the update to load
     * @return boolean|AvailableUpdate Returns an AvailableUpdate instance or
     * boolean false on failure.
     */
    public static function load($id)
    {
        // Check $id
        if (empty($id)) {
            return false;
        }

        // Load update's info
        $info = Database::getInstance()->query(
            "SELECT * FROM {availableupdate} WHERE id = :id",
            array(':id' => $id),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        // There is no update with such id in database
        if (!$info) {
            return false;
        }

        // Create and populate update object
        $update = new self();
        $update->populateFromDbFields($info);

        return $update;
    }

    /**
     * Loads update by its target.
     *
     * @param string $target Target of the update to load.
     * @return boolean|AvailableUpdate Returns an AvailableUpdate instance or
     * boolean false on failure.
     */
    public static function loadByTarget($target)
    {
        // Check the target
        if (empty($target)) {
            return false;
        }

        // Load update info
        $info = Database::getInstance()->query(
            "SELECT * FROM {availableupdate} WHERE target = :target",
            array(':target' => $target),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        // There is no update with such target in database
        if (!$info) {
            return false;
        }

        // Create and populate update object
        $update = new self();
        $update->populateFromDbFields($info);

        return $update;
    }

    /**
     * Loads available updates.
     *
     * @return array List of AvailableUpdate instances.
     *
     * @throws \RuntimeException If something went wrong and the list could not
     *   be loaded.
     */
    public static function all()
    {
        $rows = Database::getInstance()->query(
            "SELECT * FROM {availableupdate}",
            null,
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        if ($rows === false) {
            throw new \RuntimeException('List of available updates cannot be retrieved.');
        }

        $updates = array();
        foreach ($rows as $item) {
            $update = new self();
            $update->populateFromDbFields($item);
            $updates[] = $update;
        }

        return $updates;
    }

    /**
     * Class constructor.
     */
    public function __construct()
    {
        // Set default values
        $this->id = false;
        $this->target = null;
        $this->version = null;
        $this->url = '';
        $this->description = '';
    }

    /**
     * Remove record about available update from the database.
     */
    public function delete()
    {
        if (!$this->id) {
            throw new \RuntimeException('You cannot delete an update without id');
        }

        Database::getInstance()->query(
            "DELETE FROM {availableupdate} WHERE id = :id LIMIT 1",
            array(':id' => $this->id)
        );
    }

    /**
     * Save the update to the database.
     */
    public function save()
    {
        $db = Database::getInstance();

        if (!$this->target) {
            throw new \RuntimeException('Update\'s target was not set');
        }

        if (!$this->url) {
            throw new \RuntimeException('Update\'s URL was not set');
        }

        if (!$this->version) {
            throw new \RuntimeException('Update\'s version was not set');
        }

        if (!$this->id) {
            // This update is new.
            $db->query(
                ("INSERT INTO {availableupdate} (target, version, url, description) "
                    . "VALUES (:target, :version, :url, :description)"),
                array(
                    ':target' => $this->target,
                    ':version' => $this->version,
                    ':url' => $this->url,
                    ':description' => $this->description,
                )
            );
            $this->id = $db->insertedId();
        } else {
            // Update existing update
            $db->query(
                ("UPDATE {availableupdate} SET target = :target, url = :url, "
                    . "version = :version, description = :description "
                    . "WHERE id = :id"),
                array(
                    ':id' => $this->id,
                    ':target' => $this->target,
                    ':version' => $this->version,
                    ':url' => $this->url,
                    ':description' => $this->description,
                )
            );
        }
    }

    /**
     * Sets update's fields according to the fields from Database.
     *
     * @param array $db_fields Associative array of database fields which keys
     *   are fields names and the values are fields values.
     */
    protected function populateFromDbFields($db_fields)
    {
        $this->id = $db_fields['id'];
        $this->target = $db_fields['target'];
        $this->version = $db_fields['version'];
        $this->url = $db_fields['url'];
        $this->description = $db_fields['description'];
    }
}
