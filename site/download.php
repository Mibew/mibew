<?php 
$page = 'downl';
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
			<h2 class="title"><?php echo getlocal("downl.title") ?></h2>
			<div class="entry">
			<p>
				<?php echo getlocal("downl.release") ?><br/>
				<a href="http://prdownloads.sourceforge.net/webim/webim160.zip?download">
				1.6.0, April 24, 2009</a>
			</p>
<?php /*
			<p>
				<?php echo getlocal("downl.unstable.2") ?><br/>
				<a href="http://prdownloads.sourceforge.net/webim/webim160rc1.zip?download">
				1.6.0 RC1, April 10, 2009</a>
			</p>
*/?>
			<p id="tableh">
				<?php echo getlocal("downl.local") ?>
			</p>
			<table cellpadding="0" cellspacing="0" border="0" id="downl">
			<tr>
				<td><a href="http://prdownloads.sourceforge.net/webim/webim160_de.zip?download">Deutsch</a></td>
				<td>1.6.0</td>
				<td>24 Apr 2009</td></tr>
			
			<tr>
				<td><a href="http://prdownloads.sourceforge.net/webim/webim160_fr.zip?download">French</a></td>
				<td>1.6.0</td>
				<td>24 Apr 2009</td></tr>

			<tr>
				<td><a href="http://prdownloads.sourceforge.net/webim/webim160_he.zip?download">Hebrew</a></td>
				<td>1.6.0</td>
				<td>24 Apr 2009</td></tr>

			<tr>
				<td><a href="http://prdownloads.sourceforge.net/webim/webim160_it.zip?download">Italiano</a></td>
				<td>1.6.0</td>
				<td>24 Apr 2009</td></tr>

			<tr>
				<td><a href="http://prdownloads.sourceforge.net/webim/webim160_pl.zip?download">Polski</a></td>
				<td>1.6.0</td>
				<td>24 Apr 2009</td></tr>

			<tr>
				<td><a href="http://prdownloads.sourceforge.net/webim/webim160_pt-br.zip?download">Português Brasil</a></td>
				<td>1.6.0</td>
				<td>24 Apr 2009</td></tr>

			<tr>
				<td><a href="http://prdownloads.sourceforge.net/webim/webim160_ru.zip?download">Russian</a></td>
				<td>1.6.0</td>
				<td>24 Apr 2009</td></tr>
			
			<tr><td>
				<a href="http://prdownloads.sourceforge.net/webim/webim160_sp.zip?download">Spanish</a></td>
				<td>1.6.0</td>
				<td>24 Apr 2009</td></tr>
			
			<tr>
				<td><a href="http://prdownloads.sourceforge.net/webim/webim160_zh-tw.zip?download">Traditional Chinese</a></td>
				<td>1.6.0</td>
				<td>24 Apr 2009</td></tr>
				
			</table>

			<p id="tablecomment">
			<?php echo getlocal("lang.missing") ?> <a href="/forums/index.php?board=7.0"><?php echo getlocal("lang.link") ?></a>
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
require_once('inc/locales.i');
?>
		</ul>
	</div>
	<!-- end sidebar -->
	<div style="clear: both;">&nbsp;</div>
</div>

<?php require_once('inc/footer.i'); ?>
