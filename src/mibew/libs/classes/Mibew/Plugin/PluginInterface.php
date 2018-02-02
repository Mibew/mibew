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
     * Builds base path for plugin files. This path is relative to Mibew
     * Messenger root and does not contain neither leading nor trailing slash.
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
     * Each key in the array is a string with a plugin name. Each value is
     * plugin version constrain. A constrain can be in one of the following
     * formats:
     *  - "1.2.3": exact version number;
     *  - ">1.2.3": grater than a specific version;
     *  - ">=1.2.3": greater than a specific version or equal to it;
     *  - "<1.2.3": less than a specific version;
     *  - "<=1.2.3": less than a specific version or equal to it;
     *  - "1.2.3 - 2.3.4": equals to ">=1.2.3 <=2.3.4";
     *  - "~1.2.3": equivalent for ">=1.2.3 <1.3.0";
     *  - "~1.2": equivalent for ">=1.2.0 <2.0.0";
     *  - "^1.2.3" equivalent for ">=1.2.3 <2.0.0";
     *  - "^0.1.2" equivalent for ">=0.1.2 <0.2.0";
     *  - "1.2.x": equivalent for ">=1.2.0 <2.0.0";
     *  - "1.x": equivalent for ">=1.0.0 <2.0.0";
     *
     * If the plugin have no dependencies an empty array should be returned.
     *
     * @return array List of plugin's dependencies.
     */
    public static function getDependencies();

    /**
     * Returns list of plugin's system requirements.
     *
     * Each key in the array is a string with a requirement name. Each value is
     * plugin version constrain.
     *
     * A requirement name can be on of the following:
     *  - "mibew": Mibew Messenger Core;
     *  - "php": PHP used in the system;
     *  - "ext-*": name of a PHP extension.
     *
     * A constrain can be in one of the following
     * formats:
     *  - "1.2.3": exact version number;
     *  - ">1.2.3": grater than a specific version;
     *  - ">=1.2.3": greater than a specific version or equal to it;
     *  - "<1.2.3": less than a specific version;
     *  - "<=1.2.3": less than a specific version or equal to it;
     *  - "1.2.3 - 2.3.4": equals to ">=1.2.3 <=2.3.4";
     *  - "~1.2.3": equivalent for ">=1.2.3 <1.3.0";
     *  - "~1.2": equivalent for ">=1.2.0 <2.0.0";
     *  - "^1.2.3" equivalent for ">=1.2.3 <2.0.0";
     *  - "^0.1.2" equivalent for ">=0.1.2 <0.2.0";
     *  - "1.2.x": equivalent for ">=1.2.0 <2.0.0";
     *  - "1.x": equivalent for ">=1.0.0 <2.0.0";
     *
     * If the plugin have no system requirements an empty array should be
     * returned.
     *
     * @return array List of plugin's requirements.
     */
    public static function getSystemRequirements();

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
     * Returns version of the plugin.
     *
     * Version ID should follow semantic versioning convention (see
     * {@link http://semver.org/} for details). It is used to check plugin's
     * dependences.
     *
     * @return string Plugin's version.
     */
    public static function getVersion();

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
