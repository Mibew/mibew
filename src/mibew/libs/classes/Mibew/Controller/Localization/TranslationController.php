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

use Mibew\Database;
use Mibew\Http\Exception\NotFoundException;
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

        $target = $request->query->get('target');
        if (!preg_match("/^[\w-]{2,5}$/", $target)) {
            $target = get_current_locale();
        }

        $page = array(
            'localeName' => $this->getLocaleName($target),
            'errors' => array(),
        );

        // Load list of all available locales.
        $locales_list = array();
        $all_locales = get_available_locales();
        foreach ($all_locales as $loc) {
            $locales_list[] = array(
                'id' => $loc,
                'name' => $this->getLocaleName($loc)
            );
        }

        // Prepare localization constants to display.
        $strings = $this->loadStrings($target);
        foreach ($strings as $key => $item) {
            $strings[$key] = array(
                'id' => $item['translationid'],
                'source' => htmlentities($item['source']),
                'translation' => (empty($item['translation'])
                    ? "<font color=\"#c13030\"><b>absent</b></font>"
                    : htmlspecialchars($item['translation'])),
            );
        }

        // Sort localization constants in the specified order.
        $order = $request->query->get('sort');
        if (!in_array($order, array('source', 'translation'))) {
            $order = 'source';
        }

        if ($order == 'source') {
            usort(
                $strings,
                function ($a, $b) {
                    return strcmp($a['source'], $b['source']);
                }
            );
        } elseif ($order == 'translation') {
            usort(
                $strings,
                function ($a, $b) {
                    return strcmp($a['translation'], $b['translation']);
                }
            );
        }

        $pagination = setup_pagination($strings, 100);
        $page['pagination'] = $pagination['info'];
        $page['pagination.items'] = $pagination['items'];
        $page['formtarget'] = $target;
        $page['availableLocales'] = $locales_list;
        $page['availableOrders'] = array(
            array('id' => 'source', 'name' => getlocal('Source string')),
            array('id' => 'translation', 'name' => getlocal('Translation')),
        );
        $page['formsort'] = $order;
        $page['title'] = getlocal('Translations');
        $page['menuid'] = 'translation';
        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

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
        $operator = $this->getOperator();
        $string_id = $request->attributes->get('string_id');
        $string = $this->loadString($string_id);
        if (!$string) {
            throw new NotFoundException('The string is not found.');
        }

        $target = $string['locale'];

        $page = array(
            'localeName' => $this->getLocaleName($target),
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from the "submitEditForm" action.
            'errors' => $request->attributes->get('errors', array()),
        );

        // Override translation value from the request if needed.
        $translation = $request->request->get('translation', $string['translation']);

        $page['saved'] = false;
        $page['key'] = $string_id;
        $page['target'] = $target;
        $page['formoriginal'] = $string['source'];
        $page['formtranslation'] = $translation;
        $page['title'] = getlocal('Translations');
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
        $errors = array();

        $string_id = $request->attributes->get('string_id');
        $string = $this->loadString($string_id);
        if (!$string) {
            throw new NotFoundException('The string is not found.');
        }

        $target = $string['locale'];

        $translation = $request->request->get('translation');
        if (!$translation) {
            $errors[] = no_field("Translation");
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The form should be rebuild. Invoke appropriate action.
            return $this->showEditFormAction($request);
        }

        save_message($target, $string['source'], $translation);

        // Remove cached client side translations.
        $this->getCache()->getItem('translation/js/' . $target)->clear();

        $page['saved'] = true;
        $page['title'] = getlocal("Translations");
        $page = array_merge(
            $page,
            prepare_menu($operator, false)
        );

        return $this->render('translation_edit', $page);
    }

    /**
     * Loads all editable translation strings.
     *
     * @param string $locale Locale code.
     * @return array List of translated strings. Each element of this array is
     *   an associative with the following keys:
     *     - id: int, ID of the translated string in the database.
     *     - source: string, english string.
     *     - translation: string, translated string.
     */
    protected function loadStrings($locale)
    {
        $rows = Database::getInstance()->query(
            "SELECT * FROM {translation} WHERE locale = :locale",
            array(':locale' => $locale),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );

        return $rows ? $rows : array();
    }

    /**
     * Loads translated string from the database by its ID.
     *
     * @param int $id ID of the translated string in the database.
     * @return array|boolean Associative array with string info or boolean false
     *   it the string is not found.
     */
    protected function loadString($id)
    {
        $string = Database::getInstance()->query(
            "SELECT * FROM {translation} WHERE translationid = :id",
            array(':id' => $id),
            array('return_rows' => Database::RETURN_ONE_ROW)
        );

        return $string ? $string : false;
    }
}
