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

function tpl_header() { global $page, $webimroot;
?>

<!-- External libs -->
<script type="text/javascript" src="<?php echo $webimroot ?>/js/libs/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $webimroot ?>/js/libs/json2.js"></script>
<script type="text/javascript" src="<?php echo $webimroot ?>/js/libs/underscore-min.js"></script>
<script type="text/javascript" src="<?php echo $webimroot ?>/js/libs/backbone-min.js"></script>
<script type="text/javascript" src="<?php echo $webimroot ?>/js/libs/backbone.marionette.min.js"></script>
<script type="text/javascript" src="<?php echo $webimroot ?>/js/libs/handlebars.js"></script>

<!-- Application files -->
<script type="text/javascript" src="<?php echo $webimroot ?>/js/compiled/mibewapi.js"></script>
<script type="text/javascript" src="<?php echo $webimroot ?>/js/compiled/default_app.js"></script>
<script type="text/javascript" src="<?php echo $webimroot ?>/js/compiled/invite_app.js"></script>


<script type="text/javascript"><!--
	jQuery(document).ready(function(){
		Mibew.Application.start({
			server: {
				url: "<?php echo $webimroot ?>/operator/invitationstate.php",
				requestsFrequency: <?php echo $page['frequency'] ?>
			},

			visitorId: "<?php echo $page['visitor'] ?>",

			chatLink: "<?php echo $webimroot ?>/operator/agent.php"
		});
	});
//--></script>


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