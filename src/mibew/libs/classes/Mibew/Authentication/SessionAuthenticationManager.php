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
 * Controls operator's authentication.
 *
 * This manager stores operator only within session and does not provide a way
 * to remember him.
 */
class SessionAuthenticationManager implements AuthenticationManagerInterface
{
    /**
     * Indicates if the operator is logged in.
     * @var boolean
     */
    protected $loggedIn = false;

    /**
     * Indicates if the current operator is logged out.
     * @var boolean
     */
    protected $loggedOut = false;

    /**
     * The current operator.
     * @var array|null
     */
    protected $operator = null;

    /**
     * {@inheritdoc}
     */
    public function setOperatorFromRequest(Request $request)
    {
        // Try to get operator from session.
        if (isset($_SESSION[SESSION_PREFIX . 'operator'])) {
            $this->operator = $_SESSION[SESSION_PREFIX . 'operator'];

            return true;
        }

        // Operator's data cannot be extracted from the request.
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function attachOperatorToResponse(Response $response)
    {
        if ($this->loggedOut) {
            // An operator is logged out. Clean up session data.
            unset($_SESSION[SESSION_PREFIX . 'operator']);
            unset($_SESSION[SESSION_PREFIX . 'backpath']);
        } elseif ($this->loggedIn) {
            // An operator is logged in. Update operator in the session.
            $_SESSION[SESSION_PREFIX . 'operator'] = $this->operator;
        } elseif ($this->operator) {
            // Update the current operator.
            $_SESSION[SESSION_PREFIX . 'operator'] = $this->operator;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * {@inheritdoc}
     */
    public function setOperator($operator)
    {
        if ($this->isOperatorChanged($operator)) {
            // If the current operator is changed (not updated) we should
            // reset all login/logout flags.
            $this->loggedIn = false;
            $this->loggedOut = false;
        }

        // Update the current operator
        $this->operator = $operator;
    }

    /**
     * {@inheritdoc}
     */
    public function loginOperator($operator, $remember)
    {
        $this->loggedIn = true;
        $this->loggedOut = false;
        $this->operator = $operator;
    }

    /**
     * {@inheritdoc}
     */
    public function logoutOperator()
    {
        $this->loggedOut = true;
        $this->loggedIn = false;
        $this->operator = null;
    }

    /**
     * Checks if the operator changed.
     *
     * @param array $operator Operator's data.
     * @return boolean
     */
    protected function isOperatorChanged($operator)
    {
        // Check if the operator is the same but has been updated.
        $same_operator = $operator
            && $this->operator
            && ($this->operator['operatorid'] == $operator['operatorid']);

        return !$same_operator;
    }
}
