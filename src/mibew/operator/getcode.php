<?php
/*
 * Copyright 2005-2013 the original author or authors.
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

require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(dirname(dirname(__FILE__)).'/libs/operator.php');
require_once(dirname(dirname(__FILE__)).'/libs/groups.php');
require_once(dirname(dirname(__FILE__)).'/libs/getcode.php');
require_once(dirname(dirname(__FILE__)).'/libs/styles.php');
require_once(dirname(dirname(__FILE__)).'/libs/interfaces/style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/chat_style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/page_style.php');

$operator = check_login();
force_password($operator);

$imageLocales = get_image_locales_map(dirname(dirname(__FILE__)).'/locales');
$image = verifyparam(isset($_GET['image']) ? "image" : "i", "/^\w+$/", "mibew");
if (!isset($imageLocales[$image])) {
	$errors[] = "Unknown image: $image";
	$avail = array_keys($imageLocales);
	$image = $avail[0];
}
$image_locales = $imageLocales[$image];

$stylelist = ChatStyle::availableStyles();
$stylelist[""] = getlocal("page.preview.style_default");
$style = verifyparam("style", "/^\w*$/", "");
if ($style && !in_array($style, $stylelist)) {
	$style = "";
}

$invitationstylelist = get_style_list(dirname(dirname(__FILE__)).'/styles/invitations');
$invitationstylelist[""] = getlocal("page.preview.style_default");
$invitationstyle = verifyparam("invitationstyle", "/^\w*$/", "");
if ($invitationstyle && !in_array($invitationstyle, $invitationstylelist)) {
	$invitationstyle = "";
}

$groupid = verifyparam_groupid("group");
$showhost = verifyparam("hostname", "/^on$/", "") == "on";
$forcesecure = verifyparam("secure", "/^on$/", "") == "on";
$modsecurity = verifyparam("modsecurity", "/^on$/", "") == "on";

$code_type = verifyparam("codetype", "/^(button|operator_code)$/", "button");
$operator_code = ($code_type == "operator_code");

$lang = verifyparam("lang", "/^[\w-]{2,5}$/", "");
if (!$lang || !in_array($lang, $image_locales))
	$lang = in_array($current_locale, $image_locales) ? $current_locale : $image_locales[0];

$file = dirname(dirname(__FILE__)).'/locales/${lang}/button/${image}_on.gif';
$size = get_gifimage_size($file);

$imagehref = get_app_location($showhost, $forcesecure) . "/b.php?i=$image&amp;lang=$lang";
if ($groupid) {
	$imagehref .= "&amp;group=$groupid";
}
$message = get_image($imagehref, $size[0], $size[1]);

$page = array();
$page['buttonCode'] = generate_button("", $lang, $style, $invitationstyle, $groupid, $message, $showhost, $forcesecure, $modsecurity, $operator_code);
$page['availableImages'] = array_keys($imageLocales);
$page['availableLocales'] = $image_locales;
$page['availableChatStyles'] = $stylelist;
$page['availableInvitationStyles'] = $invitationstylelist;
$page['groups'] = get_groups_list();

$page['availableCodeTypes'] = array(
	'button' => getlocal('page.gen_button.button'),
	'operator_code' => getlocal('page.gen_button.operator_code')
);

$page['formgroup'] = $groupid;
$page['formstyle'] = $style;
$page['forminvitationstyle'] = $invitationstyle;
$page['formimage'] = $image;
$page['formlang'] = $lang;
$page['formhostname'] = $showhost;
$page['formsecure'] = $forcesecure;
$page['formmodsecurity'] = $modsecurity;
$page['formcodetype'] = $code_type;

$page['enabletracking'] = Settings::get('enabletracking');
$page['operator_code'] = $operator_code;

prepare_menu($operator);

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('gen_button');

?>