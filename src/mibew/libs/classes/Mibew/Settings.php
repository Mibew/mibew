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

/**
 * Encapsulates work with system settings.
 */
class Settings
{
    /**
     * An instance of Settings class
     *
     * @var Settings
     */
    protected static $instance = null;

    /**
     * Array of settings
     *
     * @var array
     */
    protected $settings = array();

    /**
     * Array of settings stored in database
     *
     * @var array
     */
    protected $settingsInDb = array();

    /**
     * Returns an instance of Settings class
     *
     * @return Settings
     */
    protected static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Settings class constructor. Set default values and load setting from
     * database.
     */
    protected function __construct()
    {
        // Set default values
        $this->settings = array(
            'dbversion' => 0,
            'title' => 'Your Company',
            'hosturl' => 'https://mibew.org',
            'logo' => '',
            'usernamepattern' => '{name}',
            'chat_style' => 'default',
            'invitation_style' => 'default',
            'page_style' => 'default',
            'chattitle' => 'Live Support',
            'max_uploaded_file_size' => 100000,
            'max_connections_from_one_host' => 10,
            'thread_lifetime' => 600,
            'email' => '', /* inbox for left messages */
            'left_messages_locale' => get_home_locale(),
            'sendmessagekey' => 'center',
            'enableban' => '0',
            'enablessl' => '0',
            'forcessl' => '0',
            'usercanchangename' => '1',
            'enablegroups' => '0',
            'enablegroupsisolation' => '0',
            'enablestatistics' => '1',
            'enabletracking' => '0',
            'enablepresurvey' => '1',
            'surveyaskmail' => '0',
            'surveyaskgroup' => '1',
            'surveyaskmessage' => '0',
            'enablepopupnotification' => '0',
            'autocheckupdates' => '1', /* Check updates automatically */
            'showonlineoperators' => '0',
            'enablecaptcha' => '0',
            'enableprivacypolicy' => '0',
            'privacypolicy' => '',
            'online_timeout' => 30, /* Timeout (in seconds) when online operator becomes offline */
            'connection_timeout' => 30, /* Timeout (in seconds) from the last ping when messaging window disconnects */
            'updatefrequency_operator' => 2,
            'updatefrequency_chat' => 2,
            'updatefrequency_tracking' => 10,
            'visitors_limit' => 20, /* Number of visitors to look over */
            'invitation_lifetime' => 60, /* Lifetime for invitation to chat */
            'tracking_lifetime' => 600, /* Time to store tracked old visitors' data */
            'trackoperators' => '0',
            'cron_key' => DEFAULT_CRON_KEY,
            // System values are listed below. They cannot be changed via
            // administrative interface. Start names for these values from
            // underscore sign(_).
            // Unix timestamp when cron job ran last time.
            '_last_cron_run' => 0,
            // Random unique ID which is used for getting info about new
            // updates. This value is initialized during Installation or Update
            // process.
            '_instance_id' => '',
        );

        // Load values from database
        $db = Database::getInstance();
        $rows = $db->query(
            "SELECT vckey, vcvalue FROM {config}",
            null,
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        foreach ($rows as $row) {
            $name = $row['vckey'];
            $this->settings[$name] = $row['vcvalue'];
            $this->settingsInDb[$name] = true;
        }
    }

    /**
     * Get setting value.
     *
     * @param string $name Variable's name
     * @param mixed $default A value which will be used if the variable is not
     *   set.
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        $instance = self::getInstance();

        return isset($instance->settings[$name])
            ? $instance->settings[$name]
            : $default;
    }

    /**
     * Set setting value.
     *
     * @param string $name Variables's name
     * @param mixed $value Variable's value
     */
    public static function set($name, $value)
    {
        $instance = self::getInstance();
        // Update value in the instance
        $instance->settings[$name] = $value;

        // Update value in the database
        $db = Database::getInstance();
        if (!isset($instance->settingsInDb[$name])) {
            $db->query(
                "INSERT INTO {config} (vckey, vcvalue) VALUES (:name, :value)",
                array(':name' => $name, ':value' => $value)
            );
            $instance->settingsInDb[$name] = true;
        } else {
            $db->query(
                'UPDATE {config} SET vcvalue=:value WHERE vckey=:name',
                array(':name' => $name, ':value' => $value)
            );
        }
    }

    /**
     * Implementation of destructor
     */
    public function __destruct()
    {
    }
}
