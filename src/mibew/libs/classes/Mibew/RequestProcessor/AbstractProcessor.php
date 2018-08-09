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

namespace Mibew\RequestProcessor;

// Import namespaces and classes of the core
use Mibew\Database;
use Mibew\EventDispatcher\EventDispatcher;
use Mibew\RequestProcessor\Exception\AbstractProcessorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Implements abstract class for request processing
 *
 * Following events can be triggered by the class:
 *  - <eventPrefix>RequestReceived
 *  - <eventPrefix>RequestError
 *  - <eventPrefix>ResponseReceived
 *  - <eventPrefix>CallError
 *  - <eventPrefix>FunctionCall
 *
 * <eventPrefix> variable specifies in
 * \Mibew\RequestProcessor\Processor::__construct()
 *
 *
 * Full description of triggered events:
 *
 * 1. "<eventPrefix>RequestReceived" - triggers when request decoded and
 * validate successfully, before execution functions from request.
 *
 * An associative array passed to event handler have following keys:
 *  - 'package' : decoded and validated package array. See Mibew API for details
 * of the package structure
 *
 *
 * 2. "<eventPrefix>RequestError" - triggers when error occurs during received
 * request processing.
 *
 * An associative array passed to event handler have following keys:
 *  - 'exception' : an object of Exception (or inherited) class related to
 * occurred error.
 *
 *
 * 3. "<eventPrefix>ResponseReceived" - triggers when request sent successfully,
 * and response received.
 *
 * An associative array passed to event handler have following keys:
 *  - 'package' : decoded and validated response package array. See Mibew API
 * for details of the package structure.
 *
 *
 * 4. "<eventPrefix>CallError" - triggers when error occurs in
 * call() method.
 *
 * An associative array passed to event handler have following keys:
 *  - 'exception' : an object of Exception (or inherited) class related to
 * occurred error.
 *
 *
 * 5. "<eventPrefix>FunctionCall" - triggers when function from request calls.
 *
 * An associative array passed to event handler contains the following items:
 *  - "request_processor": an instance of a subclass of
 *    {\Mibew\RequestProcessor\AbstractProcessor} which processes the current
 *    call.
 *  - "function": string, name of the function that was called.
 *  - "arguments": array, arguments that was passed to the function.
 *  - "results": array, results of the function execution.
 *
 * If function wants to return some results, it should add results to the
 * 'results' element of the function array.
 *
 * Example of the event handler:
 * <code>
 * public function callHandler(&$function) {
 *   if ($function['function'] == 'microtime') {
 *     $as_float = empty($function['arguments']['as_float'])
 *       ? false
 *       : $function['arguments']['as_float'];
 *     $function['results']['time'] = microtime($as_float);
 *   }
 * }
 * </code>
 *
 * Implements Singleton pattern for children classes using Late Static Bindings.
 *
 * @see \Mibew\RequestProcessor\AbstractProcessor::__construct()
 */
abstract class AbstractProcessor
{
    /**
     * Instance of the MibewAPI class
     *
     * @var \Mibew\API\API
     */
    protected $mibewAPI = null;

    /**
     * Prefix that uses for all events triggered by the class.
     *
     * @var string
     */
    protected $eventPrefix = '';

    /**
     * Array of the responses packages
     *
     * @var array
     */
    protected $responses = array();

    /**
     * Array of configurations
     *
     * @var array
     */
    protected $config = array();

    /**
     * An instance of the AbstractProcessor class
     *
     * @var \Mibew\RequestProcessor\AbstractProcessor
     */
    protected static $instance = null;

    /**
     * Return an instance of the AbstractProcessor class.
     *
     * @return \Mibew\RequestProcessor\AbstractProcessor
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Process received packages
     *
     * On any error function returns only boolean false. To handle error add
     * listener to the "<eventPrefix>RequestError" event.
     *
     * @param Request $request The request to handle.
     * @return Response|boolean A response object if request processed
     *   succussfully or boolean false on failure
     */
    public function handleRequest($request)
    {
        $package = $this->extractPackage($request);
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
                if ($package['async']) {
                    // Asynchronous request
                    // Try to load callback function for this token
                    $callback = $this->loadCallback($request['token']);

                    if (!is_null($callback)) {
                        // There is callback function. Try to get result arguments
                        $arguments = $this->processRequest($request, true);
                        $function = $callback['function'];
                        $arguments += empty($callback['arguments'])
                            ? array()
                            : $callback['arguments'];
                        call_user_func_array($function, array($arguments));
                        continue;
                    } else {
                        // Try to get result function
                        $result_function = $this->mibewAPI->getResultFunction($request['functions']);

                        if (!is_null($result_function)) {
                            // There is result function but no callback
                            continue;
                        }

                        // There is no result function
                        // Process request
                        $arguments = $this->processRequest($request, false);
                        // Send response
                        $this->responses[] = $this->mibewAPI->buildResult(
                            $request['token'],
                            $arguments
                        );
                    }
                } else {
                    // Synchronous request
                    // Process request
                    $arguments = $this->processRequest($request, false);
                    // Send response
                    $this->responses[] = $this->mibewAPI->buildResult(
                        $request['token'],
                        $arguments
                    );
                }
            }

            if (count($this->responses) != 0) {
                // Send responses
                if ($request_package['async']) {
                    return $this->buildAsyncResponses($this->responses);
                } else {
                    return $this->buildSyncResponses($this->responses);
                }
            }
        } catch (\Exception $e) {
            // Something went wrong. Trigger error event
            $vars = array('exception' => $e);
            $dispatcher->triggerEvent($this->eventPrefix . 'RequestError', $vars);
            trigger_error(
                sprintf(
                    'A request cannot be properly handled because of uncaught exception %s "%s" (%s:%u)',
                    get_class($e),
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                ),
                E_USER_WARNING
            );

            return false;
        }

        // Return an empty response
        return new Response();
    }

    /**
     * Call functions at the other side
     *
     * On any error function returns only boolean false. To handle error add
     * listener to the "<eventPrefix>CallError" event.
     *
     * @param array $functions Array of functions. See Mibew API for details.
     * @param boolean $async True for asynchronous requests and false for
     *   synchronous request
     * @param mixed $callback callback array or null for synchronous requests.
     * @return mixed request result or boolean false on failure.
     */
    public function call($functions, $async, $callback = null)
    {
        // Get an instance of the \Mibew\EventDispatcher class
        $dispatcher = EventDispatcher::getInstance();
        // Try to call function at Other side
        try {
            // Check functions to call
            if (!is_array($functions)) {
                throw new AbstractProcessorException(
                    '#1 argument must be an array!',
                    AbstractProcessorException::WRONG_ARGUMENTS
                );
            }
            foreach ($functions as $function) {
                $this->mibewAPI->checkFunction($function, true);
                $this->checkFunction($function);
            }

            // Create request
            // TODO: evaluate a possibility of using more secure method of the
            // generation of token
            $token = md5(microtime() . rand());
            $request = array(
                'token' => $token,
                'functions' => $functions
            );

            if ($async) {
                // Store callback
                if (!is_null($callback)) {
                    $this->saveCallback($token, $callback);
                }

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
        } catch (\Exception $e) {
            // Trigger error event
            $vars = array('exception' => $e);
            $dispatcher->triggerEvent($this->eventPrefix . "CallError", $vars);
            trigger_error(
                sprintf(
                    'Function call failed because of uncaught exception %s "%s" (%s:%u)',
                    get_class($e),
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                ),
                E_USER_WARNING
            );

            return false;
        }

        return $result;
    }

    /**
     * Class constructor
     *
     * @param type $config Configuration data.
     *   It must contains following keys:
     *    - 'signature': Use for verification sender
     *    - 'trusted_signatures': array of trusted signatures. Uses for identify
     *      another side of interaction.
     *   And may contains following (if not default values will be used)
     *    - 'event_prefix': prefix that uses for all events triggered by the
     *      class. The default value is the class name with first character in
     *      lower case
     */
    protected function __construct($config)
    {
        // Check signature
        if (!isset($config['signature'])) {
            trigger_error("Signature is not specified", E_USER_ERROR);
        }

        // Check trusted signatures
        if (!isset($config['trusted_signatures'])) {
            trigger_error("Trusted signatures is not specified", E_USER_ERROR);
        }

        // Get an instance of the MibewAPI class
        $this->mibewAPI = $this->getMibewAPIInstance();

        // Get class name and prefix for events and etc.
        $class_name_parts = explode('\\', get_class($this));
        $class_name = array_pop($class_name_parts);
        $this->eventPrefix = empty($config['event_prefix'])
            ? strtolower(substr($class_name, 0, 1)) . substr($class_name, 1)
            : $config['event_prefix'];

        // Store config
        $this->config = $config;
    }

    /**
     * Process request
     *
     * @param array $request 'Requests' array. See Mibew API for details.
     * @param mixed $result_function Control existence of the 'result' function
     *   in request. Use boolean true if 'result' function must exists in
     *   request, boolean false if must not and null if it doesn't matter.
     * @return array Array of requests results.
     */
    protected function processRequest($request, $result_function = null)
    {
        $context = new \Mibew\API\ExecutionContext();

        // Get result functions
        $result_function = $this->mibewAPI->getResultFunction(
            $request['functions'],
            $result_function
        );

        // Request contains not only result function
        if (!is_null($result_function) && count($request['functions']) > 1) {
            trigger_error(
                'Request contains not only result function',
                E_USER_WARNING
            );
        }

        if (is_null($result_function)) {
            // Execute functions
            foreach ($request['functions'] as $function) {
                if (!$this->processFunction($function, $context)) {
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
     * @param \Mibew\API\ExecutionContext &$context Execution context
     * @return boolean False if function returns errorCode and errorCode isn't 0
     *   and true otherwise.
     */
    protected function processFunction($function, \Mibew\API\ExecutionContext &$context)
    {
        // Get function arguments with replaced references
        $arguments = $context->getArgumentsList($function);

        $call_vars = array(
            'function' => $function['function'],
            'arguments' => $arguments,
            'results' => array(),
        );

        // Call processor function
        $this->processorCall($call_vars);

        // Trigger FunctionCall event
        $call_vars['request_processor'] = $this;
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
     * Stores callback function
     *
     * Callback is an associative array with following keys
     *  - 'function': function name to call.
     *  - 'arguments': additional arguments, that passed to the callback
     *    function.
     *
     * @param string $token Request token
     * @param array $callback Callback function array
     * @todo Create some unit tests
     */
    protected function saveCallback($token, $callback)
    {
        $db = Database::getInstance();

        $query = "INSERT INTO {requestcallback} ( "
            . "token, func, arguments "
            . ") VALUES ( "
            . ":token, :function, :arguments"
            . ")";

        $db->query(
            $query,
            array(
                ':token' => $token,
                ':function' => $callback['function'],
                ':arguments' => serialize($callback['arguments']),
            )
        );
    }

    /**
     * Loads callback function
     *
     * Callback is an associative array with following keys
     *  - 'function': function name to call.
     *  - 'arguments': additional arguments, that passed to the callback
     *    function.
     *
     * @param string $token Token of the request related to callback function
     * @return mixed callback function array or null if callback function not
     *   exists
     * @todo Create some unit tests
     */
    protected function loadCallback($token)
    {
        $db = Database::getInstance();
        $callback = $db->query(
            "SELECT * FROM {requestcallback} WHERE token = :token",
            array(':token' => $token),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );
        if (!$callback) {
            return null;
        }

        return array(
            'function' => $callback['func'],
            'arguments' => unserialize($callback['arguments']),
        );
    }

    /**
     * Dispatcher of the functions, provided by the RequestProcessor
     * (or inherited) classes as an external API.
     *
     * All API methods names starts with 'api' prefix.
     * It calls before '<eventPrefix>FunctionCall' event triggers.
     *
     * WARNING: API methods defined in that way are DEPRECATED. Please use
     * appropriate events instead.
     *
     * @param array &$func Function array equals to array, passed to the
     *   '<eventPrefix>FunctionCall' event.
     * @todo Create some unit tests
     */
    protected function processorCall(&$func)
    {
        $method_name = 'api' . ucfirst($func['function']);
        if (is_callable(array($this, $method_name))) {
            try {
                $func['results'] = $this->$method_name($func['arguments']);
            } catch (AbstractProcessorException $e) {
                $func['results'] = array(
                    'errorCode' => $e->getCode(),
                    'errorMessage' => $e->getMessage(),
                );
            }
        }
    }

    /**
     * Sends synchronous request
     *
     * @param array $request The 'request' array. See Mibew API for details
     * @return mixed response array or boolean false on failure
     */
    protected function sendSyncRequest($request)
    {
        trigger_error('Method sendSyncRequest is not implemented!', E_USER_WARNING);
    }

    /**
     * Sends asynchronous request
     *
     * @param array $request The 'request' array. See Mibew API for details
     * @return boolean true on success or false on failure
     */
    protected function sendAsyncRequest($request)
    {
        trigger_error('Method sendAsyncRequest is not implemented!', E_USER_WARNING);
    }

    /**
     * Builds synchronous responses
     *
     * @param array $responses An array of the 'Request' arrays. See Mibew API
     *   for details
     * @return Response A response object that is ready for sending to client.
     */
    protected function buildSyncResponses($responses)
    {
        trigger_error('Method buildSyncResponses is not implemented!', E_USER_WARNING);
    }

    /**
     * Builds asynchronous responses
     *
     * @param array $responses An array of the 'Request' arrays. See Mibew API
     *   for details
     * @return Response A response object that is ready for sending to client.
     */
    protected function buildAsyncResponses($responses)
    {
        trigger_error('Method buildAsyncResponses is not implemented!', E_USER_WARNING);
    }

    /**
     * Additional validation for functions that called via call method
     *
     * If something wrong function should throw an Exception.
     *
     * @param Array $function A Function array
     */
    protected function checkFunction($function)
    {
    }

    /**
     * Creates and returns an instance of the \Mibew\API\API class.
     *
     * @return \Mibew\API\API
     */
    abstract protected function getMibewAPIInstance();

    /**
     * Extrats a package from an request.
     *
     * @param Request $request The request to extract package from.
     * @return string Encoded package.
     */
    abstract protected function extractPackage(Request $request);
}
