<?php 
$page = 'home';
require_once('libs/common.php');
start_html_output();
require_once('inc/header.i');
require_once('inc/menu.i');
?>

<div id="container">
<div id="content">
<h3><?php echo getlocal("index.whatis.title") ?></h3>
<p><?php echo getlocal("index.whatis") ?></p>

<h3><?php echo getlocal("index.why.title") ?></h3>
<p><?php echo getlocal("index.why") ?></p>

<h3><?php echo getlocal("index.how.title") ?></h3>
<p><?php echo getlocal("index.how") ?></p>

<h3><?php echo getlocal("index.license.title") ?></h3>
<p><?php echo getlocal("index.license") ?></p>

<p><?php echo getlocal("index.hosted") ?></p>

</div>
</div>

<?php require_once('inc/footer.i'); ?>
