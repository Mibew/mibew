<?php 
$page = 'home';
$subpage = 'credits';
require_once('libs/common.php');
start_html_output();
$title = getlocal("credits.title");
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
			<li>Arabic - <a href="/forums/index.php?action=profile;u=1366">Mostafa Khattab</a> and Active4web.net Developers</li>
			<li>Deutsch - <a href="/forums/index.php?action=profile;u=49">Gregor</a></li>
			<li>French - Hominn, Bard of LLYDAW</li>
			<li>Hebrew - <a href="http://mediacms.net/" rel="nofollow">MediaCMS Team</a></li>
			<li>Hrvatski - <a href="/forums/index.php?action=profile;u=1266">Gorana Rabar</a></li>
			<li>Italiano - <a href="/forums/index.php?action=profile;u=781">CT32</a></li>
			<li>Polski - <a href="/forums/index.php?action=profile;u=170">Kacper Wierzbicki</a>, <a href="http://mibew.org/forums/index.php?action=profile;u=170" > WebTower</a></li>
			<li>Português Brasil - <a href="/forums/index.php?action=profile;u=1304">Leandro Luquetti</a></li>
			<li>Russian - <a href="/forums/index.php?action=profile;u=2">Evgeny Gryaznov</a></li>
			<li>Spanish - Christian Mauricio Castillo Estrada</li>
			<li>Traditional Chinese - Dawei</li>
			<li>Ukrainian - <a href="/forums/index.php?action=profile;u=1206">azzepis</a></li>
			</ul>
			
			<p><?php echo "Tech Support Team" ?></p>
			<ul>
			<li>(eddybaur) Ed Kraus - Admininstrator</li>
			</ul>
			</div>
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
