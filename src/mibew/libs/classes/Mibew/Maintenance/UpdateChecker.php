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

use Mibew\Plugin\Utils as PluginUtils;
use Mibew\Plugin\PluginInfo;

/**
 * Encapsulates available updates checking process.
 */
class UpdateChecker
{
    /**
     * URL of the updates server.
     *
     * @var string|null
     */
    private $url = null;

    /**
     * Unique 64 character length ID of the Mibew Messenger instance.
     *
     * @var string
     */
    private $instanceId = '';

    /**
     * A cache for plugins info array.
     *
     * @var array|null
     */
    private $pluginsInfo = null;

    /**
     * List of errors that took place during updates checking.
     *
     * Each item of the list is a error string.
     *
     * @var array
     */
    private $errors = array();

    /**
     * Sets URL of updates server.
     *
     * @param string $url New updates server's URL
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Retrieves URL of updates server.
     *
     * @return string
     */
    public function getUrl()
    {
        return is_null($this->url)
            ? 'https://mibew.org/api2/updates.json'
            : $this->url;
    }

    /**
     * Sets Unique ID of the Mibew Messenger instance.
     *
     * @param string $id Unique ID that is 64 characters length at most.
     * @throws \InvalidArgumentException
     */
    public function setInstanceId($id)
    {
        if (strlen($id) > 64) {
            throw new \InvalidArgumentException(
                'The ID is too long. It can be 64 characters length at most.'
            );
        }

        // Make sure the ID is always a string.
        $this->instanceId = $id ?: '';
    }

    /**
     * Retrieve Unique ID of the Mibew Messenger instance.
     *
     * @return string
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    /**
     * Retrieves list of errors that took place during update checking process.
     *
     * @return array List of errors. Each item in the list is a error string.
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Runs update checking process.
     *
     * @return boolean False on error and true otherwise. To get more info about
     * error call {@link UpdateChecker::getErrors()} method.
     */
    public function run()
    {
        $ch = curl_init($this->getUrl());

        // TODO: set timeouts
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

        $json = json_encode($this->getSystemInfo());
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $body = curl_exec($ch);
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_errno !== 0) {
            // cURL request failed.
            $this->errors[] = sprintf(
                'cURL error (#%u): %s',
                $curl_errno,
                $curl_error
            );

            return false;
        }

        if ($response_code != 200) {
            // Unexpected HTTP received.
            $this->errors[] = sprintf(
                'Update server returns %u HTTP code instead of 200',
                $response_code
            );

            return false;
        }

        $updates = json_decode($body, true);
        $json_error = json_last_error();
        if ($json_error !== JSON_ERROR_NONE) {
            // Cannot parse JSON result.
            $this->errors[] = $this->formatJsonError($json_error);

            return false;
        }

        if (!$updates) {
            // There are no available updates.
            return true;
        }

        return $this->processUpdates($updates);
    }

    /**
     * Retrieves set of system info that will be sent to updates server.
     *
     * @return array
     */
    protected function getSystemInfo()
    {
        $info = array(
            'core' => MIBEW_VERSION,
            'plugins' => $this->getPluginsInfo(),
        );

        // Attach Instance ID to the info but only if it's not empty.
        $id = $this->getInstanceId();
        if ($id) {
            $info['uid'] = $id;
        }

        return $info;
    }

    /**
     * Retrieves info about plugins available in the system.
     *
     * @return array Associative array of plugins info. Each key of the array is
     * fully qualified plugin's name and each value is an array with the
     * fillowing keys:
     *  - "version": string, version of the plugin which presents in the system.
     *  - "installed": boolean, indicates if the plugin is installed.
     *  - "enabled": boolean, indicates if the plugin is enabled.
     */
    protected function getPluginsInfo()
    {
        if (is_null($this->pluginsInfo)) {
            $this->pluginsInfo = array();
            $names = PluginUtils::discoverPlugins();
            foreach ($names as $plugin_name) {
                $info = new PluginInfo($plugin_name);
                $this->pluginsInfo[$plugin_name] = array(
                    'version' => $info->getVersion(),
                    'installed' => $info->getState()->installed,
                    'enabled' => $info->getState()->enabled,
                );
            }
        }

        return $this->pluginsInfo;
    }

    /**
     * Performs all actions that are needed to prepare store available updates.
     *
     * @param array $updates Asscociative array of available updates that is
     * retrieved from the updates server.
     * @return boolean False on error and true otherwise. To get more info about
     * error call {@link UpdateChecker::getErrors()} method.
     */
    protected function processUpdates($updates)
    {
        // Process updates of the core.
        $success = false;
        if (version_compare($updates['core']['version'], MIBEW_VERSION) > 0) {
            $update = $updates['core'];
            // Save info about update for the core only if its version changed.
            $success = $this->saveUpdate(
                'core',
                $update['version'],
                $update['download'],
                empty($update['description']) ? '' : $update['description']
            );
        } else {
            // Remove obsolete info if core already was updated.
            $success = $this->deleteUpdate('core');
        }
        if (!$success) {
            // Something went wrong. The error is already logged so just
            // notify the outer code.
            return false;
        }

        // Process plugins updates.
        $plugins_info = $this->getPluginsInfo();
        foreach ($updates['plugins'] as $plugin_name => $update) {
            if (!isset($plugins_info[$plugin_name])) {
                // It's strange. We receive update info for a plugin that does
                // not exist in the system. Just do nothing.
                continue;
            }

            $info = $plugins_info[$plugin_name];
            $success = false;
            if (version_compare($update['version'], $info['version']) > 0) {
                // Save the update
                $success = $this->saveUpdate(
                    $plugin_name,
                    $update['version'],
                    $update['download'],
                    empty($update['description']) ? '' : $update['description']
                );
            } else {
                // Version of the plugin is not updated. Remove obsolete info if need to.
                $success = $this->deleteUpdate($plugin_name);
            }

            if (!$success) {
                // Something went wrong. The error is already logged so just
                // notify the outer code.
                return false;
            }
        }

        // Remove information about updates for absent plugins.
        $updates = AvailableUpdate::all();
        $plugins = PluginUtils::discoverPlugins();
        foreach ($updates as $update) {
            $name = $update->target;
            // Skip information about the core.
            if (!strcmp($name, 'core')) {
                continue;
            } elseif (!in_array($name, $plugins)) {
                $this->deleteUpdate($name);
            }
        }

        return true;
    }

    /**
     * Saves record about available update in the database.
     *
     * @param string $target Update's target. Can be either "core" or fully
     * qualified plugin's name.
     * @param string $version The latest version at the updates server.
     * @param string $url URL of the page where the update can be downloaded.
     * @param string $description Arbitrary update's description.
     * @return boolean False on failure and true otherwise. To get more info
     * about the error call {@link UpdateChecker::getErrors()} method.
     */
    protected function saveUpdate($target, $version, $url, $description = '')
    {
        try {
            $update = AvailableUpdate::loadByTarget($target);
            if (!$update) {
                // There is no such update in the database. Create a new one.
                $update = new AvailableUpdate();
                $update->target = $target;
            }

            $update->version = $version;
            $update->url = $url;
            $update->description = $description;

            $update->save();
        } catch (\Exception $e) {
            $this->errors[] = 'Cannot save available update: ' + $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Deletes record about available update from the database.
     *
     * @param string $target Update's target. Can be either "core" or fully
     * qualified plugin's name.
     * @return boolean False on failure and true otherwise. To get more info
     * about the error call {@link UpdateChecker::getErrors()} method.
     */
    protected function deleteUpdate($target)
    {
        try {
            $update = AvailableUpdate::loadByTarget($target);
            if (!$update) {
                // There is no such update in the database. Do nothing.
                return true;
            }

            $update->delete();
        } catch (\Exception $e) {
            $this->errors[] = 'Cannot delete obsolete update: ' + $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Builds human-readable message about error in json_* PHP's function.
     *
     * @param int $error_code Error code returned by json_last_error
     * @return string Human-readable error message.
     */
    protected function formatJsonError($error_code)
    {
        $errors = array(
            JSON_ERROR_DEPTH => 'JSON_ERROR_DEPTH',
            JSON_ERROR_STATE_MISMATCH => 'JSON_ERROR_STATE_MISMATCH',
            JSON_ERROR_CTRL_CHAR => 'JSON_ERROR_CTRL_CHAR',
            JSON_ERROR_SYNTAX => 'JSON_ERROR_SYNTAX',
        );

        // Following constants may be unavailable for the current PHP version.
        if (defined('JSON_ERROR_UTF8')) {
            $errors[JSON_ERROR_UTF8] = 'JSON_ERROR_UTF8';
        }

        if (defined('JSON_ERROR_RECURSION')) {
            $errors[JSON_ERROR_RECURSION] = 'JSON_ERROR_RECURSION';
        }

        if (defined('JSON_ERROR_INF_OR_NAN')) {
            $errors[JSON_ERROR_INF_OR_NAN] = 'JSON_ERROR_INF_OR_NAN';
        }

        if (defined('JSON_ERROR_UNSUPPORTED_TYPE')) {
            $errors[JSON_ERROR_UNSUPPORTED_TYPE] = 'JSON_ERROR_UNSUPPORTED_TYPE';
        }

        $msg = isset($errors[$error_code]) ? $errors[$error_code] : 'UNKNOWN';

        return sprintf(
            'Could not parse response from update server. The error is: "%s"',
            $msg
        );
    }
}
