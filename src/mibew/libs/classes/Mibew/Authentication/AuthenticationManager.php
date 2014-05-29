<?php
/*
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controls operator's authentication.
 */
class AuthenticationManager
{
    /**
     * @var CookieFactory|null
     */
    protected $cookieFactory = null;

    /**
     * Extracts operator's data from the passed in request object.
     *
     * Triggers 'operatorAuthenticate' event if operator is not authenticated by
     * the system and pass to it an associative array with following items:
     *  - 'operator': if a plugin has extracted operator from the request it
     *    should set operator's data to this field.
     *  - 'request': {@link Request}, incoming request. Can be used by a plugin
     *    to extract an operator.
     *
     * @param Request $request A request to extract operator from.
     * @return array|bool Associative array with operator's data or boolean
     *   false if there is no operator related with the request.
     */
    public function extractOperator(Request $request)
    {
        // Try to get operator from session.
        if (isset($_SESSION[SESSION_PREFIX . 'operator'])) {
            return $_SESSION[SESSION_PREFIX . 'operator'];
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
                $_SESSION[SESSION_PREFIX . 'operator'] = $op;

                return $op;
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
            $_SESSION[SESSION_PREFIX . 'operator'] = $args['operator'];
            return $args['operator'];
        }

        // Operator's data cannot be extracted from the request.
        return false;
    }

    /**
     * Attaches operator's token to the response, thus is can be used to extract
     * operator in the next request.
     *
     * @param Response $response The response object which will be sent to the
     * client.
     * @param array $operator Operator's data.
     * @return Response Updated response.
     */
    public function attachOperator(Response $response, $operator)
    {
        if ($operator) {
            // Calculate password hashes for operator in the request and for the
            // operator in session. If the hashes are different then operator's
            // password or login was changed.
            $password_hash = calculate_password_hash(
                $operator['vclogin'],
                $operator['vcpassword']
            );

            if (isset($_SESSION[SESSION_PREFIX . 'operator'])) {
                $old_operator = $_SESSION[SESSION_PREFIX . 'operator'];
                $old_password_hash = calculate_password_hash(
                    $old_operator['vclogin'],
                    $old_operator['vcpassword']
                );
                $credentials_changed = $password_hash != $old_password_hash;
            } else {
                $credentials_changed = false;
            }

            // Check if we need to remember the operator
            if (isset($operator['remember_me'])) {
                $remember = $operator['remember_me'];
                unset($operator['remember_me']);
            } else {
                $remember = false;
            }

            // Update operator in the session
            $_SESSION[SESSION_PREFIX . 'operator'] = $operator;

            // Set or update remember me cookie if needed
            if ($remember || $credentials_changed) {
                $remember_cookie = $this->getCookieFactory()->createCookie(
                    REMEMBER_OPERATOR_COOKIE_NAME,
                    base64_encode($operator['vclogin'] . "\x0" . $password_hash),
                    time() + 60 * 60 * 24 * 1000,
                    true
                );

                $response->headers->setCookie($remember_cookie);
            }
        } else {
            // Clean up session data
            unset($_SESSION[SESSION_PREFIX . 'operator']);
            unset($_SESSION['backpath']);

            // Clear remember cookie
            $cookie_factory = $this->getCookieFactory();
            $response->headers->clearCookie(
                REMEMBER_OPERATOR_COOKIE_NAME,
                $cookie_factory->getPath(),
                $cookie_factory->getDomain()
            );
        }
    }

    /**
     * Updates instance of cookie factory related with the manager.
     *
     * @param CookieFactory $factory An instance of CookieFactory.
     */
    public function setCookieFactory(CookieFactory $factory)
    {
        $this->cookieFactory = $factory;
    }

    /**
     * Returns an instance of cookie factory related with the manager.
     *
     * @return CookieFactory
     */
    public function getCookieFactory()
    {
        if (is_null($this->cookieFactory)) {
            $this->cookieFactory = new CookieFactory();
        }

        return $this->cookieFactory;
    }
}
