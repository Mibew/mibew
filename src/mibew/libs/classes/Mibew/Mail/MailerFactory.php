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

namespace Mibew\Mail;

/**
 * A basic implementation of MailerFactoryInterface.
 */
class MailerFactory implements MailerFactoryInterface
{
    /**
     * @var \Swift_Mailer|null
     */
    protected $mailer = null;

    /**
     * {@inheritdoc}
     */
    public function getMailer()
    {
        if (is_null($this->mailer)) {
            $transport = $this->getTransport();
            $this->mailer = \Swift_Mailer::newInstance($transport);
        }

        return $this->mailer;
    }

    /**
     * Builds and returns appropriate mail transport.
     *
     * @return \Swift_Transport
     */
    protected function getTransport()
    {
        return \Swift_MailTransport::newInstance();
    }
}
