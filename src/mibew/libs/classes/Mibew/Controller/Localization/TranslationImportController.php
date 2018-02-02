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

use Mibew\Settings;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains actions for all translations import functionality.
 */
class TranslationImportController extends AbstractController
{
    /**
     * Builds a page with form for upload translation file.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function showFormAction(Request $request)
    {
        $operator = $this->getOperator();

        $target = $request->request->get('target');
        if (!preg_match("/^[\w-]{2,5}$/", $target)) {
            $target = get_current_locale();
        }

        $override = (bool)$request->request->get('override', false);

        $page = array(
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from other actions.
            'errors' => $request->attributes->get('errors', array()),
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

        $page['stored'] = $request->query->has('stored');
        $page['localesList'] = $locales_list;
        $page['formtarget'] = $target;
        $page['formoverride'] = $override;
        $page['title'] = getlocal('Translations import');
        $page['menuid'] = 'translation';
        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

        return $this->render('translation_import', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\TranslationImportController::showFormAction()}
     * method.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function submitFormAction(Request $request)
    {
        csrf_check_token($request);

        $errors = array();

        $target = $request->request->get('target');
        if (!preg_match("/^[\w-]{2,5}$/", $target)) {
            $target = get_current_locale();
        }

        $override = (bool)$request->request->get('override', false);

        // Validate uploaded file
        $file = $request->files->get('translation_file');
        if ($file) {
            // Process uploaded file.
            $orig_filename = $file->getClientOriginalName();
            $file_size = $file->getSize();

            if ($file_size == 0 || $file_size > Settings::get('max_uploaded_file_size')) {
                $errors[] = failed_uploading_file($orig_filename, "Uploaded file size exceeded");
            } elseif ($file->getClientOriginalExtension() != 'po') {
                $errors[] = failed_uploading_file($orig_filename, "Invalid file type");
            }
        } else {
            $errors[] = getlocal("No file selected");
        }

        // Try to process uploaded file
        if (count($errors) == 0) {
            try {
                // Try to import new messages.
                import_messages($target, $file->getRealPath(), $override);

                // Remove cached client side translations.
                $this->getCache()->getItem('translation/js/' . $target)->clear();

                // The file is not needed any more. Remove it.
                unlink($file->getRealPath());
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The form should be rebuild. Invoke appropriate action.
            return $this->showFormAction($request);
        }

        // Redirect the operator to the same page using GET method.
        $redirect_to = $this->generateUrl(
            'translation_import',
            array('stored' => true)
        );
        return $this->redirect($redirect_to);
    }
}
