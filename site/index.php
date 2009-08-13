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
<?php /*
    		<div class="post">
			<h2 class="title"><?php echo getlocal("index.nextpost.title") ?></h2>
			<div class="entry">
				<?php echo getlocal("index.nextpost.text") ?>
			</div>
			<div class="meta">
				<p class="byline"><?php echo getlocal("index.nextpost.when") ?></p>
				<p class="links"><?php echo getlocal("index.nextpost.link") ?></p>
			</div>
		</div> */ ?>
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
?>
			<li>
				<h2><?php echo getlocal("sidebar.quicknav") ?></h2>
				<ul>
					<li><a href="features.php"><?php echo getlocal("menu.features") ?></a></li><p></p>
				</ul>
			</li>
<?php
require_once('inc/locales.i');
?>
		</ul>
	</div>
	<!-- end sidebar -->
	<div style="clear: both;">&nbsp;</div>
</div>

<?php require_once('inc/footer.i'); ?>