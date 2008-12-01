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



<link rel="stylesheet" type="text/css" media="all" href="<?php echo $siteroot ?>/admin/styles.css" />


<link rel="shortcut icon" href="<?php echo $siteroot ?>/admin/images/favicon.ico" type="image/x-icon"/>
<title>
	Translate
</title>
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#2971C1" vlink="#2971C1" alink="#2971C1">

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td valign="top" class="text">

	<h1>Translate</h1>

	<?php if( $page['saved'] ) { ?>
	Your translation is saved.

	<script><!--
		setTimeout( (function() { window.close(); }), 500 );
	//--></script>
<?php } ?>
<?php if( !$page['saved'] ) { ?>

Enter you translation.
<br/>
<br/>

<?php if( isset($errors) && count($errors) > 0 ) { ?>
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
	    <td valign="top"><img src='<?php echo $siteroot ?>/admin/images/icon_err.gif' width="40" height="40" border="0" alt="" /></td>
	    <td width="10"></td>
	    <td class="text">
		    <?php	if( isset($errors) && count($errors) > 0 ) {
		print '<font color="#c13030"><b>Correct the mistakes:</b><br/><ul>';
		foreach( $errors as $e ) {
			print '<li class="error">';
			print $e;
			print '</li>';
		}
		print '</ul></font>';
	} ?>

		</td>
		</tr>
		</table>
	<?php } ?>

<form name="translateForm" method="post" action="<?php echo $siteroot ?>/admin/translate.php">
<table cellspacing='0' cellpadding='0' border='0'><tr><td background='<?php echo $siteroot ?>/admin/images/loginbg.gif'><table cellspacing='0' cellpadding='0' border='0'><tr><td><img src='<?php echo $siteroot ?>/admin/images/logincrnlt.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $siteroot ?>/admin/images/logincrnrt.gif' width='16' height='16' border='0' alt=''></td></tr><tr><td></td><td align='center'><table border='0' cellspacing='0' cellpadding='0'>
	<tr>
		<td colspan="3" class="formauth"><?php echo $page['title1'] ?></td>
	</tr>
	<tr><td colspan="3" height="2"></td></tr>
	<tr>
		<td colspan="3">
			<textarea name="original" tabindex="0" cols="80" rows="5" style="border:1px solid #878787; overflow:auto"><?php echo $page['formoriginal'] ?></textarea>
		</td>
	</tr>
	<tr><td colspan="3" height="5"></td></tr>

	<tr>
		<td colspan="3" class="formauth"><?php echo $page['title2'] ?></td>
	</tr>
	<tr><td colspan="3" height="2"></td></tr>
	<tr>
		<td colspan="3">
			<textarea name="translation" tabindex="0" cols="80" rows="5" style="border:1px solid #878787; overflow:auto"><?php echo $page['formtranslation'] ?></textarea>
		</td>
	</tr>
	<tr><td colspan="3" height="5"></td></tr>

	<tr><td colspan='3' height='20'></td></tr><tr><td colspan='3' background='<?php echo $siteroot ?>/admin/images/formline.gif'><img src='<?php echo $siteroot ?>/admin/images/formline.gif' width='1' height='2' border='0' alt=''></td></tr><tr><td colspan='3' height='10'></td></tr>

	<tr>
		<td class="formauth">
		<input type="hidden" name="key" value="<?php echo $page['key'] ?>"/>
		<input type="hidden" name="target" value="<?php echo $page['target'] ?>"/>
		<input type="image" name="" src='<?php echo $siteroot ?>/admin/images/save.gif' border="0" alt='Save'/></td>
		<td></td>
		<td></td>
	</tr>
</table></td><td></td></tr><tr><td><img src='<?php echo $siteroot ?>/admin/images/logincrnlb.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $siteroot ?>/admin/images/logincrnrb.gif' width='16' height='16' border='0' alt=''></td></tr></table></td></tr></table>
</form>
<?php } ?>

</td>
</tr>
</table>

</body>
</html>

