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

use Mibew\Database;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with canned messages.
 */
class CannedMessageController extends AbstractController
{
    /**
     * Generates list of available canned messages.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function indexAction(Request $request)
    {
        $operator = $this->getOperator();
        $page = array(
            'errors' => array(),
        );

        // Build list of available locales
        $all_locales = get_available_locales();
        $locales_with_label = array();
        foreach ($all_locales as $id) {
            $locale_info = get_locale_info($id);
            $locales_with_label[] = array(
                'id' => $id,
                'name' => ($locale_info ? $locale_info['name'] : $id)
            );
        }
        $page['locales'] = $locales_with_label;

        // Get selected locale, if any.
        $lang = $this->extractLocale($request);
        if (!$lang) {
            $lang = in_array(get_current_locale(), $all_locales)
                ? get_current_locale()
                : $all_locales[0];
        }

        // Get selected group ID, if any.
        $group_id = $this->extractGroupId($request);
        if ($group_id) {
            $group = group_by_id($group_id);
            if (!$group) {
                $page['errors'][] = getlocal('No such group');
                $group_id = false;
            }
        }

        // Build list of available groups
        $all_groups = in_isolation($operator)
            ? get_groups_for_operator($operator)
            : get_all_groups();
        $page['groups'] = array();
        $page['groups'][] = array(
            'groupid' => '',
            'vclocalname' => getlocal('-all operators-'),
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
        $page['title'] = getlocal('Canned Messages');
        $page['menuid'] = 'canned';
        $page = array_merge($page, prepare_menu($operator));

        return $this->render('canned_messages', $page);
    }

    /**
     * Removes a canned message from the database.
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

        $key = $request->attributes->getInt('message_id');
        $db->query("DELETE FROM {cannedmessage} WHERE id = ?", array($key));

        // Redirect user to canned messages list. Use only "lang" and "group"
        // get params for the target URL.
        $parameters = array_intersect_key(
            $request->query->all(),
            array_flip(array('lang', 'group'))
        );

        return $this->redirect($this->generateUrl('canned_messages', $parameters));
    }

    /**
     * Builds a page with form for add/edit canned message.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function showEditFormAction(Request $request)
    {
        $operator = $this->getOperator();
        $message_id = $request->attributes->getInt('message_id');
        $page = array(
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from the "submitEditForm" action.
            'errors' => $request->attributes->get('errors', array()),
        );

        if ($message_id) {
            // Load existing message
            $canned_message = load_canned_message($message_id);
            if (!$canned_message) {
                $page['errors'][] = getlocal('No such message');
                $message_id = false;
            } else {
                $title = $canned_message['vctitle'];
                $message = $canned_message['vcvalue'];
            }
        } else {
            // Create new message
            $message = '';
            $title = '';
            $page['locale'] = $this->extractLocale($request);
            $page['groupid'] = $this->extractGroupId($request);
        }

        // Override message's fields from the request if it's needed. This
        // case will take place when submit handler fails.
        if ($request->request->has('title')) {
            $title = $request->request->get('title');
        }
        if ($request->request->has('message')) {
            $message = $request->request->get('message');
        }

        $page['saved'] = false;
        $page['key'] = $message_id;
        $page['formtitle'] = $title;
        $page['formmessage'] = $message;
        $page['formaction'] = $request->getBaseUrl() . $request->getPathInfo();
        $page['title'] = empty($message_id)
            ? getlocal('New Message')
            : getlocal('Edit Message');
        $page = array_merge($page, prepare_menu($operator, false));

        return $this->render('canned_message_edit', $page);
    }

    /**
     * Processes submitting of the forms which is generated in
     * {@link \Mibew\Controller\CannedMessageController::showEditFormAction()}
     * method.
     *
     * @param Request $request
     * @return string Rendered page content
     */
    public function submitEditFormAction(Request $request)
    {
        csrf_check_token($request);

        $operator = $this->getOperator();
        $message_id = $request->attributes->getInt('message_id');
        $errors = array();

        $title = $request->request->get('title');
        if (!$title) {
            $errors[] = no_field("Title");
        }

        $message = $request->request->get('message');
        if (!$message) {
            $errors[] = no_field("Message");
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The form should be rebuild. Invoke appropriate action.
            return $this->showEditFormAction($request);
        }

        if ($message_id) {
            save_canned_message($message_id, $title, $message);
        } else {
            $locale = $this->extractLocale($request);
            $group_id = $this->extractGroupId($request);
            add_canned_message($locale, $group_id, $title, $message);
        }
        $page['saved'] = true;
        $page = array_merge($page, prepare_menu($operator, false));

        return $this->render('canned_message_edit', $page);
    }

    /**
     * Extracts locale code from the request.
     *
     * @param Request $request
     * @return string|boolean Locale code or boolean false if the code cannot be
     * extracted.
     */
    protected function extractLocale(Request $request)
    {
        $lang = $request->isMethod('POST')
            ? $request->request->get('lang')
            : $request->query->get('lang');

        $all_locales = get_available_locales();
        $correct_locale = !empty($lang)
            && preg_match("/^[\w-]{2,5}$/", $lang)
            && in_array($lang, $all_locales);
        if (!$correct_locale) {
            return false;
        }

        return $lang;
    }

    /**
     * Extracts group ID from the request.
     *
     * @param Request $request
     * @return string|boolean Group ID or boolean false if the ID cannot be
     * extracted.
     */
    protected function extractGroupId(Request $request)
    {
        $group_id = $request->isMethod('POST')
            ? $request->request->get('group')
            : $request->query->get('group');

        if (!$group_id) {
            return false;
        }

        if (!preg_match("/^\d{1,10}$/", $group_id)) {
            return false;
        }

        return $group_id;
    }
}
