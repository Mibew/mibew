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

use Mibew\RequestProcessor\ThreadProcessor;
use Mibew\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains actions which are related with thread itself.
 */
class ThreadController extends AbstractController
{
    /**
     * Update threads state.
     *
     * @param Request $request Incoming request.
     * @return A set of data to update the thread at client side.
     */
    public function updateAction(Request $request)
    {
        $processor = ThreadProcessor::getInstance();
        // TODO: Remove bufferization after *Processor classes will be rewritten
        // to play nice with symfony request/response objects
        ob_start();
        $processor->receiveRequest($request->request->get('data'));
        $content = ob_get_clean();

        return $content;
    }
}
