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
use Mibew\Style\PageStyle;

// Initialize libraries
require_once(dirname(dirname(__FILE__)) . '/libs/init.php');

$operator = check_login();
csrf_check_token();

$page = array(
    'agentId' => '',
    'errors' => array(),
);

$options = array(
    'online_timeout',
    'updatefrequency_operator',
    'updatefrequency_chat',
    'max_connections_from_one_host',
    'updatefrequency_tracking',
    'visitors_limit',
    'invitation_lifetime',
    'tracking_lifetime',
    'thread_lifetime',
    'statistics_aggregation_interval',
    'max_uploaded_file_size',
);

$params = array();
foreach ($options as $opt) {
    $params[$opt] = Settings::get($opt);
}

if (isset($_POST['onlinetimeout'])) {
    $params['online_timeout'] = get_param('onlinetimeout');
    if (!is_numeric($params['online_timeout'])) {
        $page['errors'][] = wrong_field("settings.onlinetimeout");
    }

    $params['updatefrequency_operator'] = get_param('frequencyoperator');
    if (!is_numeric($params['updatefrequency_operator'])) {
        $page['errors'][] = wrong_field("settings.frequencyoperator");
    }

    $params['updatefrequency_chat'] = get_param('frequencychat');
    if (!is_numeric($params['updatefrequency_chat'])) {
        $page['errors'][] = wrong_field("settings.frequencychat");
    }

    $params['max_connections_from_one_host'] = get_param('onehostconnections');
    if (!is_numeric($params['max_connections_from_one_host'])) {
        $page['errors'][] = getlocal("settings.wrong.onehostconnections");
    }

    $params['thread_lifetime'] = get_param('threadlifetime');
    if (!is_numeric($params['thread_lifetime'])) {
        $page['errors'][] = getlocal("settings.wrong.threadlifetime");
    }

    $params['statistics_aggregation_interval'] = get_param('statistics_aggregation_interval');
    if (!is_numeric($params['statistics_aggregation_interval'])) {
        $page['errors'][] = wrong_field("settings.statistics_aggregation_interval");
    }

    if (Settings::get('enabletracking')) {

        $params['updatefrequency_tracking'] = get_param('frequencytracking');
        if (!is_numeric($params['updatefrequency_tracking'])) {
            $page['errors'][] = wrong_field("settings.frequencytracking");
        }

        $params['visitors_limit'] = get_param('visitorslimit');
        if (!is_numeric($params['visitors_limit'])) {
            $page['errors'][] = wrong_field("settings.visitorslimit");
        }

        $params['invitation_lifetime'] = get_param('invitationlifetime');
        if (!is_numeric($params['invitation_lifetime'])) {
            $page['errors'][] = wrong_field("settings.invitationlifetime");
        }

        $params['tracking_lifetime'] = get_param('trackinglifetime');
        if (!is_numeric($params['tracking_lifetime'])) {
            $page['errors'][] = wrong_field("settings.trackinglifetime");
        }
    }

    $params['max_uploaded_file_size'] = get_param('maxuploadedfilesize');
    if (!is_numeric($params['max_uploaded_file_size'])) {
        $page['errors'][] = wrong_field("settings.maxuploadedfilesize");
    }

    if (count($page['errors']) == 0) {
        foreach ($options as $opt) {
            Settings::set($opt, $params[$opt]);
        }
        Settings::update();
        header("Location: " . MIBEW_WEB_ROOT . "/operator/performance.php?stored");
        exit;
    }
}

$page['formonlinetimeout'] = $params['online_timeout'];
$page['formfrequencyoperator'] = $params['updatefrequency_operator'];
$page['formfrequencychat'] = $params['updatefrequency_chat'];
$page['formonehostconnections'] = $params['max_connections_from_one_host'];
$page['formthreadlifetime'] = $params['thread_lifetime'];
$page['formstatistics_aggregation_interval'] = $params['statistics_aggregation_interval'];

if (Settings::get('enabletracking')) {

    $page['formfrequencytracking'] = $params['updatefrequency_tracking'];
    $page['formvisitorslimit'] = $params['visitors_limit'];
    $page['forminvitationlifetime'] = $params['invitation_lifetime'];
    $page['formtrackinglifetime'] = $params['tracking_lifetime'];
}

$page['formmaxuploadedfilesize'] = $params['max_uploaded_file_size'];

$page['enabletracking'] = Settings::get('enabletracking');
$page['stored'] = isset($_GET['stored']);

$page['title'] = getlocal("settings.title");
$page['menuid'] = "settings";

$page = array_merge($page, prepare_menu($operator));

$page['tabs'] = setup_settings_tabs(2);

$page_style = new PageStyle(PageStyle::getCurrentStyle());
$page_style->render('performance', $page);
