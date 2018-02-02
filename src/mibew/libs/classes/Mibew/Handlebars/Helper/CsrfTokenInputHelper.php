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

/**
 * Generates markup with hidden input tag for CSRF token.
 *
 * Example of usage:
 * <code>
 *   {{csrfTokenInput}}
 * </code>
 */
class CsrfTokenInputHelper implements HelperInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Template $template, Context $context, $args, $source)
    {
        return new SafeString(get_csrf_token_input());
    }
}
