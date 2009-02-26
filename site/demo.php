<?php 
$page = 'demo';
require_once('libs/common.php');
start_html_output();
require_once('inc/header.i');
require_once('inc/menu.i');
?>

<div id="page">
	<div id="content">
		<div class="box1">
			<p><img src="images/img04_3.gif" alt="" width="74" height="79" class="left" /><?php echo getlocal("head.intro") ?></p>
		</div>
		<div class="post">
			<h2 class="title"><?php echo getlocal("demo.title") ?></h2>
			<div class="entry">
			<p>
			
			<?php echo getlocal("demo.click") ?><br/>
			<br/>
			<!-- webim button --><a href="http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>', 'webim', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src="http://live-im.com/webim/button.php?image=webim&lang=<?php echo $current_locale ?>" border="0" width="163" height="61"/></a><!-- / webim button -->
			<br/>
			
			<?php echo getlocal("demo.login") ?><br/><br/>
			
			<a href="http://live-im.com/webim/"><?php echo getlocal("demo.application") ?>, 1.5.2</a>
			<br/>
			<br/>
			<?php echo getlocal("demo.styles") ?>
			
			</p>
			<table width="100%" cellpadding="5">
			<tr>
				<td>Default style</td>
				<td>Original stlye</td>
			</tr>
			<tr>
				<td>
					<!-- webim button --><a href="http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>&style=default" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>&style=default', 'webim', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src="images/style_default_tn.png" border="0"/></a><!-- / webim button -->
				</td>
				<td>
					<!-- webim button --><a href="http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>&style=original" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>&style=original', 'webim', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src="images/style_original_tn.png" border="0"/></a><!-- / webim button -->
				</td>
			</tr>
			</table>
			</div>
			<div class="nometa"></div>
		</div>
	</div>
	<!-- end content -->
	<!-- start sidebar -->
	<div id="sidebar">
		<ul>
<?php
require_once('inc/locales.i');
?>
		</ul>
	</div>
	<!-- end sidebar -->
	<div style="clear: both;">&nbsp;</div>
</div>


<?php require_once('inc/footer.i'); ?>
