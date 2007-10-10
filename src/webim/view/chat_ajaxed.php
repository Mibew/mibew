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
<title><?php echo getstring("chat.window.title.agent") ?></title>
<link rel="shortcut icon" href="/webim/images/favicon.ico" type="image/x-icon"/>
<meta http-equiv="Content-Type" content="text/html; charset=Windows-1251">
<link rel="stylesheet" type="text/css" href="/webim/chat.css" />
<script type="text/javascript" language="javascript" src="/webim/js/common.js"></script>
<script type="text/javascript" language="javascript" src="/webim/js/brws.js"></script>
<script language="javascript"><!--
var threadParams = { servl:"/webim/thread.php",frequency:2,<?php if( $page['user'] ) { ?>user:"true",<?php } ?>threadid:<?php echo $page['ct.chatThreadId'] ?>,token:<?php echo $page['ct.token'] ?> };
//--></script>
<script type="text/javascript" language="javascript" src="/webim/js/page_chat.js"></script>
</head>

<body bgcolor="#FFFFFF" background="/webim/images/bg.gif" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400" marginwidth="0" marginheight="0" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0">

<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top">

	<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
    <td></td>
    <td colspan="2" height="100" background="/webim/images/banner.gif" valign="top" class="bgrn">
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td width="50%" valign="top">
			<table width="135" cellspacing="0" cellpadding="0" border="0">
			<tr>
		    <td height="25"></td>
			</tr>
			<tr>
		    <td align="center"><?php if( $page['ct.company.chatLogoURL'] ) { ?><img src="<?php echo $page['ct.company.chatLogoURL'] ?>" width="85" height="45" border="0" alt=""><?php } ?></td>
			</tr>
			<tr>
		    <td height="5"></td>
			</tr>
			<tr>
		    <td align="center" class="text"><?php echo $page['ct.company.name'] ?></td>
			</tr>
			</table>
		</td>
    	<td width="50%" align="right" valign="top">
			<table cellspacing="0" cellpadding="0" border="0">
			<tr>
		    <td height="25" align="right">

				<table cellspacing="0" cellpadding="0" border="0">
				<tr>
			    <td class="text"><?php echo getstring("chat.window.product_name") ?></td>
			    <td width="5"></td>
			    <td>
					<table cellspacing="0" cellpadding="0" border="0">
					<tr>
				    <td width="95" height="13" bgcolor="#D09221" align="center" class="www"><a href="<?php echo getstring("site.url") ?>" title="<?php echo getstring("company.title") ?>" target="_blank"><?php echo getstring("site.title") ?></a></td>
					</tr>
					</table>
				</td>
			    <td width="5"></td>
			    <td><a class="closethread" href="javascript:void(0)" onclick="return false;" title="<?php echo getstring("chat.window.close_title") ?>"><img src='/webim/images/buttons/closewin.gif' width="15" height="15" border="0" altKey="chat.window.close_link_text"/></a></td>
			    <td width="5"></td>
				</tr>
				</table>

			</td>
			</tr>

			<tr>
		    <td height="60" align="right">
				
				<table cellspacing="0" cellpadding="0" border="0">
				<tr>
<?php if( $page['agent'] ) { ?>
				<td class="text" nowrap>
					<?php echo getstring("chat.window.chatting_with") ?> <b><a href="javascript:void(0)" onclick="return false;" title="<?php echo getstring("chat.window.chatting_with") ?> <?php echo htmlspecialchars($page['ct.user.name']) ?><?php echo $page['namePostfix'] ?>"><?php echo htmlspecialchars($page['ct.user.name']) ?></a></b><br>
				</td>
<?php } ?><?php if( $page['user'] && $page['canChangeName'] ) { ?>
				<td class="text" nowrap>
				<div id="changename1" style="display:<?php echo $page['displ1'] ?>;">
					<table cellspacing="0" cellpadding="0" border="0"><tr>
					<td class="text" nowrap><?php echo getstring("chat.client.name") ?></td> 
					<td width="10" valign="top"><img src='/webim/images/free.gif' width="10" height="1" border="0" alt="" /></td>
					<td><input id="uname" type="text" size="12" value="<?php echo $page['ct.user.name'] ?>" class="username"></td>
					<td width="5" valign="top"><img src='/webim/images/free.gif' width="5" height="1" border="0" alt="" /></td>
					<td><a href="javascript:void(0)" onclick="return false;" title="<?php echo getstring("chat.client.changename") ?>"><img src='/webim/images/buttons/exec.gif' width="25" height="25" border="0" alt="&gt;&gt;" /></a></td>
					</tr></table>
				</div>
				<div id="changename2" style="display:<?php echo $page['displ2'] ?>;">
					<table cellspacing="0" cellpadding="0" border="0"><tr>
					<td class="text" nowrap><a href="javascript:void(0)" onclick="return false;" title="<?php echo getstring("chat.client.changename") ?>"><?php echo getstring("chat.client.changename") ?></a></td>
					<td width="10" valign="top"><img src='/webim/images/free.gif' width="10" height="1" border="0" alt="" /></td>
					<td><a href="javascript:void(0)" onclick="return false;" title="<?php echo getstring("chat.client.changename") ?>"><img src='/webim/images/buttons/changeuser.gif' width="25" height="25" border="0" alt="" /></a></td>
					</tr></table>
				</div>
				</td>
<?php } ?>
<?php if( $page['agent'] ) { ?>
				<td width="10" valign="top"><img src='/webim/images/free.gif' width="10" height="1" border="0" alt="" /></td>
				<td><a class="closethread" href="javascript:void(0)" onclick="return false;" title="<?php echo getstring("chat.window.close_title") ?>">
				<img src='/webim/images/buttons/close.gif' width="25" height="25" border="0" altKey="chat.window.close_link_text"/></a></td>
<?php } ?>
<!--
				<td><img src='/webim/images/buttons/changeuser.gif' width="25" height="25" border="0" alt="Change User" /></td>
 -->

			    <td><img src='/webim/images/buttondiv.gif' width="35" height="45" border="0" alt="" /></td>
<?php if( $page['user'] ) { ?>
				<td><a href="<?php echo $page['selfLink'] ?>&act=mailthread" target="_blank" title="<?php echo getstring("chat.window.toolbar.mail_history") ?>" onclick="this.newWindow = window.open('<?php echo $page['selfLink'] ?>&act=mailthread', 'ForwardMail', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=603,height=204,resizable=0');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src='/webim/images/buttons/email.gif' width="25" height="25" border="0" alt="Mail&nbsp;"/></a></td>
<?php } ?>

				<td><a id="refresh" href="javascript:void(0)" onclick="return false;" title="<?php echo getstring("chat.window.toolbar.refresh") ?>">
				<img src='/webim/images/buttons/refresh.gif' width="25" height="25" border="0" alt="Refresh&nbsp;" /></a></td>

				<td width="20" valign="top"><img src='/webim/images/free.gif' width="20" height="1" border="0" alt="" /></td>
				</tr>
				</table>

			</td>
			</tr>
			</table>
		</td>
		</tr>
		</table>
	</td>
	</tr>

	<tr>
    <td></td>
    <td valign="top">

		<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td width="20" valign="top"><img src='<?php echo getstring("image.chat.history") ?>' width="20" height="80" border="0" alt="" /></td>
    	<td colspan="2" width="100%" height="100%" valign="top" id="chatwndtd">
			<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
		    <td colspan="3" bgcolor="#A1A1A1"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
			</tr>
			<tr>
		    <td bgcolor="#A1A1A1"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
		    <td width="100%" height="100%" bgcolor="#FFFFFF" valign="top">
				<iframe id="chatwnd" width="100%" height="100%" src='/webim/images/free.gif' frameborder="0" style="overflow:auto;">
				Sorry, your browser does not support iframes; try a browser that supports W3 standards.
				</iframe>
			</td>
		    <td bgcolor="#A1A1A1"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
			</tr>
			<tr>
		    <td colspan="3" bgcolor="#A1A1A1"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
			</tr>
			</table>
		</td>
		</tr>
<?php if( $page['canpost'] ) { ?>
		<tr>
	    <td colspan="3" height="5" style="cursor:n-resize;" id="spl1"></td>
		</tr>

		<tr>
	    <td width="20" valign="top"><img src='<?php echo getstring("image.chat.message") ?>' width="20" height="85" border="0" alt="" /></td>
    	<td width="100%" height="100" valign="top" id="msgwndtd">
			<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
		    <td colspan="3" bgcolor="#A1A1A1"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
			</tr>
			<tr>
		    <td bgcolor="#A1A1A1"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
		    <td width="100%" height="100%" bgcolor="#FFFFFF" valign="top">
				<textarea id="msgwnd" class="message" tabindex="0"></textarea>
			</td>
		    <td bgcolor="#A1A1A1"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
			</tr>
			<tr>
		    <td colspan="3" bgcolor="#A1A1A1"><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
			</tr>
			</table>
		</td>
	    <td valign="center" id="avatarwnd"></td>
		</tr>
<?php } ?>	
		</table>

	</td>
    <td></td>
	</tr>

	<tr>
    <td height="45"></td>
    <td>
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td width="33%">
			<table cellspacing="0" cellpadding="0" border="0">
			<tr>
		    <td width="20"></td>


			</tr>
			</table>
		</td>
		<td width="33%" align="center" class="copyr"><?php echo getstring("chat.window.poweredby") ?> <a href="<?php echo getstring("site.url") ?>" title="<?php echo getstring("company.title") ?>" target="_blank"><?php echo getstring("chat.window.poweredreftext") ?></a></td>
		<td width="33%" align="right">
		
<?php if( $page['canpost'] ) { ?>
			<table cellspacing="0" cellpadding="0" border="0" id="postmessage">

			<tr>
		    <td><a href="javascript:void(0)" onclick="return false;" title="<?php echo getstring("chat.window.send_message") ?>"><img src='/webim/images/submit.gif' width="40" height="35" border="0" alt="" /></a></td>
		    <td background="/webim/images/submitbg.gif" valign="top" class="submit">
				<img src='/webim/images/free.gif' width="1" height="10" border="0" alt="" /><br>
				<a href="javascript:void(0)" onclick="return false;" title="<?php echo getstring("chat.window.send_message") ?>"><?php echo getstring("chat.window.send_message_short") ?></a><br>
			</td>
			<td width="10"><a href="javascript:void(0)" onclick="return false;" title="<?php echo getstring("chat.window.send_message") ?>"><img src='/webim/images/submitrest.gif' width="10" height="35" border="0" alt="" /></a></td>
			</tr>
			</table>
<?php } ?>
		</td>
		</tr>
		</table>
	</td>
    <td></td>
	</tr>

	<tr>
    <td width="10"><img src='/webim/images/free.gif' width="10" height="1" border="0" alt="" /></td>
    <td width="100%"><img src='/webim/images/free.gif' width="585" height="1" border="0" alt="" /></td>
    <td width="5"><img src='/webim/images/free.gif' width="5" height="1" border="0" alt="" /></td>
	</tr>
	</table>

</td>
</tr>
</table>

</body>
</html>

