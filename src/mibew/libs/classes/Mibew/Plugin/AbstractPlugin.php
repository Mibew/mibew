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
 * Abstract class for plugin which implements base plugins functionality.
 */
abstract class AbstractPlugin
{
    /**
     * Plugin's configurations
     * @var array
     */
    protected $config = array();

    /**
     * Indicates if plugin was initialized correctly or not.
     * @var boolean
     */
    protected $initialized = false;

    /**
     * Plugin's weight
     * @var int
     */
    protected $weight = 0;

    /**
     * Default plugin constructor.
     *
     * Saves passed in variable in AbstractPlugin::$config property. Also
     * updates AbstractPlugin::$weight property to $config["weigth"] if the last
     * one is specified.
     *
     * @param array $config Associative array of plugin's configurations.
     */
    public function __construct($config)
    {
        // Update weight from the config
        if (isset($config['weight'])) {
            $this->weight = $config['weight'];
        }

        // Save configurations
        $this->config = $config;
    }

    /**
     * Implementation of {@link \Mibew\Plugin\PluginInterface::getFilesPath()}.
     *
     * Determine file path based on path to the file which contains a class of
     * concrete plugin.
     *
     * @return string
     */
    public function getFilesPath()
    {
        static $path = false;

        if ($path === false) {
            // Get full name of the file with the current plugin class
            $reflection = new \ReflectionClass($this);
            $file_path = $reflection->getFileName();

            // Remove FS root from the path
            if (strpos($file_path, MIBEW_FS_ROOT) === 0) {
                $file_path = substr($file_path, strlen(MIBEW_FS_ROOT));
            }

            // Remove file name
            $path_parts = explode(
                DIRECTORY_SEPARATOR,
                trim($file_path, DIRECTORY_SEPARATOR)
            );
            array_pop($path_parts);
            $path = implode(DIRECTORY_SEPARATOR, $path_parts);
        }

        return $path;
    }

    /**
     * Implementation of {@link \Mibew\Plugin\PluginInterface::getWeight()}.
     *
     * Returns value of the AbstractPlugin::$weight property. Thus the
     * property can be used to change weight of the plugin.
     *
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Implementation of {@link \Mibew\Plugin\PluginInterface::initialized()}.
     *
     * Returns value of the AbstractPlugin::$initialized property. Thus the
     * property can be used to change initialization status.
     *
     * @return boolean
     */
    public function initialized()
    {
        return $this->initialized;
    }

    /**
     * Implementation of
     * {@link \Mibew\Plugin\PluginInterface::getDependencies()}.
     *
     * Returns an empty array to tell the Plugin Manager that the plugin has
     * no dependencies.
     *
     * @return array
     */
    public static function getDependencies()
    {
        return array();
    }

    /**
     * Implementation of
     * {@link \Mibew\Plugin\PluginInterface::getSystemRequirements()}.
     *
     * Returns an empty array to tell the Plugin Manager that the plugin has
     * no system requirements.
     *
     * @return array
     */
    public static function getSystemRequirements()
    {
        return array();
    }

    /**
     * Implementation of {@link \Mibew\Plugin\PluginInterface::getInfo()}.
     *
     * Returns an empty array.
     *
     * @return array
     */
    public static function getInfo()
    {
        return array();
    }

    /**
     * Implementation of {@link \Mibew\Plugin\PluginInterface::install()}.
     *
     * The method does not perform any actions just returns boolean true.
     *
     * @return boolean
     */
    public static function install()
    {
        return true;
    }

    /**
     * Implementation of {@link \Mibew\Plugin\PluginInterface::uninstall()}.
     *
     * The method does not perform any actions just returns boolean true.
     *
     * @return boolean
     */
    public static function uninstall()
    {
        return true;
    }
}
