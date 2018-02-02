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

namespace Mibew\RequestProcessor\Exception;

/**
 * Class for {@link \Mibew\RequestProcessor\ThreadProcessor} exceptions.
 */
class ThreadProcessorException extends AbstractProcessorException
{
    /**
     * 'recipient' argument is not set
     */
    const EMPTY_RECIPIENT = 1;
    /**
     * Operator is not logged in
     */
    const ERROR_AGENT_NOT_LOGGED_IN = 2;
    /**
     * Wrong arguments set for an API function
     */
    const ERROR_WRONG_ARGUMENTS = 3;
    /**
     * Thread cannot be loaded
     */
    const ERROR_WRONG_THREAD = 4;
    /**
     * Message cannot be send
     */
    const ERROR_CANNOT_SEND = 5;
    /**
     * User rename forbidden by system configurations
     */
    const ERROR_FORBIDDEN_RENAME = 6;
    /**
     * Various recipient in different functions in one package
     */
    const VARIOUS_RECIPIENT = 7;
    /**
     * Various thread ids or thread tokens in different functions in one package
     */
    const VARIOUS_THREAD_ID = 8;
    /**
     * Wrong recipient value
     */
    const WRONG_RECIPIENT_VALUE = 9;
    /**
     * Wrong captcha value
     */
    const ERROR_WRONG_CAPTCHA = 10;
    /**
     * Wrong email address
     */
    const ERROR_WRONG_EMAIL = 11;
}
