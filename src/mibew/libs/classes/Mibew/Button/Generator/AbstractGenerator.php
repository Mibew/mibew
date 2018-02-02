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

namespace Mibew\Button\Generator;

use Canteen\HTML5;
use Mibew\Asset\Generator\UrlGeneratorInterface as AssetUrlGeneratorInterface;
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
     * An assets URL generator.
     *
     * @var AssetUrlGeneratorInterface|null
     */
    protected $assetUrlGenerator = null;

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
        AssetUrlGeneratorInterface $assetUrlGenerator,
        $options = array()
    ) {
        $this->routeUrlGenerator = $routeUrlGenerator;
        $this->assetUrlGenerator = $assetUrlGenerator;
        $this->options = $options + array(
            'unique_id' => uniqid() . dechex(rand(0, pow(2, 12))),
        );
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
     * Generates URL for the specified asset.
     *
     * @param stirng $asset The relative path of the asset.
     * @return string The URL for the specified asset.
     */
    protected function generateAssetUrl($asset)
    {
        $generator = $this->assetUrlGenerator;

        if (!$this->getOption('show_host')) {
            return $generator->generate($asset);
        }

        return $this->getOption('force_secure')
            ? $generator->generateSecure($asset, RouteUrlGeneratorInterface::ABSOLUTE_URL)
            : $generator->generate($asset, RouteUrlGeneratorInterface::ABSOLUTE_URL);
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
     * Gets the style options string for the chat popup.
     *
     * @return array
     */
    protected function getPopupStyle()
    {
        $defaults = array(
            'width' => 640,
            'height' => 480,
            'resizable' => true,
        );

        $style_name = $this->getOption('chat_style');
        if (!$style_name) {
            return $defaults + array(
                'styleLoader' =>  $this->generateUrl($this->getOption('force_secure') ? 'chat_user_popup_style_force_secure' : 'chat_user_popup_style'), // An ugly way to solve the architecture issue
            );
        }

        $chat_style = new ChatStyle($style_name);
        $chat_configs = $chat_style->getConfigurations();

        // Intersection is used to limit style options to keys from the defaults
        // array.
        return array_intersect_key(
            $chat_configs['chat']['window'] + $defaults,
            $defaults
        ) + array(
            'styleLoader' => $this->generateUrl(
                $this->getOption('force_secure') ? 'chat_user_popup_style_force_secure' : 'chat_user_popup_style', // An ugly way to solve the architecture issue
                array('style' => $style_name)
            ),
        );
    }

    /**
     * Builds options list for a chat popup.
     *
     * @return array
     */
    protected function getPopupOptions()
    {
        return array(
            'id' => $this->getOption('unique_id'),
            'url' => $this->getChatUrl(),
            'preferIFrame' => $this->getOption('prefer_iframe'),
            'modSecurity' => $this->getOption('mod_security'),
            'forceSecure' => $this->getOption('force_secure'),
        ) + $this->getPopupStyle();
    }

    /**
     * Builds markup with chat popup initialization code.
     *
     * @return \Canteen\HTML5\Fragment
     */
    protected function getPopup()
    {
        $fragment = HTML5\html('fragment');
        $fragment->addChild(
            HTML5\html('script')->setAttributes(array(
                'type' => 'text/javascript',
                'src' => $this->generateAssetUrl('js/compiled/chat_popup.js'),
            ))
        );
        $fragment->addChild(
            HTML5\html('script')
                ->setAttribute('type', 'text/javascript')
                ->addChild('Mibew.ChatPopup.init(' . json_encode($this->getPopupOptions()) . ');')
        );

        return $fragment;
    }
}
