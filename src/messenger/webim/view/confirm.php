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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>



<link rel="stylesheet" type="text/css" media="all" href="/webim/styles.css" />





<link rel="shortcut icon" href="/webim/images/favicon.ico" type="image/x-icon"/>
<title>
	<?php echo getlocal("app.title") ?>	- <?php echo getlocal("page_agent.title") ?>
</title>

<meta http-equiv="keywords" content="<?php echo getlocal("page.main_layout.meta_keyword") ?>">
<meta http-equiv="description" content="<?php echo getlocal("page.main_layout.meta_description") ?>">




</head>

<body bgcolor="#FFFFFF" text="#000000" link="#2971C1" vlink="#2971C1" alink="#2971C1">

<table width="100%" height="80%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td valign="center" align="center" class="text">





<table cellspacing="0" cellpadding="0" border="0">
<tr>
<td class="window" valign="bottom">
	<h1><b><?php echo getlocal("confirm.take.head") ?></b></h1>
</td>
</tr>
<tr>
<td height="20">
</td>
</tr>
<tr>
<td valign="top">

 <table cellspacing='0' cellpadding='0' border='0'><tr><td background='/webim/images/loginbg.gif'><table cellspacing='0' cellpadding='0' border='0'><tr><td><img src='/webim/images/logincrnlt.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='/webim/images/logincrnrt.gif' width='16' height='16' border='0' alt=''></td></tr><tr><td></td><td align='center'><table border='0' cellspacing='0' cellpadding='0'>
	<tr>
    <td><img src='/webim/images/free.gif' width="1" height="1" border="0" alt="" /></td>
	<td>

		<?php echo getlocal2("confirm.take.message",array($page['user'], $page['agent'])) ?><br/><br/>
		<br/>
		<table width="100%" cellspacing="0" border="0">
		<tr>
		<td align="center">
			<table cellspacing="0" cellpadding="0" border="0" id="postmessage"><tr>
             <td><a href="<?php echo $page['link'] ?>"><img src='/webim/images/submit.gif' width="40" height="35" border="0" alt="" /></a></td>
             <td background="/webim/images/submitbg.gif" valign="top" class="submit">
             <img src='/webim/images/free.gif' width="1" height="10" border="0" alt="" /><br>
             <a href="<?php echo $page['link'] ?>"><?php echo getlocal("confirm.take.yes") ?></a><br>
             </td>
             <td width="10"><a href="<?php echo $page['link'] ?>"><img src='/webim/images/submitrest.gif' width="10" height="35" border="0" alt="" /></a></td>
             </tr>
            </table>
		</td>
		<td align="center">
			<table cellspacing="0" cellpadding="0" border="0" id="postmessage"><tr>
             <td><a href="javascript:window.close();"><img src='/webim/images/submit.gif' width="40" height="35" border="0" alt="" /></a></td>
             <td background="/webim/images/submitbg.gif" valign="top" class="submit">
             <img src='/webim/images/free.gif' width="1" height="10" border="0" alt="" /><br>
             <a href="javascript:window.close();"><?php echo getlocal("confirm.take.no") ?></a><br>
             </td>
             <td width="10"><a href="javascript:window.close();"><img src='/webim/images/submitrest.gif' width="10" height="35" border="0" alt="" /></a></td>
             </tr>
            </table>

		</td>
		</tr>
		</table>
	</td>
	</tr>
</table></td><td></td></tr><tr><td><img src='/webim/images/logincrnlb.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='/webim/images/logincrnrb.gif' width='16' height='16' border='0' alt=''></td></tr></table></td></tr></table>

</td>
</tr>
</table>

</td>
</tr>
</table>

</body>
</html>

