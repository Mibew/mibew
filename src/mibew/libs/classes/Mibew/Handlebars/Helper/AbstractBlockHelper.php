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

namespace Mibew\Handlebars\Helper;

use Mibew\Handlebars\BlockStorage;

/**
 * Contains base functionality for all helpers related with blocks overriding.
 */
abstract class AbstractBlockHelper
{
    /**
     * @var BlockStorage
     */
    protected $blocksStorage;

    /**
     * Helper's constructor.
     *
     * @param BlockStorage $storage A Blocks context instance
     */
    public function __construct(BlockStorage $storage)
    {
        $this->blocksStorage = $storage;
    }
}
