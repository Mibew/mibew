<?php 
$page = 'downl';
require_once('libs/common.php');
start_html_output();
$title = getlocal("downl.title");
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
				<a href="https://sourceforge.net/projects/webim/files/Mibew%20Messenger/1.6.3/webim163.zip/download">
				Mibew Messenger 1.6.3, October 14, 2009</a>
				<small style="padding-left:1cm;">(requires <a href="http://php.net/">PHP</a> and <a href="http://mysql.com/">MySQL</a>)</small>
			</p>

			<p id="tableh">
				<?php echo getlocal("downl.local") ?>
			</p>
			<table cellpadding="0" cellspacing="0" border="0" id="downl">
			<tr>
				<td><a href="https://sourceforge.net/projects/webim/files/Mibew%20Messenger/1.6.3/webim163_ar.zip/download">Arabic</a></td>
				<td>1.6.3</td>
				<td>October 14, 2009</td></tr>

			<tr>
				<td><a href="https://sourceforge.net/projects/webim/files/Mibew%20Messenger/1.6.3/webim163_de.zip/download">Deutsch</a></td>
				<td>1.6.3</td>
				<td>October 14, 2009</td></tr>
			
			<tr>
				<td><a href="https://sourceforge.net/projects/webim/files/Mibew%20Messenger/1.6.3/webim163_fr.zip/download">French</a></td>
				<td>1.6.3</td>
				<td>October 14, 2009</td></tr>

			<tr>
				<td><a href="https://sourceforge.net/projects/webim/files/Mibew%20Messenger/1.6.3/webim163_he.zip/download">Hebrew</a></td>
				<td>1.6.3</td>
				<td>October 14, 2009</td></tr>

			<tr>
				<td><a href="https://sourceforge.net/projects/webim/files/Mibew%20Messenger/1.6.3/webim163_hr.zip/download">Hrvatski</a></td>
				<td>1.6.3</td>
				<td>October 14, 2009</td></tr>

			<tr>
				<td><a href="https://sourceforge.net/projects/webim/files/Mibew%20Messenger/1.6.3/webim163_it.zip/download">Italiano</a></td>
				<td>1.6.3</td>
				<td>October 14, 2009</td></tr>

			<tr>
				<td><a href="https://sourceforge.net/projects/webim/files/Mibew%20Messenger/1.6.3/webim163_pl.zip/download">Polski</a></td>
				<td>1.6.3</td>
				<td>October 14, 2009</td></tr>

			<tr>
				<td><a href="https://sourceforge.net/projects/webim/files/Mibew%20Messenger/1.6.3/webim163_pt-br.zip/download">Português Brasil</a></td>
				<td>1.6.3</td>
				<td>October 14, 2009</td></tr>
                <tr>
				
			<tr>
				<td><a href="https://sourceforge.net/projects/webim/files/Mibew%20Messenger/1.6.3/webim163_ru.zip/download">Russian</a></td>
				<td>1.6.3</td>
				<td>October 14, 2009</td></tr>

			<tr>
				<td><a href="https://sourceforge.net/projects/webim/files/Mibew%20Messenger/1.6.3/webim163_sp.zip/download">Spanish</a></td>
				<td>1.6.3</td>
				<td>October 14, 2009</td></tr>

			<tr>
				<td><a href="https://sourceforge.net/projects/webim/files/Mibew%20Messenger/1.6.3/webim163_zh-cn.zip/download">Simplified Chinese</a></td>
				<td>1.6.3</td>
				<td>October 14, 2009</td></tr>
				
			<tr>
				<td><a href="https://sourceforge.net/projects/webim/files/Mibew%20Messenger/1.6.3/webim163_zh-tw.zip/download">Traditional Chinese</a></td>
				<td>1.6.3</td>
				<td>October 14, 2009</td></tr>

			<tr>
				<td><a href="https://sourceforge.net/projects/webim/files/Mibew%20Messenger/1.6.3/webim163_ua.zip/download">Ukrainian</a></td>
				<td>1.6.3</td>
				<td>October 14, 2009</td></tr>
				
			</table>

			<p id="tablecomment">
			<?php echo getlocal("lang.missing") ?> <a href="/forums/index.php?board=7.0"><?php echo getlocal("lang.link") ?></a>
			</p>
			
			<p>
				<?php echo getlocal("downl.tray") ?><br/>
				<a href="http://prdownloads.sourceforge.net/webim/MibewTray11.msi?download">
				Mibew Tray 1.1.0 beta, May 28, 2009</a>
				<small style="padding-left:1cm;">(requires <a href="http://go.microsoft.com/fwlink/?LinkId=9832">.NET Framework 3.5</a> and <a href="http://www.microsoft.com/downloads/details.aspx?displaylang=en&FamilyID=889482fc-5f56-4a38-b838-de776fd4138c">Windows Installer 3.1</a>)</small>
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
