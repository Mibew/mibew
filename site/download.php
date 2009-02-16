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
<a href="http://prdownloads.sourceforge.net/webim/webim152.zip?download">
1.5.2, February 16, 2009</a>

<br/>
<br/>
<?php echo getlocal("downl.local") ?>
<table cellpadding="0" cellspacing="2" border="0">

<tr><td>
<a href="http://prdownloads.sourceforge.net/webim/webim_de152.zip?download">
Deutsch</a></td><td>1.5.2</td><td>16 Feb 2009</td></tr>

<tr><td style="padding-right:2cm;">
<a href="http://prdownloads.sourceforge.net/webim/webim_fr152.zip?download">
French</a></td>
<td style="padding-right:1cm;">1.5.2</td><td>16 Feb 2009</td></tr>

<tr><td>
<a href="http://prdownloads.sourceforge.net/webim/webim_ru152.zip?download">
Russian</a></td><td>1.5.2</td><td>16 Feb 2009</td></tr>

<tr><td>
<a href="http://prdownloads.sourceforge.net/webim/webim_sp152.zip?download">
Spanish</a></td><td>1.5.2</td><td>16 Feb 2009</td></tr>

<tr><td style="padding-right:1cm;">
<a href="http://prdownloads.sourceforge.net/webim/webim_zh-tw152.zip?download">
Traditional Chinese</a></td><td>1.5.2</td><td>16 Feb 2009</td></tr>
</table>
<br/>

<div><?php echo getlocal("lang.missing") ?><a href="/forums/index.php?board=7.0"><?php echo getlocal("lang.link") ?></a></div>

</div>
</div>
</div>

<?php require_once('inc/footer.i'); ?>
