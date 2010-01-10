<?php
$page = 'home';
$subpage = 'copyright';
require_once('libs/common.php');
start_html_output();
$title = getlocal("copyright.title");
require_once('inc/header.i');
require_once('inc/menu.i');
?>

<div id="page">
	<div id="content">
		<div class="box1">
			<p><img src="images/webimlogo.gif" alt="" width="74" height="79" class="left" /><?php echo getlocal("head.intro") ?></p>
		</div>
		<div class="post">
			<h2 class="title"><?php echo getlocal("copyright.title") ?></h2>
			<div class="entry">
			
			<p>
			<?php echo getlocal("copyright.text") ?>
			</p>  
			<ul class="decimal">
			<li><?php echo getlocal("copyright.item1") ?></li>
			<li><?php echo getlocal("copyright.item2") ?></li>
			<li><?php echo getlocal("copyright.item3") ?></li>
			<li><?php echo getlocal("copyright.item4") ?></li>
			</ul>
			
			<p style="padding-top:.5em;margin-bottom:1em;">
			<?php echo getlocal("copyright.contributors") ?>
			</p>
			
			<p>
			<?php echo getlocal("copyright.others") ?>
			</p>
			
			</div>
		</div>
	</div>
	<!-- end content -->
	<!-- start sidebar -->
	<div id="sidebar">
		<ul>
<?php
require_once('inc/main.i');
require_once('inc/locales.i');
?>
		</ul>
	</div>
	<!-- end sidebar -->
	<div style="clear: both;">&nbsp;</div>
</div>


<?php require_once('inc/footer.i'); ?>
