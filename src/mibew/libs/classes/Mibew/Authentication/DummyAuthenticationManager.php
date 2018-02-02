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
 * Pretend to control operator's authentication.
 *
 * Actually it does nothing and can be used as a stub in cases when operator
 * should not be authenticated.
 */
class DummyAuthenticationManager implements AuthenticationManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function setOperatorFromRequest(Request $request)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function attachOperatorToResponse(Response $response)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getOperator()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setOperator($operator)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function loginOperator($operator, $remember)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function logoutOperator()
    {
    }
}
