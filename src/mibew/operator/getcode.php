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
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');
require_once(MIBEW_FS_ROOT . '/libs/operator.php');
require_once(MIBEW_FS_ROOT . '/libs/groups.php');
require_once(MIBEW_FS_ROOT . '/libs/getcode.php');

$operator = check_login();
force_password($operator);

$page = array(
    'errors' => array(),
);

$image_locales_map = get_image_locales_map(MIBEW_FS_ROOT . '/locales');
$image = verify_param(isset($_GET['image']) ? "image" : "i", "/^\w+$/", "mibew");
if (!isset($image_locales_map[$image])) {
    $page['errors'][] = "Unknown image: $image";
    $avail = array_keys($image_locales_map);
    $image = $avail[0];
}
$image_locales = $image_locales_map[$image];

$style_list = ChatStyle::availableStyles();
$style_list[""] = getlocal("page.preview.style_default");
$style = verify_param("style", "/^\w*$/", "");
if ($style && !in_array($style, $style_list)) {
    $style = "";
}

$invitation_style_list = InvitationStyle::availableStyles();
$invitation_style_list[""] = getlocal("page.preview.style_default");
$invitation_style = verify_param("invitationstyle", "/^\w*$/", "");
if ($invitation_style && !in_array($invitation_style, $invitation_style_list)) {
    $invitation_style = "";
}

$group_id = verifyparam_groupid("group", $page['errors']);
$show_host = verify_param("hostname", "/^on$/", "") == "on";
$force_secure = verify_param("secure", "/^on$/", "") == "on";
$mod_security = verify_param("modsecurity", "/^on$/", "") == "on";

$code_type = verify_param("codetype", "/^(button|operator_code)$/", "button");
$operator_code = ($code_type == "operator_code");

$lang = verify_param("lang", "/^[\w-]{2,5}$/", "");
if (!$lang || !in_array($lang, $image_locales)) {
    $lang = in_array(CURRENT_LOCALE, $image_locales) ? CURRENT_LOCALE : $image_locales[0];
}

$file = MIBEW_FS_ROOT . '/locales/${lang}/button/${image}_on.gif';
$size = get_gifimage_size($file);

$image_href = get_app_location($show_host, $force_secure) . "/b.php?i=$image&amp;lang=$lang";
if ($group_id) {
    $image_href .= "&amp;group=$group_id";
}
$message = get_image($image_href, $size[0], $size[1]);

$page['buttonCode'] = generate_button(
    "",
    $lang,
    $style,
    $invitation_style,
    $group_id,
    $message,
    $show_host,
    $force_secure,
    $mod_security,
    $operator_code
);
$page['availableImages'] = array_keys($image_locales_map);
$page['availableLocales'] = $image_locales;
$page['availableChatStyles'] = $style_list;
$page['availableInvitationStyles'] = $invitation_style_list;
$page['groups'] = get_groups_list();

$page['availableCodeTypes'] = array(
    'button' => getlocal('page.gen_button.button'),
    'operator_code' => getlocal('page.gen_button.operator_code')
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

$page['title'] = getlocal("page.gen_button.title");
$page['menuid'] = "getcode";

$page = array_merge($page, prepare_menu($operator));

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('get_code', $page);
