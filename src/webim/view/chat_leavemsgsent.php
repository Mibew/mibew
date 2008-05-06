<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Internet Services Ltd.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title><?php echo getlocal("leavemessage.sent.title") ?></title>
<link rel="shortcut icon" href="<?php echo $webimroot ?>/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="<?php echo $webimroot ?>/chat.css" />
</head>
<body bgcolor="#FFFFFF" background="<?php echo $webimroot ?>/images/bg.gif" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400" marginwidth="0" marginheight="0" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0">
<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top">

<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td height="75"></td>
<td class="window">
	<h1><?php echo getlocal("leavemessage.sent.title") ?></h1>
</td>
<td></td>
</tr>

<tr>
<td height="100%"></td>
<td>

	<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
    <td width="15"><img src='<?php echo $webimroot ?>/images/wincrnlt.gif' width="15" height="15" border="0" alt="" /></td>
	<td width="100%" background="<?php echo $webimroot ?>/images/winbg.gif" class="bgcy"><img src='<?php echo $webimroot ?>/images/free.gif' width="1" height="1" border="0" alt="" /></td>
    <td width="15"><img src='<?php echo $webimroot ?>/images/wincrnrt.gif' width="15" height="15" border="0" alt="" /></td>
	</tr>

	<tr>
    <td height="100%" bgcolor="#FED840"><img src='<?php echo $webimroot ?>/images/free.gif' width="1" height="1" border="0" alt="" /></td>
	<td background="<?php echo $webimroot ?>/images/winbg.gif" class="bgcy">

		<?php echo getlocal("leavemessage.sent.message") ?><br/>
	</td>
    <td bgcolor="#E8A400"><img src='<?php echo $webimroot ?>/images/free.gif' width="1" height="1" border="0" alt="" /></td>
	</tr>

	<tr>
    <td><img src='<?php echo $webimroot ?>/images/wincrnlb.gif' width="15" height="15" border="0" alt="" /></td>
	<td background="<?php echo $webimroot ?>/images/winbg.gif" class="bgcy"><img src='<?php echo $webimroot ?>/images/free.gif' width="1" height="1" border="0" alt="" /></td>
    <td><img src='<?php echo $webimroot ?>/images/wincrnrb.gif' width="15" height="15" border="0" alt="" /></td>
	</tr>
	</table>

</td>
<td></td>
</tr>

<tr>
<td height="70"></td>
<td>

	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
    <td width="100%" align="right">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td><a href="javascript:window.close();" title="<?php echo getlocal("chat.mailthread.sent.close") ?>"><img src='<?php echo $webimroot ?>/images/buttons/back.gif' width="25" height="25" border="0" alt="" /></a></td>
	    <td width="5"></td>
	    <td class="button"><a href="javascript:window.close();" title="<?php echo getlocal("chat.mailthread.sent.close") ?>"><?php echo getlocal("chat.mailthread.sent.close") ?></a></td>
		</tr>
		</table>
	</td>
	</tr>
	</table>
	
</td>
<td></td>
</tr>

<tr>
<td width="30"><img src='<?php echo $webimroot ?>/images/free.gif' width="30" height="1" border="0" alt="" /></td>
<td width="100%"><img src='<?php echo $webimroot ?>/images/free.gif' width="540" height="1" border="0" alt="" /></td>
<td width="30"><img src='<?php echo $webimroot ?>/images/free.gif' width="30" height="1" border="0" alt="" /></td>
</tr>
</table>

</td>
</tr>
</table>

</body>
</html>

