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

namespace Mibew\Asset;

use Mibew\Asset\Generator\UrlGenerator;
use Mibew\Asset\Generator\UrlGeneratorInterface;
use Mibew\EventDispatcher;
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
     * @var array
     */
    protected $jsAssets = array();
    /**
     * List of attached CSS assets.
     *
     * @var array
     */
    protected $cssAssets = array();

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
        $this->jsAssets = array();
        $this->cssAssets = array();
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
        $this->jsAssets[] = array(
            'content' => $content,
            'type' => $type,
            'weight' => $weight,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getJsAssets()
    {
        return $this->sort(
            array_merge(
                $this->jsAssets,
                $this->triggerJsEvent()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function attachCss($content, $type = AssetManagerInterface::RELATIVE_URL, $weight = 0)
    {
        $this->cssAssets[] = array(
            'content' => $content,
            'type' => $type,
            'weight' => $weight,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCssAssets()
    {
        return $this->sort(
            array_merge(
                $this->cssAssets,
                $this->triggerCssEvent()
            )
        );
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
     * Gets additional JS assets by triggering some events.
     *
     * Triggers "pageAddJS" and pass to the listeners an associative array with
     * the following keys:
     *  - "request": {@link \Symfony\Component\HttpFoundation\Request}, a
     *    request instance. JavaScript files will be attached to the requested
     *    page.
     *  - "js": array of assets. Each asset can be either a string with
     *    relative URL of a JavaScript file or an array with "content",
     *    "type" and "weight" items. See
     *    {@link \Mibew\Asset\AssetManagerInterface::getJsAssets()} for details
     *    of their meaning. Modify this array to add or remove additional
     *    JavaScript files.
     *
     * Triggers "pageAddJSPluginOptions" and pass to the listeners an
     * associative array with the following keys:
     *  - "request": {@link \Symfony\Component\HttpFoundation\Request}, a
     *    request instance. Plugins will work at the requested page.
     *  - "plugins": associative array, whose keys are plugins names and values
     *    are plugins options. Modify this array to add or change plugins
     *    options.
     *
     * @return array Assets list.
     */
    protected function triggerJsEvent()
    {
        // Get additional JavaScript from plugins
        $event = array(
            'request' => $this->getRequest(),
            'js' => array(),
        );
        EventDispatcher::getInstance()->triggerEvent('pageAddJS', $event);
        $assets = $this->normalizeAssets($event['js']);

        // Get plugins options, transform them into raw JS and attache to the
        // other assets.
        $event = array(
            'request' => $this->getRequest(),
            'plugins' => array(),
        );
        EventDispatcher::getInstance()->triggerEvent('pageAddJSPluginOptions', $event);
        $assets[] = array(
            'content' => sprintf(
                'var Mibew = Mibew || {}; Mibew.PluginOptions = %s;',
                json_encode($event['plugins'])
            ),
            'type' => AssetManagerInterface::INLINE,
            'weight' => 0,
        );

        return $assets;
    }

    /**
     * Gets additional CSS assets by triggering some events.
     *
     * Triggers "pageAddCSS" and passes to the listeners an associative array
     * with the following keys:
     *  - "request": {@link \Symfony\Component\HttpFoundation\Request}, a
     *    request instance. CSS files will be attached to the requested page.
     *  - "css": array of assets. Each asset can be either a string with
     *    relative URL of a CSS file or an array with "content", "type" and
     *    "weight" items. See
     *    {@link \Mibew\Asset\AssetManagerInterface::getCssAssets()} for details
     *    of their meaning. Modify this array to add or remove additional CSS
     *    files.
     *
     * @return array Assets list.
     */
    protected function triggerCssEvent()
    {
        $event = array(
            'request' => $this->getRequest(),
            'css' => array(),
        );
        EventDispatcher::getInstance()->triggerEvent('pageAddCSS', $event);

        return $this->normalizeAssets($event['css']);
    }

    /**
     * Validates passed assets lists and builds a normalized one.
     *
     * @param array $assets Assets list. Each item of the list can be either a
     *   string or an asset array. If a string is used it is treated as an
     *   absolute URL of the asset. If an array is used it is treated as a
     *   normal asset array and must have "content" and "type" items.
     * @return array A list of normalized assets.
     * @throws \InvalidArgumentException If the passed in assets list is not
     *   valid.
     */
    protected function normalizeAssets($assets)
    {
        $normalized_assets = array();

        foreach ($assets as $asset) {
            if (is_string($asset)) {
                $normalized_assets[] = array(
                    'content' => $asset,
                    'type' => AssetManagerInterface::RELATIVE_URL,
                    'weight' => 0,
                );
            } elseif (is_array($asset) && !empty($asset['type']) && !empty($asset['content'])) {
                // Weight is optional so we have to make sure it is in place.
                $normalized_assets[] = $asset + array('weight' => 0);
            } else {
                throw new \InvalidArgumentException('Invalid asset item');
            }
        }

        return $normalized_assets;
    }

    /**
     * Sort assets according to their weights.
     *
     * If weights of two assets are equal the order from the original array will
     * be kept.
     *
     * @param array $assets The original List of assets.
     * @return array Sorted list of assets.
     */
    protected function sort($assets)
    {
        // We should keep order for assets with equal weight. Thus we must
        // combine original order and weight property before real sort.
        $tmp = array();
        $offset = 0;
        foreach ($assets as $asset) {
            $key = $asset['weight'] . '|' . $offset;
            $tmp[$key] = $asset;
            $offset++;
        }

        uksort($tmp, function ($a, $b) {
            list($a_weight, $a_offset) = explode('|', $a, 2);
            list($b_weight, $b_offset) = explode('|', $b, 2);

            if ($a_weight != $b_weight) {
                return ($a_weight < $b_weight) ? -1 : 1;
            }

            // Weights are equal. Check the offset to determine which asset was
            // attached first.
            if ($a_offset != $b_offset) {
                return ($a_offset < $b_offset) ? -1 : 1;
            }

            return 0;
        });

        // Artificial sorting keys should be removed from the resulting array.
        return array_values($tmp);
    }
}
