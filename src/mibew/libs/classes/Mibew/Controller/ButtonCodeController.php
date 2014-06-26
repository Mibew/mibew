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

use Mibew\Http\Exception\BadRequestException;
use Mibew\Settings;
use Mibew\Style\ChatStyle;
use Mibew\Style\InvitationStyle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Represents actions that are related with button code generation.
 */
class ButtonCodeController extends AbstractController
{
    /**
     * Generates a page with Mibew button code form.
     *
     * @param Request $request Incoming request
     * @return Response Rendered content of the page.
     */
    public function generateAction(Request $request)
    {
        $operator = $this->getOperator();

        $page = array(
            'errors' => array(),
        );

        $image_locales_map = get_image_locales_map(MIBEW_FS_ROOT . '/locales');
        $image = $request->query->get('i', 'mibew');
        if (!isset($image_locales_map[$image])) {
            $page['errors'][] = 'Unknown image: ' . $image;
            $avail = array_keys($image_locales_map);
            $image = $avail[0];
        }
        $image_locales = $image_locales_map[$image];

        $style_list = ChatStyle::getAvailableStyles();
        $style_list[''] = getlocal('-from general settings-');
        $style = $request->query->get('style', '');
        if ($style && !in_array($style, $style_list)) {
            $style = '';
        }

        $invitation_style_list = InvitationStyle::getAvailableStyles();
        $invitation_style_list[''] = getlocal('-from general settings-');
        $invitation_style = $request->query->get('invitationstyle', '');
        if ($invitation_style && !in_array($invitation_style, $invitation_style_list)) {
            $invitation_style = '';
        }

        $locales_list = get_available_locales();

        $group_id = $request->query->getInt('group');
        if ($group_id && !group_by_id($group_id)) {
            $page['errors'][] = getlocal("No such group");
            $group_id = false;
        }

        $show_host = $request->query->get('hostname') == 'on';
        $force_secure = $request->query->get('secure') == 'on';
        $mod_security = $request->query->get('modsecurity') == 'on';

        $code_type = $request->query->get('codetype', 'button');
        if (!in_array($code_type, array('button', 'operator_code', 'text_link'))) {
            throw new BadRequestException('Wrong value of "codetype" param.');
        }

        $lang = $request->query->get('lang', '');
        if (!preg_match("/^[\w-]{2,5}$/", $lang)) {
            $lang = '';
        }

        $operator_code = ($code_type == 'operator_code');
        $generate_button = ($code_type == 'button');

        if ($generate_button) {
            $disable_invitation = false;

            if (!$lang || !in_array($lang, $image_locales)) {
                $lang = in_array(CURRENT_LOCALE, $image_locales)
                    ? CURRENT_LOCALE
                    : $image_locales[0];
            }

            $file = MIBEW_FS_ROOT . '/locales/${lang}/button/${image}_on.gif';
            $size = get_gifimage_size($file);

            $image_link_args = array(
                'i' => $image,
                'lang' => $lang,
            );
            if ($group_id) {
                $image_link_args['group'] = $group_id;
            }
            $host = ($force_secure ? 'https://' : 'http://') . $request->getHost();
            $image_href = ($show_host ? $host : '')
                . $this->generateUrl('button', $image_link_args, UrlGeneratorInterface::ABSOLUTE_PATH);

            $message = get_image(htmlspecialchars($image_href), $size[0], $size[1]);
        } else {
            $disable_invitation = true;

            if (!$lang || !in_array($lang, $locales_list)) {
                $lang = in_array(CURRENT_LOCALE, $locales_list) ? CURRENT_LOCALE : $locales_list[0];
            }

            $message = getlocal('Click to chat');
        }

        $page['buttonCode'] = generate_button(
            '',
            $lang,
            $style,
            $invitation_style,
            $group_id,
            $message,
            $show_host,
            $force_secure,
            $mod_security,
            $operator_code,
            $disable_invitation
        );
        $page['availableImages'] = array_keys($image_locales_map);
        $page['availableLocales'] = $generate_button ? $image_locales : $locales_list;
        $page['availableChatStyles'] = $style_list;
        $page['availableInvitationStyles'] = $invitation_style_list;
        $page['groups'] = get_groups_list();

        $page['availableCodeTypes'] = array(
            'button' => getlocal('button'),
            'operator_code' => getlocal('operator code field'),
            'text_link' => getlocal('text link')
        );

        $page['formgroup'] = $group_id;
        $page['formstyle'] = $style;
        $page['forminvitationstyle'] = $invitation_style;
        $page['formimage'] = $image;
        $page['formlang'] = $lang;
        $page['formhostname'] = $show_host;
        $page['formsecure'] = $force_secure;
        $page['formmodsecurity'] = $mod_security;
        $page['formcodetype'] = $code_type;

        $page['enabletracking'] = Settings::get('enabletracking');
        $page['operator_code'] = $operator_code;
        $page['generateButton'] = $generate_button;

        $page['title'] = getlocal("Button HTML code generation");
        $page['menuid'] = "getcode";

        $page = array_merge($page, prepare_menu($operator));

        return $this->render('button_code', $page);
    }
}
