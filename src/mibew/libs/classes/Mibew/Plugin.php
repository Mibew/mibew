<?php
/*
 * Copyright 2005-2013 the original author or authors.
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
 * Base plugin class
 */
abstract class Plugin
{
    /**
     * Constructor must set this value to true after successful initialization
     * failures
     * @var boolean
     */
    public $initialized = false;

    /**
     * An array of plugin configuration
     * @var array
     */
    protected $config = array();

    /**
     * Returns plugin weight. Weight is used for determine loading order and as
     * default listner priority.
     *
     * @return int Plugin weight
     */
    abstract public function getWeight();

    /**
     * Register listeners
     *
     * Event listener take one argument by reference. For example:
     * <code>
     *   public function testListener(&$arguments) {
     *      $arguments['result'] = 'Test string';
     *   }
     * </code>
     */
    abstract public function registerListeners();

    /**
     * Returns list of plugin's dependences.
     *
     * Each element of dependenses list is a string with a plugin name.
     * If plugin have no dependenses do not override this method.
     *
     * @return array List of plugin's dependences.
     */
    public static function getDependences()
    {
        return array();
    }
}
