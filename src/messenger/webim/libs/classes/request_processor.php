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
 * Implements abstract class for request processing
 *
 * Register events (see RequestProcessor::registerEvents() for details):
 *  - <eventPrefix>RequestReceived
 *  - <eventPrefix>ReceiveRequestError
 *  - <eventPrefix>ResponseReceived
 *  - <eventPrefix>CallError
 *  - <eventPrefix>FunctionCall
 *
 * <eventPrefix> variable specifies in RequestProcessor::__construct()
 *
 * @see RequestProcessor::__construct()
 * @see RequestProcessor::registerEvents()
 */
abstract class RequestProcessor {

	/**
	 * Instance of the MibewAPI class
	 * @var MibewAPI
	 */
	protected $mibewAPI = null;

	/**
	 * Prefix that uses for all registered by the class events.
	 * @var string
	 */
	protected $eventPrefix = '';

	/**
	 * Array of the responses packages
	 * @var array
	 */
	protected $responses = array();

	/**
	 * Array of configurations
	 * @var array
	 */
	protected $config = array();

	/**
	 * Class constructor
	 *
	 * @param type $config Configuration data.
	 * It must contains following keys:
	 *  - 'signature': Use for verification sender
	 *  - 'trusted_signatures': array of trusted signatures. Uses for identify another
	 *    side of interaction.
	 * And may contains following (if not default values will be used)
	 *  - 'event_prefix': prefix that uses for all registered by the class events. The default value is the class
	 *    name with first character in lower case
	 */
	public function __construct($config) {
		// Check signature
		if (! isset($config['signature'])) {
			trigger_error("Signature is not specified", E_USER_ERROR);
		}

		// Check trusted signatures
		if (! isset($config['trusted_signatures'])) {
			trigger_error("Trusted signatures is not specified", E_USER_ERROR);
		}

		// Get an instance of the MibewAPI class
		$this->mibewAPI = $this->getMibewAPIInstance();

		// Get class name and prefix for events and etc.
		$class_name = get_class($this);
		$this->eventPrefix = empty($config['event_prefix'])
			? strtolower(substr($class_name, 0, 1)) . substr($class_name, 1)
			: $config['event_prefix'];

		// Store config
		$this->config = $config;

		// Register Events
		$this->registerEvents();
	}

	/**
	 * Proccess received packages
	 *
	 * On any error function returns only boolean false. To handle error add listener to the
	 * "<eventPrefix>ReceiveRequestError" event.
	 *
	 * @param string $package Encoded package
	 * @return boolean true if request processed succussfully or false on failure
	 */
	public function receiveRequest($package){
		$dispatcher = EventDispatcher::getInstance();
		// Try to handle request
		try {
			// Decode package
			$request_package = $this->mibewAPI->decodePackage(
				$package,
				$this->config['trusted_signatures']
			);

			// Trigger request received event
			$vars = array('package' => $request_package);
			$dispatcher->triggerEvent(
				$this->eventPrefix . 'RequestReceived',
				$vars
			);
			$package = $vars['package'];

			// Process requests in package
			// Clear responses
			$this->responses = array();
			foreach ($package['requests'] as $request) {
				// Try to load callback function for this token
				$callback = $this->loadCallback($request['token']);
				$need_result = ! is_null($callback);
				$arguments = $this->processRequest($request, $need_result);

				if ($need_result) {
					// There is callback function
					// TODO: Think about callback functions nature
					$object = $callback['object'];
					$method = $callback['method'];
					$object->$method($arguments);
				} else {
					// There is no callback function
					$this->responses[] = $this->mibewAPI->buildResult(
						$request['token'],
						$arguments
					);
				}
			}

			if ($request_package['async']) {
				$this->sendAsyncResponses($this->responses);
			} else {
				$this->sendSyncResponses($this->responses);
			}

			// Output response
		} catch (Exception $e) {
			// Something went wrong. Trigger error event
			$vars = array('exception' => $e);
			$dispatcher->triggerEvent($this->eventPrefix . 'RequestError', $vars);
			return false;
		}
		return true;
	}

	/**
	 * Call functions at the other side
	 *
	 * On any error function returns only boolean false. To handle error add listener to the
	 * "<eventPrefix>CallError" event.
	 *
	 * @param array $functions Array of functions. See Mibew API for details.
	 * @param boolean $async True for asynchronous requests and false for synchronous request
	 * @param mixed $callback callback array or null for synchronous requests.
	 * @return mixed request result or boolean false on failure.
	 */
	public function call($functions, $async, $callback = null) {
		// Get an instance of the EventDispatcher class
		$dispatcher = EventDispatcher::getInstance();
		// Try to call function at Other side
		try {
			// Check functions to call
			if (! is_array($functions)) {
				throw new RequestProcessorException(
					'#1 argument must be an array!',
					RequestProcessorException::WRONG_ARGUMENTS
				);
			}
			foreach ($functions as $function) {
				$this->mibewAPI->checkFunction($function, true);
			}

			// Create request
			$token = md5(microtime() . rand());
			$request = array(
				'token' => $token,
				'functions' => $functions
			);

			if ($async) {
				// TODO: Think about callbacks
				// TODO: May be add exception if $callback = null

				// Store callback
				$this->saveCallback($token, $callback);

				// Send asynchronous request
				$this->sendAsyncRequest($request);
				return true;
			}

			// Send synchronous request
			$response_package = $this->sendSyncRequest($request);

			// Trigger response received event
			$vars = array('package' => $response_package);
			$dispatcher->triggerEvent($this->eventPrefix . 'ResponseReceived', $vars);

			// Process requests in response
			$result = null;
			foreach ($response_package['requests'] as $request) {
				// Use only response with token equals to request token. Ignore other packages.
				// TODO: May be not ignore other packages
				if ($request['token'] == $token) {
					$result = $this->processRequest($request, true);
				}
			}

			if (is_null($result)) {
				throw new RequestProcessorException(
					"There is no 'result' function in response",
					RequestProcessorException::NO_RESULT_FUNCTION
				);
			}
		} catch (Exception $e) {
			// Trigger error event
			$vars = array('exception' => $e);
			$dispatcher->triggerEvent($this->eventPrefix . "CallError", $vars);
			return false;
		}
		return $result;
	}

	/**
	 * Register events
	 *
	 * Registered Events:
	 *
	 * 1. "<eventPrefix>RequestReceived" - triggers when request decoded and validate
	 * successfully, before execution functions from request.
	 *
	 * An associative array passed to event handler have following keys:
	 *  - 'package' : decoded and validated package array. See Mibew API for details of the
	 *    package structure
	 *
	 *
	 * 2. "<eventPrefix>ReceiveRequestError" - triggers when error occurs during received
	 * request processing.
	 *
	 * An associative array passed to event handler have following keys:
	 *  - 'exception' : an object of Exception (or inherited) class related to occurred error.
	 *
	 *
	 * 3. "<eventPrefix>ResponseReceived" - triggers when request sent successfully, and
	 * response received.
	 *
	 * An associative array passed to event handler have following keys:
	 *  - 'package' : decoded and validated response package array. See Mibew API for details of
	 *    the package structure.
	 *
	 *
	 * 4. "<eventPrefix>CallError" - triggers when error occurs in
	 * call() method.
	 *
	 * An associative array passed to event handler have following keys:
	 *  - 'exception' : an object of Exception (or inherited) class related to occurred error.
	 *
	 *
	 * 5. "<eventPrefix>FunctionCall" - triggers when function from request calls.
	 *
	 * An associative array passed to event handler is 'function' array. See Mibew API for
	 * detail of the 'function' array structure.
	 *
	 * If function wants to return some results, it should add results to the 'results' element
	 * of the function array.
	 *
	 * Example of the event handler:
	 * <code>
	 * public function callHandler(&$function) {
	 *	if ($function['function'] == 'microtime') {
	 *		$as_float = empty($function['arguments']['as_float'])
	 *			? false
	 *			: $function['arguments']['as_float'];
	 *		$function['results']['time'] = microtime($as_float);
	 *	}
	 * }
	 * </code>
	 */
	protected function registerEvents() {
		$dispatcher = EventDispatcher::getInstance();
		$dispatcher->registerEvent($this->eventPrefix . 'RequestReceived');
		$dispatcher->registerEvent($this->eventPrefix . 'RequestError');
		$dispatcher->registerEvent($this->eventPrefix . 'ResponseReceived');
		$dispatcher->registerEvent($this->eventPrefix . 'CallError');
		$dispatcher->registerEvent($this->eventPrefix . 'FunctionCall');
	}

	/**
	 * Process request
	 *
	 * @param array $request 'Requests' array. See Mibew API for details.
	 * @param mixed $result_function Control existance of the 'result' function in request.
	 * Use boolean true if 'result' function must exists in request, boolean false if must not
	 * and null if it doesn't matter.
	 * @return array Array of requests results.
	 */
	protected function processRequest($request, $result_function = null) {
		$context = new MibewAPIExecutionContext();

		// Get result functions
		$result_function = $this->mibewAPI->getResultFunction(
			$request['functions'],
			$result_function
		);

		// Request contains not only result function
		if (! is_null($result_function) && count($request['functions']) > 1) {
			trigger_error(
				'Request contains not only result function',
				E_USER_WARNING
			);
		}

		if (is_null($result_function)) {
			// Execute functions
			foreach ($request['functions'] as $function) {
				if (! $this->processFunction($function, $context)) {
					// Stop if errorCode is set and not equals to 0
					break;
				}
			}
			return $context->getResults();
		} else {
			// Return result
			return $result_function['arguments'];
		}
	}

	/**
	 * Process function
	 *
	 * @param array $function 'Function' array. See Mibew API for details
	 * @param MibewAPIExecutionContext &$context Execution context
	 * @return boolean lase if function returns errorCode and errorCode differs from 0.
	 */
	protected function processFunction($function, MibewAPIExecutionContext &$context) {
		// Get function arguments with replaced references
		$arguments = $context->getArgumentsList($function);

		$call_vars = array(
			'function' => $function['function'],
			'arguments' => $arguments,
			'results' => array()
		);

		// Call processor function
		$this->processorCall($call_vars);

		// Trigger FunctionCall event
		$dispatcher = EventDispatcher::getInstance();
		$dispatcher->triggerEvent($this->eventPrefix . 'FunctionCall', $call_vars);

		// Get results
		$results = $call_vars['results'];

		// Add function results to execution context
		$context->storeFunctionResults($function, $results);

		// Check errorCode
		return empty($results['errorCode']);
	}

	/**
	 * Sends synchronous request
	 *
	 * @param array $request The 'request' array. See Mibew API for details
	 * @return mixed response array or boolean false on failure
	 */
	protected function sendSyncRequest($request) {
		trigger_error('Method sendSyncRequest does not implement!', E_USER_WARNING);
	}

	/**
	 * Sends asynchronous request
	 *
	 * @param array $request The 'request' array. See Mibew API for details
	 * @return boolean true on success or false on failure
	 */
	protected function sendAsyncRequest($request) {
		trigger_error('Method sendAsyncRequest does not implement!', E_USER_WARNING);
	}

	/**
	 * Sends synchronous responses
	 *
	 * @param array $responses An array of the 'Request' arrays. See Mibew API for details
	 */
	protected function sendSyncResponses($responses) {
		trigger_error('Method sendSyncResponses does not implement!', E_USER_WARNING);
	}

	/**
	 * Sends asynchronous responses
	 *
	 * @param array $responses An array of the 'Request' arrays. See Mibew API for details
	 */
	protected function sendAsyncResponses($responses) {
		trigger_error('Method sendAsyncResponses does not implement!', E_USER_WARNING);
	}

	/**
	 * Creates and returns an instance of the MibewAPI class.
	 *
	 * @return MibewAPI
	 */
	protected abstract function getMibewAPIInstance();

	/**
	 * Stores callback function
	 *
	 * @param string $token Request token
	 * @param array $callback Callback function array
	 */
	protected abstract function saveCallback($token, $callback);

	/**
	 * Loads callback function
	 *
	 * @param string $token Token of the request related to callback function
	 * @return mixed callback function array or null if callback function not exists
	 */
	protected abstract function loadCallback($token);

	/**
	 * Dispatcher of the functions, provided by the RequestProcessor (or inherited) classes as an external API.
	 *
	 * It calls before '<eventPrefix>FunctionCall' event triggers.
	 *
	 * @param array &$func Function array equals to array, passed to the '<eventPrefix>FunctionCall' event.
	 * @see RequestProcessor::registerEvents()
	 */
	protected abstract function processorCall(&$func);
}

class RequestProcessorException extends Exception {
	/**
	 * Result function is absent
	 */
	const NO_RESULT_FUNCTION = 1;
	/**
	 * Wrong function arguments
	 */
	const WRONG_ARGUMENTS = 2;
}

?>