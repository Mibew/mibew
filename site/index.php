<?php 
$page = 'home';
$subpage = 'news';
require_once('libs/common.php');
start_html_output();
require_once('inc/header.i');
require_once('inc/menu.i');
?>

<div id="page">
	<!-- start content -->
	<div id="content">
		<div class="box1">
			<p><img src="images/webimlogo.gif" alt="" width="74" height="79" class="left" /><?php echo getlocal("head.intro") ?></p>
		</div>
		<div class="post">
			<h2 class="title"><?php echo getlocal("index.how.title") ?></h2>
			<div class="entry">
				<p><?php echo getlocal("index.how.text") ?></p>
				<p><?php echo getlocal("index.license") ?></p>

			</div>
			<div class="nometa"></div>
		</div>
        <div class="post">
			<h2 class="title"><?php echo getlocal("index.nextpost.title") ?></h2>
			<div class="entry">
				<?php echo getlocal("index.nextpost.text") ?>
			</div>
			<div class="meta">
				<p class="byline"><?php echo getlocal("index.nextpost.when") ?></p>
				<p class="links"><?php echo getlocal("index.nextpost.link") ?></p>
			</div>
		</div>
		<div class="post">
			<h2 class="title"><?php echo getlocal("index.post.title") ?></h2>
			<div class="entry">
				<?php echo getlocal("index.post.text") ?>
			</div>
			<div class="meta">
				<p class="byline"><?php echo getlocal("index.post.when") ?></p>
				<p class="links"><?php echo getlocal("index.post.link") ?></p>
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
			<li>
				<h2><?php echo getlocal("sidebar.quicknav") ?></h2>
				<ul>
					<li><a href="features.php"><?php echo getlocal("menu.features") ?></a></li><p></p>
                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="4710959">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

				</ul>
			</li>
		</ul>
	</div>
	<!-- end sidebar -->
	<div style="clear: both;">&nbsp;</div>
</div>

<?php require_once('inc/footer.i'); ?>