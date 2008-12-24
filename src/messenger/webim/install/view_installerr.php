<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Web Messenger Community
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
	<?php echo getlocal("install.err.title") ?> - <?php echo getlocal("app.title") ?>
</title>

<meta http-equiv="keywords" content="<?php echo getlocal("page.main_layout.meta_keyword") ?>">
<meta http-equiv="description" content="<?php echo getlocal("page.main_layout.meta_description") ?>">
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#2971C1" vlink="#2971C1" alink="#2971C1">

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td valign="top" class="text">

		<h1><?php echo getlocal("install.err.title") ?></h1>


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

<br/>
<?php echo getlocal("install.err.back") ?>

<table width="200" cellspacing="0" cellpadding="0" border="0">
<tr>
  <td height="20"></td>
</tr>
<tr>
  <td bgcolor="#D6D6D6"><img src='<?php echo $webimroot ?>/images/free.gif' height="1" width="1" border="0" alt=""></td>
</tr>
<tr>
  <td height="7"></td>
</tr>
</table>

&laquo;<span style="color:#bb5500;">Open</span> Web Messenger&raquo; <?php echo $page['version'] ?> &bull; <?php echo $page['localeLinks'] ?> &bull; <a href="<?php echo $webimroot ?>/epl-v10.html" target="_blank"><?php echo getlocal("install.license") ?></a>

</td>
</tr>
</table>

</body>
</html>

