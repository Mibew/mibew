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

// Import namespaces and classes of the core
use Mibew\Settings;
use Mibew\Style\ChatStyle;
use Mibew\Style\InvitationStyle;

/**
 * Return chat button code.
 *
 * @param string $title Page title
 * @param string $locale 2-digit ISO-639-1 code for language
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
function generate_button(
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
    $app_location = get_app_location($show_host, $force_secure);
    $link = $app_location . "/client.php";
    if ($locale) {
        $link = append_query($link, "locale=$locale");
    }
    if ($style) {
        $link = append_query($link, "style=$style");
    }
    if ($group) {
        $link = append_query($link, "group=$group");
    }

    $modsecfix = $mod_security ? ".replace('http://','').replace('https://','')" : "";
    $js_link = append_query(
        "'" . $link,
        "url='+escape(document.location.href$modsecfix)+'&amp;referrer='+escape(document.referrer$modsecfix)"
    );

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
        $widget_data['inviteStyle'] = $app_location . '/' .
            $invitation_style->getFilesPath() .
            '/invite.css';

        // Time between requests to the server in milliseconds
        $widget_data['requestTimeout'] = Settings::get('updatefrequency_tracking') * 1000;

        // URL for requests
        $widget_data['requestURL'] = $app_location . '/widget';

        // Locale for invitation
        $widget_data['locale'] = $locale;

        // Name of the cookie to track user. Use if third-party cookie blocked
        $widget_data['visitorCookieName'] = VISITOR_COOKIE_NAME;

        // Build additional button code
        $temp = preg_replace('/^(<a )/', '\1id="mibewAgentButton" ', $temp)
            . '<div id="mibewinvitation"></div>'
            . '<script type="text/javascript" src="'
            . $app_location . '/js/compiled/widget.js'
            . '"></script>'
            . '<script type="text/javascript">'
            . 'Mibew.Widget.init(' . json_encode($widget_data) . ')'
            . '</script>';
    }

    return "<!-- mibew button -->" . $temp . "<!-- / mibew button -->";
}
/**
 * Return chat group id from GET or POST arrays.
 *
 * @param string $param_id key of group_id in client request
 *
 * @return integer Chat group id from client request
 */
function verifyparam_groupid($param_id, &$errors)
{
    $group_id = verify_param($param_id, "/^\d{0,8}$/", "");
    if ($group_id) {
        $group = group_by_id($group_id);
        if (!$group) {
            $errors[] = getlocal("page.group.no_such");
            $group_id = "";
        }
    }

    return $group_id;
}
/**
 * Return list of all chat groups.
 *
 * @return array It is chat groups structure. contains (groupid integer,
 * parent integer, vclocalname string, vclocaldescription string)
 */
function get_groups_list()
{
    $result = array();
    $all_groups = get_all_groups();
    $result[] = array(
        'groupid' => '',
        'vclocalname' => getlocal("page.gen_button.default_group"),
        'level' => 0,
    );
    foreach ($all_groups as $g) {
        $result[] = $g;
    }

    return $result;
}
/**
 * Return map of chat button images.
 *
 * @param string $locales_dir Base directory of locales
 *
 * @return array locales map images.
 */
function get_image_locales_map($locales_dir)
{
    $image_locales = array();
    $all_locales = get_available_locales();
    foreach ($all_locales as $curr) {
        $images_dir = "$locales_dir/$curr/button";
        if ($handle = @opendir($images_dir)) {
            while (false !== ($file = readdir($handle))) {
                $both_files_exist = preg_match("/^(\w+)_on.gif$/", $file, $matches)
                    && is_file("$images_dir/" . $matches[1] . "_off.gif");
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
