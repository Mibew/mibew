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

namespace Mibew\Controller\Chat;

use Mibew\Controller\AbstractController as BaseAbstractController;
use Mibew\Settings;
use Mibew\Style\ChatStyle;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains base actions which are related with operator's and user's chat
 * windows.
 */
abstract class AbstractController extends BaseAbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function getStyle()
    {
        if (is_null($this->style)) {
            $this->style = $this->prepareStyle(new ChatStyle(ChatStyle::getDefaultStyle()));
        }

        return $this->style;
    }

    /**
     * Checks if the user should be forced to use SSL connections.
     *
     * @param Request $request Request to check.
     * @return boolean|\Symfony\Component\HttpFoundation\RedirectResponse False
     *   if the redirect is not needed and redirect response object otherwise.
     */
    protected function sslRedirect(Request $request)
    {
        $need_redirect = Settings::get('enablessl') == '1'
            && Settings::get('forcessl') == '1'
            && !$request->isSecure();

        if (!$need_redirect) {
            return false;
        }

        if (null !== ($qs = $request->getQueryString())) {
            $qs = '?'.$qs;
        }

        $path = 'https://' . $request->getHttpHost() . $request->getBasePath()
            . $request->getPathInfo() . $qs;

        return $this->redirect($path);
    }
}
