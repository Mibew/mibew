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

require_once('../libs/common.php');
require_once('../libs/invitation.php');
require_once('../libs/operator.php');

$operator = check_login();

loadsettings();

$visitorid = verifyparam("visitor", "/^\d{1,8}$/");

if (!invitation_invite($visitorid, $operator['operatorid'])) {
    die("Invitation failed!");
}

$page = array();
$page['visitor'] = $visitorid;
$page['frequency'] = $settings['updatefrequency_operator'];

start_html_output();
require('../view/invite.php');
?>