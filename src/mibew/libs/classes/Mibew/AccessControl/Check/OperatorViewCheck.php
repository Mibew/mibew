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
 * Checks if operator from the request is logged in and has permissions to
 * view target operators profile. Request must contain id of the target
 * operator in "operator_id" attribute.
 */
class OperatorViewCheck extends LoggedInCheck
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
        $target_operator_id = $request->attributes->getInt('operator_id', false);

        return is_capable(CAN_ADMINISTRATE, $operator)
            || $operator['operatorid'] == $target_operator_id;
    }
}
