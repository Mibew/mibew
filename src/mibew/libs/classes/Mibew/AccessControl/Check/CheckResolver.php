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

namespace Mibew\AccessControl\Check;

use Mibew\Authentication\AuthenticationManagerAwareInterface;
use Mibew\Authentication\AuthenticationManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CheckResolver implements AuthenticationManagerAwareInterface
{
    /**
     * @var AuthenticationManagerInterface|null
     */
    protected $authenticationManager = null;

    /**
     * Class contructor.
     *
     * @param AuthenticationManagerInterface $manager An instance of
     * authentication manager.
     */
    public function __construct(AuthenticationManagerInterface $manager)
    {
        $this->authenticationManager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthenticationManager()
    {
        return $this->authenticationManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthenticationManager(AuthenticationManagerInterface $manager)
    {
        $this->authenticationManager = $manager;
    }

    /**
     * Resolves access check callable by request.
     *
     * @param Request $request Incoming request.
     * @return callable
     * @throws \InvalidArgumentException If the access check cannot be resolved.
     */
    public function getCheck(Request $request)
    {
        // Get access check name from the request
        $access_check = $request->attributes->get('_access_check');
        if (!$access_check) {
            // By default we do not need to restrict access
            return function () {
                return true;
            };
        }

        // Check if specified access check is something that can be called
        // directly
        if (strpos($access_check, ':') === false) {
            if (method_exists($access_check, '__invoke')) {
                $object = new $access_check();
                if ($object instanceof AuthenticationManagerAwareInterface) {
                    $object->setAuthenticationManager($this->getAuthenticationManager());
                }

                return $object;
            } elseif (function_exists($access_check)) {
                return $access_check;
            } else {
                throw new \InvalidArgumentException(sprintf(
                    'Unable to find access check "%s".',
                    $access_check
                ));
            }
        }

        // Build callable for specified access check
        $callable = $this->createCheck($access_check);

        if (!is_callable($callable)) {
            throw new \InvalidArgumentException(sprintf(
                'Access check "%s" for URI "%s" is not callable.',
                $access_check,
                $request->getPathInfo()
            ));
        }

        return $callable;
    }

    /**
     * Builds access check callable by its full name.
     *
     * @param string $access_check Full access check name in "<Class>::<method>"
     *   format.
     * @return callable Access check callable
     * @throws \InvalidArgumentException
     */
    protected function createCheck($access_check)
    {
        if (strpos($access_check, '::') === false) {
            throw new \InvalidArgumentException(sprintf(
                'Unable to find access check "%s".',
                $access_check
            ));
        }

        list($class, $method) = explode('::', $access_check, 2);
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $object = new $class();
        if ($object instanceof AuthenticationManagerAwareInterface) {
            $object->setAuthenticationManager($this->getAuthenticationManager());
        }

        return array($object, $method);
    }
}
