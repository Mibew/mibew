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

// Import namespaces and classes of the core
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(MIBEW_FS_ROOT.'/libs/operator.php');
require_once(MIBEW_FS_ROOT.'/libs/settings.php');

$operator = check_login();

$stylelist = PageStyle::availableStyles();

$preview = verifyparam("preview", "/^\w+$/", "default");
if (!in_array($preview, $stylelist)) {
	$style_names = array_keys($stylelist);
	$preview = $stylelist[$style_names[0]];
}

$preview_style = new PageStyle($preview);
$style_config = $preview_style->configurations();

$screenshots = array();
foreach($style_config['screenshots'] as $name => $desc) {
	$screenshots[] = array(
		'name' => $name,
		'file' => MIBEW_WEB_ROOT . '/' . $preview_style->filesPath()
			. '/screenshots/' . $name . '.png',
		'description' => $desc
	);
}

$page['formpreview'] = $preview;
$page['availablePreviews'] = $stylelist;
$page['screenshotsList'] = $screenshots;
$page['title'] = getlocal("page.preview.title");
$page['menuid'] = "settings";

$page = array_merge(
	$page,
	prepare_menu($operator)
);

$page['tabs'] = setup_settings_tabs(3);

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('page_themes', $page);

?>