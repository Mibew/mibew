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
<?php echo getlocal("downl.local") ?>
<table cellpadding="0" cellspacing="2" border="0">
<tr><td style="padding-right:2cm;">
<a href="http://prdownloads.sourceforge.net/webim/webim_fr151.zip?download">
French</a></td>
<td style="padding-right:1cm;">1.5.1</td>
<td>11 Jan 2009</td></tr>

<tr><td>
<a href="http://prdownloads.sourceforge.net/webim/webim_ru151.zip?download">
Russian</a></td><td>1.5.1</td><td>11 Jan 2009</td></tr>

<tr><td>
<a href="http://prdownloads.sourceforge.net/webim/webim_sp151.zip?download">
Spanish</a></td><td>1.5.1.1</td><td>13 Jan 2009</td></tr>

<tr><td style="padding-right:1cm;">
<a href="http://prdownloads.sourceforge.net/webim/webim_zh-tw151.zip?download">
Traditional Chinese</a></td><td>1.5.1</td><td>15 Jan 2009</td></tr>
</table>


</div>
</div>
</div>

<?php require_once('inc/footer.i'); ?>
