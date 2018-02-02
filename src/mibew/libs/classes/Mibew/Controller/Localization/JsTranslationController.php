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
            // Store JSON-encoded data to reduce count of json_encode calls.
            $content = sprintf(
                '%s(%s);',
                'Mibew.Localization.set',
                json_encode($messages)
            );

            $item->set($content);
        }

        // Session is started automatically during application initialization
        // and PHP sets "Cache-Control" and "Expires" headers to forbid caching
        // and to keep the session private. In this script we actually does not
        // use session stuff, thus we can remove these headers to provide
        // caching. Notice that all headers are removed to clear "Set-Cookie"
        // header with session ID and may be some other unsafe headers that
        // must not be cached.
        header_remove();

        // The whole response body (JSON-encoded with a callback function) is
        // cached via cache backend, thus it's simpler to use Symfony's
        // Response class instead of JsonResponse.
        $response = new Response();
        $response->headers->set('Content-Type', 'text/javascript');

        // Set various cache headers
        $response->setPublic();
        $response->setMaxAge(120);
        if ($item->getCreation()) {
            // Creation field can be unavailable for some cache drivers.
            $response->setLastModified($item->getCreation());
        }
        $response->setETag(sha1($content));

        if ($response->isNotModified($request)) {
            $response->setNotModified();

            // We does not need to send content for the client. Just return 304
            // status code.
            return $response;
        }

        // Pass the whole response for the client.
        $response->setContent($content);

        return $response;
    }
}
