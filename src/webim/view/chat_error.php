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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title><?php echo getstring("chat.error_page.title") ?></title>
<link rel="shortcut icon" href="/webim/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="/webim/chat.css" />
</head>
<body bgcolor="#FFFFFF" background="/webim/images/bg.gif" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400" marginwidth="0" marginheight="0" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0">
<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top">





<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td height="75"></td>
<td class="window">
	<h1><?php echo getstring("chat.error_page.head") ?></h1>
</td>
<td></td>
</tr>

<tr>
<td height="100%"></td>
<td>

	<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0"><tr>
    <td width="15"><img src='/webim/images/wincrnlt.gif' width="15" height="15" border="0" alt="" /></td>
	<td width="100%" background="/webim/images/winbg.gif" class="bgcy"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
    <td width="15"><img src='/webim/images/wincrnrt.gif' width="15" height="15" border="0" alt="" /></td>
	</tr><tr><td height="100%" bgcolor="#FED840"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
	<td background="/webim/images/winbg.gif" class="bgcy">

		<?php	if( isset($errors) && count($errors) > 0 ) {
		print getstring("errors.header");
		foreach( $errors as $e ) {
			print getstring("errors.prefix");
			print $e;
			print getstring("errors.suffix");
		}
		print getstring("errors.footer");
	} ?>


	</td><td bgcolor="#E8A400"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
	</tr><tr><td><img src='/webim/images/wincrnlb.gif' width="15" height="15" border="0" alt="" /></td>
	<td background="/webim/images/winbg.gif" class="bgcy"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
    <td><img src='/webim/images/wincrnrb.gif' width="15" height="15" border="0" alt="" /></td>
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
	    <td><a href="javascript:window.close();" title="<?php echo getstring("chat.error_page.close") ?>"><img src='/webim/images/buttons/back.gif' width="25" height="25" border="0" alt="" /></a></td>
	    <td width="5"></td>
	    <td class="button"><a href="javascript:window.close();" title="<?php echo getstring("chat.error_page.close") ?>"><?php echo getstring("chat.error_page.close") ?></a></td>
		</tr>
		</table>
	</td>
	</tr>
	</table>
	
</td>
<td></td>
</tr>

<tr>
<td width="30"><img src='/webim/images/free.gif' width="30" height="1" border="0" alt="" /></td>
<td width="100%"><img src='/webim/images/free.gif' width="540" height="1" border="0" alt="" /></td>
<td width="30"><img src='/webim/images/free.gif' width="30" height="1" border="0" alt="" /></td>
</tr>
</table>

</td>
</tr>
</table>

</body>
</html>

