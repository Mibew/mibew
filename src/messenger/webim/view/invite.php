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

$page['title'] = getlocal("invitation.title");

function tpl_header() { global $page, $webimroot, $jsver;
?>
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/<?php echo $jsver ?>/common.js"></script>
<script type="text/javascript" language="javascript"><!--
var updaterOptions = {
	url:"<?php echo $webimroot ?>/operator/invitationstate.php",wroot:"<?php echo $webimroot ?>",
	agentservl:"<?php echo $webimroot ?>/operator/agent.php", frequency:<?php echo $page['frequency'] ?>,
	visitor: "<?php echo $page['visitor'] ?>" };
//--></script>
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/<?php echo $jsver ?>/invite_op.js"></script>
<?php
}

function tpl_content() { global $page, $webimroot;
?>

<?php echo getlocal("invitation.sent"); ?>

<div class="ajaxWait"></div>

<?php
} /* content */

require_once('inc_main.php');
?>