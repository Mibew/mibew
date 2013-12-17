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
require_once(dirname(dirname(__FILE__)).'/libs/chat.php');
require_once(dirname(dirname(__FILE__)).'/libs/pagination.php');
require_once(dirname(dirname(__FILE__)).'/libs/operator.php');
require_once(dirname(dirname(__FILE__)).'/libs/groups.php');
require_once(dirname(dirname(__FILE__)).'/libs/expand.php');
require_once(dirname(dirname(__FILE__)).'/libs/settings.php');
require_once(dirname(dirname(__FILE__)).'/libs/styles.php');
require_once(dirname(dirname(__FILE__)).'/libs/view.php');

$operator = check_login();

$stylelist = get_style_list(dirname(dirname(__FILE__)).'/styles/dialogs');

$preview = verifyparam("preview", "/^\w+$/", "default");
if (!in_array($preview, $stylelist)) {
	$style_names = array_keys($stylelist);
	$preview = $stylelist[$style_names[0]];
}

$style_config = get_dialogs_style_config($preview);

$screenshots = array();
foreach($style_config['screenshots'] as $name => $desc) {
	$screenshots[] = array(
		'name' => $name,
		'file' => $mibewroot . '/styles/dialogs/' . $preview
			. '/screenshots/' . $name . '.png',
		'description' => $desc
	);
}

$page['formpreview'] = $preview;
$page['availablePreviews'] = $stylelist;
$page['screenshotsList'] = $screenshots;

prepare_menu($operator);
setup_settings_tabs(3);
render_view('themes');

?>