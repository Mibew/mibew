<?php 
$page = 'demo';
$subpage = "features";
require_once('libs/common.php');
start_html_output();
$title = getlocal("features.title");
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
			<h2 class="title"><?php echo getlocal("features.title") ?></h2>
			<div class="entry">
<a name='price'></a>
<p><?php echo getlocal("features.price.title") ?></p>
<?php echo getlocal("features.price") ?>

<a name='main'></a>
<p><?php echo getlocal("features.main.title") ?></p>
<?php echo getlocal("features.main") ?>

<a name='chat'></a>
<p><?php echo getlocal("features.chat.title") ?></p>
<?php echo getlocal("features.chat") ?>

<a name='operator'></a>
<p><?php echo getlocal("features.operator.title") ?></p>
<?php echo getlocal("features.operator") ?>

<a name='next'></a>
<p><?php echo getlocal("features.next.title") ?></p>
<?php echo getlocal("features.next") ?>

<a name='requirements'></a>
<p><?php echo getlocal("features.requirements.title") ?></p>
<?php echo getlocal("features.requirements") ?>

<a name='browsers'></a>
<p><?php echo getlocal("features.browsers.title") ?></p>
<?php echo getlocal("features.browsers") ?>



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
			<li>
				<h2><?php echo getlocal("features.content.head") ?></h2>
				<ul>
					<li><a href="#price"><?php echo getlocal("features.price.title") ?></a></li>
					<li><a href="#main"><?php echo getlocal("features.main.title") ?></a></li>
					<li><a href="#chat"><?php echo getlocal("features.chat.title") ?></a></li>
					<li><a href="#operator"><?php echo getlocal("features.operator.title") ?></a></li>
					<li><a href="#next"><?php echo getlocal("features.next.title") ?></a></li>
					<li><a href="#requirements"><?php echo getlocal("features.requirements.title") ?></a></li>
					<li><a href="#browsers"><?php echo getlocal("features.browsers.title") ?></a></li>
				</ul>
			</li>
		</ul>
	</div>
	<!-- end sidebar -->
	<div style="clear: both;">&nbsp;</div>
</div>

<?php require_once('inc/footer.i'); ?>
