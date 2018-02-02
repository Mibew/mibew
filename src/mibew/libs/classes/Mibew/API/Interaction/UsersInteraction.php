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

namespace Mibew\API\Interaction;

/**
 * Implements Mibew Messenger Core - Mibew Messenger Users list interaction
 */
class UsersInteraction extends AbstractInteraction
{
    /**
     * Returns reserved (system) functions' names.
     *
     * @return array
     * @see \Mibew\API\Interaction\AbstractInteraction::getReservedFunctionsNames
     */
    public function getReservedFunctionsNames()
    {
        return array(
            'result',
        );
    }

    /**
     * Defines mandatory arguments and default values for them.
     *
     * @return array
     * @see \Mibew\API\Interaction\AbstractInteraction::mandatoryArguments
     */
    protected function mandatoryArguments()
    {
        return array(
            '*' => array(
                'agentId' => null,
                'references' => array(),
                'return' => array(),
            ),
            'updateThreads' => array(
                'revision' => 0,
            ),
            'result' => array(
                'errorCode' => 0,
            ),
        );
    }
}
