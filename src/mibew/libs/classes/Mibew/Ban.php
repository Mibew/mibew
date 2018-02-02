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

use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;

/**
 * A class that represents Ban entity.
 */
class Ban
{
    /**
     * Unique ban ID.
     *
     * @var int
     */
    public $id;

    /**
     * Unix timestamp of the moment the ban was created.
     *
     * @var int
     */
    public $created;

    /**
     * Unix timestamp of the moment the ban will expire.
     *
     * @var int
     */
    public $till;

    /**
     * Banned IP address.
     *
     * @var string
     */
    public $address;

    /**
     * Arbitrary ban comment.
     *
     * @var string
     */
    public $comment;

    /**
     * Loads ban by its ID.
     *
     * @param int $id ID of the ban to load
     * @return boolean|Ban Returns a Ban instance or boolean false on failure.
     */
    public static function load($id)
    {
        // Check $id
        if (empty($id)) {
            return false;
        }

        // Load ban info
        $ban_info = Database::getInstance()->query(
            "SELECT * FROM {ban} WHERE banid = :id",
            array(':id' => $id),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        // There is no ban with such id in database
        if (!$ban_info) {
            return false;
        }

        // Create and populate ban object
        $ban = new self();
        $ban->populateFromDbFields($ban_info);

        return $ban;
    }

    /**
     * Loads ban by IP address.
     *
     * @param int $address Address of the ban to load
     * @return boolean|Ban Returns a Ban instance or boolean false on failure.
     */
    public static function loadByAddress($address)
    {
        // Check $id
        if (empty($address)) {
            return false;
        }

        // Load ban info
        $ban_info = Database::getInstance()->query(
            "SELECT * FROM {ban} WHERE address = :address",
            array(':address' => $address),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        // There is no ban with such id in database
        if (!$ban_info) {
            return false;
        }

        // Create and populate ban object
        $ban = new self();
        $ban->populateFromDbFields($ban_info);

        return $ban;
    }

    /**
     * Loads all bans.
     *
     * @return array List of Ban instances.
     *
     * @throws \RuntimeException If something went wrong and the list could not
     *   be loaded.
     */
    public static function all()
    {
        $rows = Database::getInstance()->query(
            "SELECT * FROM {ban}",
            null,
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        if ($rows === false) {
            throw new \RuntimeException('Bans list cannot be retrieved.');
        }

        $bans = array();
        foreach ($rows as $item) {
            $ban = new self();
            $ban->populateFromDbFields($item);
            $bans[] = $ban;
        }

        return $bans;
    }

    /**
     * Class constructor.
     */
    public function __construct()
    {
        // Set default values
        $this->id = false;
        $this->created = time();
        $this->till = $this->created + 24 * 60 * 60;
    }

    /**
     * Remove ban from the database.
     *
     * Triggers {@link \Mibew\EventDispatcher\Events::BAN_DELETE} event.
     */
    public function delete()
    {
        if (!$this->id) {
            throw new \RuntimeException('You cannot delete a ban without id');
        }

        Database::getInstance()->query(
            "DELETE FROM {ban} WHERE banid = :id LIMIT 1",
            array(':id' => $this->id)
        );

        $args = array('id' => $this->id);
        EventDispatcher::getInstance()->triggerEvent(Events::BAN_DELETE, $args);
    }

    /**
     * Save the ban to the database.
     *
     * Triggers {@link \Mibew\EventDispatcher\Events::BAN_CREATE} event.
     */
    public function save()
    {
        $db = Database::getInstance();

        if (!$this->id) {
            // This ban is new.
            $db->query(
                ("INSERT INTO {ban} (dtmcreated, dtmtill, address, comment) "
                    . "VALUES (:created, :till, :address, :comment)"),
                array(
                    ':created' => (int)$this->created,
                    ':till' => (int)$this->till,
                    ':address' => $this->address,
                    ':comment' => $this->comment,
                )
            );
            $this->id = $db->insertedId();

            $args = array('ban' => $this);
            EventDispatcher::getInstance()->triggerEvent(Events::BAN_CREATE, $args);
        } else {
            // Get the original state of the ban for "update" event.
            $original_ban = Ban::load($this->id);

            // Update existing ban
            $db->query(
                ("UPDATE {ban} SET dtmtill = :till, address = :address, "
                    . "comment = :comment WHERE banid = :id"),
                array(
                    ':id' => $this->id,
                    ':till' => (int)$this->till,
                    ':address' => $this->address,
                    ':comment' => $this->comment,
                )
            );

            $args = array(
                'ban' => $this,
                'original_ban' => $original_ban,
            );
            EventDispatcher::getInstance()->triggerEvent(Events::BAN_UPDATE, $args);
        }
    }

    /**
     * Checks if the ban is expired or not.
     *
     * @return boolean
     */
    public function isExpired()
    {
        return ($this->till > 0 && $this->till < time());
    }

    /**
     * Sets ban's fields according to the fields from Database.
     *
     * @param array $db_fields Associative array of database fields which keys
     *   are fields names and the values are fields values.
     */
    protected function populateFromDbFields($db_fields)
    {
        $this->id = $db_fields['banid'];
        $this->created = $db_fields['dtmcreated'];
        $this->till = $db_fields['dtmtill'];
        $this->address = $db_fields['address'];
        $this->comment = $db_fields['comment'];
    }
}
