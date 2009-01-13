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


<link rel="shortcut icon" href="<?php echo $webimroot ?>/images/favicon.ico" type="image/x-icon"/>
<title>
	<?php echo getlocal("page_ban.title") ?> - <?php echo getlocal("app.title") ?>
</title>

<meta http-equiv="keywords" content="<?php echo getlocal("page.main_layout.meta_keyword") ?>">
<meta http-equiv="description" content="<?php echo getlocal("page.main_layout.meta_description") ?>">
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#2971C1" vlink="#2971C1" alink="#2971C1">

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td valign="top" class="text">

 <table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="left" valign="top">
		<h1><?php echo getlocal("page_ban.title") ?></h1>
 </td><td align="right" class="text" valign="top"><table cellspacing="0" cellpadding="0" border="0"><tr><td class="textform"><?php echo getlocal2("menu.operator",array($page['operator'])) ?></td><td class="textform"><img src='<?php echo $webimroot ?>/images/topdiv.gif' width="25" height="15" border="0" alt="|" /></td><td class="textform"><a href="<?php echo $webimroot ?>/operator/blocked.php" title="<?php echo getlocal("menu.blocked") ?>"><?php echo getlocal("menu.blocked") ?></a></td><td class="textform"><img src='<?php echo $webimroot ?>/images/topdiv.gif' width="25" height="15" border="0" alt="|" /></td><td class="textform"><a href="<?php echo $webimroot ?>/operator/index.php" title="<?php echo getlocal("menu.main") ?>"><?php echo getlocal("menu.main") ?></a></td></tr></table></td></tr></table>



<?php if( $page['saved'] ) { ?>
	<?php echo getlocal2("page_ban.sent",array($page['address'])) ?>

	<script><!--
		setTimeout( (function() { window.close(); }), 1500 );
	//--></script>
<?php } ?>
<?php if( !$page['saved'] ) { ?>

	<?php echo getlocal("page_ban.intro") ?><br/>
	<br/>

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

	<?php if( $page['thread'] ) { ?>
		<?php echo getlocal2("page_ban.thread",array(htmlspecialchars($page['thread']))) ?><br/>
		<br/>
	<?php } ?>

	<form name="banForm" method="post" action="<?php echo $webimroot ?>/operator/ban.php">
		<table cellspacing='0' cellpadding='0' border='0'><tr><td background='<?php echo $webimroot ?>/images/loginbg.gif'><table cellspacing='0' cellpadding='0' border='0'><tr><td><img src='<?php echo $webimroot ?>/images/logincrnlt.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $webimroot ?>/images/logincrnrt.gif' width='16' height='16' border='0' alt=''></td></tr><tr><td></td><td align='center'><table border='0' cellspacing='0' cellpadding='0'>
			<tr><td class='formauth'><?php echo getlocal('form.field.address') ?><b><span style='font-size:8.0pt;color:red'>*</span></b></td><td width='10'><img width='10' height='1' border='0' alt='' src='<?php echo $webimroot ?>/images/free.gif'></td><td></td></tr><tr><td height='2' colspan='3'></td></tr><tr><td>
				<input type="text" name="address" size="40" value="<?php echo form_value('address') ?>" class="formauth"/>
			</td><td></td><td class='formauth'><span class='formdescr'> &mdash; <?php echo getlocal('form.field.address.description') ?></span></td></tr><tr><td colspan='3' height='10'></td></tr>

			<tr><td class='formauth'><?php echo getlocal('form.field.ban_days') ?><b><span style='font-size:8.0pt;color:red'>*</span></b></td><td width='10'><img width='10' height='1' border='0' alt='' src='<?php echo $webimroot ?>/images/free.gif'></td><td></td></tr><tr><td height='2' colspan='3'></td></tr><tr><td>
				<input type="text" name="days" size="4" value="<?php echo form_value('days') ?>" class="formauth"/>
			</td><td></td><td class='formauth'><span class='formdescr'> &mdash; <?php echo getlocal('form.field.ban_days.description') ?></span></td></tr><tr><td colspan='3' height='10'></td></tr>

			<tr><td class='formauth'><?php echo getlocal('form.field.ban_comment') ?></td><td width='10'><img width='10' height='1' border='0' alt='' src='<?php echo $webimroot ?>/images/free.gif'></td><td></td></tr><tr><td height='2' colspan='3'></td></tr><tr><td>
				<input type="text" name="comment" size="40" value="<?php echo form_value('comment') ?>" class="formauth"/>
			</td><td></td><td class='formauth'><span class='formdescr'> &mdash; <?php echo getlocal('form.field.ban_comment.description') ?></span></td></tr><tr><td colspan='3' height='10'></td></tr>

			<tr><td colspan='3' height='20'></td></tr><tr><td colspan='3' background='<?php echo $webimroot ?>/images/formline.gif'><img src='<?php echo $webimroot ?>/images/formline.gif' width='1' height='2' border='0' alt=''></td></tr><tr><td colspan='3' height='10'></td></tr>

			<tr>
				<td class="formauth">
				<input type="hidden" name="banId" value="<?php echo $page['banId'] ?>"/>
				<?php if( $page['threadid'] ) { ?>
					<input type="hidden" name="threadid" value="<?php echo $page['threadid'] ?>"/>
				<?php } ?>
				<input type="image" name="" src='<?php echo $webimroot.getlocal("image.button.save") ?>' border="0" alt='<?php echo getlocal("button.save") ?>'/></td>
				<td></td>
				<td></td>
			</tr>
		</table></td><td></td></tr><tr><td><img src='<?php echo $webimroot ?>/images/logincrnlb.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $webimroot ?>/images/logincrnrb.gif' width='16' height='16' border='0' alt=''></td></tr></table></td></tr></table>
	</form>
<?php } ?>
</td>
</tr>
</table>

</body>
</html>

