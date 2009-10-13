<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
 * 
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

$isrtl = getlocal("localedirection") == 'rtl';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"<?php if($isrtl) { ?> dir="rtl"<?php } ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="<?php echo $webimroot ?>/images/favicon.ico" type="image/x-icon"/>
<?php
	if(function_exists('tpl_header'))
		tpl_header(); 
?>
<title>
	<?php echo $page['title'] ?> - <?php echo getlocal("app.title") ?>
</title>
<link href="<?php echo $webimroot ?>/default.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><script language="JavaScript" type="text/javascript" src="<?php echo $webimroot ?>/<?php echo jspath() ?>/ie.js"></script><![endif]-->
</head>
<body<?php if(!function_exists('tpl_menu')) { ?> style="min-width: 400px;"<?php } ?>>
<div id="<?php echo (isset($page) && isset($page['fixedwrap'])) ? "fixedwrap" : (function_exists('tpl_menu') ? "wrap700" : "wrap400" )?>" class="l<?php echo getlocal("localedirection") ?>">
	<div id="header">
		<div id="title">
			<h1><img src="<?php echo $webimroot ?>/images/logo.gif" alt="" width="32" height="32" class="left" />
				<a href="#"><?php echo isset($page['headertitle']) ? $page['headertitle'] : $page['title'] ?></a></h1>
		</div>
<?php if(isset($page) && isset($page['operator'])) { ?>
		<div id="path"><p><?php echo getlocal2("menu.operator",array($page['operator'])) ?></p></div>
<?php } else if(isset($page) && isset($page['show_small_login'])) { ?>
		<div id="loginsmallpane">
			<form name="smallLogin" method="post" action="<?php echo $webimroot ?>/operator/login.php">
				<?php echo getlocal("page_login.login") ?>
				<input type="text" name="login" size="8" class="formauth"/>
				<input type="password" name="password" size="8" class="formauth"/>
				<input type="hidden" name="isRemember" value=""/>
				<input type="submit" value="&gt;&gt;" class="butt"/>
			</form>
		
		</div>
<?php } ?>
	</div>
	
	<br clear="all"/>
	
	<div class="contentdiv">
<?php if(function_exists('tpl_menu')) { ?>
	<div id="wcontent" class="contentinner">
<?php } else { ?>
	<div id="wcontent" class="contentnomenu">
<?php } ?>
<?php
	tpl_content();
?>	
	</div>
	</div>

<?php if(function_exists('tpl_menu')) { ?>	
	<div id="sidebar">
		<ul>
<?php 
	tpl_menu();
?>
		</ul>
	</div>
<?php } ?>
	<div style="clear: both;">&nbsp;</div>
	
   	<div class="empty_inner" style="">&#160;</div>
</div>
<div id="footer">
	<p id="legal"><a href="http://mibew.org/" class="flink">Mibew Messenger</a> <?php echo $version ?> | (c) 2009 mibew.org</p>
</div>
</body>
</html>