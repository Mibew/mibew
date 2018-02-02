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
 * Contains actions which are related with performance system settings.
 */
class PerformanceController extends AbstractController
{
    /**
     * Builds a page with form for performance system settings.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function showFormAction(Request $request)
    {
        $operator = $this->getOperator();
        $page = array(
            'agentId' => '',
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from the "submitForm" action.
            'errors' => $request->attributes->get('errors', array()),
        );

        // Load settings from the database
        $options = array(
            'online_timeout',
            'connection_timeout',
            'updatefrequency_operator',
            'updatefrequency_chat',
            'max_connections_from_one_host',
            'updatefrequency_tracking',
            'visitors_limit',
            'invitation_lifetime',
            'tracking_lifetime',
            'thread_lifetime',
            'max_uploaded_file_size',
        );

        $params = array();
        foreach ($options as $opt) {
            $params[$opt] = Settings::get($opt);
        }

        // Build form values
        $form = $request->request;

        $page['formonlinetimeout'] = $form->get('onlinetimeout', $params['online_timeout']);
        $page['formconnectiontimeout'] = $form->get('connectiontimeout', $params['connection_timeout']);
        $page['formfrequencyoperator'] = $form->get('frequencyoperator', $params['updatefrequency_operator']);
        $page['formfrequencychat'] = $form->get('frequencychat', $params['updatefrequency_chat']);
        $page['formonehostconnections'] = $form->get('onehostconnections', $params['max_connections_from_one_host']);
        $page['formthreadlifetime'] = $form->get('threadlifetime', $params['thread_lifetime']);
        $page['formmaxuploadedfilesize'] = $form->get('maxuploadedfilesize', $params['max_uploaded_file_size']);

        if (Settings::get('enabletracking')) {
            $page['formfrequencytracking'] = $form->get('frequencytracking', $params['updatefrequency_tracking']);
            $page['formvisitorslimit'] = $form->get('visitorslimit', $params['visitors_limit']);
            $page['forminvitationlifetime'] = $form->get('invitationlifetime', $params['invitation_lifetime']);
            $page['formtrackinglifetime'] = $form->get('trackinglifetime', $params['tracking_lifetime']);
        }

        $page['enabletracking'] = Settings::get('enabletracking');
        $page['stored'] = $request->query->get('stored');
        $page['title'] = getlocal("Messenger settings");
        $page['menuid'] = "settings";

        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

        return $this->render('settings_performance', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\Settings\PerformanceController::showFormAction()}
     * method.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function submitFormAction(Request $request)
    {
        csrf_check_token($request);

        $errors = array();
        $params = array();

        $params['online_timeout'] = $request->request->get('onlinetimeout');
        if (!is_numeric($params['online_timeout'])) {
            $errors[] = wrong_field("Operator online time threshold");
        }

        $params['connection_timeout'] = $request->request->get('connectiontimeout');
        if (!is_numeric($params['connection_timeout'])) {
            $errors[] = wrong_field("Connection timeout for messaging window");
        }

        $params['updatefrequency_operator'] = $request->request->get('frequencyoperator');
        if (!is_numeric($params['updatefrequency_operator'])) {
            $errors[] = wrong_field("Operator's console refresh time");
        }

        $params['updatefrequency_chat'] = $request->request->get('frequencychat');
        if (!is_numeric($params['updatefrequency_chat'])) {
            $errors[] = wrong_field("Chat refresh time");
        }

        $params['max_connections_from_one_host'] = $request->request->get('onehostconnections');
        if (!is_numeric($params['max_connections_from_one_host'])) {
            $errors[] = getlocal("\"Max number of threads\" field should be a number");
        }

        $params['thread_lifetime'] = $request->request->get('threadlifetime');
        if (!is_numeric($params['thread_lifetime'])) {
            $errors[] = getlocal("\"Thread lifetime\" field should be a number");
        }

        if (Settings::get('enabletracking')) {
            $params['updatefrequency_tracking'] = $request->request->get('frequencytracking');
            if (!is_numeric($params['updatefrequency_tracking'])) {
                $errors[] = wrong_field("Tracking refresh time");
            }

            $params['visitors_limit'] = $request->request->get('visitorslimit');
            if (!is_numeric($params['visitors_limit'])) {
                $errors[] = wrong_field("Limit for tracked visitors list");
            }

            $params['invitation_lifetime'] = $request->request->get('invitationlifetime');
            if (!is_numeric($params['invitation_lifetime'])) {
                $errors[] = wrong_field("Invitation lifetime");
            }

            $params['tracking_lifetime'] = $request->request->get('trackinglifetime');
            if (!is_numeric($params['tracking_lifetime'])) {
                $errors[] = wrong_field("Track lifetime");
            }
        }

        $params['max_uploaded_file_size'] = $request->request->get('maxuploadedfilesize');
        if (!is_numeric($params['max_uploaded_file_size'])) {
            $errors[] = wrong_field("Maximum size of uploaded files");
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The form should be rebuild. Invoke appropriate action.
            return $this->showFormAction($request);
        }

        // Update settings in the database
        foreach ($params as $key => $value) {
            Settings::set($key, $value);
        }

        // Redirect the current operator to the same page using get method.
        $redirect_to = $this->generateUrl('settings_performance', array('stored' => true));

        return $this->redirect($redirect_to);
    }
}
