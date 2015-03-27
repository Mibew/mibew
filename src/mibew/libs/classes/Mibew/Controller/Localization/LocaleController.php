<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2015 the original author or authors.
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

use Mibew\Http\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains actions which are related with locales management.
 */
class LocaleController extends AbstractController
{
    /**
     * Generates list of all locales in the system.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function indexAction(Request $request)
    {
        $operator = $this->getOperator();
        $page = array(
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from other actions.
            'errors' => $request->attributes->get('errors', array()),
        );

        $fs_locales = discover_locales();
        $available_locales = get_available_locales();

        $locales_list = array();
        foreach ($fs_locales as $locale) {
            $locales_list[] = array(
                'code' => $locale,
                'name' => $this->getLocaleName($locale),
                'isDisabled' => !in_array($locale, $available_locales),
            );
        }

        $page['localesList'] = $locales_list;
        $page['title'] = getlocal('Locales');
        $page['menuid'] = 'translation';
        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

        return $this->render('locales', $page);
    }

    /**
     * Enables a locale.
     *
     * @param Request $request Incoming request.
     * @return \Symfony\Component\HttpFoundation\Response A response object.
     * @throws NotFoundException If the locale which should be enabled is not
     *   found.
     */
    public function enableAction(Request $request)
    {
        csrf_check_token($request);

        $locale = $request->attributes->get('locale');

        // Check if locale exists.
        if (!in_array($locale, discover_locales())) {
            throw new NotFoundException();
        }

        // Enable locale if it is needed and redirect the operator to the
        // locales page.
        if (!in_array($locale, get_available_locales())) {
            enable_locale($locale);
        }

        return $this->redirect($this->generateUrl('locales'));
    }

    /**
     * Disables a locale.
     *
     * @param Request $request Incoming request.
     * @return \Symfony\Component\HttpFoundation\Response A response object.
     * @throws NotFoundException If the locale which should be disabled is not
     *   found.
     */
    public function disableAction(Request $request)
    {
        csrf_check_token($request);

        $locale = $request->attributes->get('locale');
        $errors = array();

        // Check if locale exists.
        if (!in_array($locale, discover_locales())) {
            throw new NotFoundException();
        }

        // Disable locale if we can do so.
        $available_locales = get_available_locales();
        if (in_array($locale, $available_locales)) {
            if (count($available_locales) > 1) {
                disable_locale($locale);
            } else {
                $errors[] = getlocal('Cannot disable all locales.');
            }
        }

        if (count($errors) != 0) {
            // Something went wrong. Re-render locales list.
            $request->attributes->set('errors', $errors);

            return $this->indexAction($request);
        }

        return $this->redirect($this->generateUrl('locales'));
    }
}
