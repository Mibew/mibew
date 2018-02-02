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

namespace Mibew\Asset;

use Mibew\Asset\Generator\UrlGenerator;
use Mibew\Asset\Generator\UrlGeneratorInterface;
use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Symfony\Component\HttpFoundation\Request;

/**
 * The class works with assets related with the current request.
 */
class AssetManager implements AssetManagerInterface
{
    /**
     * @var UrlGeneratorInterface|null
     */
    protected $generator = null;
    /**
     * @var Request|null
     */
    protected $request = null;

    /**
     * List of attached JS assets.
     *
     * @var Package
     */
    protected $jsPackage = null;
    /**
     * List of attached CSS assets.
     *
     * @var Package
     */
    protected $cssPackage = null;

    /**
     * Sets a request which will be used as a context.
     *
     * You can pass null as the first argument to notify the manager that there
     * is no current request.
     *
     * @param Request $request Request that should be used.
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;
        $this->getUrlGenerator()->setRequest($request);

        // The request has been changed thus all attaches assets are outdated
        // now. Clear them all.
        $this->jsAssets = null;
        $this->cssAssets = null;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlGenerator(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlGenerator()
    {
        if (is_null($this->generator)) {
            $this->generator = new UrlGenerator();
        }

        return $this->generator;
    }

    /**
     * {@inheritdoc}
     */
    public function attachJs($content, $type = AssetManagerInterface::RELATIVE_URL, $weight = 0)
    {
        $this->getJsPackage()->addAsset($content, $type, $weight);
    }

    /**
     * {@inheritdoc}
     */
    public function getJsAssets()
    {
        return $this->getJsPackage()->getAssets();
    }

    /**
     * {@inheritdoc}
     */
    public function attachCss($content, $type = AssetManagerInterface::RELATIVE_URL, $weight = 0)
    {
        $this->getCssPackage()->addAsset($content, $type, $weight);
    }

    /**
     * {@inheritdoc}
     */
    public function getCssAssets()
    {
        return $this->getCssPackage()->getAssets();
    }

    /**
     * Returns the request which is associated with the manager.
     *
     * @return Request
     * @throws \RuntimeException If a request was not associated with the
     *   manager yet.
     */
    protected function getRequest()
    {
        if (is_null($this->request)) {
            throw new \RuntimeException('Request instance was not set yet.');
        }

        return $this->request;
    }

    /**
     * Returns a package with JavaScript assets.
     *
     * @return Package
     */
    protected function getJsPackage()
    {
        if (is_null($this->jsPackage)) {
            $this->jsPackage = $this->triggerJsEvent();
        }

        return $this->jsPackage;
    }

    /**
     * Returns a package with CSS assets.
     *
     * @return Package
     */
    protected function getCssPackage()
    {
        if (is_null($this->cssPackage)) {
            $this->cssPackage = $this->triggerCssEvent();
        }

        return $this->cssPackage;
    }

    /**
     * Gets additional JS assets by triggering some events.
     *
     * Triggers {@link \Mibew\EventDispatcher\Events::PAGE_ADD_JS} and
     * {@link \Mibew\EventDispatcher\Events::PAGE_ADD_JS_PLUGIN_OPTIONS} events.
     *
     * @return Package Assets list.
     */
    protected function triggerJsEvent()
    {
        // Get additional JavaScript from plugins
        $event = array(
            'request' => $this->getRequest(),
            'js' => array(),
        );
        EventDispatcher::getInstance()->triggerEvent(Events::PAGE_ADD_JS, $event);
        $assets = $this->normalizeAssets($event['js']);

        // Get plugins options, transform them into raw JS and attache to the
        // other assets.
        $event = array(
            'request' => $this->getRequest(),
            'plugins' => array(),
        );
        EventDispatcher::getInstance()->triggerEvent(Events::PAGE_ADD_JS_PLUGIN_OPTIONS, $event);
        $assets->addAsset(
            sprintf(
                'var Mibew = Mibew || {}; Mibew.PluginOptions = %s;',
                json_encode($event['plugins'])
            ),
            AssetManagerInterface::INLINE,
            0
        );

        return $assets;
    }

    /**
     * Gets additional CSS assets by triggering some events.
     *
     * Triggers {@link \Mibew\EventDispatcher\Events::PAGE_ADD_CSS} event.
     *
     * @return Package Assets list.
     */
    protected function triggerCssEvent()
    {
        $event = array(
            'request' => $this->getRequest(),
            'css' => array(),
        );
        EventDispatcher::getInstance()->triggerEvent(Events::PAGE_ADD_CSS, $event);

        return $this->normalizeAssets($event['css']);
    }

    /**
     * Validates passed assets lists and builds a normalized one.
     *
     * @param array $assets Assets list. Each item of the list can be either a
     *   string or an asset array. If a string is used it is treated as an
     *   absolute URL of the asset. If an array is used it is treated as a
     *   normal asset array and must have "content" and "type" items.
     * @return Package A list of normalized assets.
     * @throws \InvalidArgumentException If the passed in assets list is not
     *   valid.
     */
    protected function normalizeAssets($assets)
    {
        $normalized_assets = new Package();

        foreach ($assets as $asset) {
            if (is_string($asset)) {
                $normalized_assets->addAsset(
                    $asset,
                    AssetManagerInterface::RELATIVE_URL,
                    500
                );
            } elseif (is_array($asset) && !empty($asset['type']) && !empty($asset['content'])) {
                // Weight is optional so we have to make sure it is in place.
                $asset += array('weight' => 500);
                $normalized_assets->addAsset(
                    $asset['content'],
                    $asset['type'],
                    $asset['weight']
                );
            } else {
                throw new \InvalidArgumentException('Invalid asset item');
            }
        }

        return $normalized_assets;
    }
}
