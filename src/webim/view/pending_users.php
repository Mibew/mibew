<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2007 Internet Services Ltd.
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



<link rel="stylesheet" type="text/css" media="all" href="/webim/styles.css" />
<script type="text/javascript" language="javascript" src="/webim/js/common.js"></script>








<script><!--
var localized = new Array(
    "<?php echo getstring("pending.table.speak") ?>",
    "<?php echo getstring("pending.table.view") ?>",
    "<?php echo getstring("pending.table.ban") ?>"
);
var updaterOptions = {
	url:"/webim/operator/update.php", 

	agentservl:"/webim/operator/agent.php",
	noclients:"<?php echo getstring("clients.no_clients") ?>" };
//--></script>
<script type="text/javascript" language="javascript" src="/webim/js/page_pendingclients.js"></script>




<link rel="shortcut icon" href="/webim/images/favicon.ico" type="image/x-icon"/>
<title>
	<?php echo getstring("app.title") ?>	- <?php echo getstring("clients.title") ?>
</title>

<meta http-equiv="keywords" content="<?php echo getstring("page.main_layout.meta_keyword") ?>">
<meta http-equiv="description" content="<?php echo getstring("page.main_layout.meta_description") ?>">




</head>

<body bgcolor="#FFFFFF" text="#000000" link="#2971C1" vlink="#2971C1" alink="#2971C1">

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td valign="top" class="text">
	
 <table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="left" valign="top"> 
		<h1><?php echo getstring("clients.title") ?></h1>
 </td><td align="right" class="text" valign="top"><table cellspacing="0" cellpadding="0" border="0"><tr><td class="textform"><?php echo getstring2("menu.operator",array($page['operator'])) ?></td><td class="textform"><img src='/webim/images/topdiv.gif' width="25" height="15" border="0" alt="|" /></td><td class="textform"><a href="/webim/operator/index.php" title="<?php echo getstring("menu.main") ?>"><?php echo getstring("menu.main") ?></a></td></tr></table></td></tr></table> 
	

	




<?php echo getstring("clients.intro") ?><br>

<?php echo getstring("clients.how_to") ?><br>
<br>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr><td colspan="3" bgcolor="#DADADA"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td></tr>
<tr><td bgcolor="#DADADA"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
   <td width="100%">
   
   <!-- Pending -->

<table width="100%" id="threadlist" cellspacing="0" cellpadding="0" border="0">
<tr>
    <td width="150" height="30" bgcolor="#276DB8" class="table"><span class="header"><?php echo getstring("pending.table.head.name") ?></span></td>
    <td width="3"></td>
    <td bgcolor="#276DB8" align="center" class="table" nowrap><span class="header"><?php echo getstring("pending.table.head.contactid") ?></span></td>
    <td width="3"></td>
    <td bgcolor="#276DB8" align="center" class="table" nowrap><span class="header"><?php echo getstring("pending.table.head.state") ?></span></td>
    <td width="3"></td>
    <td bgcolor="#276DB8" align="center" class="table" nowrap><span class="header"><?php echo getstring("pending.table.head.operator") ?></span></td>
    <td width="3"></td>
    <td bgcolor="#276DB8" align="center" class="table" nowrap><span class="header"><?php echo getstring("pending.table.head.total") ?></span></td>
    <td width="3"></td>
    <td bgcolor="#276DB8" align="center" class="table" nowrap><span class="header"><?php echo getstring("pending.table.head.waittime") ?></span></td>
    <td width="3"></td>
    <td bgcolor="#276DB8" align="center" class="table" nowrap><span class="header"><?php echo getstring("pending.table.head.etc") ?></span></td>
</tr>

<tr>
    <td colspan="13" height="2"></td>
</tr>

<tr id="prio">
    <td colspan="13" height="30" bgcolor="#F5F5F5" class="table">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td><img src='/webim/images/tblicusers.gif' width="15" height="15" border="0" alt="" /></td>
    	<td class="table"><span class="black"><?php echo getstring("clients.queue.prio") ?></span></td>
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
	    <td><img src='/webim/images/tblicusers2.gif' width="15" height="15" border="0" alt="" /></td>
    	<td class="table"><span class="black"><?php echo getstring("clients.queue.wait") ?></span></td>
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
	    <td><img src='/webim/images/tblicusers3.gif' width="30" height="15" border="0" alt="" /></td>
    	<td class="table"><span class="black"><?php echo getstring("clients.queue.chat") ?></span></td>
		</tr>
		</table>
	</td>
</tr>

<tr id="chatend">
    <td colspan="13" height="30" class="table" id="status"></td>
</tr>

</table>

</td><td bgcolor="#DADADA"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
</tr><tr>
   <td colspan="3" bgcolor="#DADADA"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
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

