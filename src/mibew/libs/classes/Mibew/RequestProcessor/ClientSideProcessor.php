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

namespace Mibew\RequestProcessor;

// Import namespaces and classes of the core
use Mibew\Database;

/**
 * Base class for all request processors that interact with JavaScript
 * applications at the client side.
 */
abstract class ClientSideProcessor extends Processor
{

    /**
     * Call function at client side
     *
     * @param array $functions Array of functions to call. See Mibew API for
     * details.
     * @param array|null $callback callback array for synchronous requests.
     * @return mixed request result or boolean false on failure.
     */
    public function call($functions, $callback = null)
    {
        return parent::call($functions, true, $callback);
    }

    /**
     * Sends asynchronous responses
     *
     * @param array $responses An array of the 'Request' arrays. See Mibew API
     * for details
     */
    protected function sendAsyncResponses($responses)
    {
        header("Content-type: text/plain; charset=UTF-8");
        echo($this->mibewAPI->encodePackage(
            $responses,
            $this->config['signature'],
            true
        ));
    }

    /**
     * Add request to client side to the buffer. Use database as storage.
     * Override this method if you want to use another storage and/or save logic.
     *
     * @param String $key Request key. Use to load request from buffer.
     * @param $request Request array.
     */
    protected function addRequestToBuffer($key, $request)
    {
        // Save request to database
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO {requestbuffer} (request, requestkey) VALUES (:request, :key)",
            array(':request' => serialize($request), ':key' => md5($key))
        );
    }

    /**
     * Load stored requests to the client side
     *
     * @param String $key Request key
     * @return array Array of requests with given key
     */
    protected function getRequestsFromBuffer($key)
    {
        $db = Database::getInstance();

        $key = md5($key);

        // Get requests from database
        $requests = $db->query(
            "SELECT request FROM {requestbuffer} WHERE requestkey = :key",
            array(':key' => $key),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );
        // Remove got requests from database
        $db->query(
            "DELETE FROM {requestbuffer} WHERE requestkey = :key",
            array(':key' => $key)
        );
        // Unserialize requests
        $result = array();
        foreach ($requests as $request_info) {
            $result[] = unserialize($request_info['request']);
        }

        return $result;
    }
}
