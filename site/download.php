<?php 
$page = 'downl';
require_once('libs/common.php');
start_html_output();
require_once('inc/header.i');
require_once('inc/menu.i');
?>

<div id="container">
<div id="content">
<h3><?php echo getlocal("downl.title") ?></h3>

<div id="downl">

<?php echo getlocal("downl.release") ?><br/>
<a href="http://prdownloads.sourceforge.net/webim/webim150.zip?download">
1.5.0, December 10, 2008
</a>

<br/>
<br/>
<?php echo getlocal("downl.prev") ?><br/>
<a href="http://prdownloads.sourceforge.net/webim/webim142.zip?download">
1.4.2, October 6, 2008
</a>


</div>
</div>
</div>

<?php require_once('inc/footer.i'); ?>
