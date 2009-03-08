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
?>
<html>
<head>



<link rel="stylesheet" type="text/css" media="all" href="<?php echo $webimroot ?>/styles.css" />
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/common.js?v=152"></script>
<script><!--
var localized = new Array(
    "<?php echo getlocal("pending.table.speak") ?>",
    "<?php echo getlocal("pending.table.view") ?>",
    "<?php echo getlocal("pending.table.ban") ?>"
);
var updaterOptions = {
	url:"<?php echo $webimroot ?>/operator/update.php",wroot:"<?php echo $webimroot ?>",
	agentservl:"<?php echo $webimroot ?>/operator/agent.php",
	noclients:"<?php echo getlocal("clients.no_clients") ?>" };
//--></script>
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/users.js?v=152"></script>

<link rel="shortcut icon" href="<?php echo $webimroot ?>/images/favicon.ico" type="image/x-icon"/>
<title>
	<?php echo getlocal("clients.title") ?> - <?php echo getlocal("app.title") ?>
</title>

<meta http-equiv="keywords" content="<?php echo getlocal("page.main_layout.meta_keyword") ?>">
<meta http-equiv="description" content="<?php echo getlocal("page.main_layout.meta_description") ?>">
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#2971C1" vlink="#2971C1" alink="#2971C1">

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td valign="top" class="text">

 <table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="left" valign="top">
		<h1><?php echo getlocal("clients.title") ?></h1>
 </td><td align="right" class="text" valign="top"><table cellspacing="0" cellpadding="0" border="0"><tr><td class="textform"><?php echo getlocal2("menu.operator",array($page['operator'])) ?></td><td class="textform"><img src='<?php echo $webimroot ?>/images/topdiv.gif' width="25" height="15" border="0" alt="|" /></td><td class="textform"><a href="<?php echo $webimroot ?>/operator/index.php" title="<?php echo getlocal("menu.main") ?>"><?php echo getlocal("menu.main") ?></a></td></tr></table></td></tr></table>


	<?php echo getlocal("clients.intro") ?><br>

<?php echo getlocal("clients.how_to") ?><br>
<br>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr><td colspan="3" bgcolor="#DADADA"><img src='<?php echo $webimroot ?>/images/free.gif' width="1" height="1" border="0" alt="" /></td></tr>
<tr><td bgcolor="#DADADA"><img src='<?php echo $webimroot ?>/images/free.gif' width="1" height="1" border="0" alt="" /></td>
   <td width="100%">

   <!-- Pending -->

<table width="100%" id="threadlist" cellspacing="0" cellpadding="0" border="0">
<tr>
    <td width="150" height="30" bgcolor="#276DB8" class="table"><span class="header"><?php echo getlocal("pending.table.head.name") ?></span></td>
    <td width="3"></td>
    <td bgcolor="#276DB8" align="center" class="table" nowrap><span class="header"><?php echo getlocal("pending.table.head.contactid") ?></span></td>
    <td width="3"></td>
    <td bgcolor="#276DB8" align="center" class="table" nowrap><span class="header"><?php echo getlocal("pending.table.head.state") ?></span></td>
    <td width="3"></td>
    <td bgcolor="#276DB8" align="center" class="table" nowrap><span class="header"><?php echo getlocal("pending.table.head.operator") ?></span></td>
    <td width="3"></td>
    <td bgcolor="#276DB8" align="center" class="table" nowrap><span class="header"><?php echo getlocal("pending.table.head.total") ?></span></td>
    <td width="3"></td>
    <td bgcolor="#276DB8" align="center" class="table" nowrap><span class="header"><?php echo getlocal("pending.table.head.waittime") ?></span></td>
    <td width="3"></td>
    <td bgcolor="#276DB8" align="center" class="table" nowrap><span class="header"><?php echo getlocal("pending.table.head.etc") ?></span></td>
</tr>

<tr>
    <td colspan="13" height="2"></td>
</tr>

<tr id="prio">
    <td colspan="13" height="30" bgcolor="#F5F5F5" class="table">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td><img src='<?php echo $webimroot ?>/images/tblicusers.gif' width="15" height="15" border="0" alt="" /></td>
    	<td class="table"><span class="black"><?php echo getlocal("clients.queue.prio") ?></span></td>
		</tr>
		</table>
	</td>
</tr>

<tr id="prioend">
    <td colspan="13" height="30" class="table" id="status"></td>
</tr>

<tr id="wait">
    <td colspan="13" height="30" bgcolor="#F5F5F5" class="table">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td><img src='<?php echo $webimroot ?>/images/tblicusers2.gif' width="15" height="15" border="0" alt="" /></td>
    	<td class="table"><span class="black"><?php echo getlocal("clients.queue.wait") ?></span></td>
		</tr>
		</table>
	</td>
</tr>

<tr id="waitend">
    <td colspan="13" height="30" class="table" id="status"></td>
</tr>

<tr id="chat">
    <td colspan="13" height="30" bgcolor="#F5F5F5" class="table">
		<table cellspacing="0" cellpadding="0" border="0"><tr>
	    <td><img src='<?php echo $webimroot ?>/images/tblicusers3.gif' width="30" height="15" border="0" alt="" /></td>
    	<td class="table"><span class="black"><?php echo getlocal("clients.queue.chat") ?></span></td>
		</tr>
		</table>
	</td>
</tr>

<tr id="chatend">
    <td colspan="13" height="30" class="table" id="status"></td>
</tr>

</table>

</td><td bgcolor="#DADADA"><img src='<?php echo $webimroot ?>/images/free.gif' width="1" height="1" border="0" alt="" /></td>
</tr><tr>
   <td colspan="3" bgcolor="#DADADA"><img src='<?php echo $webimroot ?>/images/free.gif' width="1" height="1" border="0" alt="" /></td>
</tr></table>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr><td colspan="2" height="15"></td></tr>
	<tr>
    <td class="text"></td>
    <td align="right" class="text" id="connstatus">
    </td>
	</tr>

</table>

</td>
</tr>
</table>

</body>
</html>

