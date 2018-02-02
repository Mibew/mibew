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

namespace Mibew\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Renders CAPTCHA image.
 */
class CaptchaController extends AbstractController
{
    /**
     * Returns captcha image content.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function drawAction(Request $request)
    {
        $captcha_code = gen_captcha();
        $_SESSION[SESSION_PREFIX . 'mibew_captcha'] = $captcha_code;
        $image = draw_captcha($captcha_code, true);

        $response = new Response(
            $image,
            Response::HTTP_OK,
            array('content-type' => 'image/jpeg')
        );

        return $response;
    }
}
