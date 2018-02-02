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

namespace Mibew\Controller;

use Mibew\Button\Generator\ImageGenerator as ImageButtonGenerator;
use Mibew\Button\Generator\OperatorCodeGenerator as OperatorCodeFieldGenerator;
use Mibew\Button\Generator\TextGenerator as TextButtonGenerator;
use Mibew\Http\Exception\BadRequestException;
use Mibew\Settings;
use Mibew\Style\ChatStyle;
use Mibew\Style\InvitationStyle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Represents actions that are related with button code generation.
 */
class ButtonCodeController extends AbstractController
{
    /**
     * Generates a page with Mibew Messenger button code form.
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
        $force_windows = $request->query->get('forcewindows') == 'on';

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
        $button_generator_options = array(
            'chat_style' => $style,
            'group_id' => $group_id,
            'show_host' => $show_host,
            'force_secure' => $force_secure,
            'mod_security' => $mod_security,
            'prefer_iframe' => !$force_windows,
        );

        if ($operator_code) {
            $button_generator = new OperatorCodeFieldGenerator(
                $this->getRouter(),
                $this->getAssetManager()->getUrlGenerator(),
                $button_generator_options
            );
        } elseif ($generate_button) {
            // Make sure locale exists
            if (!$lang || !in_array($lang, $image_locales)) {
                $lang = in_array(get_current_locale(), $image_locales)
                    ? get_current_locale()
                    : $image_locales[0];
            }

            $button_generator = new ImageButtonGenerator(
                $this->getRouter(),
                $this->getAssetManager()->getUrlGenerator(),
                $button_generator_options
            );

            // Set generator-specific options
            $button_generator->setOption('image', $image);
            $button_generator->setOption('invitation_style', $invitation_style);
        } else {
            // Make sure locale exists
            if (!$lang || !in_array($lang, $locales_list)) {
                $lang = in_array(get_current_locale(), $locales_list)
                    ? get_current_locale()
                    : $locales_list[0];
            }

            $button_generator = new TextButtonGenerator(
                $this->getRouter(),
                $this->getAssetManager()->getUrlGenerator(),
                $button_generator_options
            );

            // Set generator-specific options
            $button_generator->setOption('caption', getlocal('Click to chat'));
        }

        // Set verified locale code to a button generator
        $button_generator->setOption('locale', $lang);

        $page['buttonCode'] = $button_generator->generate();
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
        $page['formforcewindows'] = $force_windows;

        $page['enabletracking'] = Settings::get('enabletracking');
        $page['operator_code'] = $operator_code;
        $page['generateButton'] = $generate_button;

        $page['title'] = getlocal("Button HTML code generation");
        $page['menuid'] = "getcode";

        $page = array_merge($page, prepare_menu($operator));

        return $this->render('button_code', $page);
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
