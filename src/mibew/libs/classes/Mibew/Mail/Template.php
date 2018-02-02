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

namespace Mibew\Mail;

use Mibew\Database;

/**
 * A class that represents Mail Template.
 */
class Template
{
    /**
     * Unique template ID.
     *
     * @var int|bool
     */
    public $id;

    /**
     * Locale code the template belongs to.
     *
     * @var string
     */
    public $locale;

    /**
     * Machine name of the template.
     *
     * @var string
     */
    public $name;

    /**
     * E-mail subject.
     *
     * @var string
     */
    public $subject;

    /**
     * E-mail body.
     *
     * @var string
     */
    public $body;

    /**
     * Loads template by its ID.
     *
     * @param int $id ID of the template to load
     * @return boolean|Ban Returns a Template instance or boolean false on
     *   failure.
     */
    public static function load($id)
    {
        // Check $id
        if (empty($id)) {
            return false;
        }

        $template_info = Database::getInstance()->query(
            "SELECT * FROM {mailtemplate} WHERE templateid = :id",
            array(':id' => $id),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        // There is no template with such id in database
        if (!$template_info) {
            return false;
        }

        return self::buildFromDbFields($template_info);
    }

    /**
     * Loads template by its machine name and locale.
     *
     * @param string $name Name of the template to load.
     * @param string $locale Locale of the template to load.
     * @param boolean $strict Indicates if only specified locale should be used.
     *   If the argument is set to false and there is no template for
     *   specified locale a template for "en" locale will be loaded.
     * @return boolean|Ban Returns a Template instance or boolean false on
     *   failure.
     */
    public static function loadByName($name, $locale, $strict = false)
    {
        // Try to load the template from the database.
        $template_info = Database::getInstance()->query(
            "SELECT * FROM {mailtemplate} WHERE name = :name AND locale = :locale",
            array(
                ':name' => $name,
                ':locale' => $locale,
            ),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        if ($template_info) {
            // The template exists in the database.
            return self::buildFromDbFields($template_info);
        }

        if ($strict) {
            // There is no appropriate template in the database and we cannot
            // use one for default locale.
            return false;
        }

        // Try to load a template with specified name for "en" locale.
        if ($locale != 'en') {
            return self::loadByName($name, 'en');
        }

        // There is no template with specified name neither for specified locale
        // nor for "en" locale.
        return false;
    }

    /**
     * Class constructor.
     *
     * @param string $name Machine name of the template.
     * @param string $locale A locale name the template belongs to.
     *
     * @return Template
     */
    public function __construct($name, $locale)
    {
        // Set default values
        $this->id = false;
        $this->locale = $locale;
        $this->name = $name;
        $this->subject = '';
        $this->body = '';
    }

    /**
     * Saves the template to database.
     */
    public function save()
    {
        $db = Database::getInstance();

        if (!$this->id) {
            // This template is new.
            $db->query(
                ('INSERT INTO {mailtemplate} (locale, name, subject, body) '
                    . 'VALUES (:locale, :name, :subject, :body)'),
                array(
                    ':locale' => $this->locale,
                    ':name' => $this->name,
                    ':subject' => $this->subject,
                    ':body' => $this->body,
                )
            );
            $this->id = $db->insertedId();
        } else {
            // Update the existing template
            $db->query(
                ('UPDATE {mailtemplate} SET locale = :locale, name = :name, '
                    . 'subject = :subject, body = :body WHERE templateid = :id'),
                array(
                    ':id' => $this->id,
                    ':locale' => $this->locale,
                    ':name' => $this->name,
                    ':subject' => $this->subject,
                    ':body' => $this->body,
                )
            );
        }
    }

    /**
     * Builds e-mail subject by replacing all placeholders with specified
     * values.
     *
     * @param array $params List of values that should replace subject's
     *   placeholders.
     * @return string Ready to use e-mail subject.
     */
    public function buildSubject($params = array())
    {
        $subject = $this->subject;
        for ($i = 0; $i < count($params); $i++) {
            $subject = str_replace("{" . $i . "}", $params[$i], $subject);
        }

        return $subject;
    }

    /**
     * Builds e-mail body by replacing all placeholders with specified values.
     *
     * @param array $params List of values that should replace body's
     *   placeholders.
     * @return string Ready to use e-mail body.
     */
    public function buildBody($params = array())
    {
        $body = $this->body;
        for ($i = 0; $i < count($params); $i++) {
            $body = str_replace("{" . $i . "}", $params[$i], $body);
        }

        return $body;
    }

    /**
     * Builds and instance of Template based on fields from Database.
     *
     * @param array $db_fields Associative array of database fields which keys
     *   are fields names and the values are fields values.
     * @return Template
     */
    protected static function buildFromDbFields($db_fields)
    {
        $template = new self($db_fields['name'], $db_fields['locale']);
        $template->id = $db_fields['templateid'];
        $template->subject = $db_fields['subject'];
        $template->body = $db_fields['body'];

        return $template;
    }
}
