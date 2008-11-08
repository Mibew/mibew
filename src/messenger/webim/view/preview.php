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
	<?php echo getlocal("app.title") ?>	- <?php echo getlocal("page.preview.title") ?>
</title>

<meta http-equiv="keywords" content="<?php echo getlocal("page.main_layout.meta_keyword") ?>">
<meta http-equiv="description" content="<?php echo getlocal("page.main_layout.meta_description") ?>">
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#2971C1" vlink="#2971C1" alink="#2971C1">

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td valign="top" class="text">

 <table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="left" valign="top">
		<h1><?php echo getlocal("page.preview.title") ?></h1>
 </td><td align="right" class="text" valign="top"><table cellspacing="0" cellpadding="0" border="0"><tr><td class="textform"><?php echo getlocal2("menu.operator",array($page['operator'])) ?></td><td class="textform"><img src='<?php echo $webimroot ?>/images/topdiv.gif' width="25" height="15" border="0" alt="|" /></td><td class="textform"><a href="<?php echo $webimroot ?>/operator/index.php" title="<?php echo getlocal("menu.main") ?>"><?php echo getlocal("menu.main") ?></a></td></tr></table></td></tr></table>


	<?php echo getlocal("page.preview.intro") ?>
<br />
<br />

<form name="preview" method="get" action="<?php echo $webimroot ?>/operator/preview.php">
<table cellspacing='0' cellpadding='0' border='0'><tr><td background='<?php echo $webimroot ?>/images/loginbg.gif'><table cellspacing='0' cellpadding='0' border='0'><tr><td><img src='<?php echo $webimroot ?>/images/logincrnlt.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $webimroot ?>/images/logincrnrt.gif' width='16' height='16' border='0' alt=''></td></tr><tr><td></td><td align='center'><table border='0' cellspacing='0' cellpadding='0'>
	<tr>
		<td colspan="3" class="formauth"><?php echo getlocal("page.preview.choose") ?></td>
	</tr>
	<tr><td colspan="3" height="2"></td></tr>
	<tr>
		<td colspan="3">
			<select name="preview" onchange="this.form.submit();"><?php foreach($page['availablePreviews'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("preview") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
		</td>
	</tr>
	<tr><td colspan="3" height="10"></td></tr>
	<tr>
		<td colspan="3" class="formauth"><?php echo getlocal("page.preview.choosetpl") ?></td>
	</tr>
	<tr><td colspan="3" height="2"></td></tr>
	<tr>
		<td colspan="3">
			<select name="template" onchange="this.form.submit();"><?php foreach($page['availableTemplates'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("template") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
		</td>
	</tr>
<?php if( $page['canshowerrors'] ) { ?>
	<tr><td colspan="3" height="10"></td></tr>
	<tr>
		<td colspan="3" class="formauth"><?php echo getlocal("page.preview.showerr") ?></td>
	</tr>
	<tr><td colspan="3" height="2"></td></tr>
	<tr>
		<td colspan="3">
			<input type="checkbox" name="showerr" value="on"<?php echo form_value_cb('showerr') ? " checked=\"checked\"" : "" ?> onchange="this.form.submit();"/>
		</td>
	</tr>
<?php } ?>
<?php foreach( $page['previewList'] as $pp ) { ?>
	<tr><td colspan="3" height="10"></td></tr>
	<tr>
		<td colspan="3" class="formauth"><?php echo htmlspecialchars($pp['label']) ?>
		<a href="<?php echo $page['showlink'] ?><?php echo $pp['id'] ?>" target="_blank" title="in separate window" onclick="this.newWindow = window.open('<?php echo $page['showlink'] ?><?php echo $pp['id'] ?>', '<?php echo $pp['id'] ?>', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,width=<?php echo $pp['w'] ?>,height=<?php echo $pp['h'] ?>,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;">link</a>
		</td>
	</tr>
	<tr><td colspan="3" height="7"></td></tr>
	<tr>
		<td colspan="3">
			<iframe id="sample<?php echo $pp['id'] ?>" width="<?php echo $pp['w'] ?>" height="<?php echo $pp['h'] ?>" src="<?php echo $page['showlink'] ?><?php echo $pp['id'] ?>" frameborder="1" scrolling="no">
			No iframes
			</iframe>
		</td>
	</tr>
<?php } ?>
</table></td><td></td></tr><tr><td><img src='<?php echo $webimroot ?>/images/logincrnlb.gif' width='16' height='16' border='0' alt=''></td><td></td><td><img src='<?php echo $webimroot ?>/images/logincrnrb.gif' width='16' height='16' border='0' alt=''></td></tr></table></td></tr></table>
</form>
</td>
</tr>
</table>

</body>
</html>

