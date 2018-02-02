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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Dumper\PoFileDumper;

/**
 * Contains actions for all translations export functionality.
 */
class TranslationExportController extends AbstractController
{
    /**
     * Builds a page with form for downloading translation file.
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

        // Load list of all available locales.
        $locales_list = array();
        $all_locales = get_available_locales();
        foreach ($all_locales as $loc) {
            $locales_list[] = array(
                'id' => $loc,
                'name' => $this->getLocaleName($loc)
            );
        }

        $page['localesList'] = $locales_list;
        $page['formtarget'] = $target;
        $page['title'] = getlocal('Translations export');
        $page['menuid'] = 'translation';
        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

        return $this->render('translation_export', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\TranslationExportController::showFormAction()}
     * method.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function submitFormAction(Request $request)
    {
        csrf_check_token($request);

        $target = $request->request->get('target');
        if (!preg_match("/^[\w-]{2,5}$/", $target)) {
            $target = get_current_locale();
        }

        $messages = load_messages($target);
        ksort($messages);

        $catalogue = new MessageCatalogue($target, array('messages' => $messages));
        $dumper = new PoFileDumper();
        $output = $dumper->format($catalogue);

        $response = new Response();
        $response->headers->set('Content-type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename=translation-%s.po', $target));
        $response->headers->set('Content-Length', strlen($output));
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->setContent($output);

        return $response;
    }
}
