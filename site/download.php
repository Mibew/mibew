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
<a href="http://prdownloads.sourceforge.net/webim/webim151.zip?download">
1.5.1, January 11, 2009</a>

<br/>
<br/>
<?php echo getlocal("downl.local") ?><br/>
<a href="http://prdownloads.sourceforge.net/webim/webim_fr151.zip?download">
French, 1.5.1</a><br/>

<a href="http://prdownloads.sourceforge.net/webim/webim_ru151.zip?download">
Russian, 1.5.1</a><br/>

<a href="http://prdownloads.sourceforge.net/webim/webim_sp151.zip?download">
Spanish, 1.5.1</a>


</div>
</div>
</div>

<?php require_once('inc/footer.i'); ?>
