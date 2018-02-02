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

namespace Mibew\Mail;

/**
 * MailerFactoryInterface is the interface that all mailer factory classes
 * must implement.
 *
 * Mailer factory is created to encapsulate \Swift_Mailer instantiating logic
 * and to provide a lazy way for creating instance of the mailer.
 */
interface MailerFactoryInterface
{
    /**
     * Builds and returns an instance of mailer.
     *
     * @return \Swift_Mailer
     */
    public function getMailer();
}
