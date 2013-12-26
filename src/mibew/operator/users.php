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
require_once(MIBEW_FS_ROOT.'/libs/operator.php');
require_once(MIBEW_FS_ROOT.'/libs/groups.php');
require_once(MIBEW_FS_ROOT.'/libs/interfaces/style.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/style.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/chat_style.php');
require_once(MIBEW_FS_ROOT.'/libs/classes/page_style.php');

$operator = check_login();
force_password($operator);

$status = isset($_GET['away']) ? 1 : 0;

notify_operator_alive($operator['operatorid'], $status);

$_SESSION[$session_prefix."operatorgroups"] = get_operator_groupslist($operator['operatorid']);

$page = array();
$page['havemenu'] = isset($_GET['nomenu']) ? "0" : "1";
$page['showpopup'] = Settings::get('enablepopupnotification') == '1' ? "1" : "0";
$page['frequency'] = Settings::get('updatefrequency_operator');
$page['istatus'] = $status;
$page['showonline'] = Settings::get('showonlineoperators') == '1' ? "1" : "0";
$page['showvisitors'] = Settings::get('enabletracking') == '1' ? "1" : "0";
$page['agentId'] = $operator['operatorid'];
$page['geoLink'] = Settings::get('geolink');
$page['geoWindowParams'] = Settings::get('geolinkparams');

// Load dialogs style options
$chat_style = new ChatStyle(ChatStyle::currentStyle());
$style_config = $chat_style->configurations();
$page['chatStyles.chatWindowParams'] = $style_config['chat']['window_params'];

// Load page style options
$page_style = new PageStyle(PageStyle::currentStyle());
$style_config = $page_style->configurations();
$page['coreStyles.threadTag'] = $style_config['users']['thread_tag'];
$page['coreStyles.visitorTag'] = $style_config['users']['visitor_tag'];
$page['coreStyles.trackedUserWindowParams'] = $style_config['tracked']['user_window_params'];
$page['coreStyles.trackedVisitorWindowParams'] = $style_config['tracked']['visitor_window_params'];
$page['coreStyles.inviteWindowParams'] = $style_config['invitation']['window_params'];
$page['coreStyles.banWindowParams'] = $style_config['ban']['window_params'];

$page['title'] = getlocal("clients.title");
$page['menuid'] = "users";

// Get additional plugins data
$page = array_merge($page, get_plugins_data('users'));

prepare_menu($operator);
$page_style->render('pending_users');

?>