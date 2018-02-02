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

use Symfony\Component\Yaml\Parser as YamlParser;
use Mibew\Database;

/**
 * Get information about all existing canned message from database
 *
 * @param string $locale RFC 5646 code for language
 * @param integer $group_id Group ID for the canned messages
 *
 * @return array Each its element is canned message structure. contains
 * (id integer, vctitle string, vcvalue string)
 */
function load_canned_messages($locale, $group_id)
{
    $db = Database::getInstance();
    $values = array(':locale' => $locale);
    if ($group_id) {
        $values[':groupid'] = $group_id;
    }

    return $db->query(
        ("SELECT id, vctitle, vcvalue FROM {cannedmessage} "
            . "WHERE locale = :locale AND ("
                . ($group_id ? "groupid = :groupid" : "groupid is NULL OR groupid = 0")
            . ") ORDER BY vcvalue"),
        $values,
        array('return_rows' => Database::RETURN_ALL_ROWS)
    );
}

/**
 * Get information about first existing canned message from database
 *
 * @param integer $key ID of canned message which will be returned
 *
 * @return null|array It is canned message structure. contains (vctitle string,
 * vcvalue string)
 */
function load_canned_message($key)
{
    $db = Database::getInstance();
    $result = $db->query(
        "SELECT vctitle, vcvalue FROM {cannedmessage} WHERE id = ?",
        array($key),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );

    return $result ? $result : null;
}

/**
 * Updates information about existing canned message in database
 *
 * @param integer $key id of canned message which must be update
 * @param string $title new title for this canned message
 * @param string $message new message for this canned message
 */
function save_canned_message($key, $title, $message)
{
    $db = Database::getInstance();
    $db->query(
        "UPDATE {cannedmessage} SET vcvalue = ?, vctitle = ? WHERE id = ?",
        array($message, $title, $key)
    );
}

/**
 * Add new canned message to database
 *
 * @param string $locale RFC 5646 code for language
 * @param integer $group_id Group ID for the canned messages
 * @param string $title title of canned message
 * @param string $message body of canned message
 */
function add_canned_message($locale, $group_id, $title, $message)
{
    $db = Database::getInstance();
    $db->query(
        ("INSERT INTO {cannedmessage} (locale,groupid,vctitle,vcvalue) "
            . "VALUES (?, ?, ?, ?)"),
        array(
            $locale,
            ($group_id ? $group_id : null),
            $title,
            $message,
        )
    );
}

/**
 * Import canned messages from a YAML file.
 *
 * @param string $locale Locale code.
 * @param string $file Full path to the file that should be imported.
 */
function import_canned_messages($locale, $file)
{
    // Get new canned messages that should be imported.
    $parser = new YamlParser();
    $new_messages = $parser->parse(file_get_contents($file));
    if (empty($new_messages)) {
        // Nothing to import.
        return;
    }

    // Get list of existing canned messages for specified locale an key it by
    // message value.
    $loaded_messages = load_canned_messages($locale, false);
    $existing_messages = array();
    if (!empty($loaded_messages)) {
        foreach ($loaded_messages as $message) {
            $existing_messages[$message['vcvalue']] = $message;
        }
    }

    // Save only new messages to avoid duplication
    foreach ($new_messages as $message) {
        if (array_key_exists($message, $existing_messages)) {
            continue;
        }

        add_canned_message(
            $locale,
            null,
            cut_string($message, 97, '...'),
            $message
        );
    }
}
