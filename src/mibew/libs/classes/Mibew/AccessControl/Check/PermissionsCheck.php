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

use Symfony\Component\HttpFoundation\Request;

/**
 * Checks if operator from the request is logged in and has permissions which
 * are specified in route's "_access_permissions" default.
 *
 * Here is an example of how one can use the check with a route definition:
 * <code>
 * test:
 *     path: /test
 *     defaults:
 *         _controller: Mibew\Controller\TestController::testAction
 *         # Set check for the route
 *         _access_check: Mibew\AccessControl\Check\PermissionsCheck
 *         # Define array of permissions. An operator should has all these
 *         # permissions to access the route
 *         _access_permissions: [CAN_ADMINISTRATE, CAN_MODIFYPROFILE]
 * </code>
 */
class PermissionsCheck extends LoggedInCheck
{
    /**
     * Checks the access.
     *
     * @param Request $request Incoming request
     * @return boolean Indicates if an operator has access or not.
     */
    public function __invoke(Request $request)
    {
        // Check if the operator is logged in
        if (!parent::__invoke($request)) {
            return false;
        }

        $operator = $this->getOperator();
        $permissions = $request->attributes->get('_access_permissions', array());
        foreach ($permissions as $permission) {
            if (!is_capable($this->resolvePermission($permission), $operator)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Resolves permission name and returns its code.
     *
     * @param string $permission_name Name of permission. Can be one of
     *   "CAN_ADMINISTRATE", "CAN_TAKEOVER", "CAN_VIEWTHREADS",
     *   "CAN_MODIFYPROFILE", "CAN_VIEWSTATISTICS".
     * @return int Permission code.
     * @throws \InvalidArgumentException
     */
    protected function resolvePermission($permission_name)
    {
        switch ($permission_name) {
            case 'CAN_ADMINISTRATE':
                $permission_code = CAN_ADMINISTRATE;
                break;
            case 'CAN_TAKEOVER':
                $permission_code = CAN_TAKEOVER;
                break;
            case 'CAN_VIEWTHREADS':
                $permission_code = CAN_VIEWTHREADS;
                break;
            case 'CAN_MODIFYPROFILE':
                $permission_code = CAN_MODIFYPROFILE;
                break;
            case 'CAN_VIEWSTATISTICS':
                $permission_code = CAN_VIEWSTATISTICS;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown permission "%s".', $permission_name));
        }

        return $permission_code;
    }
}
