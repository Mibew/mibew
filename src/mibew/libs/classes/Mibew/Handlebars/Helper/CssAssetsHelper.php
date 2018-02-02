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

/**
 * A helper that generates additional CSS list from assets attached to
 * Asset Manager.
 *
 * Example of usage:
 * <code>
 *   {{cssAssets}}
 * </code>
 */
class CssAssetsHelper extends AbstractAssetsHelper
{
    /**
     * {@inheritdoc}
     */
    protected function getAssetsList()
    {
        return $this->getAssetManager()->getCssAssets();
    }

    /**
     * {@inheritdoc}
     */
    protected function renderUrl($url)
    {
        return sprintf(
            '<link rel="stylesheet" type="text/css" href="%s" />',
            safe_htmlspecialchars($url)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function renderContent($content)
    {
        return sprintf(
            '<style type="text/css">%s</style>',
            $content
        );
    }
}
