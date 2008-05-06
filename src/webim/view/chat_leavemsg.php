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
<title><?php echo getlocal("leavemessage.title") ?></title>
<link rel="shortcut icon" href="<?php echo $webimroot ?>/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="<?php echo $webimroot ?>/chat.css" />
</head>
<body bgcolor="#FFFFFF" background="<?php echo $webimroot ?>/images/bg.gif" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400" marginwidth="0" marginheight="0" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0">
<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top">

<form name="leaveMessageForm" method="post" action="<?php echo $webimroot ?>/leavemessage.php">

<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr><td colspan="3" height="15"></td></tr>
<tr>
<td height="40"></td>
<td class="window">
	<h1><?php echo getlocal("leavemessage.title") ?></h1>
</td>
<td></td>
</tr>
<tr><td></td>
<td height="25">
<?php if( isset($errors) && count($errors) > 0 ) { ?>
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td valign="top"><img src='<?php echo $webimroot ?>/images/icon_err.gif' width="40" height="40" border="0" alt="" /></td>
	    <td width="10"></td>
	    <td class="text">
		    <?php	if( isset($errors) && count($errors) > 0 ) {
		print getlocal("errors.header");
		foreach( $errors as $e ) {
			print getlocal("errors.prefix");
			print $e;
			print getlocal("errors.suffix");
		}
		print getlocal("errors.footer");
	} ?>

		</td>
		</tr>
		</table>
	<?php } ?>
</td><td></td>
</tr>

<tr>
<td height="60"></td>
<td>

	<table cellspacing="0" cellpadding="0" border="0"><tr><td width="15"><img src="<?php echo $webimroot ?>/images/wincrnlt.gif" width="15" height="15" border="0" alt="" /></td><td width="100%" background="<?php echo $webimroot ?>/images/winbg.gif" class="bgcy"><img src="<?php echo $webimroot ?>/images/free.gif" width="1" height="1" border="0" alt="" /></td><td width="15"><img src="<?php echo $webimroot ?>/images/wincrnrt.gif" width="15" height="15" border="0" alt="" /></td></tr><tr><td height="100%" bgcolor="#FED840"><img src="<?php echo $webimroot ?>/images/free.gif" width="1" height="1" border="0" alt="" /></td><td background="<?php echo $webimroot ?>/images/winbg.gif" class="bgcy"><table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td colspan="3" class="text"><?php echo getlocal("leavemessage.descr") ?></td>
		</tr>
		<tr><td height="20" colspan="3"></td></tr>		
		<tr>
	    <td class="text"><?php echo getlocal("form.field.email") ?>:</td>
	    <td width="20"></td>
	    <td><input type="text" name="email" size="50" value="<?php echo form_value('email') ?>" class="username"/></td>
		</tr>
		<tr><td height="7" colspan="3"></td></tr>		
		<tr>
	    <td class="text"><?php echo getlocal("form.field.name") ?>:</td>
	    <td width="20"></td>
	    <td><input type="text" name="name" size="50" value="<?php echo form_value('name') ?>" class="username"/></td>
		</tr>
		<tr><td height="7" colspan="3"></td></tr>		
		<tr>
	    <td class="text"><?php echo getlocal("form.field.message") ?>:</td>
	    <td width="20"></td>
	    <td height="120" valign="top"><table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0"><tr><td colspan="3" bgcolor="#A1A1A1"><img src="<?php echo $webimroot ?>/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr><tr><td bgcolor="#A1A1A1"><img src="<?php echo $webimroot ?>/images/free.gif" width="1" height="1" border="0" alt="" /></td><td width="100%" height="100%" bgcolor="#FFFFFF" valign="top">
	    <textarea name="message" class="message" tabindex="0"><?php echo form_value('message') ?></textarea>
	    </td><td bgcolor="#A1A1A1"><img src="<?php echo $webimroot ?>/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr><tr><td colspan="3" bgcolor="#A1A1A1"><img src="<?php echo $webimroot ?>/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr></table></td>
		</tr>
	</table></td><td bgcolor="#E8A400"><img src="<?php echo $webimroot ?>/images/free.gif" width="1" height="1" border="0" alt="" /></td></tr><tr><td><img src="<?php echo $webimroot ?>/images/wincrnlb.gif" width="15" height="15" border="0" alt="" /></td><td background="<?php echo $webimroot ?>/images/winbg.gif" class="bgcy"><img src="<?php echo $webimroot ?>/images/free.gif" width="1" height="1" border="0" alt="" /></td><td><img src="<?php echo $webimroot ?>/images/wincrnrb.gif" width="15" height="15" border="0" alt=""/></td></tr></table>

</td>
<td></td>
</tr>

<tr>
<td height="70"></td>
<td>

	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
    <td width="33%">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td><a href="javascript:document.leaveMessageForm.submit();" title="<?php echo getlocal("leavemessage.perform") ?>"><img src='<?php echo $webimroot ?>/images/submit.gif' width="40" height="35" border="0" alt="" /></a></td>
	    <td background="<?php echo $webimroot ?>/images/submitbg.gif" valign="top" class="submit">
			<img src='<?php echo $webimroot ?>/images/free.gif' width="1" height="10" border="0" alt="" /><br>
			<a href="javascript:document.leaveMessageForm.submit();" title="<?php echo getlocal("leavemessage.perform") ?>"><?php echo getlocal("leavemessage.perform") ?></a><br>
		</td>
	    <td width="10"><a href="javascript:document.leaveMessageForm.submit();" title="<?php echo getlocal("leavemessage.perform") ?>"><img src='<?php echo $webimroot ?>/images/submitrest.gif' width="10" height="35" border="0" alt="" /></a></td>
		</tr>
		</table>
	</td>

  	<td width="33%" align="center" class="copyr"><?php echo getlocal("chat.window.poweredby") ?> <a href="<?php echo getlocal("site.url") ?>" title="<?php echo getlocal("company.title") ?>" target="_blank"><?php echo getlocal("chat.window.poweredreftext") ?></a></td>

    <td width="33%" align="right">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td><a href="javascript:window.close();" title="<?php echo getlocal("leavemessage.close") ?>"><img src='<?php echo $webimroot ?>/images/buttons/back.gif' width="25" height="25" border="0" alt="" /></a></td>
	    <td width="5"></td>
	    <td class="button"><a href="javascript:window.close();" title="<?php echo getlocal("leavemessage.close") ?>"><?php echo getlocal("leavemessage.close") ?></a></td>
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

</form>

</td>
</tr>
</table>

</body>
</html>

