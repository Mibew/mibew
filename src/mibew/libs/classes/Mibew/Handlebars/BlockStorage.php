<?php
/*
 * This file is a part of Mibew Messenger.
 *
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

namespace Mibew\Handlebars;

/**
 * A storage for overridable blocks' content.
 */
class BlockStorage
{
    /**
     * Associative array of blocks. The keys are blocks names and the values are
     * blocks content.
     *
     * @type string[]
     */
    protected $blocks = array();

    /**
     * Gets content of a block.
     *
     * @param string $name Block's name.
     * @return string Block's content.
     */
    public function get($name)
    {
        return isset($this->blocks[$name]) ? $this->blocks[$name] : false;
    }

    /**
     * Sets content of a block.
     *
     * @param string $name Block's name.
     * @param string $content Block's content.
     */
    public function set($name, $content)
    {
        $this->blocks[$name] = $content;
    }

    /**
     * Checks if a block exists in the storage.
     *
     * @param string $name Block's name
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->blocks[$name]);
    }

    /**
     * Removes block from the storage.
     *
     * @param string $name Block's name.
     */
    public function remove($name)
    {
        unset($this->blocks[$name]);
    }
}
