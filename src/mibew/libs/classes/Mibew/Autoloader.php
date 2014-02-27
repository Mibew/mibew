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

namespace Mibew;

/**
 * Autoloader for classes which implements PSR-0 standard.
 */
class Autoloader
{
    /**
     * Base path for autoloaded classes.
     *
     * @var string
     */
    private $basePath;

    /**
     * Class loader.
     *
     * Map class name to file system using PSR-0 and base path specified via
     * \Mibew\Autoloader::register().
     *
     * @param string $class_name Class name to load.
     */
    public function autoload($class_name)
    {
        $class_name = ltrim($class_name, '\\');
        $file_name = '';
        $namespace = '';
        if ($last_ns_pos = strrpos($class_name, '\\')) {
            $namespace = substr($class_name, 0, $last_ns_pos);
            $class_name = substr($class_name, $last_ns_pos + 1);
            $file_name = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $file_name = $this->basePath . DIRECTORY_SEPARATOR . $file_name
            . str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

        if (is_file($file_name)) {
            include_once($file_name);
        }
    }

    /**
     * Register a new instance as an SPL autoloader.
     *
     * @param string $base_path Base path for classes-to-files mapping.
     *
     * @return \Mibew\Autoloader Registered autoloader instance.
     */
    public static function register($base_path)
    {
        $loader = new self($base_path);
        spl_autoload_register(array($loader, 'autoload'));

        return $loader;
    }

    /**
     * Class constructor.
     *
     * @param string $base_path Base path for classes-to-files mapping.
     */
    protected function __construct($base_path)
    {
        $this->basePath = rtrim($base_path, '/\\');
    }
}
