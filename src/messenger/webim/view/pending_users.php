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
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/compiled/common.js"></script>
<script type="text/javascript" language="javascript"><!--
var localized = new Array(
    "<?php echo getlocal("pending.table.speak") ?>",
    "<?php echo getlocal("pending.table.view") ?>",
    "<?php echo getlocal("pending.table.ban") ?>",
    "<?php echo htmlspecialchars(getlocal("pending.menu.show")) ?>",
    "<?php echo htmlspecialchars(getlocal("pending.menu.hide")) ?>",
    "<?php echo htmlspecialchars(getlocal("pending.popup_notification")) ?>",
    "<?php echo getlocal("pending.table.tracked") ?>",
    "<?php echo getlocal("pending.table.invite") ?>",
    "<?php echo getlocal("pending.status.away") ?>",
    "<?php echo getlocal("pending.status.online") ?>"
);
var updaterOptions = {
	url:"<?php echo $webimroot ?>/operator/update.php",wroot:"<?php echo $webimroot ?>",
	agentservl:"<?php echo $webimroot ?>/operator/agent.php", frequency:<?php echo $page['frequency'] ?>, istatus:<?php echo $page['istatus'] ?>,  
	noclients:"<?php echo getlocal("clients.no_clients") ?>", havemenu: <?php echo $page['havemenu'] ?>, showpopup: <?php echo $page['showpopup'] ?>,
	showonline: <?php echo $page['showonline'] ?>, showvisitors: <?php echo $page['showvisitors'] ?>, novisitors: "<?php echo getlocal("visitors.no_visitors") ?>",
	trackedservl:"<?php echo $webimroot ?>/operator/tracked.php", inviteservl:"<?php echo $webimroot ?>/operator/invite.php" };
//--></script>
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/compiled/users.js"></script>
<?php
}

function tpl_content() { global $page, $webimroot;
?>

<div>
<div id="togglediv">
<a href="#" id="togglemenu"></a>
</div>
<?php echo getlocal("clients.intro") ?>
<br/>
<?php echo getlocal("clients.how_to") ?>
</div>
<br/>

<table id="threadlist" class="awaiting" border="0">
<thead>
<tr>
	<th class="first"><?php echo getlocal("pending.table.head.name") ?></th>
	<th><?php echo getlocal("pending.table.head.contactid") ?></th>
    <th><?php echo getlocal("pending.table.head.state") ?></th>
    <th><?php echo getlocal("pending.table.head.operator") ?></th>
    <th><?php echo getlocal("pending.table.head.total") ?></th>
    <th><?php echo getlocal("pending.table.head.waittime") ?></th>
    <th><?php echo getlocal("pending.table.head.etc") ?></th>
</tr>
</thead>
<tbody>
<tr id="tprio"><td colspan="7"></td></tr>
<tr id="tprioend"><td colspan="7"></td></tr>

<tr id="twait"><td colspan="7"></td></tr>
<tr id="twaitend"><td colspan="7"></td></tr>

<tr id="tchat"><td colspan="7"></td></tr>
<tr id="tchatend"><td colspan="7"></td></tr>

<tr><td id="statustd" colspan="7" height="30">Loading....</td></tr>
</tbody>
</table>

<?php if ($page['showvisitors']) { ?>
<div class="tabletitle"><?php echo getlocal("visitors.title") ?></div>
<?php echo getlocal("visitors.intro") ?>
<br/>
<?php echo getlocal("visitors.how_to") ?>
<table id="visitorslist" class="awaiting" border="0">
<thead>
<tr>
	<th class="first"><?php echo getlocal("visitors.table.head.name") ?></th>
    <th><?php echo getlocal("visitors.table.head.contactid") ?></th>
    <th><?php echo getlocal("visitors.table.head.firsttimeonsite") ?></th>
    <th><?php echo getlocal("visitors.table.head.lasttimeonsite") ?></th>
    <th><?php echo getlocal("visitors.table.head.invited.by") ?></th>
    <th><?php echo getlocal("visitors.table.head.invitationtime") ?></th>
    <th><?php echo getlocal("visitors.table.head.invitations") ?></th>
    <th><?php echo getlocal("visitors.table.head.etc") ?></th>
</tr>
</thead>
<tbody>
<tr id="visfree"><td colspan="8"></td></tr>
<tr id="visfreeend"><td colspan="8"></td></tr>

<tr id="visinvited"><td colspan="8"></td></tr>
<tr id="visinvitedend"><td colspan="8"></td></tr>

<tr><td id="visstatustd" colspan="8" height="30">Loading....</td></tr>
</tbody>
</table>
<hr/>
<?php } ?>

<div id="connstatus">
</div>

<div id="connlinks">
<?php if($page['istatus']) { ?>
<a href="users.php<?php echo $page['havemenu'] ? "" : "?nomenu" ?>"><?php echo getlocal("pending.status.setonline") ?></a>
<?php } else { ?>
<a href="users.php?away<?php echo $page['havemenu'] ? "" : "&amp;nomenu" ?>"><?php echo getlocal("pending.status.setaway") ?></a>
<?php } ?>
</div>

<?php if($page['showonline'] == "1") { ?>
<div id="onlineoperators">
</div>
<?php } ?>

<?php 
} /* content */

require_once('inc_main.php');
?>