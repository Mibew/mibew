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

use Symfony\Component\HttpFoundation\Request;

/**
 * Contains actions for all translation functionality.
 */
class TranslationController extends AbstractController
{
    /**
     * Generates list of all localization constants in the system.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function indexAction(Request $request)
    {
        $operator = $this->getOperator();

        $source = $request->query->get('source');
        if (!preg_match("/^[\w-]{2,5}$/", $source)) {
            $source = DEFAULT_LOCALE;
        }

        $target = $request->query->get('target');
        if (!preg_match("/^[\w-]{2,5}$/", $target)) {
            $target = CURRENT_LOCALE;
        }

        $lang1 = load_messages($source);
        $lang2 = load_messages($target);

        $page = array(
            'lang1' => $source,
            'lang2' => $target,
            'title1' => isset($lang1['localeid']) ? $lang1['localeid'] : $source,
            'title2' => isset($lang2['localeid']) ? $lang2['localeid'] : $target,
            'errors' => array(),
        );

        // Load list of all available locales.
        $locales_list = array();
        $all_locales = get_available_locales();
        foreach ($all_locales as $loc) {
            $locales_list[] = array('id' => $loc, 'name' => getlocal('localeid', null, $loc));
        }

        // Prepare localization constants to display.
        $result = array();
        $all_keys = array_keys($lang1);
        foreach ($all_keys as $key) {
            $t_source = htmlspecialchars($lang1[$key]);
            if (isset($lang2[$key])) {
                $value = htmlspecialchars($lang2[$key]);
            } else {
                $value = "<font color=\"#c13030\"><b>absent</b></font>";
            }
            $result[] = array(
                'id' => $key,
                'l1' => $t_source,
                'l2' => $value,
            );
        }

        // Sort localization constants in the specified order.
        $order = $request->query->get('sort');
        if (!in_array($order, array('id', 'l1'))) {
            $order = 'id';
        }

        if ($order == 'id') {
            usort(
                $result,
                function ($a, $b) {
                    return strcmp($a['id'], $b['id']);
                }
            );
        } elseif ($order == 'l1') {
            usort(
                $result,
                function ($a, $b) {
                    return strcmp($a['l1'], $b['l1']);
                }
            );
        }

        $pagination = setup_pagination($result, 100);
        $page['pagination'] = $pagination['info'];
        $page['pagination.items'] = $pagination['items'];
        $page['formtarget'] = $target;
        $page['formsource'] = $source;
        $page['availableLocales'] = $locales_list;
        $page['availableOrders'] = array(
            array('id' => 'id', 'name' => getlocal('translate.sort.key')),
            array('id' => 'l1', 'name' => getlocal('translate.sort.lang')),
        );
        $page['formsort'] = $order;
        $page['title'] = getlocal('page.translate.title');
        $page['menuid'] = 'translation';
        $page = array_merge($page, prepare_menu($operator));

        return $this->render('translations', $page);
    }

    /**
     * Builds a page with form for edit localization constant.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function showEditFormAction(Request $request)
    {
        set_csrf_token();

        $operator = $this->getOperator();
        $string_id = $request->attributes->get('string_id');

        $source = $request->query->get('source');
        if (!preg_match("/^[\w-]{2,5}$/", $source)) {
            $source = DEFAULT_LOCALE;
        }

        $target = $request->query->has('target')
            ? $request->query->get('target')
            : $request->request->get('target');
        if (!preg_match("/^[\w-]{2,5}$/", $target)) {
            $target = CURRENT_LOCALE;
        }

        $lang1 = load_messages($source);
        $lang2 = load_messages($target);

        $page = array(
            'title1' => isset($lang1['localeid']) ? $lang1['localeid'] : $source,
            'title2' => isset($lang2['localeid']) ? $lang2['localeid'] : $target,
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from the "submitEditForm" action.
            'errors' => $request->attributes->get('errors', array()),
        );


        $translation = isset($lang2[$string_id]) ? $lang2[$string_id] : '';
        // Override translation value from the request if needed.
        if ($request->request->has('translation')) {
            $translation = $request->request->get('translation');
        }

        $page['saved'] = false;
        $page['key'] = $string_id;
        $page['target'] = $target;
        $page['formoriginal'] = isset($lang1[$string_id]) ? $lang1[$string_id] : '<b><unknown></b>';
        $page['formtranslation'] = $translation;
        $page['title'] = getlocal('page.translate.title');
        $page = array_merge(
            $page,
            prepare_menu($operator, false)
        );

        return $this->render('translation_edit', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\TranslateController::showEditFormAction()}
     * method.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function submitEditFormAction(Request $request)
    {
        csrf_check_token($request);

        $operator = $this->getOperator();
        $string_id = $request->attributes->get('string_id');
        $errors = array();

        $target = $request->request->get('target');
        if (!preg_match("/^[\w-]{2,5}$/", $target)) {
            $target = CURRENT_LOCALE;
        }

        $translation = $request->request->get('translation');
        if (!$translation) {
            $errors[] = no_field("form.field.translation");
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The form should be rebuild. Invoke appropriate action.
            return $this->showEditFormAction($request);
        }

        save_message($target, $string_id, $translation);

        $page['saved'] = true;
        $page['title'] = getlocal("page.translate.title");
        $page = array_merge(
            $page,
            prepare_menu($operator, false)
        );

        return $this->render('translation_edit', $page);
    }
}
