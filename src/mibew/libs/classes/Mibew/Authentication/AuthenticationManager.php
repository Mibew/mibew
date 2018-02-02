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

use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Mibew\Http\CookieFactory;
use Mibew\Http\CookieFactoryAwareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controls operator's authentication.
 *
 * This is the base authentication manager for the system.
 */
class AuthenticationManager extends SessionAuthenticationManager implements CookieFactoryAwareInterface
{
    /**
     * Indicates if the operator should be remembered after login.
     * @var boolean
     */
    protected $remember = false;

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
     * Triggers {@link \Mibew\EventDispatcher\Events::OPERATOR_AUTHENTICATE}
     * event.
     */
    public function setOperatorFromRequest(Request $request)
    {
        // Try to get operator from session.
        if (parent::setOperatorFromRequest($request)) {
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
        $dispatcher->triggerEvent(Events::OPERATOR_AUTHENTICATE, $args);

        if (!empty($args['operator'])) {
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
        parent::attachOperatorToResponse($response);

        if ($this->loggedOut) {
            // Clear remember cookie.
            $cookie_factory = $this->getCookieFactory();
            $response->headers->clearCookie(
                REMEMBER_OPERATOR_COOKIE_NAME,
                $cookie_factory->getPath(),
                $cookie_factory->getDomain()
            );
        } elseif ($this->loggedIn) {
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
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOperator($operator)
    {
        if ($this->isOperatorChanged($operator)) {
            // If the current operator is changed (not updated) we should
            // reset remember flag.
            $this->remember = false;
        }

        parent::setOperator($operator);
    }

    /**
     * {@inheritdoc}
     *
     * Triggers {@link \Mibew\EventDispatcher\Events::OPERATOR_LOGIN} event.
     */
    public function loginOperator($operator, $remember)
    {
        parent::loginOperator($operator, $remember);
        $this->remember = $remember;

        // Trigger login event
        $args = array(
            'operator' => $operator,
            'remember' => $remember,
        );
        $dispatcher = EventDispatcher::getInstance();
        $dispatcher->triggerEvent(Events::OPERATOR_LOGIN, $args);
    }

    /**
     * {@inheritdoc}
     *
     * Triggers {@link \Mibew\EventDispatcher\Events::OPERATOR_LOGOUT} event.
     */
    public function logoutOperator()
    {
        parent::logoutOperator();
        $this->remember = false;

        // Trigger logout event
        $dispatcher = EventDispatcher::getInstance();
        $dispatcher->triggerEvent(Events::OPERATOR_LOGOUT);
    }
}
