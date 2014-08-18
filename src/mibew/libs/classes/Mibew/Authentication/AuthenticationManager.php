<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2014 the original author or authors.
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

use Mibew\EventDispatcher;
use Mibew\Http\CookieFactory;
use Mibew\Http\CookieFactoryAwareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controls operator's authentication.
 */
class AuthenticationManager implements AuthenticationManagerInterface, CookieFactoryAwareInterface
{
    /**
     * Indicates if the operator is logged in.
     * @var boolean
     */
    protected $loggedIn = false;

    /**
     * Indicates if the operator should be remembered after login.
     * @var boolean
     */
    protected $remember = false;

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
     * @var CookieFactory|null
     */
    protected $cookieFactory = null;

    /**
     * {@inheritdoc}
     */
    public function setCookieFactory(CookieFactory $factory)
    {
        $this->cookieFactory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieFactory()
    {
        if (is_null($this->cookieFactory)) {
            $this->cookieFactory = new CookieFactory();
        }

        return $this->cookieFactory;
    }

    /**
     * {@inheritdoc}
     *
     * Triggers 'operatorAuthenticate' event if operator is not authenticated by
     * the system and pass to it an associative array with following items:
     *  - 'operator': if a plugin has extracted operator from the request it
     *    should set operator's data to this field.
     *  - 'request': {@link Request}, incoming request. Can be used by a plugin
     *    to extract an operator.
     */
    public function setOperatorFromRequest(Request $request)
    {
        // Try to get operator from session.
        if (isset($_SESSION[SESSION_PREFIX . 'operator'])) {
            $this->operator = $_SESSION[SESSION_PREFIX . 'operator'];

            return true;
        }

        // Check if operator had used "remember me" feature.
        if ($request->cookies->has(REMEMBER_OPERATOR_COOKIE_NAME)) {
            $cookie_value = $request->cookies->get(REMEMBER_OPERATOR_COOKIE_NAME);
            list($login, $pwd) = preg_split('/\x0/', base64_decode($cookie_value), 2);
            $op = operator_by_login($login);
            $can_login = $op
                && isset($pwd)
                && isset($op['vcpassword'])
                && calculate_password_hash($op['vclogin'], $op['vcpassword']) == $pwd
                && !operator_is_disabled($op);
            if ($can_login) {
                // Cache operator in the session data
                $_SESSION[SESSION_PREFIX . 'operator'] = $op;
                $this->operator = $op;

                return true;
            }
        }

        // Provide an ability for plugins to authenticate operator
        $args = array(
            'operator' => false,
            'request' => $request,
        );
        $dispatcher = EventDispatcher::getInstance();
        $dispatcher->triggerEvent('operatorAuthenticate', $args);

        if (!empty($args['operator'])) {
            // Cache operator in the session
            $_SESSION[SESSION_PREFIX . 'operator'] = $args['operator'];
            $this->operator = $args['operator'];

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
            unset($_SESSION['backpath']);

            // Clear remember cookie.
            $cookie_factory = $this->getCookieFactory();
            $response->headers->clearCookie(
                REMEMBER_OPERATOR_COOKIE_NAME,
                $cookie_factory->getPath(),
                $cookie_factory->getDomain()
            );
        } elseif ($this->loggedIn) {
            // An operator is logged in. Update operator in the session.
            $_SESSION[SESSION_PREFIX . 'operator'] = $this->operator;

            // Set remember me cookie if needed
            if ($this->remember) {
                $password_hash = calculate_password_hash(
                    $this->operator['vclogin'],
                    $this->operator['vcpassword']
                );
                $remember_cookie = $this->getCookieFactory()->createCookie(
                    REMEMBER_OPERATOR_COOKIE_NAME,
                    base64_encode($this->operator['vclogin'] . "\x0" . $password_hash),
                    time() + 60 * 60 * 24 * 1000,
                    true
                );

                $response->headers->setCookie($remember_cookie);
            }
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
        $operator_updated = $operator
            && $this->operator
            && ($this->operator['operatorid'] == $operator['operatorid']);
        if (!$operator_updated) {
            // If the current operator is changed (not updated) we should
            // reset all login/logout flags.
            $this->loggedIn = false;
            $this->loggedOut = false;
            $this->remember = false;
        }

        // Update the current operator
        $this->operator = $operator;
    }

    /**
     * {@inheritdoc}
     *
     * Triggers 'operatorLogin' event after operator logged in and pass to it an
     * associative array with following items:
     *  - 'operator': array of the logged in operator info;
     *  - 'remember': boolean, indicates if system should remember operator.
     */
    public function loginOperator($operator, $remember)
    {
        $this->loggedIn = true;
        $this->remember = $remember;
        $this->loggedOut = false;
        $this->operator = $operator;

        // Trigger login event
        $args = array(
            'operator' => $operator,
            'remember' => $remember,
        );
        $dispatcher = EventDispatcher::getInstance();
        $dispatcher->triggerEvent('operatorLogin', $args);
    }

    /**
     * {@inheritdoc}
     *
     * Triggers 'operatorLogout' event after operator logged out.
     */
    public function logoutOperator()
    {
        $this->loggedOut = true;
        $this->loggedIn = false;
        $this->remember = false;

        $this->operator = null;

        // Trigger logout event
        $dispatcher = EventDispatcher::getInstance();
        $dispatcher->triggerEvent('operatorLogout');
    }
}
