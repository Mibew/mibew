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

namespace Mibew\Controller\Chat;

use Mibew\Asset\Generator\UrlGeneratorInterface;
use Mibew\Style\ChatStyle;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with dynamic styles loading.
 */
class StyleController extends AbstractController
{
    /**
     * Generates a JavaScript file with popup CSS file loader.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function loadPopupStyleAction(Request $request)
    {
        $style_name = $request->attributes->get('style');
        if (!$style_name) {
            $style_name = ChatStyle::getDefaultStyle();
        }

        $style = new ChatStyle($style_name);
        $configs = $style->getConfigurations();

        $response = new JsonResponse();
        if ($configs['chat']['iframe']['css']) {
            $generator = $this->getAssetManager()->getUrlGenerator();
            $css = $request->attributes->get('force_secure') ?
                    $generator->generateSecure(
                        $style->getFilesPath() . '/' . $configs['chat']['iframe']['css'],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ) :
                    $generator->generate(
                        $style->getFilesPath() . '/' . $configs['chat']['iframe']['css'],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
            $response->setData($css);
            $response->setCallback('Mibew.Utils.loadStyleSheet');
        }

        return $response;
    }
}
