<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="<?php echo $webimroot ?>/images/favicon.ico" type="image/x-icon"/>
<title>
	<?php echo $page['title'] ?> - <?php echo getlocal("app.title") ?>
</title>
<link href="<?php echo $webimroot ?>/default.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><script language="JavaScript" type="text/javascript" src="<?php echo $webimroot ?>/js/ie.js"></script><![endif]-->
</head>
<body>
<div id="wrap">
	<div id="header">
		<div id="title">
			<h1><img src="<?php echo $webimroot ?>/images/logo.gif" alt="" width="32" height="32" class="left" />
				<a href="#"><?php echo $page['title'] ?></a></h1>
		</div>
<?php if(isset($page) && isset($page['operator'])) { ?>
		<div id="path"><p><?php echo getlocal2("menu.operator",array($page['operator'])) ?></p></div>
<?php } ?>
	</div>
	
	<br clear="all"/>
	
	<div class="contentdiv">
	<div class="contentinner">
<?php
	tpl_content();
?>	
	</div>
	</div>

<?php if(!isset($page['no_right_menu'])) { ?>	
	<div id="sidebar">
		<ul>
<?php if(function_exists('rightmenu_content')) { rightmenu_content(); } ?>
<?php if(isset($page['right_menu'])) { echo $page['right_menu']; } ?>
		</ul>
	</div>
<?php } ?>
	<div style="clear: both;">&nbsp;</div>
	
   	<div class="empty_inner" style="">&#160;</div>
</div>
<div id="footer">
	<p id="legal"><a href="http://openwebim.org/" class="flink">Open Web Messenger</a> 1.5.2 | (c) 2009 openwebim.org</p>
</div>
</body>
</html>