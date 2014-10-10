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

use Mibew\Asset\Generator\UrlGeneratorInterface;

/**
 * This is the interface that all Asset manager classes must implement.
 */
interface AssetManagerInterface
{
    /**
     * Indicates that content of an asset is an absolute URL.
     */
    const ABSOLUTE_URL = 'absolute';
    /**
     * Indicates that content of an asset is a relative URL.
     */
    const RELATIVE_URL = 'relative';
    /**
     * Indicates that content of an asset is raw CSS or JS.
     */
    const INLINE = 'inline';

    /**
     * Gets an instance of Assets URL Generator.
     *
     * @return UrlGeneratorInterface
     */
    public function getUrlGenerator();

    /**
     * Sets an instance of Assets URL Generator.
     *
     * @param UrlGeneratorInterface $generator
     */
    public function setUrlGenerator(UrlGeneratorInterface $generator);

    /**
     * Attaches a JavaScript asset.
     *
     * @param type $content Content of the asset. It can be a kind of URL of
     *   plain content depends on the second argument of the method.
     * @param mixed $type Determines asset type. It can be one of
     *   AssetManagerInterface::ABSOLUTE_URL,
     *   AssetManagerInterface::RELATIVE_URL or AssetManagerInterface::INLINE
     *   constants.
     */
    public function attachJs($content, $type = self::RELATIVE_URL);

    /**
     * Retrieves all attached and provided by plugins JavaScript assets.
     *
     * @return array List of attached assets. Each item is an array with
     *   the following keys:
     *    - content: string, can be either a kind of URL or raw JavaScript
     *      content.
     *    - type: mixed, determines asset type. It can be one of
     *      AssetManagerInterface::ABSOLUTE_URL,
     *      AssetManagerInterface::RELATIVE_URL or AssetManagerInterface::INLINE
     *      constants.
     */
    public function getJsAssets();

    /**
     * Attaches a CSS asset.
     *
     * @param type $content Content of the asset. It can be a kind of URL of
     *   plain content depends on the second argument of the method.
     * @param mixed $type Determines asset type. It can be one of
     *   AssetManagerInterface::ABSOLUTE_URL,
     *   AssetManagerInterface::RELATIVE_URL or AssetManagerInterface::INLINE
     *   constants.
     */
    public function attachCss($content, $type = self::RELATIVE_URL);

    /**
     * Retrieves all attached and provided by plugins CSS assets.
     *
     * @return array List of attached assets. Each item is an array with
     *   the following keys:
     *    - content: string, can be either a kind of URL or raw CSS content.
     *    - type: mixed, determines asset type. It can be one of
     *      AssetManagerInterface::ABSOLUTE_URL,
     *      AssetManagerInterface::RELATIVE_URL or AssetManagerInterface::INLINE
     *      constants.
     */
    public function getCssAssets();
}
