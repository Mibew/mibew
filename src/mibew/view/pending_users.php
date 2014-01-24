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

require_once("inc_menu.php");
$page['title'] = getlocal("clients.title");
$page['menuid'] = "users";


function tpl_header() { global $page, $mibewroot, $jsver;
?>
<script type="text/javascript" language="javascript" src="<?php echo $mibewroot ?>/js/<?php echo $jsver ?>/common.js"></script>
<script type="text/javascript" language="javascript"><!--
var localized = new Array(
    "<?php echo safe_htmlspecialchars(getlocal("pending.table.speak")) ?>",
    "<?php echo safe_htmlspecialchars(getlocal("pending.table.view")) ?>",
    "<?php echo safe_htmlspecialchars(getlocal("pending.table.ban")) ?>",
    "<?php echo safe_htmlspecialchars(getlocal("pending.menu.show")) ?>",
    "<?php echo safe_htmlspecialchars(getlocal("pending.menu.hide")) ?>",
    "<?php echo safe_htmlspecialchars(getlocal("pending.popup_notification")) ?>"
);
var updaterOptions = {
	url:"<?php echo $mibewroot ?>/operator/update.php",wroot:"<?php echo $mibewroot ?>",
	agentservl:"<?php echo $mibewroot ?>/operator/agent.php", frequency:<?php echo safe_htmlspecialchars($page['frequency']) ?>, istatus:<?php echo safe_htmlspecialchars($page['istatus']) ?>,
	noclients:"<?php echo safe_htmlspecialchars(getlocal("clients.no_clients")) ?>", havemenu: <?php echo safe_htmlspecialchars($page['havemenu']) ?>, showpopup: <?php echo safe_htmlspecialchars($page['showpopup']) ?>, showonline: <?php echo safe_htmlspecialchars($page['showonline']) ?> };
//--></script>
<script type="text/javascript" language="javascript" src="<?php echo $mibewroot ?>/js/<?php echo $jsver ?>/users.js"></script>
<?php
}

function tpl_content() { global $page, $mibewroot;
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

<tr><td id="statustd" colspan="7" height="30"><?php echo getlocal("pending.loading") ?></td></tr>
</tbody>
</table>


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