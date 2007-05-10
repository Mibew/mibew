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





<link rel="shortcut icon" href="/webim/images/favicon.ico" type="image/x-icon"/>
<title>
	<?php echo getstring("app.title") ?>	- <?php echo getstring("topMenu.admin") ?>
</title>

<meta http-equiv="keywords" content="<?php echo getstring("page.main_layout.meta_keyword") ?>">
<meta http-equiv="description" content="<?php echo getstring("page.main_layout.meta_description") ?>">




</head>

<body bgcolor="#FFFFFF" text="#000000" link="#2971C1" vlink="#2971C1" alink="#2971C1">

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td valign="top" class="text">
	
 <table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="left" valign="top"> 
		<h1><?php echo getstring("topMenu.admin") ?></h1>
 </td><td align="right" class="text" valign="top"><table cellspacing="0" cellpadding="0" border="0"><tr><td class="textform"><?php echo getstring2("menu.operator",array($page['operator'])) ?></td></tr></table></td></tr></table> 
	

	<?php echo getstring("admin.content.description") ?>
<br>
<br>

<table cellspacing="0" cellpadding="0" border="0">

	<tr><td width='20' valign='top'><img src='/webim/images/lidiv.gif' width='5' height='45' border='0' alt=''></td><td valign='top' class='text'><a href='/webim/operator/operators.php'><?php echo getstring('leftMenu.client_agents') ?></a><br><img src='/webim/images/free.gif' width='1' height='10' border='0' alt=''><br><?php echo getstring('admin.content.client_agents') ?><br></td></tr><tr><td colspan='2' height='20'></td></tr>

	<tr><td width='20' valign='top'><img src='/webim/images/lidiv.gif' width='5' height='45' border='0' alt=''></td><td valign='top' class='text'><a href='/webim/operator/users.php'><?php echo getstring('topMenu.users') ?></a><br><img src='/webim/images/free.gif' width='1' height='10' border='0' alt=''><br><?php echo getstring('page_client.pending_users') ?><br></td></tr><tr><td colspan='2' height='20'></td></tr>

	<tr><td width='20' valign='top'><img src='/webim/images/lidiv.gif' width='5' height='45' border='0' alt=''></td><td valign='top' class='text'><a href='/webim/operator/getcode.php'><?php echo getstring('leftMenu.client_gen_button') ?></a><br><img src='/webim/images/free.gif' width='1' height='10' border='0' alt=''><br><?php echo getstring('admin.content.client_gen_button') ?><br></td></tr><tr><td colspan='2' height='20'></td></tr>

	<tr><td width='20' valign='top'><img src='/webim/images/lidiv.gif' width='5' height='45' border='0' alt=''></td><td valign='top' class='text'><a href='/webim/operator/logout.php'><?php echo getstring('topMenu.logoff') ?></a><br><img src='/webim/images/free.gif' width='1' height='10' border='0' alt=''><br><?php echo getstring('content.logoff') ?><br></td></tr><tr><td colspan='2' height='20'></td></tr>

</table>
</td>
</tr>
</table>

</body>
</html>

