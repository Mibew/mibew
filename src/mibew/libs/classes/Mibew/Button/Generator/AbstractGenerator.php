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

namespace Mibew\Button\Generator;

use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Mibew\Routing\Generator\SecureUrlGeneratorInterface as RouteUrlGeneratorInterface;
use Mibew\Style\ChatStyle;

/**
 * Contains base button generation functionality.
 */
abstract class AbstractGenerator implements GeneratorInterface
{
    /**
     * A routes URL generator.
     *
     * @var RouteUrlGeneratorInterface|null
     */
    protected $routeUrlGenerator = null;

    /**
     * List of the generator's options.
     *
     * @var array
     */
    protected $options = array();

    /**
     * Class contructor.
     *
     * @param RouteUrlGeneratorInterface $routeUrlGenerator A routes URL
     *   generator.
     * @param array $options Associative array with generator's initial options.
     *   The set of options can vary for the child classes.
     */
    public function __construct(
        RouteUrlGeneratorInterface $routeUrlGenerator,
        $options = array()
    ) {
        $this->routeUrlGenerator = $routeUrlGenerator;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name, $default = false)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $args = array(
            'button' => $this->doGenerate(),
            'generator' => $this,
        );
        EventDispatcher::getInstance()->triggerEvent(Events::BUTTON_GENERATE, $args);

        return (string)$args['button'];
    }

    /**
     * Really generates the button.
     *
     * @return \Canteen\HTML5\Fragment Button's markup.
     */
    abstract protected function doGenerate();

    /**
     * Generates URL for the specified route.
     *
     * @param stirng $route The name of the route.
     * @param array $parameters List of parameters that will be used for URL
     *   generating.
     * @return string The URL for the specified route.
     */
    protected function generateUrl($route, $parameters = array())
    {
        $generator = $this->routeUrlGenerator;

        if (!$this->getOption('show_host')) {
            return $generator->generate($route, $parameters);
        }

        return $this->getOption('force_secure')
            ? $generator->generateSecure($route, $parameters, RouteUrlGeneratorInterface::ABSOLUTE_URL)
            : $generator->generate($route, $parameters, RouteUrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Gets the URL of the chat start point.
     *
     * @return string
     */
    protected function getChatUrl()
    {
        $link_params = array();

        if ($this->getOption('locale')) {
            $link_params['locale'] = $this->getOption('locale');
        }
        if ($this->getOption('chat_style')) {
            $link_params['style'] = $this->getOption('chat_style');
        }
        if ($this->getOption('group_id')) {
            $link_params['group'] = $this->getOption('group_id');
        }

        return $this->generateUrl('chat_user_start', $link_params);
    }

    /**
     * Gets the URL of the chat start point.
     *
     * The result is a JavaScript String with several additional dynamic
     * parameters. It can be use only as a JS String.
     *
     * @return string
     */
    protected function getChatUrlForJs()
    {
        $url = str_replace('&', '&amp;', $this->getChatUrl());
        $modsecfix = $this->getOption('mod_security')
            ? ".replace('http://','').replace('https://','')"
            : '';

        return "'" . $url
            . ((strpos($url, '?') === false) ? '?' : '&amp;')
            . "url='+escape(document.location.href$modsecfix)+'&amp;"
            . "referrer='+escape(document.referrer$modsecfix)";
    }

    /**
     * Gets the options string for the chat popup window.
     *
     * @return string
     */
    protected function getPopupOptions()
    {
        $style_name = $this->getOption('chat_style');

        if (!$style_name) {
            return "toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1";
        }

        $chat_style = new ChatStyle($style_name);
        $chat_configurations = $chat_style->getConfigurations();

        return $chat_configurations['chat']['window_params'];
    }
}
