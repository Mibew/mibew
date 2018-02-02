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

namespace Mibew\Handlebars\Helper;

use Handlebars\Context;
use Handlebars\Helper as HelperInterface;
use Handlebars\SafeString;
use Handlebars\Template;
use Mibew\Asset\AssetManagerAwareInterface;
use Mibew\Asset\AssetManagerInterface;

/**
 * Contains for basic functionality for all helpers which renders assets lists.
 */
abstract class AbstractAssetsHelper implements HelperInterface
{
    /**
     * @var AssetManagerAwareInterface|null
     */
    protected $assetManagerContainer = null;

    /**
     * Class constructor.
     *
     * @param AssetManagerAwareInterface $manager_container An object which know
     * where to get an appropriate Asset Manager.
     */
    public function __construct(AssetManagerAwareInterface $manager_container)
    {
        $this->assetManagerContainer = $manager_container;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Template $template, Context $context, $args, $source)
    {
        $generator = $this->getAssetManager()->getUrlGenerator();
        $buffer = array();

        foreach ($this->getAssetsList() as $asset) {
            switch ($asset['type']) {
                case AssetManagerInterface::ABSOLUTE_URL:
                    $buffer[] = $this->renderUrl($asset['content']);
                    break;

                case AssetManagerInterface::RELATIVE_URL:
                    $buffer[] = $this->renderUrl($generator->generate($asset['content']));
                    break;

                case AssetManagerInterface::INLINE:
                    $buffer[] = $this->renderContent($asset['content']);
                    break;

                default:
                    throw new \RuntimeException(sprintf(
                        'Unknown asset type "%s"',
                        $asset['type']
                    ));
            }
        }

        return new SafeString(implode("\n", $buffer));
    }

    /**
     * Extracts asset manager from the asset manager's container related with
     * the object.
     *
     * @return AssetManagerInterface
     */
    protected function getAssetManager()
    {
        return $this->assetManagerContainer->getAssetManager();
    }

    /**
     * Renders URL of an asset.
     *
     * @param string $url URL of an asset.
     * @return string HTML markup.
     */
    abstract protected function renderUrl($url);

    /**
     * Renders content of an asset.
     *
     * @param string $content Content of an asset.
     * @return string HTML markup.
     */
    abstract protected function renderContent($content);

    /**
     * Retrieves list of assets which should be rendered by the helper.
     *
     * @return array List of assets. See
     *   {@link \Mibew\Asset\AssetManagerInterface::getJsAssets()} and
     *   {@link \Mibew\Asset\AssetManagerInterface::getCssAssets()} for details
     *   about array's structure.
     */
    abstract protected function getAssetsList();
}
