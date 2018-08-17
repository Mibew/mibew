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

namespace Mibew\Controller\Settings;

use Mibew\Settings;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains actions which are related with features system settings.
 */
class FeaturesController extends AbstractController
{
    /**
     * Builds a page with form for features system settings.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function showFormAction(Request $request)
    {
        $operator = $this->getOperator();
        $page = array(
            'agentId' => '',
            'errors' => array(),
        );

        // Load all needed options and fill form with them.
        $options = $this->getOptionsList();
        foreach ($options as $opt) {
            $page['form' . $opt] = (Settings::get($opt) == '1');
        }

        // Load all needed featured values and fill the form.
        $values = $this->getValuesList();
        foreach ($values as $val) {
            $page['form' . $val] = Settings::get($val);
        }

        $page['canmodify'] = is_capable(CAN_ADMINISTRATE, $operator);
        $page['stored'] = $request->query->get('stored');
        $page['title'] = getlocal('Messenger settings');
        $page['menuid'] = 'settings';
        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

        $this->getAssetManager()->attachJs('js/compiled/features.js');

        return $this->render('settings_features', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\Settings\FeaturesController::showFormAction()}
     * method.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function submitFormAction(Request $request)
    {
        csrf_check_token($request);

        // Update options in the database.
        $options = $this->getOptionsList();
        foreach ($options as $opt) {
            $value = $request->request->get($opt) == 'on' ? '1' : '0';
            Settings::set($opt, $value);
        }

        // Update featured values in the database.
        $values = $this->getValuesList();
        foreach ($values as $val) {
            $value = $request->request->get($val);
            Settings::set($val, $value);
        }

        // Redirect the current operator to the same page using GET method.
        $redirect_to = $this->generateUrl(
            'settings_features',
            array('stored' => true)
        );

        return $this->redirect($redirect_to);
    }

    /**
     * Returns list with names of all features options.
     *
     * @return array Features options names.
     */
    protected function getOptionsList()
    {
        return array(
            'enableban',
            'usercanchangename',
            'enablegroups',
            'enablegroupsisolation',
            'enablestatistics',
            'enabletracking',
            'enablessl',
            'forcessl',
            'enablepresurvey',
            'surveyaskmail',
            'surveyaskgroup',
            'surveyaskmessage',
            'enablepopupnotification',
            'showonlineoperators',
            'enablecaptcha',
            'enableprivacypolicy',
            'trackoperators',
            'autocheckupdates',
        );
    }

    /**
     * Returns list with names of all featured values.
     *
     * @return array Featured values names.
     */
    protected function getValuesList()
    {
        return array(
            'privacypolicy',
        );
    }
}
