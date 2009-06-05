<?php 
$page = 'demo';
$subpage = "demo";
require_once('libs/common.php');
start_html_output();
require_once('inc/header.i');
require_once('inc/menu.i');
?>

<div id="page">
	<div id="content">
		<div class="box1">
			<p><img src="images/webimlogo.gif" alt="" width="74" height="79" class="left" /><?php echo getlocal("head.intro") ?></p>
		</div>
		<div class="post">
			<h2 class="title"><?php echo getlocal("demo.title") ?></h2>
			<div class="entry">
			<p style="margin-bottom:15px;">
			<?php echo getlocal("demo.login") ?><br/>
			<a href="http://live-im.com/webim/" target="_blank"><?php echo getlocal("demo.application") ?>, 1.6.1</a>  <small style="padding-left:10px;">(username: admin, password is empty)</small>
			</p>
			
			<?php echo getlocal("demo.click") ?>
			<div style="margin: 10px 0px 15px;">
			<!-- webim button --><a href="http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>', 'webim', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src="http://live-im.com/webim/button.php?image=webim&amp;lang=<?php echo $current_locale ?>" border="0" width="163" height="61"/></a><!-- / webim button -->
			</div>
<?php /*			
			<p>
			<?php echo getlocal("demo.tryrc") ?>
			<br/>
			<a href="/webim/" target="_blank"><?php echo getlocal("demo.application") ?>, 1.6.0 RC1</a><br/> 
			<!-- webim button --><a href="/webim/client.php?locale=en" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 &amp;&amp; window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('/webim/client.php?locale=en&amp;url='+escape(document.location.href)+'&amp;referrer='+escape(document.referrer), 'webim', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;">Click to chat</a><!-- / webim button -->
			</p>
		*/
?>				
			<p>
			<?php echo getlocal("demo.styles") ?>
			
			</p>
			<table width="100%" cellpadding="5">
			<tr>
				<td>Default style</td>
				<td>Original style</td>
			</tr>
			<tr>
				<td>
					<!-- webim button --><a href="http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>&amp;style=default" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>&amp;style=default', 'webim', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src="images/style_default_tn.png" border="0"/></a><!-- / webim button -->
				</td>
				<td>
					<!-- webim button --><a href="http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>&amp;style=original" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>&amp;style=original', 'webim', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src="images/style_original_tn.png" border="0"/></a><!-- / webim button -->
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
require_once('inc/demo.i');
require_once('inc/locales.i');
?>
		</ul>
	</div>
	<!-- end sidebar -->
	<div style="clear: both;">&nbsp;</div>
</div>


<?php require_once('inc/footer.i'); ?>
