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

namespace Mibew\Controller;

use Mibew\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains acctions related with operator logout process.
 */
class LogoutController extends AbstractController
{
    /**
     * Logs operator out.
     *
     * Triggers 'operatorLogout' event after operator logged out.
     *
     * @param Request $request Incoming request.
     * @return Response Prepared response object.
     */
    public function logoutAction(Request $request)
    {
        // Detach operator's object from the request. This should tells
        // authentication manager that operator session should be closed.
        $request->attributes->remove('_operator');

        // Trigger logout event
        $dispatcher = EventDispatcher::getInstance();
        $dispatcher->triggerEvent('operatorLogout');

        // Redirect the current operator to the login page.
        return $this->redirect($this->generateUrl('login'));
    }
}
