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
require_once(dirname(dirname(__FILE__)).'/libs/invitation.php');
require_once(dirname(dirname(__FILE__)).'/libs/track.php');
require_once(dirname(dirname(__FILE__)).'/libs/operator.php');
require_once(dirname(dirname(__FILE__)).'/libs/classes/thread.php');

$operator = check_login();

$visitorid = verifyparam("visitor", "/^\d{1,8}$/");

$thread = invitation_invite($visitorid, $operator);
if (!$thread) {
    die("Invitation failed!");
}

// Open chat window for operator
$redirect_to = $mibewroot .
	'/operator/agent.php?thread=' . intval($thread->id) .
	'&token=' . urlencode($thread->lastToken);
header('Location: ' . $redirect_to);

?>