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

namespace Mibew\EventDispatcher;

use Mibew\Plugin\PluginInterface;

/**
 * Provide event-related functionality.
 * Implements singleton pattern.
 */
class EventDispatcher
{
    /**
     * An instance of EventDispatcher class.
     *
     * @var EventDispatcher
     */
    protected static $instance = null;

    /**
     * Events and listeners array.
     *
     * @var array
     */
    protected $events = array();

    /**
     * Increments any time when plugin adds. Is used for determine plugins order
     * for plugins with equal priority.
     *
     * @var int
     */
    protected $offset = 0;

    /**
     * Returns an instance of EventDispatcher class.
     *
     * @return EventDispatcher
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Attaches listener function to event.
     *
     * All event listeners must receive one argument of array type by reference.
     *
     * @param string $event_name Event's name
     * @param \Mibew\Plugin\PluginInterface $plugin Plugin object, that handles
     *   the event
     * @param string $listener Plugins method, that handles the event
     * @param int $priority Priority of listener. If $priority = null, the
     *   plugin weight will use instead.
     * @return boolean true on success or false on failure.
     *
     * @see \Mibew\Plugin::getWeight()
     */
    public function attachListener(
        $event_name,
        PluginInterface $plugin,
        $listener,
        $priority = null
    ) {
        // Check method is callable
        if (!is_callable(array($plugin, $listener))) {
            trigger_error("Method '{$listener}' is not callable!", E_USER_WARNING);

            return false;
        }
        // Create empty array for event listener if it not exists
        if (!array_key_exists($event_name, $this->events)) {
            $this->events[$event_name] = array();
        }
        // Check priority
        if (is_null($priority)) {
            $priority = $plugin->getWeight();
        }
        // Attach listener
        $this->events[$event_name][$priority . "_" . $this->offset] = array(
            'plugin' => $plugin,
            'listener' => $listener,
        );
        $this->offset++;

        return true;
    }

    /**
     * Detach listener function from event
     *
     * @param string $event_name Event's name
     * @param \Mibew\Plugin $plugin Plugin object, that handles the event
     * @param string $listener Plugins method, that handles the event
     * @return boolean true on success or false on failure.
     */
    public function detachListener($event_name, PluginInterface $plugin, $listener)
    {
        // Check event exists
        if (!array_key_exists($event_name, $this->events)) {
            return false;
        }
        // Search event and $plugin->$listener
        foreach ($this->events[$event_name] as $index => $event) {
            if ($event['plugin'] === $plugin && $event['listener'] == $listener) {
                // Detach listener
                unset($this->events[$event_name][$index]);

                return true;
            }
        }

        return false;
    }

    /**
     * Triggers the event
     *
     * @param string $event_name Event's name
     * @param array &$arguments Arguments passed to listener
     * @return boolean true on success or false on failure
     */
    public function triggerEvent($event_name, &$arguments = array())
    {
        // Check event listeners exists
        if (!array_key_exists($event_name, $this->events)) {
            return true;
        }
        // Sorting listeners by priority
        uksort($this->events[$event_name], 'strnatcmp');
        // Invoke listeners
        foreach ($this->events[$event_name] as $event) {
            $plugin = $event['plugin'];
            $listener = $event['listener'];
            $plugin->$listener($arguments);
        }

        return true;
    }

    /**
     * Make constructor unavailable for client code
     */
    protected function __constructor()
    {
    }
}
