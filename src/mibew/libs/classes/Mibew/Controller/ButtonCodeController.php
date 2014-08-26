<?php
/*
 * This file is a part of Mibew Messenger.
 *
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

        $image_locales_map = $this->getImageLocalesMap(MIBEW_FS_ROOT . '/locales');
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
                $lang = in_array(get_current_locale(), $image_locales)
                    ? get_current_locale()
                    : $image_locales[0];
            }

            $file = MIBEW_FS_ROOT . "/locales/{$lang}/button/{$image}_on.png";
            if (!is_readable($file)) {
                // Fallback to .gif image
                $file = MIBEW_FS_ROOT . "/locales/{$lang}/button/{$image}_on.gif";
            }
            $size = get_image_size($file);

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
                $lang = in_array(get_current_locale(), $locales_list)
                    ? get_current_locale()
                    : $locales_list[0];
            }

            $message = getlocal('Click to chat');
        }

        $page['buttonCode'] = $this->generateButton(
            $request,
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
        $page['groups'] = $this->getGroupsList();

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

    /**
     * Generates button code.
     *
     * @param string $request Request incoming request.
     * @param string $title Page title
     * @param string $locale RFC 5646 code for language
     * @param string $style name of available style from styles/dialogs folder
     * @param string $invitation_style_name name of avalabel style from
     * styles/invitations folder
     * @param integer $group chat group id
     * @param integer $inner chat link message or html code like image code
     * @param bool $show_host generated link contains protocol and domain or not
     * @param bool $force_secure force protocol to secure (https) or not
     * @param bool $mod_security add rule to remove protocol from document location
     * in generated javascript code
     * @param bool $operator_code add operator code to generated button code or not
     * @param bool $disable_invitation forcibly disable invitation regadless of
     * tracking settings
     *
     * @return string Generate chat button code
     */
    protected function generateButton(
        $request,
        $title,
        $locale,
        $style,
        $invitation_style_name,
        $group,
        $inner,
        $show_host,
        $force_secure,
        $mod_security,
        $operator_code,
        $disable_invitation
    ) {
        $host = ($force_secure ? 'https://' : 'http://') . $request->getHost();
        $base_url = ($show_host ? $host : '')
            . $request->getBasePath();

        $url_type = $show_host
            ? UrlGeneratorInterface::ABSOLUTE_URL
            : UrlGeneratorInterface::ABSOLUTE_PATH;

        // Build the main link
        $link_params = array();
        if ($locale) {
            $link_params['locale'] = $locale;
        }
        if ($style) {
            $link_params['style'] = $style;
        }
        if ($group) {
            $link_params['group'] = $group;
        }
        $link = ($show_host && $force_secure)
            ? $this->generateSecureUrl('chat_user_start', $link_params, $url_type)
            : $this->generateUrl('chat_user_start', $link_params, $url_type);

        $modsecfix = $mod_security ? ".replace('http://','').replace('https://','')" : "";
        $js_link = "'" . $link
            . (empty($link_params) ? '?' : '&amp;')
            . "url='+escape(document.location.href$modsecfix)+'&amp;referrer='+escape(document.referrer$modsecfix)";

        // Get popup window configurations
        if ($style) {
            $chat_style = new ChatStyle($style);
            $chat_configurations = $chat_style->getConfigurations();
            $popup_options = $chat_configurations['chat']['window_params'];
        } else {
            $popup_options = "toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1";
        }

        // Generate operator code field
        if ($operator_code) {
            $form_on_submit = "if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 "
                . "&amp;&amp; window.event.preventDefault) window.event.preventDefault();"
                . "this.newWindow = window.open({$js_link} + '&amp;operator_code=' "
                . "+ document.getElementById('mibewOperatorCodeField').value, 'mibew', '{$popup_options}');"
                . "this.newWindow.focus();this.newWindow.opener=window;return false;";
            $temp = '<form action="" onsubmit="' . $form_on_submit . '" id="mibewOperatorCodeForm">'
                . '<input type="text" id="mibewOperatorCodeField" />'
                . '</form>';
            return "<!-- mibew operator code field -->" . $temp . "<!-- / mibew operator code field -->";
        }

        // Generate button
        $temp = get_popup($link, "$js_link", $inner, $title, "mibew", $popup_options);
        if (!$disable_invitation && Settings::get('enabletracking')) {
            $widget_data = array();

            // Get actual invitation style instance
            if (!$invitation_style_name) {
                $invitation_style_name = InvitationStyle::getCurrentStyle();
            }
            $invitation_style = new InvitationStyle($invitation_style_name);

            // URL of file with additional CSS rules for invitation popup
            $widget_data['inviteStyle'] = $base_url . '/' .
                $invitation_style->getFilesPath() .
                '/invite.css';

            // Time between requests to the server in milliseconds
            $widget_data['requestTimeout'] = Settings::get('updatefrequency_tracking') * 1000;

            // URL for requests
            $widget_data['requestURL'] = ($show_host && $force_secure)
                ? $this->generateSecureUrl('widget_gateway', array())
                : $this->generateUrl('widget_gateway', array(), $url_type);

            // Locale for invitation
            $widget_data['locale'] = $locale;

            // Name of the cookie to track user. Use if third-party cookie blocked
            $widget_data['visitorCookieName'] = VISITOR_COOKIE_NAME;

            // Build additional button code
            $temp = preg_replace('/^(<a )/', '\1id="mibewAgentButton" ', $temp)
                . '<div id="mibewinvitation"></div>'
                . '<script type="text/javascript" src="'
                . $base_url . '/js/compiled/widget.js'
                . '"></script>'
                . '<script type="text/javascript">'
                . 'Mibew.Widget.init(' . json_encode($widget_data) . ')'
                . '</script>';
        }

        return "<!-- mibew button -->" . $temp . "<!-- / mibew button -->";
    }

    /**
     * Prepares list of group options.
     *
     * @return array Each element of the resultion array is an array with group
     *   info. See {@link get_all_groups()} description for more details.
     */
    protected function getGroupsList()
    {
        $result = array();
        $all_groups = get_all_groups();

        $result[] = array(
            'groupid' => '',
            'vclocalname' => getlocal("-all operators-"),
            'level' => 0,
        );
        foreach ($all_groups as $g) {
            $result[] = $g;
        }

        return $result;
    }

    /**
     * Maps locales onto existing images.
     *
     * @param string $locales_dir Base directory of locales.
     *
     * @return array The keys of the resulting array are images names and the
     *   values are arrays of locales which contains the image.
     */
    protected function getImageLocalesMap($locales_dir)
    {
        $image_locales = array();
        $all_locales = get_available_locales();
        foreach ($all_locales as $curr) {
            $images_dir = "$locales_dir/$curr/button";
            if ($handle = @opendir($images_dir)) {
                while (false !== ($file = readdir($handle))) {
                    $both_files_exist = preg_match("/^(\w+)_on\.(gif|png)$/", $file, $matches)
                        && is_file("$images_dir/" . $matches[1] . "_off." . $matches[2]);
                    if ($both_files_exist) {
                        $image = $matches[1];
                        if (!isset($image_locales[$image])) {
                            $image_locales[$image] = array();
                        }
                        $image_locales[$image][] = $curr;
                    }
                }
                closedir($handle);
            }
        }

        return $image_locales;
    }
}
