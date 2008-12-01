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
<a href="http://prdownloads.sourceforge.net/webim/webim142.zip?download">
1.4.2, October 6, 2008
</a>

<br/>
<br/>
<?php echo getlocal("downl.nightly") ?><br/>
<a href="http://openwebim.org/download/webim150b1.zip">
1.5.0 beta 1, November 28, 2008
</a>


</div>
</div>
</div>

<?php require_once('inc/footer.i'); ?>
