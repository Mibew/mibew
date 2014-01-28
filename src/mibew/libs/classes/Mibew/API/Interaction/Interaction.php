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

namespace Mibew\API\Interaction;

/**
 * Encapsulates interaction type
 */
abstract class Interaction
{
    /**
     * Reserved function's names
     *
     * Defines reserved(system) function's names described in the Mibew API.
     * @var array
     */
    public $reservedFunctionNames = array();

    /**
     * Defines obligatory arguments and default values for them
     *
     * @var array Keys of the array are function names ('*' for all functions).
     * Values are arrays of obligatory arguments with key for name of an
     * argument and value for default value.
     *
     * For example:
     * <code>
     * protected $obligatoryArguments = array(
     *     '*' => array(
     *         // Obligatory arguments for all functions are:
     *         'return' => array(),     // 'return' with array() by default and
     *         'references' => array()  // 'references' with array() by default
     *     ),
     *
     *     'result' => array(
     *         // There is an additional argument for the result function
     *         'errorCode' => 0        // This is 'error_code' with 0 by default
     *     )
     * );
     * </code>
     */
    protected $obligatoryArguments = array();

    /**
     * Returns obligatory arguments for the $function_name function
     *
     * @param string $function_name Function name
     * @return array An array of obligatory arguments
     */
    public function getObligatoryArguments($function_name)
    {
        $obligatory_arguments = array();
        // Add obligatory for all functions arguments
        if (!empty($this->obligatoryArguments['*'])) {
            $obligatory_arguments = array_merge(
                $obligatory_arguments,
                array_keys($this->obligatoryArguments['*'])
            );
        }
        // Add obligatory arguments for given function
        if (!empty($this->obligatoryArguments[$function_name])) {
            $obligatory_arguments = array_merge(
                $obligatory_arguments,
                array_keys($this->obligatoryArguments[$function_name])
            );
        }

        return array_unique($obligatory_arguments);
    }

    /**
     * Returns default values of obligatory arguments for the $function_name
     * function
     *
     * @param string $function_name Function name
     * @return array Associative array with keys are obligatory arguments and
     *   values are default values of them
     */
    public function getObligatoryArgumentsDefaults($function_name)
    {
        $obligatory_arguments = array();
        // Add obligatory for all functions arguments
        if (!empty($this->obligatoryArguments['*'])) {
            $obligatory_arguments = array_merge(
                $obligatory_arguments,
                $this->obligatoryArguments['*']
            );
        }
        // Add obligatory arguments for given function
        if (!empty($this->obligatoryArguments[$function_name])) {
            $obligatory_arguments = array_merge(
                $obligatory_arguments,
                $this->obligatoryArguments[$function_name]
            );
        }

        return $obligatory_arguments;
    }
}
