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
require_once(dirname(dirname(__FILE__)).'/libs/settings.php');
require_once(dirname(dirname(__FILE__)).'/libs/cron.php');
require_once(dirname(dirname(__FILE__)).'/libs/interfaces/style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/chat_style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/page_style.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/invitation_style.php');

$operator = check_login();
force_password($operator);
csrfchecktoken();

$page = array('agentId' => '');
$errors = array();

$stylelist = ChatStyle::availableStyles();
$page_style_list = PageStyle::availableStyles();

$options = array(
	'email',
	'title',
	'logo',
	'hosturl',
	'usernamepattern',
	'page_style',
	'chat_style',
	'chattitle',
	'geolink',
	'geolinkparams',
	'sendmessagekey',
	'cron_key'
);

if (Settings::get('enabletracking')) {
	$options[] = 'invitation_style';
	$invitationstylelist = InvitationStyle::availableStyles();
}

$params = array();
foreach ($options as $opt) {
	$params[$opt] = Settings::get($opt);
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

	$params['chat_style'] = verifyparam("chat_style", "/^\w+$/", $params['chat_style']);
	if (!in_array($params['chat_style'], $stylelist)) {
		$params['chat_style'] = $stylelist[0];
	}

	$params['page_style'] = verifyparam("page_style", "/^\w+$/", $params['page_style']);
	if (!in_array($params['page_style'], $page_style_list)) {
		$params['page_style'] = $page_style_list[0];
	}

	if (Settings::get('enabletracking')) {
		$params['invitation_style'] = verifyparam("invitation_style", "/^\w+$/", $params['invitation_style']);
		if (!in_array($params['invitation_style'], $invitationstylelist)) {
			$params['invitation_style'] = $invitationstylelist[0];
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
		foreach ($options as $opt) {
			Settings::set($opt,$params[$opt]);
		}
		Settings::update();
		header("Location: $mibewroot/operator/settings.php?stored");
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
$page['formpagestyle'] = $params['page_style'];
$page['availablePageStyles'] = $page_style_list;
$page['formchatstyle'] = $params['chat_style'];
$page['formchattitle'] = topage($params['chattitle']);
$page['formsendmessagekey'] = $params['sendmessagekey'];
$page['availableChatStyles'] = $stylelist;
$page['stored'] = isset($_GET['stored']);
$page['enabletracking'] = Settings::get('enabletracking');
$page['formcronkey'] = $params['cron_key'];

$page['cron_path'] = cron_get_uri($params['cron_key']);

if (Settings::get('enabletracking')) {
	$page['forminvitationstyle'] = $params['invitation_style'];
	$page['availableInvitationStyles'] = $invitationstylelist;
}

prepare_menu($operator);
setup_settings_tabs(0);

$page_style = new PageStyle(PageStyle::currentStyle());
$page_style->render('settings');

?>