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

use Mibew\Settings;
use Mibew\Thread;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Represents button-related actions
 */
class ButtonController extends AbstractController
{
    /**
     * Returns content of the chat button.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function indexAction(Request $request)
    {
        $referer = $request->server->get('HTTP_REFERER', '');

        // We need to display message about visited page only if the visitor
        // really change it.
        $new_page = empty($_SESSION[SESSION_PREFIX . 'last_visited_page'])
            || $_SESSION[SESSION_PREFIX . 'last_visited_page'] != $referer;

        // Display message about page change
        if ($referer && isset($_SESSION[SESSION_PREFIX . 'threadid']) && $new_page) {
            $thread = Thread::load($_SESSION[SESSION_PREFIX . 'threadid']);
            if ($thread && $thread->state != Thread::STATE_CLOSED) {
                $msg = getlocal(
                    "Visitor navigated to {0}",
                    array($referer),
                    $thread->locale,
                    true
                );
                $thread->postMessage(Thread::KIND_FOR_AGENT, $msg);
            }
        }
        $_SESSION[SESSION_PREFIX . 'last_visited_page'] = $referer;

        $image = $request->query->get('i', '');
        if (!preg_match("/^\w+$/", $image)) {
            $image = 'mibew';
        }

        $lang = $request->query->get('lang', '');
        if (!preg_match("/^[\w-]{2,5}$/", $lang)) {
            $lang = '';
        }
        if (!$lang || !locale_is_available($lang)) {
            $lang = get_current_locale();
        }

        $group_id = $request->query->get('group', '');
        if (!preg_match("/^\d{1,8}$/", $group_id)) {
            $group_id = false;
        }
        if ($group_id) {
            if (Settings::get('enablegroups') == '1') {
                $group = group_by_id($group_id);
                if (!$group) {
                    $group_id = false;
                }
            } else {
                $group_id = false;
            }
        }

        // Get image file content
        $image_postfix = has_online_operators($group_id) ? "on" : "off";
        $file_name = "locales/${lang}/button/${image}_${image_postfix}.png";
        $content_type = 'image/png';
        if (!is_readable($file_name)) {
            // Fall back to .gif image
            $file_name = "locales/${lang}/button/${image}_${image_postfix}.gif";
            $content_type = 'image/gif';
        }

        $fh = fopen($file_name, 'rb');
        if ($fh) {
            // Create response with image in body
            $file_size = filesize($file_name);
            $content = fread($fh, $file_size);
            fclose($fh);
            $response = new Response($content, 200);

            // Set correct content info
            $response->headers->set('Content-Type', $content_type);
            $response->headers->set('Content-Length', $file_size);
        } else {
            $response = new Response('Not found', 404);
        }

        // Disable caching
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('no-store', true);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setExpires(new \DateTime('yesterday noon'));
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}
