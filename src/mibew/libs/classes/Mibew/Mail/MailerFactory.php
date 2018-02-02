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
 * A basic implementation of MailerFactoryInterface.
 */
class MailerFactory implements MailerFactoryInterface
{
    /**
     * @var \Swift_Mailer|null
     */
    protected $mailer = null;

    /**
     * @var array|null
     */
    protected $options = array();

    /**
     * Class constructor.
     *
     * @param Array $options Associative array of options that should be used.
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Gets factory's option.
     *
     * @param string $name Name of the option to retrieve.
     * @throws \InvalidArgumentException If the option is unknown.
     */
    public function getOption($name)
    {
        if (!isset($this->options[$name])) {
            throw new \InvalidArgumentException(sprintf('Unknown option "%s"', $name));
        }

        return $this->options[$name];
    }

    /**
     * Sets factory's option.
     *
     * @param string $name Name of the option to set.
     * @param string $value New value.
     * @throws \InvalidArgumentException If the option is unknown.
     */
    public function setOption($name, $value)
    {
        if (!isset($this->options[$name])) {
            throw new \InvalidArgumentException(sprintf('Unknown option "%s"', $name));
        }

        $this->options[$name] = $value;
    }

    /**
     * Sets factory's options.
     *
     * @param array $options Associative array of options.
     * @throws \InvalidArgumentException If specified array has unknow options.
     */
    public function setOptions($options)
    {
        $defaults = array(
            'transport' => 'mail',
            'host' => 'localhost',
            'port' => 25,
            'user' => 'user',
            'pass' => '',
            'encryption' => false,
        );

        // Make sure all passed options are known
        $unknown_options = array_diff(array_keys($options), array_keys($defaults));
        if (count($unknown_options) != 0) {
            throw new \InvalidArgumentException(sprintf(
                'These options are unknown: %s',
                implode(', ', $unknown_options)
            ));
        }

        if (empty($this->options)) {
            // The options are set for the first time.
            $this->options = $options + $defaults;
        } else {
            // Update only specified options.
            $this->options = $options + $this->options;
        }
    }

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
        switch ($this->getOption('transport')) {
            case 'mail':
                return \Swift_MailTransport::newInstance();
            case 'smtp':
                return \Swift_SmtpTransport::newInstance()
                    ->setHost($this->getOption('host'))
                    ->setPort($this->getOption('port'))
                    ->setUsername($this->getOption('user'))
                    ->setPassword($this->getOption('pass'))
                    ->setEncryption($this->getOption('encryption') ?: null);
            default:
                throw new \RuntimeException(sprintf('Unknown transport "%s"', $this->getOption('transport')));
        }
    }
}
