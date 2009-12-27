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
<p class="featuretitle"><?php echo getlocal("features.price.title") ?></p>
<ul>
<?php foreach(preg_split("/\n/", getlocal("features.price")) as $val) {
echo "<li>$val</li>\n"; } ?>
</ul>

<a name='main'></a>
<p class="featuretitle"><?php echo getlocal("features.main.title") ?></p>
<ul>
<?php foreach(preg_split("/\n/", getlocal("features.main")) as $val) {
echo "<li>$val</li>\n"; } ?>
</ul>

<a name='button'></a>
<p class="featuretitle"><?php echo getlocal("features.button.title") ?></p>
<ul>
<?php foreach(preg_split("/\n/", getlocal("features.button")) as $val) {
echo "<li>$val</li>\n"; } ?>
</ul>

<a name='chat'></a>
<p class="featuretitle"><?php echo getlocal("features.chat.title") ?></p>
<ul>
<?php foreach(preg_split("/\n/", getlocal("features.chat")) as $val) {
echo "<li>$val</li>\n"; } ?>
</ul>

<a name='operator'></a>
<p class="featuretitle"><?php echo getlocal("features.operator.title") ?></p>
<ul>
<?php foreach(preg_split("/\n/", getlocal("features.operator")) as $val) {
echo "<li>$val</li>\n"; } ?>
</ul>

<a name='groups'></a>
<p class="featuretitle"><?php echo getlocal("features.groups.title") ?></p>
<ul>
<?php foreach(preg_split("/\n/", getlocal("features.groups")) as $val) {
echo "<li>$val</li>\n"; } ?>
</ul>

<a name='admin'></a>
<p class="featuretitle"><?php echo getlocal("features.admin.title") ?></p>
<ul>
<?php foreach(preg_split("/\n/", getlocal("features.admin")) as $val) {
echo "<li>$val</li>\n"; } ?>
</ul>

<a name='requirements'></a>
<p class="featuretitle"><?php echo getlocal("features.requirements.title") ?></p>
<ul>
<?php foreach(preg_split("/\n/", getlocal("features.requirements")) as $val) {
echo "<li>$val</li>\n"; } ?>
</ul>

<a name='browsers'></a>
<p class="featuretitle"><?php echo getlocal("features.browsers.title") ?></p>
<ul>
<?php foreach(preg_split("/\n/", getlocal("features.browsers")) as $val) {
echo "<li>$val</li>\n"; } ?>
</ul>



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
					<li><a href="#button"><?php echo getlocal("features.button.title") ?></a></li>
					<li><a href="#chat"><?php echo getlocal("features.chat.title") ?></a></li>
					<li><a href="#operator"><?php echo getlocal("features.operator.title") ?></a></li>
					<li><a href="#groups"><?php echo getlocal("features.groups.title") ?></a></li>
					<li><a href="#admin"><?php echo getlocal("features.admin.title") ?></a></li>
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
