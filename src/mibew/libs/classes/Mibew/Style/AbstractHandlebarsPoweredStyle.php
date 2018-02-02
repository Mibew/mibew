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

namespace Mibew\Style;

use Handlebars\Handlebars as HandlebarsEngine;
use Mibew\Handlebars\Helpers;
use Mibew\Handlebars\HandlebarsAwareInterface;

/**
 * A base class for all styles that use Handlebars templates engine.
 */
abstract class AbstractHandlebarsPoweredStyle extends AbstractStyle implements HandlebarsAwareInterface
{
    /**
     * An instance of Handlebars template engine.
     *
     * @var HandlebarsEngine
     */
    protected $templateEngine = null;

    /**
     * {@inheritdoc}
     */
    public function getHandlebars()
    {
        if (is_null($this->templateEngine)) {
            $templates_loader = new \Handlebars\Loader\FilesystemLoader(
                MIBEW_FS_ROOT . '/' . $this->getFilesPath() . '/templates_src/server_side/'
            );

            $this->templateEngine = new \Handlebars\Handlebars(array(
                'loader' => $templates_loader,
                'partials_loader' => $templates_loader,
                'helpers' => new Helpers(),
            ));

            // Use custom function to escape strings
            $this->templateEngine->setEscape('safe_htmlspecialchars');
            $this->templateEngine->setEscapeArgs(array());
        }

        return $this->templateEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function setHandlebars(HandlebarsEngine $engine)
    {
        $this->templateEngine = $engine;
    }
}
