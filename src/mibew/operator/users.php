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

require_once('../libs/common.php');
require_once('../libs/operator.php');
require_once('../libs/groups.php');

$operator = check_login();

$status = isset($_GET['away']) ? 1 : 0;

notify_operator_alive($operator['operatorid'], $status);

$link = connect();
loadsettings_($link);
$_SESSION["${mysqlprefix}operatorgroups"] = get_operator_groupslist($operator['operatorid'], $link);
mysql_close($link);

$page = array();
$page['havemenu'] = isset($_GET['nomenu']) ? "0" : "1";
$page['showpopup'] = $settings['enablepopupnotification'] == '1' ? "1" : "0";
$page['frequency'] = $settings['updatefrequency_operator'];
$page['istatus'] = $status;
$page['showonline'] = $settings['showonlineoperators'] == '1' ? "1" : "0";

prepare_menu($operator);
start_html_output();
require('../view/pending_users.php');
?>