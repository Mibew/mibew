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

namespace Mibew\Button\Generator;

/**
 * An interface that all button generators must implement.
 */
interface GeneratorInterface
{
    /**
     * Sets a generator's option.
     *
     * @param string $name Name of the option.
     * @param mixed $value Value of the option.
     */
    public function setOption($name, $value);

    /**
     * Gets a generator's option.
     *
     * @param type $name
     * @param type $default
     */
    public function getOption($name, $default);

    /**
     * Generates a button code.
     *
     * @return string
     */
    public function generate();
}
