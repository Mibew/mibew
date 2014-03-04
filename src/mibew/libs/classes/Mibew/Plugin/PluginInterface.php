<?php
/*
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

namespace Mibew\Plugin;

/**
 * Interface that must be implemented by all plugins.
 */
interface PluginInterface
{
    /**
     * Returns plugin weight. Weight is used to determine run priority.
     *
     * @return int Plugin weight
     */
    public function getWeight();

    /**
     * Builds base path for plugin files. This path is relative to Mibew root
     * and does not contain neither leading nor trailing slash.
     *
     * @return string Base path for plugin files
     */
    public function getFilesPath();

    /**
     * Indicates if plugin has been initialized correctly or not.
     *
     * A concrete plugin can return false if something go wrong and it cannot be
     * used but the whole system should works anyway.
     *
     * @return boolean
     */
    public function initialized();

    /**
     * This method will be executed when all plugins have been initialized.
     *
     * The order based on weights and order in the main configuration file will
     * be preserved.
     */
    public function run();

    /**
     * Returns list of plugin's dependencies.
     *
     * Each element in the list is a string with a plugin name. If plugin have
     * no dependencies an empty array should be returned.
     *
     * @return array List of plugin's dependencies.
     */
    public static function getDependencies();

    /**
     * Returns some info about plugin such as human-readable name, description,
     * version, etc.
     *
     * At the moment this info is not used at all, so a plugin can return an
     * empty array.
     *
     * @return array Associative array with plugin info
     */
    public static function getInfo();

    /**
     * Makes all actions that are needed to install the plugin.
     *
     * @return boolean True if the plugin was successfully installed and false
     * otherwise.
     */
    public static function install();

    /**
     * Makes all actions that are needed to uninstall the plugin.
     *
     * @return boolean True if the plugin was successfully uninstalled and false
     * otherwise.
     */
    public static function uninstall();
}
