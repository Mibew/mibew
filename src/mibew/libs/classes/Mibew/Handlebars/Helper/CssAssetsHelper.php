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

namespace Mibew\Handlebars\Helper;

use Handlebars\Context;
use Handlebars\Helper as HelperInterface;
use Handlebars\SafeString;
use Handlebars\Template;
use Mibew\Asset\AssetManagerAwareInterface;
use Mibew\Asset\AssetManagerInterface;

/**
 * A helper that generates additional CSS list from assets attached to
 * Asset Manager.
 *
 * Example of usage:
 * <code>
 *   {{cssAssets}}
 * </code>
 */
class CssAssetsHelper implements HelperInterface, AssetManagerAwareInterface
{
    /**
     * @var AssetManagerInterface|null
     */
    protected $manager = null;

    /**
     * Class constructor.
     *
     * @param AssetUrlGeneratorInterface $manager An instance of Asset Manager.
     */
    public function __construct(AssetManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetManager()
    {
        return $this->manager;
    }

    /**
     * {@inheritdoc}
     */
    public function setAssetManager(AssetManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Template $template, Context $context, $args, $source)
    {
        $generator = $this->getAssetManager()->getUrlGenerator();
        $buffer = array();

        foreach ($this->getAssetManager()->getCssAssets() as $asset) {
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
     * Renders URL of an asset.
     *
     * @param string $url URL of an asset.
     * @return string HTML markup.
     */
    protected function renderUrl($url)
    {
        return sprintf(
            '<link rel="stylesheet" type="text/css" href="%s" />',
            safe_htmlspecialchars($url)
        );
    }

    /**
     * Renders content of an asset.
     *
     * @param string $content Content of an asset.
     * @return string HTML markup.
     */
    protected function renderContent($content)
    {
        return sprintf(
            '<style type="text/css">%s</style>',
            $content
        );
    }
}
