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

namespace Mibew\Controller\Localization;

use Mibew\Controller\AbstractController;
use Stash\Invalidation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Generates translation file for a client side application.
 */
class JsTranslationController extends AbstractController
{
    /**
     * Generates a JavaScript file with client-side localization constants.
     *
     * @param Request $request Incoming request.
     * @return Response Prepared JavaScript file with client side localization
     * constants.
     */
    public function indexAction(Request $request)
    {
        $locale = $request->attributes->get('locale');

        $item = $this->getCache()->getItem('translation/js/' . $locale);
        $content = $item->get(Invalidation::OLD);
        if ($item->isMiss()) {
            $item->lock();

            $messages = load_messages($locale);
            $content = sprintf(
                '%s(%s);',
                'Mibew.Localization.set',
                json_encode($messages)
            );

            $item->set($content);
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/javascript');
        $response->setContent($content);

        return $response;
    }
}
