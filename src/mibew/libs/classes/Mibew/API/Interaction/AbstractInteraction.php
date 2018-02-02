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
 * Encapsulates interaction type
 */
abstract class AbstractInteraction
{
    /**
     * Returns reserved (system) functions' names.
     *
     * Reserved functions cannon be called directly by the other side and are
     * used for low-level purposes. For example function "result" is used to
     * send back a result of request execution.
     *
     * @return array
     */
    abstract public function getReservedFunctionsNames();

    /**
     * Defines mandatory arguments and default values for them.
     *
     * This method implements "template method" design pattern.
     *
     * @return array Keys of the array are function names ('*' for all functions).
     * Values are arrays of Mandatory arguments with key for name of an
     * argument and value for default value.
     *
     * For example:
     * <code>
     * protected function mandatoryArguments()
     * {
     *     return array(
     *         // Mandatory arguments for all functions are:
     *         '*' => array(
     *             // 'return' with array() by default and
     *             'return' => array(),
     *             // 'references' with array() by default
     *             'references' => array(),
     *         ),
     *
     *         // There is an additional argument for the result function
     *         'result' => array(
     *             // This is 'error_code' with 0 by default
     *             'errorCode' => 0,
     *         ),
     *     );
     * }
     * </code>
     */
    abstract protected function mandatoryArguments();

    /**
     * Returns mandatory arguments for the $function_name function
     *
     * @param string $function_name Function name
     * @return array An array of mandatory arguments
     */
    public function getMandatoryArguments($function_name)
    {
        $all_mandatory_arguments = $this->mandatoryArguments();
        $mandatory_arguments = array();
        // Add mandatory for all functions arguments
        if (!empty($all_mandatory_arguments['*'])) {
            $mandatory_arguments = array_merge(
                $mandatory_arguments,
                array_keys($all_mandatory_arguments['*'])
            );
        }
        // Add mandatory arguments for given function
        if (!empty($all_mandatory_arguments[$function_name])) {
            $mandatory_arguments = array_merge(
                $mandatory_arguments,
                array_keys($all_mandatory_arguments[$function_name])
            );
        }

        return array_unique($mandatory_arguments);
    }

    /**
     * Returns default values of mandatory arguments for the $function_name
     * function
     *
     * @param string $function_name Function name
     * @return array Associative array with keys are mandatory arguments and
     *   values are default values of them
     */
    public function getMandatoryArgumentsDefaults($function_name)
    {
        $all_mandatory_arguments = $this->mandatoryArguments();
        $mandatory_arguments = array();
        // Add mandatory for all functions arguments
        if (!empty($all_mandatory_arguments['*'])) {
            $mandatory_arguments = array_merge(
                $mandatory_arguments,
                $all_mandatory_arguments['*']
            );
        }
        // Add mandatory arguments for given function
        if (!empty($all_mandatory_arguments[$function_name])) {
            $mandatory_arguments = array_merge(
                $mandatory_arguments,
                $all_mandatory_arguments[$function_name]
            );
        }

        return $mandatory_arguments;
    }
}
