<?php
/*
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

namespace Mibew\Controller;

use Mibew\Database;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with canned messages.
 */
class CannedMessageController extends AbstractController
{
    /**
     * Generate content for "canned_message" route.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function indexAction(Request $request)
    {
        set_csrf_token();

        $operator = $request->attributes->get('_operator');
        $page = array(
            'errors' => array(),
        );

        // Get selected locale, if any.
        $all_locales = get_available_locales();
        $locales_with_label = array();
        foreach ($all_locales as $id) {
            $locales_with_label[] = array(
                'id' => $id,
                'name' => getlocal_($id, 'names')
            );
        }
        $page['locales'] = $locales_with_label;

        $lang = $request->query->get('lang');
        $correct_locale = $lang
            && preg_match("/^[\w-]{2,5}$/", $lang)
            && in_array($lang, $all_locales);
        if (!$correct_locale) {
            $lang = in_array(CURRENT_LOCALE, $all_locales)
                ? CURRENT_LOCALE
                : $all_locales[0];
        }

        // Get selected group ID, if any.
        $group_id = $request->query->get('group');
        if ($group_id && preg_match("/^\d{0,8}$/", $group_id)) {
            $group = group_by_id($group_id);
            if (!$group) {
                $page['errors'][] = getlocal('page.group.no_such');
                $group_id = false;
            }
        } else {
            $group_id = false;
        }

        $all_groups = in_isolation($operator)
            ? get_all_groups_for_operator($operator)
            : get_all_groups();
        $page['groups'] = array();
        $page['groups'][] = array(
            'groupid' => '',
            'vclocalname' => getlocal('page.gen_button.default_group'),
            'level' => 0,
        );
        foreach ($all_groups as $g) {
            $page['groups'][] = $g;
        }

        // Get messages and setup pagination
        $canned_messages = load_canned_messages($lang, $group_id);
        foreach ($canned_messages as &$message) {
            $message['vctitle'] = $message['vctitle'];
            $message['vcvalue'] = $message['vcvalue'];
        }
        unset($message);

        $pagination = setup_pagination($canned_messages);
        $page['pagination'] = $pagination['info'];
        $page['pagination.items'] = $pagination['items'];

        // Buil form values
        $page['formlang'] = $lang;
        $page['formgroup'] = $group_id;

        // Set other needed page values and render the response
        $page['title'] = getlocal('canned.title');
        $page['menuid'] = 'canned';
        $page = array_merge($page, prepare_menu($operator));

        return $this->render('canned_message', $page);
    }

    /**
     * Generate content for "canned_message_delete" route.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function deleteAction(Request $request)
    {
        // Check for CSRF attack
        csrf_check_token($request);

        // Remove message from the database.
        $db = Database::getInstance();

        $key = (int)$request->attributes->get('message_id');
        $db->query("DELETE FROM {chatresponses} WHERE id = ?", array($key));

        // Redirect user to canned messages list. Use only "lang" and "group"
        // get params for the target URL.
        $parameters = array_intersect_key(
            $request->query->all(),
            array_flip(array('lang', 'group'))
        );

        return $this->redirect($this->generateUrl('canned_message', $parameters));
    }
}
