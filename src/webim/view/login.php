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
<html>
<head>



<link rel="stylesheet" type="text/css" media="all" href="<?php echo $webimroot ?>/styles.css" />


<link rel="shortcut icon" href="<?php echo $webimroot ?>/images/favicon.ico" type="image/x-icon"/>
<title>
	<?php echo getstring("app.title") ?>	- <?php echo getstring("page_login.title") ?>
</title>

<meta http-equiv="keywords" content="<?php echo getstring("page.main_layout.meta_keyword") ?>">
<meta http-equiv="description" content="<?php echo getstring("page.main_layout.meta_description") ?>">
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#2971C1" vlink="#2971C1" alink="#2971C1">

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td valign="top" class="text">
	
		<h1><?php echo getstring("page_login.title") ?></h1>
	

	


<?php if( isset($errors) && count($errors) > 0 ) { ?>
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td valign="top"><img src='<?php echo $webimroot ?>/images/icon_err.gif' width="40" height="40" border="0" alt="" /></td>
	    <td width="10"></td>
	    <td class="text">
		    <?php	if( isset($errors) && count($errors) > 0 ) {
		print getstring("errors.header");
		foreach( $errors as $e ) {
			print getstring("errors.prefix");
			print $e;
			print getstring("errors.suffix");
		}
		print getstring("errors.footer");
	} ?>

		</td>
		</tr>
		</table>
	<?php } ?>

<form name="loginForm" method="post" action="<?php echo $webimroot ?>/operator/login.php">
	<table cellspacing='0' cellpadding='0' border='0'><tr><td background='<?php echo $webimroot ?>/images/loginbg.gif'><table cellspacing='0' cellpadding='0' border='0'><tr><td><img src='<?php echo $webimroot ?>/images/logincrnlt.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $webimroot ?>/images/logincrnrt.gif' width='16' height='16' border='0' alt=''></td></tr><tr><td></td><td align='center'><table border='0' cellspacing='0' cellpadding='0'>
		<tr>
			<td colspan="2">
			<table cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="formauth"><?php echo getstring("page_login.login") ?></td>
					<td width="20"></td>
					<td><input type="text" name="login" size="20" value="<?php echo form_value('login') ?>" class="formauth"/></td>
				</tr>

				<tr>
					<td colspan="3" height="10"></td>
				</tr>

				<tr>
					<td class="formauth"><?php echo getstring("page_login.password") ?></td>
					<td></td>
					<td><input type="password" name="password" size="20" value="" class="formauth"/></td>
				</tr>

				<tr>
					<td colspan="3" height="5"></td>
				</tr>

				<tr>
					<td class="formauth"></td>
					<td></td>
					<td>
					<table cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td><input type="checkbox" name="isRemember" value="on"<?php echo form_value_cb('isRemember') ? " checked=\"checked\"" : "" ?> /></td>
							<td width="5"></td>
							<td class="formauth" nowrap><span><?php echo getstring("page_login.remember") ?></span></td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
			</td>
		</tr>

		<tr><td colspan='2' height='20'></td></tr><tr><td colspan='2' background='<?php echo $webimroot ?>/images/formline.gif'><img src='<?php echo $webimroot ?>/images/formline.gif' width='1' height='2' border='0' alt=''></td></tr><tr><td colspan='2' height='10'></td></tr>

		<tr>
			<td><input type="hidden" name="backPath" value="<?php echo $page['backPath'] ?>"/> <input type="image" name="" src='<?php echo $webimroot.getstring("image.button.login") ?>' border="0" alt='<?php echo getstring("button.enter") ?>'/>
			</td>
			
		</tr>
	</table></td><td></td></tr><tr><td><img src='<?php echo $webimroot ?>/images/logincrnlb.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $webimroot ?>/images/logincrnrb.gif' width='16' height='16' border='0' alt=''></td></tr></table></td></tr></table>
</form>


</td>
</tr>
</table>

</body>
</html>

