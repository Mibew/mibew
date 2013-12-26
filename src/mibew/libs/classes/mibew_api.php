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
	 * @param string $class_name A name of the interaction type class
	 * @return MibeAPI object
	 * @throws MibewAPIException
	 */
	public static function getAPI($class_name) {
		if (! class_exists($class_name)) {
			throw new MibewAPIException(
				"Wrong interaction type",
				MibewAPIException::WRONG_INTERACTION_TYPE
			);
		}
		if (empty(self::$interactions[$class_name])) {
			self::$interactions[$class_name] = new self(new $class_name());
		}
		return self::$interactions[$class_name];
	}

	/**
	 * Class constructor
	 *
	 * @param MibewAPIInteraction $interaction Interaction type object
	 */
	protected function __construct(MibewAPIInteraction $interaction) {
		$this->interaction = $interaction;
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
		// Try to decode package
		$decoded_package = urldecode($package);
		$decoded_package = json_decode($decoded_package, true);

		// Check package
		$json_error_code = json_last_error();
		if ($json_error_code != JSON_ERROR_NONE) {
			// Not valid JSON
			throw new MibewAPIException(
				"Package have invalid json structure. " .
					"JSON error code is '" . $json_error_code . "'",
				MibewAPIException::NOT_VALID_JSON
			);
		}
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
		$arguments = $result_arguments + $this->interaction->getObligatoryArgumentsDefaults('result');
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