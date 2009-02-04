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
	<?php echo getlocal("updates.title") ?> - <?php echo getlocal("app.title") ?>
</title>

<meta http-equiv="keywords" content="<?php echo getlocal("page.main_layout.meta_keyword") ?>">
<meta http-equiv="description" content="<?php echo getlocal("page.main_layout.meta_description") ?>">
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#2971C1" vlink="#2971C1" alink="#2971C1">

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td valign="top" class="text">

 <table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="left" valign="top">
		<h1><?php echo getlocal("updates.title") ?></h1>
 </td><td align="right" class="text" valign="top"><table cellspacing="0" cellpadding="0" border="0"><tr><td class="textform"><?php echo getlocal2("menu.operator",array($page['operator'])) ?></td><td class="textform"><img src='<?php echo $webimroot ?>/images/topdiv.gif' width="25" height="15" border="0" alt="|" /></td><td class="textform"><a href="<?php echo $webimroot ?>/operator/index.php" title="<?php echo getlocal("menu.main") ?>"><?php echo getlocal("menu.main") ?></a></td></tr></table></td></tr></table>


	<?php echo getlocal("updates.intro") ?>
<br />
<br />

<table cellspacing='0' cellpadding='0' border='0'>
<?php if($page['tabs']) { ?>
<tr><td align="right" style="padding-right:16px;"><table cellspacing="0" cellpadding="0" border="0"><tr><?php foreach($page['tabs'] as $k => $v) { if($v) { ?><td class="textform" style="padding: 2px 9px 3px 9px;"><a href="<?php echo $v ?>"><?php echo $k ?></a></td><?php } else { ?><td class="textform" background="<?php echo $webimroot ?>/images/loginbg.gif" style="border-left:1px solid #bbbbbb;border-top:1px solid #bbbbbb;border-right:1px solid #bbbbbb;padding: 2px 9px 3px 9px;"><?php echo $k ?></td><?php }} ?></tr></table></td></tr>
<?php } ?>
<tr><td background='<?php echo $webimroot ?>/images/loginbg.gif'><table cellspacing='0' cellpadding='0' border='0'><tr><td><img src='<?php echo $webimroot ?>/images/logincrnlt.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $webimroot ?>/images/logincrnrt.gif' width='16' height='16' border='0' alt=''></td></tr><tr><td></td><td align='center'><table border='0' cellspacing='0' cellpadding='0' width="700">
	<tr>
		<td colspan="3" class="formauth">You are using:</td>
	</tr>
	<tr><td colspan="3" height="2"></td></tr>
	<tr>
		<td colspan="3" class="formauth" style="font-size:80%;">
			&laquo;<span style="color:#bb5500;">Open</span> Web Messenger&raquo; <?php echo $page['version'] ?>
		</td>
	</tr>
	<tr><td colspan="3" height="8"></td></tr>
	<tr>
		<td colspan="3" class="formauth">Installed localizations:</td>
	</tr>
	<tr><td colspan="3" height="2"></td></tr>
	<tr>
		<td colspan="3" class="formauth" style="font-size:80%;">
			<?php foreach( $page['localizations'] as $loc ) { ?>
				<?php echo $loc ?>
			<?php } ?>
		</td>
	</tr>
	<tr><td colspan="3" height="8"></td></tr>
	<tr>
		<td colspan="3" class="formauth">Environment:</td>
	</tr>
	<tr><td colspan="3" height="2"></td></tr>
	<tr>
		<td colspan="3" class="formauth" style="font-size:80%;">
			PHP <?php echo $page['phpVersion'] ?>
		</td>
	</tr>
</table></td><td></td></tr><tr><td><img src='<?php echo $webimroot ?>/images/logincrnlb.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $webimroot ?>/images/logincrnrb.gif' width='16' height='16' border='0' alt=''></td></tr></table></td></tr></table>

</td>
</tr>
</table>

</body>
</html>

