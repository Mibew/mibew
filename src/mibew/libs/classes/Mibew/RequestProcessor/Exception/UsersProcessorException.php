<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2022 the original author or authors.
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

namespace Mibew\RequestProcessor\Exception;

/**
 * Class for {@link \Mibew\RequestProcessor\UsersProcessor} exceptions
 */
class UsersProcessorException extends AbstractProcessorException
{
    /**
     * Operator is not logged in
     */
    const ERROR_AGENT_NOT_LOGGED_IN = 1;
    /**
     * Wrong agent id
     */
    const ERROR_WRONG_AGENT_ID = 2;
    /**
     * Various agent ids in different functions in one package
     */
    const VARIOUS_AGENT_ID = 3;
}
