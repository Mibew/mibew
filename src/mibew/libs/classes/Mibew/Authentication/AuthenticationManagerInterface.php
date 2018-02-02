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

namespace Mibew\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base interface for all authentication managers.
 */
interface AuthenticationManagerInterface
{
    /**
     * Set the current operator using request to extract him.
     *
     * @param Request $request Incoming request.
     * @return boolean true if an operator was extracted from the request and
     *   false otherwise.
     */
    public function setOperatorFromRequest(Request $request);

    /**
     * Attaches some data to the response that are needed to identify operator
     * in the next requests.
     *
     * @param Response $response A response which will be returned to the client.
     */
    public function attachOperatorToResponse(Response $response);

    /**
     * Returns the current operator.
     *
     * @return array Operator's data
     */
    public function getOperator();

    /**
     * Sets the current operator.
     *
     * @param array $operator The current operator's data.
     */
    public function setOperator($operator);

    /**
     * Login specified operator into the system and use him as the current
     * operator.
     *
     * @param array $operator An operator to login.
     */
    public function loginOperator($operator, $remember);

    /**
     * Logout the current operator from the system.
     */
    public function logoutOperator();
}
