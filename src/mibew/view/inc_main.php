<?php
/*
 * Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

$isrtl = getlocal("localedirection") == 'rtl';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"<?php if($isrtl) { ?> dir="rtl"<?php } ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="<?php echo $mibewroot ?>/images/favicon.ico" type="image/x-icon"/>
<?php
	if(function_exists('tpl_header'))
		tpl_header();
?>
<title>
	<?php echo safe_htmlspecialchars($page['title']) ?> - <?php echo getlocal("app.title") ?>
</title>
<link href="<?php echo $mibewroot ?>/default.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><script language="JavaScript" type="text/javascript" src="<?php echo $mibewroot ?>/<?php echo jspath() ?>/ie.js"></script><![endif]-->
</head>
<body<?php if(!function_exists('tpl_menu')) { ?> style="min-width: 400px;"<?php } ?>>
<div id="<?php echo (isset($page) && isset($page['fixedwrap'])) ? "fixedwrap" : (function_exists('tpl_menu') ? "wrap700" : "wrap400" )?>" class="l<?php echo getlocal("localedirection") ?>">
	<div id="header">
		<div id="title">
			<h1><img src="<?php echo $mibewroot ?>/images/logo.png" alt="" width="32" height="32" class="left logo" />
				<a href="#"><?php echo isset($page['headertitle']) ? safe_htmlspecialchars($page['headertitle']) : safe_htmlspecialchars($page['title']) ?></a></h1>
		</div>
<?php if(isset($page) && isset($page['operator'])) { ?>
		<div id="path"><p><?php echo getlocal2("menu.operator",array(safe_htmlspecialchars($page['operator']))) ?></p></div>
<?php } else if(isset($page) && isset($page['show_small_login']) && $page['show_small_login']) { ?>
		<div id="loginsmallpane">
			<form name="smallLogin" method="post" action="<?php echo $mibewroot ?>/operator/login.php">
				<?php echo getlocal("page_login.login") ?>
				<input type="text" name="login" size="8" class="formauth"/>
				<input type="password" name="password" size="8" class="formauth" autocomplete="off"/>
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
	<p id="legal"><a href="http://mibew.org/" target="_blank" class="flink">Mibew Messenger</a> <?php echo safe_htmlspecialchars($version) ?> | (c) 2011-2014 mibew.org</p>
</div>
</body>
</html>