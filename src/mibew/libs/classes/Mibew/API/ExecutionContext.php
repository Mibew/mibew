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

namespace Mibew\API;

/**
 * Implements functions execution context
 */
class ExecutionContext
{
    /**
     * Values which returns after execution of all functions in request
     *
     * @var array
     */
    protected $return = array();

    /**
     * Results of execution of all function in request
     *
     * @var array
     */
    protected $functionsResults = array();

    /**
     * Returns requets results
     *
     * @return array Request results
     * @see \Mibew\API\ExecutionContext::$return
     */
    public function getResults()
    {
        return $this->return;
    }

    /**
     * Build arguments list by replace all references by values of execution
     * context.
     *
     * @param array $function Function array. See MibewAPI for details.
     * @return array Arguments list
     * @throws \Mibew\API\APIException
     */
    public function getArgumentsList($function)
    {
        $arguments = $function['arguments'];
        $references = $function['arguments']['references'];
        foreach ($references as $variable => $func_num) {
            // Check target function in context
            if (!isset($this->functionsResults[$func_num - 1])) {
                // Wrong function num
                $message = "Wrong reference in '%s' function. "
                    . "Function #%s does not call yet.";
                throw new APIException(
                    sprintf(
                        $message,
                        $function['function'],
                        $func_num
                    ),
                    APIException::WRONG_FUNCTION_NUM_IN_REFERENCE
                );
            }

            // Check reference
            if (empty($arguments[$variable])) {
                // Empty argument that should contains reference
                throw new APIException(
                    sprintf(
                        "Wrong reference in '%s' function. Empty %s argument.",
                        $function['function'],
                        $variable
                    ),
                    APIException::EMPTY_VARIABLE_IN_REFERENCE
                );
            }
            $reference_to = $arguments[$variable];

            // Check target value
            if (!isset($this->functionsResults[$func_num - 1][$reference_to])) {
                // Undefined target value
                $message = "Wrong reference in '%s' function. "
                    . "There is no '%s' argument in #%s function results";
                throw new APIException(
                    sprintf(
                        $message,
                        $function['function'],
                        $reference_to,
                        $func_num
                    ),
                    APIException::VARIABLE_IS_UNDEFINED_IN_REFERENCE
                );
            }

            // Replace reference by target value
            $arguments[$variable] = $this->functionsResults[$func_num - 1][$reference_to];
        }

        return $arguments;
    }

    /**
     * Stores functions results in execution context and add values to request
     * result.
     *
     * @param array $function Function array. See MibewAPI for details.
     * @param array $results Associative array of the function results.
     * @throws \Mibew\API\APIException
     */
    public function storeFunctionResults($function, $results)
    {
        // Check if function return correct results
        if (empty($results['errorCode'])) {
            // Add value to request results
            foreach ($function['arguments']['return'] as $name => $alias) {
                if (!isset($results[$name])) {
                    // Value that defined in 'return' argument is undefined
                    $message = "Variable with name '%s' is undefined "
                        . "in the results of the '%s' function";
                    throw new APIException(
                        sprintf(
                            $message,
                            $name,
                            $function['function']
                        ),
                        APIException::VARIABLE_IS_UNDEFINED_IN_RESULT
                    );
                }
                $this->return[$alias] = $results[$name];
            }
        } else {
            // Something went wrong during function execution
            // Store error code and error message
            $this->return['errorCode'] = $results['errorCode'];
            $this->return['errorMessage'] = empty($results['errorMessage'])
                ? ''
                : $results['errorMessage'];
        }

        // Store function results in execution context
        $this->functionsResults[] = $results;
    }
}
