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

require_once('libs/init.php');
require_once('libs/chat.php');
require_once('libs/operator.php');
require_once('libs/invitation.php');
require_once('libs/groups.php');
require_once('libs/captcha.php');
require_once('libs/notify.php');
require_once('libs/classes/thread.php');
require_once('libs/classes/mibew_api.php');
require_once('libs/classes/mibew_api_interaction.php');
require_once('libs/classes/mibew_api_chat_interaction.php');
require_once('libs/classes/mibew_api_execution_context.php');
require_once('libs/classes/client_side_processor.php');
require_once('libs/classes/thread_processor.php');

$processor = ThreadProcessor::getInstance();
$processor->receiveRequest($_POST['data']);

?>