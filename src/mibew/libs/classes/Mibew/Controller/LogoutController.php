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

namespace Mibew\Controller;

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
        // Logout the operator from the system
        $this->getAuthenticationManager()->logoutOperator();

        // Redirect the current operator to the login page.
        return $this->redirect($this->generateUrl('login'));
    }
}
