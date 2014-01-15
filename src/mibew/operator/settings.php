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
use Mibew\Settings;
use Mibew\Style\ChatStyle;
use Mibew\Style\InvitationStyle;
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)).'/libs/init.php');
require_once(MIBEW_FS_ROOT.'/libs/operator.php');
require_once(MIBEW_FS_ROOT.'/libs/settings.php');
require_once(MIBEW_FS_ROOT.'/libs/cron.php');

$operator = check_login();
force_password($operator);
csrfchecktoken();

$page = array('agentId' => '');
$errors = array();

// Load system configs
$options = array(
	'email',
	'title',
	'logo',
	'hosturl',
	'usernamepattern',
	'chattitle',
	'geolink',
	'geolinkparams',
	'sendmessagekey',
	'cron_key'
);

$params = array();
foreach ($options as $opt) {
	$params[$opt] = Settings::get($opt);
}

// Load styles configs
$styles_params = array(
	'chat_style' => ChatStyle::defaultStyle(),
	'page_style' => PageStyle::defaultStyle(),
);

$chat_style_list = ChatStyle::availableStyles();
$page_style_list = PageStyle::availableStyles();

if (Settings::get('enabletracking')) {
	$styles_params['invitation_style'] = InvitationStyle::defaultStyle();
	$invitation_style_list = InvitationStyle::availableStyles();
}

if (isset($_POST['email']) && isset($_POST['title']) && isset($_POST['logo'])) {
	$params['email'] = getparam('email');
	$params['title'] = getparam('title');
	$params['logo'] = getparam('logo');
	$params['hosturl'] = getparam('hosturl');
	$params['usernamepattern'] = getparam('usernamepattern');
	$params['chattitle'] = getparam('chattitle');
	$params['geolink'] = getparam('geolink');
	$params['geolinkparams'] = getparam('geolinkparams');
	$params['sendmessagekey'] = verifyparam('sendmessagekey', "/^c?enter$/");
	$params['cron_key'] = getparam('cronkey');

	$styles_params['chat_style'] = verifyparam("chat_style", "/^\w+$/", $styles_params['chat_style']);
	if (!in_array($styles_params['chat_style'], $chat_style_list)) {
		$styles_params['chat_style'] = $chat_style_list[0];
	}

	$styles_params['page_style'] = verifyparam("page_style", "/^\w+$/", $styles_params['page_style']);
	if (!in_array($styles_params['page_style'], $page_style_list)) {
		$styles_params['page_style'] = $page_style_list[0];
	}

	if (Settings::get('enabletracking')) {
		$styles_params['invitation_style'] = verifyparam("invitation_style", "/^\w+$/", $styles_params['invitation_style']);
		if (!in_array($styles_params['invitation_style'], $invitation_style_list)) {
			$styles_params['invitation_style'] = $invitation_style_list[0];
		}
	}

	if ($params['email'] && !is_valid_email($params['email'])) {
		$errors[] = getlocal("settings.wrong.email");
	}

	if ($params['geolinkparams']) {
		foreach (preg_split("/,/", $params['geolinkparams']) as $oneparam) {
			if (!preg_match("/^\s*(toolbar|scrollbars|location|status|menubar|width|height|resizable)=\d{1,4}$/", $oneparam)) {
				$errors[] = "Wrong link parameter: \"$oneparam\", should be one of 'toolbar, scrollbars, location, status, menubar, width, height or resizable'";
			}
		}
	}

	if (preg_match("/^[0-9A-z]*$/", $params['cron_key']) == 0) {
		$errors[] = getlocal("settings.wrong.cronkey");
	}

	if (count($errors) == 0) {
		// Update system settings
		foreach ($options as $opt) {
			Settings::set($opt,$params[$opt]);
		}
		Settings::update();

		// Update styles params
		ChatStyle::setDefaultStyle($styles_params['chat_style']);
		PageStyle::setDefaultStyle($styles_params['page_style']);
		if (Settings::get('enabletracking')) {
			InvitationStyle::setDefaultStyle($styles_params['invitation_style']);
		}

		// Redirect the user
		header("Location: " . MIBEW_WEB_ROOT . "/operator/settings.php?stored");
		exit;
	}
}

$page['formemail'] = topage($params['email']);
$page['formtitle'] = topage($params['title']);
$page['formlogo'] = topage($params['logo']);
$page['formhosturl'] = topage($params['hosturl']);
$page['formgeolink'] = topage($params['geolink']);
$page['formgeolinkparams'] = topage($params['geolinkparams']);
$page['formusernamepattern'] = topage($params['usernamepattern']);
$page['formpagestyle'] = $styles_params['page_style'];
$page['availablePageStyles'] = $page_style_list;
$page['formchatstyle'] = $styles_params['chat_style'];
$page['formchattitle'] = topage($params['chattitle']);
$page['formsendmessagekey'] = $params['sendmessagekey'];
$page['availableChatStyles'] = $chat_style_list;
$page['stored'] = isset($_GET['stored']);
$page['enabletracking'] = Settings::get('enabletracking');
$page['formcronkey'] = $params['cron_key'];

$page['cron_path'] = cron_get_uri($params['cron_key']);

$page['title'] = getlocal("settings.title");
$page['menuid'] = "settings";

if (Settings::get('enabletracking')) {
	$page['forminvitationstyle'] = $styles_params['invitation_style'];
	$page['availableInvitationStyles'] = $invitation_style_list;
}

prepare_menu($operator);
$page['tabs'] = setup_settings_tabs(0);

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('settings');

?>