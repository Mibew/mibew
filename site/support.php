<?php 
$page = 'supp';
require_once('libs/common.php');
start_html_output();
require_once('inc/header.i');
require_once('inc/menu.i');
?>

<div id="container">
<div id="content">
<h3><?php echo getlocal("support.title") ?></h3>
<p>
<div id="downl">
<a href="http://openwebim.org/tutorials.php">
<?php echo getlocal("support.tutorials") ?>
</a>
<p></p>
<a href="http://openwebim.org/screenshots.php">
<?php echo getlocal("support.ScreenShots") ?>
</a>
<p></p>
<a href="http://openwebim.org/faq.php">
<?php echo ("FAQ") ?>
</a>
</div>

<?php require_once('inc/footer.i'); ?>
