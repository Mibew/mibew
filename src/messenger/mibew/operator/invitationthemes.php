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

require_once('../libs/init.php');
require_once('../libs/pagination.php');
require_once('../libs/operator.php');
require_once('../libs/groups.php');
require_once('../libs/expand.php');
require_once('../libs/settings.php');
require_once('../libs/styles.php');

$operator = check_login();

$stylelist = get_style_list("../styles/invitations");

$preview = verifyparam("preview", "/^\w+$/", "default");
if (!in_array($preview, $stylelist)) {
	$preview = $stylelist[0];
}

$page['formpreview'] = $preview;
$page['preview'] = $preview;
$page['availablePreviews'] = $stylelist;
$page['operatorName'] = (empty($operator['vclocalname'])?$operator['vccommonname']:$operator['vclocalname']);

prepare_menu($operator);
start_html_output();
setup_settings_tabs(4);
require('../view/invitation_themes.php');
?>