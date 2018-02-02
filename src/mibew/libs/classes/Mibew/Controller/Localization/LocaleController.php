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

    /**
     * Builds locale edit page.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the locale with specified code is not found
     *   in the system.
     */
    public function showEditFormAction(Request $request)
    {
        $page = array(
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from the "submitEditForm" action.
            'errors' => $request->attributes->get('errors', array()),
        );

        $locale = $request->attributes->get('locale');

        // Check if locale exists and enabled.
        if (!in_array($locale, get_available_locales())) {
            throw new NotFoundException();
        }

        $info = get_locale_info($locale);

        $page['formtimelocale'] = $info['time_locale'];
        $page['formdateformatfull'] = $info['date_format']['full'];
        $page['formdateformatdate'] = $info['date_format']['date'];
        $page['formdateformattime'] = $info['date_format']['time'];

        // Override fields from the request if it's needed. This case will take
        // place when a save handler fails and passes the request to this
        // action.
        if ($request->isMethod('POST')) {
            $page['formtimelocale'] = $request->request->get('timelocale');
            $page['formdateformatfull'] = $request->request->get('dateformatfull');
            $page['formdateformatdate'] = $request->request->get('dateformatdate');
            $page['formdateformattime'] = $request->request->get('dateformattime');
        }

        $page['stored'] = $request->query->has('stored');
        $page['title'] = getlocal('Locale details');
        $page['menuid'] = 'translation';
        $page['formaction'] = $request->getBaseUrl() . $request->getPathInfo();
        $page = array_merge($page, prepare_menu($this->getOperator()));
        $page['tabs'] = $this->buildTabs($request);

        return $this->render('locale_edit', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\Localization\LocaleController::showEditFormAction()}
     * method.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the locale with specified code is not found
     *   in the system.
     */
    public function submitEditFormAction(Request $request)
    {
        csrf_check_token($request);

        $errors = array();
        $locale = $request->attributes->get('locale');
        $time_locale = $request->request->get('timelocale');
        $date_format_full = $request->request->get('dateformatfull');
        $date_format_date = $request->request->get('dateformatdate');
        $date_format_time = $request->request->get('dateformattime');

        if (!$locale) {
            throw new NotFoundException();
        }

        if (!$time_locale) {
            $errors[] = no_field('Time locale');
        }

        if (!$date_format_full) {
            $errors[] = no_field('Date format (full)');
        }

        if (!$date_format_date) {
            $errors[] = no_field('Date format (date)');
        }

        if (!$date_format_time) {
            $errors[] = no_field('Date format (time)');
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The form should be rebuild. Invoke appropriate action.
            return $this->showEditFormAction($request);
        }

        $locale_info = get_locale_info($locale);

        $locale_info['time_locale'] = $time_locale;
        $locale_info['date_format'] = array(
            'full' => $date_format_full,
            'date' => $date_format_date,
            'time' => $date_format_time,
        );

        // Save the locale
        set_locale_info($locale, $locale_info);

        // Redirect the user to edit page again to use GET method instead of
        // POST.
        $redirect_to = $this->generateUrl(
            'locale_edit',
            array(
                'locale' => $locale,
                'stored' => true,
            )
        );

        return $this->redirect($redirect_to);
    }
}
