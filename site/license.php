<?php 
$page = 'home';
$subpage = 'terms';
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
			<h2 class="title"><?php echo getlocal("license.title") ?></h2>
			<div class="entry">
			
			<p>Mibew Messenger is distributed under the terms of the Eclipse Public License (or
			the General Public License, this means that you can choose one of two, and use it
			accordingly) with the following special exception.</p>
			
			<b>License exception:</b>
			<p>No one may remove, alter or hide any copyright notices or links to the community
			site ("http://openwebim.org") contained within the Program. Any derivative work
			must include this license exception.</p>
			
			<p>Eclipse Public License:<br/>
			<a href="http://www.eclipse.org/legal/epl-v10.html">http://www.eclipse.org/legal/epl-v10.html</a>
			</p>
			
			<p>
			General Public License:<br/>
			<a href="http://www.gnu.org/copyleft/gpl.html">http://www.gnu.org/copyleft/gpl.html</a>
			</p>
			
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
