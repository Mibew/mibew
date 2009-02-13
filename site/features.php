<?php 
$page = 'feat';
require_once('libs/common.php');
start_html_output();
require_once('inc/header.i');
require_once('inc/menu.i');
?>

<div id="container">
<div id="content">
<a name='price'></a>
<h3><?php echo getlocal("features.price.title") ?></h3>
<?php echo getlocal("features.price") ?>

<a name='main'></a>
<h3><?php echo getlocal("features.main.title") ?></h3>
<?php echo getlocal("features.main") ?>


<a name='chat'></a>
<h3><?php echo getlocal("features.chat.title") ?></h3>
<?php echo getlocal("features.chat") ?>

<a name='operator'></a>
<h3><?php echo getlocal("features.operator.title") ?></h3>
<?php echo getlocal("features.operator") ?>

<a name='next'></a>
<h3><?php echo getlocal("features.next.title") ?></h3>
<?php echo getlocal("features.next") ?>

<a name='requirements'></a>
<h3><?php echo getlocal("features.requirements.title") ?></h3>
<?php echo getlocal("features.requirements") ?>

<a name='browsers'></a>
<h3><?php echo getlocal("features.browsers.title") ?></h3>
<?php echo getlocal("features.browsers") ?>

</div>

<div id="side">
<p><?php echo getlocal("features.content.head") ?></p>
<p>
<a href="#price"><?php echo getlocal("features.price.title") ?></a><br/>
<a href="#main"><?php echo getlocal("features.main.title") ?></a><br/>
<a href="#chat"><?php echo getlocal("features.chat.title") ?></a><br/>
<a href="#operator"><?php echo getlocal("features.operator.title") ?></a><br/>
<a href="#next"><?php echo getlocal("features.next.title") ?></a><br/>
<a href="#requirements"><?php echo getlocal("features.requirements.title") ?></a><br/>
<a href="#browsers"><?php echo getlocal("features.browsers.title") ?></a><br/>
</p>
</div>
<?php /*
<div id="side2" style="margin-top:20px;">
<p>Feature voting:</p>
<?php
if(isset($_POST['answer'])) {
?>	
<p>
Thank you for your vote.
</p>
<?php
} else {
?>
<form action="features.php" name="vote" method="post" style="margin:5px 0 10px;padding: 0 10px;">
<label><input type="radio" name="answer" value="ssl" style="margin-right:5px;"/>SSL Encryption<br/></label>
<label><input type="radio" name="answer" value="dep" style="margin-right:5px;"/>Departments<br/></label>
<label><input type="radio" name="answer" value="track" style="margin-right:5px;"/>Visitor tracking<br/></label>
<label><input type="radio" name="answer" value="invite" style="margin-right:5px;"/>Proactive Chat Invitations<br/></label>
<label><input type="radio" name="answer" value="pushpage" style="margin-right:5px;"/>Push Pages Capability<br/></label>
<label><input type="radio" name="answer" value="prechat" style="margin-right:5px;"/>Pre-Chat Questionnaire<br/></label>
</form>
<p align="right">
<a href="javascript:document.vote.submit();">Vote</a>
</p>
<?php
}
?>
</div>
*/  ?>
</div>

<?php require_once('inc/footer.i'); ?>
