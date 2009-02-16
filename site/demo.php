<?php 
$page = 'demo';
require_once('libs/common.php');
start_html_output();
require_once('inc/header.i');
require_once('inc/menu.i');
?>

<div id="container">
<div id="content">
<h3><?php echo getlocal("demo.title") ?></h3>
<p>

<?php echo getlocal("demo.click") ?><br/>
<br/>
<!-- webim button --><a href="http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>', 'webim', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src="http://live-im.com/webim/button.php?image=webim&lang=<?php echo $current_locale ?>" border="0" width="163" height="61"/></a><!-- / webim button -->
<br/>
<br/>

<?php echo getlocal("demo.login") ?><br/><br/>

<a href="http://live-im.com/webim/"><?php echo getlocal("demo.application") ?>, 1.5.2</a>
<br/>
<br/>
<br/>
<?php echo getlocal("demo.styles") ?>

</p>
</div>

<div id="styles">
<div id="style1">
<p>Default style</p>
<p>
<!-- webim button --><a href="http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>&style=default" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>&style=default', 'webim', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src="images/style_default_tn.png" border="0"/></a><!-- / webim button -->
</p>
</div>
<div id="style2">
<p>Original style</p>
<p>
<!-- webim button --><a href="http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>&style=original" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('http://live-im.com/webim/client.php?locale=<?php echo $current_locale ?>&style=original', 'webim', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=640,height=480,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src="images/style_original_tn.png" border="0"/></a><!-- / webim button -->
</p>
</div>
</div>

</div>


<?php require_once('inc/footer.i'); ?>
