<?php 
$page = 'home';
$subpage = 'credits';
require_once('libs/common.php');
start_html_output();
require_once('inc/header.i');
require_once('inc/menu.i');
?>

<div id="page">
	<div id="content">
		<div class="box1">
			<p><img src="images/webimlogo.gif" alt="" width="74" height="79" class="left" /><?php echo getlocal("head.intro") ?></p>
		</div>
		<div class="post">
			<h2 class="title"><?php echo getlocal("credits.title") ?></h2>
			<div class="entry">
			<p>
			<?php echo getlocal("credits.content") ?>
			</p>
			
			<p><?php echo getlocal("credits.translators") ?></p>
			<ul>
			<li>Deutsch - Gregor</li>
			<li>French - Hominn, Bard of LLYDAW</li>
			<li>Polski - Kacper Wierzbicki, WebTower</li>
			<li>Russian - Evgeny Gryaznov</li>
			<li>Spanish - Christian Mauricio Castillo Estrada</li>
			<li>Traditional Chinese - Dawei</li>
			</ul>
			
			<p><?php echo "Tech Support Team" ?></p>
			<ul>
			<li>(eddybaur)Ed Kraus - Admininstrator</li>
			<li>(lamies) Mario Alejandro Llerena Vasquez - Operator, Spanish Translator</li>
			</div>
			<div class="nometa"></div>
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
		</ul>
	</div>
	<!-- end sidebar -->
	<div style="clear: both;">&nbsp;</div>
</div>


<?php require_once('inc/footer.i'); ?>
