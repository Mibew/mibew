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

use Mibew\Database;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base class for all request processors that interact with JavaScript
 * applications at the client side.
 */
abstract class ClientSideProcessor extends AbstractProcessor
{

    /**
     * Call function at client side
     *
     * WARNING: This processor does not support synchronous requests.
     *
     * @param array $functions Array of functions to call. See Mibew API for
     * details.
     * @param boolean $async True for asynchronous requests and false for
     *   synchronous request
     * @param array|null $callback callback array for synchronous requests.
     * @return mixed request result or boolean false on failure.
     * @see \Mibew\RequestProcessor\AbstractProcessor
     */
    public function call($functions, $async, $callback = null)
    {
        if (!$async) {
            trigger_error(
                'Synchronous requests are not supported.',
                E_USER_WARNING
            );

            return false;
        }

        return parent::call($functions, true, $callback);
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
        $resp = new Response($this->mibewAPI->encodePackage(
            $responses,
            $this->config['signature'],
            true
        ));

        $resp->headers->set('Content-type', 'text/plain');
        $resp->setCharset('UTF-8');

        return $resp;
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

    /**
     * Extrats a package from an request.
     *
     * @param Request $request The request to extract package from.
     * @return string Encoded package.
     */
    protected function extractPackage(Request $request)
    {
        return $request->request->get('data');
    }
}
