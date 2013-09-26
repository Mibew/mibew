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

/**
 * Implements functions execution context
 */
Class MibewAPIExecutionContext {
	/**
	 * Values which returns after execution of all functions in request
	 * @var array
	 */
	protected $return = array();

	/**
	 * Results of execution of all function in request
	 * @var array
	 */
	protected $functions_results = array();

	/**
	 * Returns requets results
	 *
	 * @return array Request results
	 * @see MibewAPIExecutionContext::$return
	 */
	public function getResults () {
		return $this->return;
	}

	/**
	 * Build arguments list by replace all references by values of execution context
	 *
	 * @param array $function Function array. See MibewAPI for details.
	 * @return array Arguments list
	 * @throws MibewAPIException
	 */
	public function getArgumentsList ($function) {
		$arguments = $function['arguments'];
		$references = $function['arguments']['references'];
		foreach ($references as $variable => $func_num) {
			// Check target function in context
			if (! isset($this->functions_results[$func_num - 1])) {
				// Wrong function num
				throw new MibewAPIException(
					"Wrong reference in '{$function['function']}' function. " .
					"Function #{$func_num} does not call yet.",
					MibewAPIException::WRONG_FUNCTION_NUM_IN_REFERENCE
				);
			}

			// Check reference
			if (empty($arguments[$variable])) {
				// Empty argument that should contains reference
				throw new MibewAPIException(
					"Wrong reference in '{$function['function']}' function. " .
					"Empty {$variable} argument.",
					MibewAPIException::EMPTY_VARIABLE_IN_REFERENCE
				);
			}
			$reference_to = $arguments[$variable];

			// Check target value
			if (! isset($this->functions_results[$func_num - 1][$reference_to])) {
				// Undefined target value
				throw new MibewAPIException(
					"Wrong reference in '{$function['function']}' function. " .
					"There is no '{$reference_to}' argument in #{$func_num} " .
					"function results",
					MibewAPIException::VARIABLE_IS_UNDEFINED_IN_REFERENCE
				);
			}

			// Replace reference by target value
			$arguments[$variable] = $this->functions_results[$func_num - 1][$reference_to];
		}
		return $arguments;
	}

	/**
	 * Stores functions results in execution context and add values to request result
	 *
	 * @param array $function Function array. See MibewAPI for details.
	 * @param array $results Associative array of the function results.
	 * @throws MibewAPIException
	 */
	public function storeFunctionResults ($function, $results) {
		// Check if function return correct results
		if (empty($results['errorCode'])) {
			// Add value to request results
			foreach ($function['arguments']['return'] as $name => $alias) {
				if (! isset($results[$name])) {
					// Value that defined in 'return' argument is undefined
					throw new MibewAPIException(
						"Variable with name '{$name}' is undefined in the " .
						"results of the '{$function['function']}' function",
						MibewAPIException::VARIABLE_IS_UNDEFINED_IN_RESULT
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
		$this->functions_results[] = $results;
	}

}

?>