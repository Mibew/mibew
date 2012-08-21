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
 * Implements Mibew API specification version 1.0
 *
 * @todo May be use regular methods instead of static one
 */
Class MibewAPI {

	/**
	 * Version of the MIBEW API protocol implemented by the class
	 */
	const PROTOCOL_VERSION = '1.0';

	/**
	 * Array of MibewAPI objects
	 * @var array
	 */
	protected static $interactions = array();

	/**
	 * An object that encapsulates type of the interaction
	 *
	 * @var MibewAPIInteraction
	 */
	protected $interaction = NULL;

	/**
	 * Returns MibewAPI object
	 *
	 * @param string $interaction_type A name of the interaction type
	 * @return MibeAPI object
	 * @throws MibewAPIException
	 */
	public static function getAPI($interaction_type) {
		$class_name = "MibewAPI".  ucfirst($interaction_type) . "Interaction";
		if (! class_exists($class_name)) {
			throw new MibewAPIException(
				"Wrong interaction type",
				MibewAPIException::WRONG_INTERACTION_TYPE
			);
		}
		if (empty(self::$interactions[$interaction_type])) {
			self::$interactions[$interaction_type] = new self(new $class_name());
		}
		return self::$interactions[$interaction_type];
	}

	/**
	 * Class constructor
	 *
	 * @param MibewAPIInteraction $interaction_type Interaction type object
	 */
	protected function __construct(MibewAPIInteraction $interaction_type) {
		$this->interaction = $interaction_type;
	}

	/**
	 * Validate package
	 *
	 * @param array $package Package array. See Mibew API for details.
	 * @param array $trusted_signatures Array of trusted signatures.
	 * @throws MibewAPIException
	 */
	public function checkPackage($package, $trusted_signatures) {
		// Check signature
		if (! isset($package['signature'])) {
			throw new MibewAPIException(
				"Package signature is empty",
				MibewAPIException::EMPTY_SIGNATURE
			);
		}
		if (! in_array($package['signature'], $trusted_signatures)) {
			throw new MibewAPIException(
				"Package signed with untrusted signature",
				MibewAPIException::UNTRUSTED_SIGNATURE
			);
		}

		// Check protocol
		if (empty($package['proto'])) {
			throw new MibewAPIException(
				"Package protocol is empty",
				MibewAPIException::EMPTY_PROTOCOL
			);
		}
		if ($package['proto'] != self::PROTOCOL_VERSION) {
			throw new MibewAPIException(
				"Wrong package protocol version '{$package['proto']}'",
				MibewAPIException::WRONG_PROTOCOL_VERSION
			);
		}

		// Check async flag
		if (! isset($package['async'])) {
			throw new MibewAPIException(
				"'async' flag is missed",
				MibewAPIException::ASYNC_FLAG_MISSED
			);
		}
		if (! is_bool($package['async'])) {
			throw new MibewAPIException(
				"Wrong 'async' flag value",
				MibewAPIException::WRONG_ASYNC_FLAG_VALUE
			);
		}

		// Package must have at least one request
		if (empty($package['requests'])) {
			throw new MibewAPIException(
				"Empty requests set",
				MibewAPIException::EMPTY_REQUESTS
			);
		}
		// Check requests in package
		foreach ($package['requests'] as $request) {
			$this->checkRequest($request);
		}
	}

	/**
	 * Validate request
	 *
	 * @param array $request Request array. See Mibew API for details.
	 * @throws MibewAPIException
	 */
	public function checkRequest($request) {
		// Check token
		if (empty($request['token'])) {
			throw new MibewAPIException(
				"Empty request token",
				MibewAPIException::EMPTY_TOKEN
			);
		}
		// Request must have at least one function
		if (empty($request['functions'])) {
			throw new MibewAPIException(
				"Empty functions set",
				MibewAPIException::EMPTY_FUNCTIONS
			);
		}
		// Check functions in request
		foreach ($request['functions'] as $function) {
			$this->checkFunction($function);
		}
	}

	/**
	 * Validate function
	 *
	 * @param array $function Function array. See Mibew API for details.
	 * @param boolean $filter_reserved_functions Determine if function name must not be in
	 * reserved list
	 * @throws MibewAPIException
	 */
	public function checkFunction($function, $filter_reserved_functions = false) {
		// Check function name
		if (empty($function['function'])) {
			throw new MibewAPIException(
				'Cannot call for function with empty name',
				MibewAPIException::EMPTY_FUNCTION_NAME
			);
		}
		if ($filter_reserved_functions) {
			if (in_array(
				$function['function'],
				$this->interaction->reservedFunctionNames
			)) {
				throw new MibewAPIException(
					"'{$function['function']}' is reserved function name",
					MibewAPIException::FUNCTION_NAME_RESERVED
				);
			}
		}
		// Check function's arguments
		if (empty($function['arguments'])) {
			throw new MibewAPIException(
				"There are no arguments in '{$function['function']}' function",
				MibewAPIException::EMPTY_ARGUMENTS
			);
		}
		if (! is_array($function['arguments'])) {
			throw new MibewAPIException(
				"Arguments must be an array",
				MibewAPIException::WRONG_ARGUMENTS_TYPE
			);
		}
		$unset_arguments = array_diff(
			$this->interaction->getObligatoryArguments($function['function']),
			array_keys($function['arguments'])
		);
		if (! empty($unset_arguments)) {
			throw new MibewAPIException(
				"Arguments '" . implode("', '", $unset_arguments) . "' must be set",
				MibewAPIException::OBLIGATORY_ARGUMENTS_MISSED
			);
		}
	}

	/**
	 * Encodes package
	 *
	 * @param array $requests Requests array. See Mibew API for details.
	 * @param string $signature Sender signature.
	 * @param boolean $async true for asynchronous request and false for synchronous request
	 * @return string Ready for transfer encoded package
	 */
	public function encodePackage($requests, $signature, $async) {
		$package = array();
		$package['signature'] = $signature;
		$package['proto'] = self::PROTOCOL_VERSION;
		$package['async'] = $async;
		$package['requests'] = $requests;
		return urlencode(json_encode($package));
	}

	/**
	 * Decodes package and validate package structure
	 *
	 * @param string $package Encoded package
	 * @param array $trusted_signatures List of trusted signatures
	 * @return array Decoded package array. See Mibew API for details.
	 * @throws MibewAPIException
	 */
	public function decodePackage($package, $trusted_signatures) {
		$decoded_package = urldecode($package);
		// JSON regular expression
		$pcre_regex = '/
		(?(DEFINE)
		(?<number>   -? (?= [1-9]|0(?!\d) ) \d+ (\.\d+)? ([eE] [+-]? \d+)? )
		(?<boolean>   true | false | null )
		(?<string>    " ([^"\\\\]* | \\\\ ["\\\\bfnrt\/] | \\\\ u [0-9a-f]{4} )* " )
		(?<array>     \[  (?:  (?&json)  (?: , (?&json)  )*  )?  \s* \] )
		(?<pair>      \s* (?&string) \s* : (?&json)  )
		(?<object>    \{  (?:  (?&pair)  (?: , (?&pair)  )*  )?  \s* \} )
		(?<json>   \s* (?: (?&number) | (?&boolean) | (?&string) | (?&array) | (?&object) ) \s* )
		)
		\A (?&json) \Z
		/six';
		// Check JSON
		if (!preg_match($pcre_regex, $decoded_package)) {
			// Not valid JSON
			throw new MibewAPIException(
				"Package have not valid json structure",
				MibewAPIException::NOT_VALID_JSON
			);
		}
		$decoded_package = json_decode($decoded_package, true);
		$this->checkPackage($decoded_package, $trusted_signatures);
		return $decoded_package;
	}

	/**
	 * Builds result package
	 *
	 * @param string $token Token of the result package
	 * @param array $result_arguments Arguments of result function
	 * @return array Result package
	 */
	public function buildResult($token, $result_arguments) {
		$arguments = $result_arguments + $this->interaction->getDefaultObligatoryArguments('result');
		$package = array(
			'token' => $token,
			'functions' => array(
				array(
					'function' => 'result',
					'arguments' => $arguments
				)
			)
		);
		return $package;
	}

	/**
	 * Search 'result' function in $function_list. If request contains more than one result
	 * functions throws an MibewAPIException
	 *
	 * @param array $functions_list Array of functions. See MibewAPI for function structure
	 * details
	 * @param mixed $existance Control existance of the 'result' function in request.
	 * Use boolean true if 'result' function must exists in request, boolean false if must not
	 * and null if it doesn't matter.
	 * @return mixed Function array if 'result' function found and NULL otherwise
	 * @throws MibewAPIException
	 */
	public function getResultFunction ($functions_list, $existence = null) {
		$result_function = null;
		// Try to find 'result' function
		foreach ($functions_list as $function) {
			if ($function['function'] == 'result') {
				if (! is_null($result_function)) {
					// Another 'result' function found
					throw new MibewAPIException(
						"Function 'result' already exists in request",
						MibewAPIException::RESULT_FUNCTION_ALREADY_EXISTS
					);
				}
				// First 'result' function found
				$result_function = $function;
			}
		}
		if ($existence === true && is_null($result_function)) {
			// 'result' function must present in request
			throw new MibewAPIException(
				"There is no 'result' function in request",
				MibewAPIException::NO_RESULT_FUNCTION
			);
		}
		if ($existence === false && !is_null($result_function)) {
			// 'result' function must not present in request
			throw new MibewAPIException(
				"There is 'result' function in request",
				MibewAPIException::RESULT_FUNCTION_EXISTS
			);
		}
		return $result_function;
	}
}

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
		// Store function results in execution context
		$this->functions_results[] = $results;
	}

}

/**
 * Encapsulates interaction type
 */
abstract class MibewAPIInteraction {
	/**
	 * Defines obligatory arguments and default values for them
	 *
	 * @var array Keys of the array are function names ('*' for all functions). Values are arrays of obligatory
	 * arguments with key for name of an argument and value for default value.
	 *
	 * For example:
	 * <code>
	 * protected $obligatoryArguments = array(
	 *		'*' => array(                          // Obligatory arguments for all functions are
	 *			'return' => array(),               // 'return' with array() by default and
	 *			'references' => array()            // 'references' with array() by default
	 *		),
	 *		'result' => array(                     // There is an additional argument for the result function
	 *			'errorCode' => 0                   // This is 'error_code' with 0 by default
	 *		)
	 * );
	 * </code>
	 */
	protected $obligatoryArguments = array();

	/**
	 * Reserved function's names
	 *
	 * Defines reserved(system) function's names described in the Mibew API.
	 * @var array
	 */
	public $reservedFunctionNames = array();

	/**
	 * Returns obligatory arguments for the $function_name function
	 *
	 * @param string $function_name Function name
	 * @return array An array of obligatory arguments
	 */
	public function getObligatoryArguments($function_name) {
		$obligatory_arguments = array();
		// Add obligatory for all functions arguments
		if (! empty($this->obligatoryArguments['*'])) {
			$obligatory_arguments = array_merge(
				$obligatory_arguments,
				array_keys($this->obligatoryArguments['*'])
			);
		}
		// Add obligatory arguments for given function
		if (! empty($this->obligatoryArguments[$function_name])) {
			$obligatory_arguments = array_merge(
				$obligatory_arguments,
				array_keys($this->obligatoryArguments[$function_name])
			);
		}
		return array_unique($obligatory_arguments);
	}

	/**
	 * Returns default values of obligatory arguments for the $function_name function
	 *
	 * @param string $function_name Function name
	 * @return array Associative array with keys are obligatory arguments and values are default
	 * values of them
	 */
	public function getDefaultObligatoryArguments($function_name) {
		$obligatory_arguments = array();
		// Add obligatory for all functions arguments
		if (! empty($this->obligatoryArguments['*'])) {
			$obligatory_arguments = array_merge($obligatory_arguments, $this->obligatoryArguments['*']);
		}
		// Add obligatory arguments for given function
		if (! empty($this->obligatoryArguments[$function_name])) {
			$obligatory_arguments = array_merge($obligatory_arguments, $this->obligatoryArguments[$function_name]);
		}
		return $obligatory_arguments;
	}
}

/**
 * Implements Base Mibew Interaction
 */
class MibewAPIBaseInteraction extends MibewAPIInteraction {
	/**
	 * Defines obligatory arguments and default values for them
	 * @var array
	 * @see MibewAPIInteraction::$obligatoryArgumnents
	 */
	protected $obligatoryArguments = array(
		'*' => array(
			'references' => array(),
			'return' => array()
		)
	);

	/**
	 * Reserved function's names
	 * @var array
	 * @see MibewAPIInteraction::$reservedFunctionNames
	 */
	public $reservedFunctionNames = array(
		'result'
	);
}

/**
 * Implements Mibew Core - Mibew Chat Window interaction
 */
class MibewAPIWindowInteraction extends MibewAPIInteraction {
	/**
	 * Defines obligatory arguments and default values for them
	 * @var array
	 * @see MibewAPIInteraction::$obligatoryArgumnents
	 */
	protected $obligatoryArguments = array(
		'*' => array(
			'references' => array(),
			'return' => array()
		)
	);

	/**
	 * Reserved function's names
	 * @var array
	 * @see MibewAPIInteraction::$reservedFunctionNames
	 */
	public $reservedFunctionNames = array(
		'result'
	);
}

/**
 * Mibew API Exception class.
 */
class MibewAPIException extends Exception {
	/**
	 * Async flag is missed.
	 */
	const ASYNC_FLAG_MISSED = 1;
	/**
	 * There are no arguments in function
	 */
	const EMPTY_ARGUMENTS = 2;
	/**
	 * Cannot call for function with empty name
	 */
	const EMPTY_FUNCTION_NAME = 3;
	/**
	 * Functions set is empty
	 */
	const EMPTY_FUNCTIONS = 4;
	/**
	 * Package protocol is empty
	 */
	const EMPTY_PROTOCOL = 5;
	/**
	 * Requests set is empty
	 */
	const EMPTY_REQUESTS = 6;
	/**
	 * Package signature is empty
	 */
	const EMPTY_SIGNATURE = 7;
	/**
	 * Request token is empty
	 */
	const EMPTY_TOKEN = 8;
	/**
	 * Wrong reference. Reference variable is empty
	 */
	const EMPTY_VARIABLE_IN_REFERENCE = 9;
	/**
	 * This function name is reserved
	 */
	const FUNCTION_NAME_RESERVED = 10;
	/**
	 * There is no result function
	 */
	const NO_RESULT_FUNCTION = 11;
	/**
	 * Package have not valid JSON structure
	 */
	const NOT_VALID_JSON = 12;
	/**
	 * Some of the function's obligatory arguments are missed
	 */
	const OBLIGATORY_ARGUMENTS_MISSED = 13;
	/**
	 * Request contains more than one result functions
	 */
	const RESULT_FUNCTION_ALREADY_EXISTS = 14;
	/**
	 * There is 'result' function in request
	 */
	const RESULT_FUNCTION_EXISTS = 15;
	/**
	 * Package signed with untrusted signature
	 */
	const UNTRUSTED_SIGNATURE = 16;
	/**
	 * Wrong reference. Variable is undefined in functions results
	 */
	const VARIABLE_IS_UNDEFINED_IN_REFERENCE = 17;
	/**
	 * Variable is undefined in function's results
	 */
	const VARIABLE_IS_UNDEFINED_IN_RESULT = 18;
	/**
	 * Arguments must be an array
	 */
	const WRONG_ARGUMENTS_TYPE = 19;
	/**
	 * Async flag value is wrong
	 */
	const WRONG_ASYNC_FLAG_VALUE = 20;
	/**
	 * Wrong reference. Function with this number does not call yet
	 */
	const WRONG_FUNCTION_NUM_IN_REFERENCE = 21;
	/**
	 * Wrong interaction type
	 */
	const WRONG_INTERACTION_TYPE = 22;
	/**
	 * Wrong package protocol version
	 */
	const WRONG_PROTOCOL_VERSION = 23;
}

?>