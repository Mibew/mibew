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
 * Mibew API Exception class.
 */
class APIException extends \Exception
{
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
     * Some of the function's mandatory arguments are missed
     */
    const MANDATORY_ARGUMENTS_MISSED = 13;
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
