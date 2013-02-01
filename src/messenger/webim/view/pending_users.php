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

require_once("inc_menu.php");
$page['title'] = getlocal("clients.title");
$page['menuid'] = "users";


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
<script type="text/javascript" src="<?php echo $webimroot ?>/js/compiled/users_app.js"></script>

<script type="text/javascript"><!--
	Mibew.Localization.set({
		'pending.table.speak': "<?php echo getlocal('pending.table.speak') ?>",
		'pending.table.view': "<?php echo getlocal('pending.table.view') ?>",
		'pending.table.ban': "<?php echo getlocal('pending.table.ban') ?>",
		'pending.menu.show': "<?php echo htmlspecialchars(getlocal('pending.menu.show')) ?>",
		'pending.menu.hide': "<?php echo htmlspecialchars(getlocal('pending.menu.hide')) ?>",
		'pending.popup_notification': "<?php echo htmlspecialchars(getlocal('pending.popup_notification')) ?>",
		'pending.table.tracked': "<?php echo getlocal('pending.table.tracked') ?>",
		'pending.table.invite': "<?php echo getlocal('pending.table.invite') ?>",
		'pending.status.away': "<?php echo getlocal('pending.status.away') ?>",
		'pending.status.online': "<?php echo getlocal('pending.status.online') ?>",
		'pending.status.setonline': "<?php echo addslashes(getlocal('pending.status.setonline')) ?>",
		'pending.status.setaway': "<?php echo addslashes(getlocal('pending.status.setaway')) ?>",
		'pending.table.head.name': "<?php echo getlocal('pending.table.head.name') ?>",
		'pending.table.head.actions': "<?php echo getlocal('pending.table.head.actions') ?>",
		'pending.table.head.contactid': "<?php echo getlocal('pending.table.head.contactid') ?>",
		'pending.table.head.state': "<?php echo getlocal('pending.table.head.state') ?>",
		'pending.table.head.operator': "<?php echo getlocal('pending.table.head.operator') ?>",
		'pending.table.head.total': "<?php echo getlocal('pending.table.head.total') ?>",
		'pending.table.head.waittime': "<?php echo getlocal('pending.table.head.waittime') ?>",
		'pending.table.head.etc': "<?php echo getlocal('pending.table.head.etc') ?>",
		'visitors.table.head.actions': "<?php echo getlocal('visitors.table.head.actions') ?>",
		'visitors.table.head.name': "<?php echo getlocal('visitors.table.head.name') ?>",
		'visitors.table.head.contactid': "<?php echo getlocal('visitors.table.head.contactid') ?>",
		'visitors.table.head.firsttimeonsite': "<?php echo getlocal('visitors.table.head.firsttimeonsite') ?>",
		'visitors.table.head.lasttimeonsite': "<?php echo getlocal('visitors.table.head.lasttimeonsite') ?>",
		'visitors.table.head.invited.by': "<?php echo getlocal('visitors.table.head.invited.by') ?>",
		'visitors.table.head.invitationtime': "<?php echo getlocal('visitors.table.head.invitationtime') ?>",
		'visitors.table.head.invitations': "<?php echo getlocal('visitors.table.head.invitations') ?>",
		'visitors.table.head.etc': "<?php echo getlocal('visitors.table.head.etc') ?>",
		'visitors.no_visitors': "<?php echo getlocal('visitors.no_visitors') ?>",
		'clients.no_clients': "<?php echo getlocal('clients.no_clients') ?>",
		'chat.thread.state_wait': "<?php echo getlocal('chat.thread.state_wait'); ?>",
		'chat.thread.state_wait_for_another_agent': "<?php echo getlocal('chat.thread.state_wait_for_another_agent'); ?>",
		'chat.thread.state_chatting_with_agent': "<?php echo getlocal('chat.thread.state_chatting_with_agent'); ?>",
		'chat.thread.state_closed': "<?php echo getlocal('chat.thread.state_closed'); ?>",
		'chat.thread.state_loading': "<?php echo getlocal('chat.thread.state_loading'); ?>",
		'chat.client.spam.prefix': "<?php echo getstring('chat.client.spam.prefix'); ?>"
	});
//--></script>

<script type="text/javascript"><!--
	jQuery(document).ready(function(){
		Mibew.Application.start({
			server: {
				url: "<?php echo $webimroot ?>/operator/update.php",
				requestsFrequency: <?php echo $page['frequency'] ?>
			},

			agent: {
				id: <?php echo $page['agentId'] ?>
			},

			page: {
				showOnlineOperators: <?php echo($page['showonline']?'true':'false'); ?>,
				showVisitors: <?php echo ($page['showvisitors']?'true':'false'); ?>,

				threadTag: "<?php echo $page['coreStyles.threadTag']; ?>",
				visitorTag: "<?php echo $page['coreStyles.visitorTag']; ?>",

				agentLink: "<?php echo $webimroot ?>/operator/agent.php",
				geoLink: "<?php echo $page['geoLink']; ?>",
				trackedLink: "<?php echo $webimroot ?>/operator/tracked.php",
				banLink: "<?php echo $webimroot ?>/operator/ban.php",
				inviteLink: "<?php echo $webimroot ?>/operator/invite.php",

				chatWindowParams: "<?php echo $page['chatStyles.chatWindowParams']; ?>",
				geoWindowParams: "<?php echo $page['geoWindowParams'];?>",
				trackedUserWindowParams: "<?php echo $page['coreStyles.trackedUserWindowParams']; ?>",
				trackedVisitorWindowParams: "<?php echo $page['coreStyles.trackedVisitorWindowParams']; ?>",
				banWindowParams: "<?php echo $page['coreStyles.banWindowParams']; ?>",
				inviteWindowParams: "<?php echo $page['coreStyles.inviteWindowParams']; ?>"
			}
		});
	});
//--></script>

<?php
}

function tpl_content() { global $page, $webimroot;
?>

<div>

<?php echo getlocal("clients.intro") ?>
<br/>
<?php echo getlocal("clients.how_to") ?>
</div>
<br/>

<div id="threads-region"></div>

<?php if ($page['showvisitors']) { ?>
<div class="tabletitle"><?php echo getlocal("visitors.title") ?></div>
<?php echo getlocal("visitors.intro") ?>
<br/>
<?php echo getlocal("visitors.how_to") ?>
<div id="visitors-region"></div>
<hr/>
<?php } ?>

<div id="status-panel-region"></div>
<div id="agents-region"></div>
<div id="sound-region"></div>
<?php 
} /* content */

require_once('inc_main.php');
?>