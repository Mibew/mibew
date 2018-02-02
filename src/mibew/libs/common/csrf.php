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

use Symfony\Component\HttpFoundation\Request;
use Mibew\Http\Exception\BadRequestException;

/**
 * Checks authorization token for CSRF attack.
 *
 * @param Request $request Incoming request. If it is not specified values from
 * $_POST and $_GET arrays will be used.
 *
 * @throws BadRequestException If CSRF token check is faild.
 */
function csrf_check_token(Request $request)
{
    set_csrf_token();

    $token = $request->isMethod('POST')
        ? $token = $request->request->get('csrf_token', false)
        : $token = $request->query->get('csrf_token', false);

    if ($token !== $_SESSION[SESSION_PREFIX . 'csrf_token']) {
        throw new BadRequestException('CSRF failure');
    }

    return;
}

function get_csrf_token_input()
{
    set_csrf_token();

    return '<input name="csrf_token" type="hidden" value="' . $_SESSION[SESSION_PREFIX . 'csrf_token'] . '" />';
}

function get_csrf_token()
{
    set_csrf_token();

    return $_SESSION[SESSION_PREFIX . 'csrf_token'];
}

/**
 * Sets CSRF token.
 *
 * This function is internal and should not be used directly in controllers.
 */
function set_csrf_token()
{
    if (!isset($_SESSION[SESSION_PREFIX . 'csrf_token'])) {
        $_SESSION[SESSION_PREFIX . 'csrf_token'] = sha1(session_id() . (function_exists('openssl_random_pseudo_bytes')
            ? openssl_random_pseudo_bytes(32)
            : (time() + microtime()) . mt_rand(0, 99999999)));
    }
}
