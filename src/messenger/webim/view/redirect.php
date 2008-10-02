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
<title><?php echo getlocal("chat.window.title.agent") ?></title>
<link rel="shortcut icon" href="<?php echo $webimroot ?>/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="<?php echo $webimroot ?>/chat.css" />
</head>
<body bgcolor="#FFFFFF" background="<?php echo $webimroot ?>/images/bg.gif" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400" marginwidth="0" marginheight="0" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0">
<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top">


<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td height="90"></td>
<td>
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
    <td width="50%" height="90" class="window"><h1><?php echo getlocal("chat.redirect.title") ?></h1></td>
	<td width="50%" align="right" valign="bottom" class="window">
		<h2><?php echo getlocal("chat.redirect.choose_operator") ?></h2>
		<img src='<?php echo $webimroot ?>/images/free.gif' width="1" height="5" border="0" alt="" /><br>
	</td>
	</tr>
	</table>
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

		<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td width="33%" valign="top" class="window">

<?php for( $indagent = 0; $indagent < count($page['pagination.items']); $indagent += 3 ) { $agent = $page['pagination.items'][$indagent]; ?>
			<?php $page['params']['nextAgent'] = $agent['operatorid']; ?>
			<a href="<?php echo add_params($webimroot."/operator/redirect.php",$page['params']) ?>" title="<?php echo topage($agent['vclocalename']) ?>"><?php echo topage($agent['vclocalename']) ?></a><br>
			<img src='<?php echo $webimroot ?>/images/free.gif' width="1" height="5" border="0" alt="" /><br>
<?php } ?>

		</td>
	    <td width="50" background="<?php echo $webimroot ?>/images/textdiv.gif" valign="top">
	    	<img src='<?php echo $webimroot ?>/images/textdiv.gif' width="50" height="1" border="0" alt="" /></td>
	    <td width="33%" valign="top" class="window">

<?php for( $indagent = 1; $indagent < count($page['pagination.items']); $indagent += 3 ) { $agent = $page['pagination.items'][$indagent]; ?>
			<?php $page['params']['nextAgent'] = $agent['operatorid']; ?>
			<a href="<?php echo add_params($webimroot."/operator/redirect.php",$page['params']) ?>" title="<?php echo topage($agent['vclocalename']) ?>"><?php echo topage($agent['vclocalename']) ?></a><br>
			<img src='<?php echo $webimroot ?>/images/free.gif' width="1" height="5" border="0" alt="" /><br>
<?php } ?>

		</td>
	    <td width="50" background="<?php echo $webimroot ?>/images/textdiv.gif" valign="top">
	    	<img src='<?php echo $webimroot ?>/images/textdiv.gif' width="50" height="1" border="0" alt="" /></td>
	    <td width="33%" valign="top" class="window">

<?php for( $indagent = 2; $indagent < count($page['pagination.items']); $indagent += 3 ) { $agent = $page['pagination.items'][$indagent]; ?>
			<?php $page['params']['nextAgent'] = $agent['operatorid']; ?>
			<a href="<?php echo add_params($webimroot."/operator/redirect.php",$page['params']) ?>" title="<?php echo topage($agent['vclocalename']) ?>"><?php echo topage($agent['vclocalename']) ?></a><br>
			<img src='<?php echo $webimroot ?>/images/free.gif' width="1" height="5" border="0" alt="" /><br>
<?php } ?>

		</td>
		</tr>
		</table>

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
<td height="90"></td>
<td>

	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
    <td width="50%" align="left">
		<?php echo generate_pagination($page['pagination']) ?>
	</td>
    <td width="50%" align="right">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td><a href="javascript:window.back();" title="<?php echo getlocal("chat.redirect.back") ?>"><img src='<?php echo $webimroot ?>/images/buttons/back.gif' width="25" height="25" border="0" alt="" /></a></td>
	    <td width="5"></td>
	    <td class="button"><a href="javascript:window.back();" title="<?php echo getlocal("chat.redirect.back") ?>"><?php echo getlocal("chat.redirect.back") ?></a></td>
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

