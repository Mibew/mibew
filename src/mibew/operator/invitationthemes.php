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
use Mibew\Style\InvitationStyle;
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');

$operator = check_login();

$style_list = InvitationStyle::getAvailableStyles();

$preview = verify_param("preview", "/^\w+$/", "default");
if (!in_array($preview, $style_list)) {
    $preview = $style_list[0];
}

$page['formpreview'] = $preview;
$page['preview'] = $preview;
$page['availablePreviews'] = $style_list;
$page['operatorName'] = (empty($operator['vclocalname'])
    ? $operator['vccommonname']
    : $operator['vclocalname']);
$page['title'] = getlocal("page.preview.title");
$page['menuid'] = "settings";

$page = array_merge($page, prepare_menu($operator));

$page['tabs'] = setup_settings_tabs(5);

$page_style = new PageStyle(PageStyle::getCurrentStyle());
$page_style->render('invitation_themes', $page);
