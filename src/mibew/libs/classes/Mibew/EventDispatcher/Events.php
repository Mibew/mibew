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

namespace Mibew\EventDispatcher;

/**
 * This class contains a list of events that are present in the system.
 */
final class Events
{
    /**
     * An operator cannot be authenticated.
     *
     * This event is triggered if an operator cannot be authenticated by the
     * system. It provides an ability for plugins to implement custom
     * authentication logic. An associative array with the following items is
     * passed to the event handlers:
     *  - "operator": array, if a plugin has extracted operator from the request
     *    it should set operator's data to this field.
     *  - "request": {@link \Symfony\Component\HttpFoundation\Request},
     *    incoming request. Can be used by a plugin to extract an operator.
     */
    const OPERATOR_AUTHENTICATE = 'operatorAuthenticate';

    /**
     * An operator logged in.
     *
     * This event is triggered after an operator logged in using system login
     * form. An associative array with the following items is passed to the
     * event handlers:
     *  - "operator": array of the logged in operator info;
     *  - "remember": boolean, indicates if system should remember operator.
     */
    const OPERATOR_LOGIN = 'operatorLogin';

    /**
     * An operator logged out.
     *
     * This event is triggered after an operator is logged out.
     */
    const OPERATOR_LOGOUT = 'operatorLogout';

    /**
     * An operator is created.
     *
     * This event is triggered after an operator has been created. An
     * associative array with the following items is passed to the event
     * handlers:
     *  - "operator": operator's array.
     */
    const OPERATOR_CREATE = 'operatorCreate';

    /**
     * An operator is deleted.
     *
     * This event is triggered after an operator has been deleted. An
     * associative array with the following items is passed to the event
     * handlers:
     *  - "id": int, deleted operator ID.
     */
    const OPERATOR_DELETE = 'operatorDelete';
}
