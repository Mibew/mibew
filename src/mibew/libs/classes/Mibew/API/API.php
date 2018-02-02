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
 * Implements Mibew API specification version 1.0
 *
 * @todo May be use regular methods instead of static one
 */
class API
{
    /**
     * Version of the MIBEW API protocol implemented by the class
     */
    const PROTOCOL_VERSION = '1.0';

    /**
     * Array of \Mibew\API\API objects
     *
     * @var array
     */
    protected static $interactions = array();

    /**
     * An object that encapsulates type of the interaction
     *
     * @var \Mibew\API\Interaction
     */
    protected $interaction = null;

    /**
     * Returns \Mibew\API\API object
     *
     * @param string $class_name A name of the interaction type class
     * @return MibeAPI object
     * @throws \Mibew\API\APIException
     */
    public static function getAPI($class_name)
    {
        if (!class_exists($class_name)) {
            throw new APIException(
                "Wrong interaction type",
                APIException::WRONG_INTERACTION_TYPE
            );
        }
        if (empty(self::$interactions[$class_name])) {
            self::$interactions[$class_name] = new self(new $class_name());
        }

        return self::$interactions[$class_name];
    }

    /**
     * Validate package
     *
     * @param array $package Package array. See Mibew API for details.
     * @param array $trusted_signatures Array of trusted signatures.
     * @throws \Mibew\API\APIException
     */
    public function checkPackage($package, $trusted_signatures)
    {
        // Check signature
        if (!isset($package['signature'])) {
            throw new APIException(
                "Package signature is empty",
                APIException::EMPTY_SIGNATURE
            );
        }
        if (!in_array($package['signature'], $trusted_signatures)) {
            throw new APIException(
                "Package signed with untrusted signature",
                APIException::UNTRUSTED_SIGNATURE
            );
        }

        // Check protocol
        if (empty($package['proto'])) {
            throw new APIException(
                "Package protocol is empty",
                APIException::EMPTY_PROTOCOL
            );
        }
        if ($package['proto'] != self::PROTOCOL_VERSION) {
            throw new APIException(
                "Wrong package protocol version '{$package['proto']}'",
                APIException::WRONG_PROTOCOL_VERSION
            );
        }

        // Check async flag
        if (!isset($package['async'])) {
            throw new APIException(
                "'async' flag is missed",
                APIException::ASYNC_FLAG_MISSED
            );
        }
        if (!is_bool($package['async'])) {
            throw new APIException(
                "Wrong 'async' flag value",
                APIException::WRONG_ASYNC_FLAG_VALUE
            );
        }

        // Package must have at least one request
        if (empty($package['requests'])) {
            throw new APIException(
                "Empty requests set",
                APIException::EMPTY_REQUESTS
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
     * @throws \Mibew\API\APIException
     */
    public function checkRequest($request)
    {
        // Check token
        if (empty($request['token'])) {
            throw new APIException(
                "Empty request token",
                APIException::EMPTY_TOKEN
            );
        }
        // Request must have at least one function
        if (empty($request['functions'])) {
            throw new APIException(
                "Empty functions set",
                APIException::EMPTY_FUNCTIONS
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
     * @param boolean $filter_reserved_functions Determine if function name must
     *   not be in reserved list
     * @throws \Mibew\API\APIException
     */
    public function checkFunction($function, $filter_reserved_functions = false)
    {
        // Check function name
        if (empty($function['function'])) {
            throw new APIException(
                'Cannot call for function with empty name',
                APIException::EMPTY_FUNCTION_NAME
            );
        }
        if ($filter_reserved_functions) {
            if (in_array($function['function'], $this->interaction->getReservedFunctionsNames())) {
                throw new APIException(
                    "'{$function['function']}' is reserved function name",
                    APIException::FUNCTION_NAME_RESERVED
                );
            }
        }
        // Check function's arguments
        if (empty($function['arguments'])) {
            throw new APIException(
                "There are no arguments in '{$function['function']}' function",
                APIException::EMPTY_ARGUMENTS
            );
        }
        if (!is_array($function['arguments'])) {
            throw new APIException(
                "Arguments must be an array",
                APIException::WRONG_ARGUMENTS_TYPE
            );
        }
        $unset_arguments = array_diff(
            $this->interaction->getMandatoryArguments($function['function']),
            array_keys($function['arguments'])
        );
        if (!empty($unset_arguments)) {
            throw new APIException(
                "Arguments '" . implode("', '", $unset_arguments) . "' must be set",
                APIException::MANDATORY_ARGUMENTS_MISSED
            );
        }
    }

    /**
     * Encodes package
     *
     * @param array $requests Requests array. See Mibew API for details.
     * @param string $signature Sender signature.
     * @param boolean $async true for asynchronous request and false for
     *   synchronous request
     * @return string Ready for transfer encoded package
     */
    public function encodePackage($requests, $signature, $async)
    {
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
     * @throws \Mibew\API\APIException
     */
    public function decodePackage($package, $trusted_signatures)
    {
        // Try to decode package
        $decoded_package = urldecode($package);
        $decoded_package = json_decode($decoded_package, true);

        // Check package
        $json_error_code = json_last_error();
        if ($json_error_code != JSON_ERROR_NONE) {
            // Not valid JSON
            throw new APIException(
                "Package have invalid json structure. JSON error code is '" . $json_error_code . "'",
                APIException::NOT_VALID_JSON
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
    public function buildResult($token, $result_arguments)
    {
        $arguments = $result_arguments + $this->interaction->getMandatoryArgumentsDefaults('result');
        $package = array(
            'token' => $token,
            'functions' => array(
                array(
                    'function' => 'result',
                    'arguments' => $arguments,
                ),
            ),
        );

        return $package;
    }

    /**
     * Search 'result' function in $function_list.
     *
     * If request contains more than one result the functions throws
     * an \Mibew\API\APIException.
     *
     * @param array $functions_list Array of functions. See Mibew API
     *   specification for function structure details.
     * @param mixed $existence Control existence of the 'result' function in
     *   request. Use boolean true if 'result' function must exists in request,
     *   boolean false if must not and null if it doesn't matter.
     * @return mixed Function array if 'result' function found and NULL
     * otherwise
     * @throws \Mibew\API\APIException
     */
    public function getResultFunction($functions_list, $existence = null)
    {
        $result_function = null;
        // Try to find 'result' function
        foreach ($functions_list as $function) {
            if ($function['function'] == 'result') {
                if (!is_null($result_function)) {
                    // Another 'result' function found
                    throw new APIException(
                        "Function 'result' already exists in request",
                        APIException::RESULT_FUNCTION_ALREADY_EXISTS
                    );
                }
                // First 'result' function found
                $result_function = $function;
            }
        }
        if ($existence === true && is_null($result_function)) {
            // 'result' function must present in request
            throw new APIException(
                "There is no 'result' function in request",
                APIException::NO_RESULT_FUNCTION
            );
        }
        if ($existence === false && !is_null($result_function)) {
            // 'result' function must not present in request
            throw new APIException(
                "There is 'result' function in request",
                APIException::RESULT_FUNCTION_EXISTS
            );
        }

        return $result_function;
    }

    /**
     * Class constructor
     *
     * @param \Mibew\API\Interaction $interaction Interaction type object
     */
    protected function __construct(Interaction\AbstractInteraction $interaction)
    {
        $this->interaction = $interaction;
    }
}
