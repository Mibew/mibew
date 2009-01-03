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
<a href="tutorials.php">
<?php echo getlocal("support.tutorials") ?>
</a>
<p></p>
<a href="screenshots.php">
<?php echo getlocal("support.ScreenShots") ?>
</a>
</div>

<?php require_once('inc/footer.i'); ?>
