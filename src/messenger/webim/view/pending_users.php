<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2009 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once("inc_menu.php");
$page['title'] = getlocal("clients.title");
$page['menuid'] = "users";


function tpl_header() { global $page, $webimroot;
?>	
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/common.js?v=160a1"></script>
<script type="text/javascript" language="javascript"><!--
var localized = new Array(
    "<?php echo getlocal("pending.table.speak") ?>",
    "<?php echo getlocal("pending.table.view") ?>",
    "<?php echo getlocal("pending.table.ban") ?>",
    "<?php echo htmlspecialchars(getlocal("pending.menu.show")) ?>",
    "<?php echo htmlspecialchars(getlocal("pending.menu.hide")) ?>"
);
var updaterOptions = {
	url:"<?php echo $webimroot ?>/operator/update.php",wroot:"<?php echo $webimroot ?>",
	agentservl:"<?php echo $webimroot ?>/operator/agent.php",
	noclients:"<?php echo getlocal("clients.no_clients") ?>" };
//--></script>
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/users.js?v=160a1"></script>
<?php
}

function tpl_content() { global $page, $webimroot;
?>

<div>
<div style="float:right;padding-right:10px;">
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


<div id="connstatus">
</div>



<?php 
} /* content */

require_once('inc_main.php');
?>