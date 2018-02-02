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

use Mibew\Http\Exception\BadRequestException;
use Mibew\Mail\Utils as MailUtils;
use Mibew\Settings;
use Mibew\Style\ChatStyle;
use Mibew\Style\InvitationStyle;
use Mibew\Style\PageStyle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Contains actions which are related with common system settings.
 */
class CommonController extends AbstractController
{
    /**
     * Builds a page with form for common system settings.
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

        // Load settings values from the database
        $options = array(
            'email',
            'title',
            'logo',
            'hosturl',
            'usernamepattern',
            'chattitle',
            'sendmessagekey',
            'cron_key',
            'left_messages_locale',
        );

        $params = array();
        foreach ($options as $opt) {
            $params[$opt] = Settings::get($opt);
        }

        // Set form values
        $form = $request->request;

        $page['formemail'] = $form->get('email', $params['email']);
        $page['formleftmessageslocale'] = $form->get('leftmessageslocale', $params['left_messages_locale']);
        $page['formtitle'] = $form->get('title', $params['title']);
        $page['formlogo'] = $form->get('logo', $params['logo']);
        $page['formhosturl'] = $form->get('hosturl', $params['hosturl']);
        $page['formusernamepattern'] = $form->get('usernamepattern', $params['usernamepattern']);
        $page['formchatstyle'] = $form->get('chatstyle', ChatStyle::getDefaultStyle());
        $page['formpagestyle'] = $form->get('pagestyle', PageStyle::getDefaultStyle());
        $page['formchattitle'] = $form->get('chattitle', $params['chattitle']);
        $page['formsendmessagekey'] = $form->get('sendmessagekey', $params['sendmessagekey']);
        $page['formcronkey'] = $form->get('cronkey', $params['cron_key']);

        if (Settings::get('enabletracking')) {
            $page['forminvitationstyle'] = $form->get('invitationstyle', InvitationStyle::getDefaultStyle());
            $page['availableInvitationStyles'] = InvitationStyle::getAvailableStyles();
        }

        $page['availableLocales'] = get_available_locales();
        $page['availableChatStyles'] = ChatStyle::getAvailableStyles();
        $page['availablePageStyles'] = PageStyle::getAvailableStyles();
        $page['chatStylePreviewPath'] = $this->generateUrl('style_preview', array('type' => 'chat'));
        $page['pageStylePreviewPath'] = $this->generateUrl('style_preview', array('type' => 'page'));
        $page['invitationStylePreviewPath'] = $this->generateUrl('style_preview', array('type' => 'invitation'));
        $page['stored'] = $request->query->has('stored');
        $page['enabletracking'] = Settings::get('enabletracking');
        $page['cron_path'] = $this->generateUrl(
            'cron',
            array('cron_key' => $params['cron_key']),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $page['title'] = getlocal('Messenger settings');
        $page['menuid'] = 'settings';

        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

        return $this->render('settings_common', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\Settings\CommonController::showFormAction()}
     * method.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws BadRequestException If one or more parameters of the request have
     *   wrong format.
     */
    public function submitFormAction(Request $request)
    {
        csrf_check_token($request);

        $errors = array();
        $params = array();

        $params['email'] = $request->request->get('email');
        $params['title'] = $request->request->get('title');
        $params['logo'] = $request->request->get('logo');
        $params['hosturl'] = $request->request->get('hosturl');
        $params['usernamepattern'] = $request->request->get('usernamepattern');
        $params['chattitle'] = $request->request->get('chattitle');
        $params['cron_key'] = $request->request->get('cronkey');

        $send_key = $request->request->get('sendmessagekey');
        if (!preg_match("/^c?enter$/", $send_key)) {
            throw new BadRequestException('Wrong format of "sendmessagekey" field.');
        }
        $params['sendmessagekey'] = $send_key;

        $params['left_messages_locale'] = $request->request->get('leftmessageslocale');
        if (!in_array($params['left_messages_locale'], get_available_locales())) {
            $params['left_messages_locale'] = get_home_locale();
        }

        if ($params['email'] && !MailUtils::isValidAddress($params['email'])) {
            $errors[] = getlocal('Enter a valid email address');
        }

        if (preg_match("/^[0-9A-Za-z]*$/", $params['cron_key']) == 0) {
            $errors[] = getlocal('Use only Latin letters(upper and lower case) and numbers in cron key.');
        }

        // Load styles configs
        $chat_style = $request->request->get('chat_style', ChatStyle::getDefaultStyle());
        $chat_style_list = ChatStyle::getAvailableStyles();
        if (!in_array($chat_style, $chat_style_list)) {
            $chat_style = $chat_style_list[0];
        }

        $page_style = $request->request->get('page_style', PageStyle::getDefaultStyle());
        $page_style_list = PageStyle::getAvailableStyles();
        if (!in_array($page_style, $page_style_list)) {
            $page_style = $page_style_list[0];
        }

        if (Settings::get('enabletracking')) {
            $invitation_style = $request->request->get(
                'invitation_style',
                InvitationStyle::getDefaultStyle()
            );
            $invitation_style_list = InvitationStyle::getAvailableStyles();
            if (!in_array($invitation_style, $invitation_style_list)) {
                $invitation_style = $invitation_style_list[0];
            }
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The form should be rebuild. Invoke appropriate action.
            return $this->showFormAction($request);
        }

        // Update system settings
        foreach ($params as $key => $value) {
            Settings::set($key, $value);
        }

        // Update styles params
        ChatStyle::setDefaultStyle($chat_style);
        PageStyle::setDefaultStyle($page_style);
        if (Settings::get('enabletracking')) {
            InvitationStyle::setDefaultStyle($invitation_style);
        }

        // Redirect the user to the same page using GET method
        $redirect_to = $this->generateUrl('settings_common', array('stored' => true));

        return $this->redirect($redirect_to);
    }
}
