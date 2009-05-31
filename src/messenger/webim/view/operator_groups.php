<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2009 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once("inc_menu.php");
$page['title'] = getlocal("operator.groups.title");
$page['menuid'] = $page['opid'] == $page['currentopid'] ? "profile" : "operators";

function tpl_content() { global $page, $webimroot, $errors;
?>

<?php echo getlocal("operator.groups.intro") ?>
<br />
<br />
<?php 
require_once('inc_errors.php');
?>
<?php if( $page['stored'] ) { ?>
<div id="formmessage"><?php echo getlocal("data.saved") ?></div>
<?php } ?>

<form name="opgroupsForm" method="post" action="<?php echo $webimroot ?>/operator/opgroups.php">
<input type="hidden" name="op" value="<?php echo $page['opid'] ?>"/>
	<div>
<?php if($page['tabs']) { ?>
	<ul class="tabs">

<?php foreach($page['tabs'] as $k => $v) { if($v) { ?>
	<li><a href="<?php echo $v ?>"><?php echo $k ?></a></li>
<?php } else { ?>
	<li class="active"><a href="#"><?php echo $k ?></a></li><?php }} ?>
	</ul>
<?php } ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<p>
		<b><?php echo $page['currentop'] ?>&lrm;</b>
	</p>
<?php foreach( $page['groups'] as $pm ) { ?>
	<div class="field">
		<div class="flabel"><?php echo htmlspecialchars(topage($pm['vclocalname'])) ?></div>
		<div class="fvalue">
			<input type="checkbox" name="group<?php echo $pm['groupid'] ?>" value="on"<?php echo form_value_mb('group',$pm['groupid']) ? " checked=\"checked\"" : "" ?><?php echo $page['canmodify'] ? "" : " disabled=\"disabled\"" ?>/>
		</div>
		<div class="fdescr"> &mdash; <?php echo $pm['vclocaldescription'] ? htmlspecialchars(topage($pm['vclocaldescription'])) : getlocal("operator.group.no_description") ?></div>
		<br clear="all"/>
	</div>
<?php } ?>

<?php if($page['canmodify']) { ?>
	<div class="fbutton">
		<input type="image" name="save" value="" src='<?php echo $webimroot.getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
	</div>
<?php } ?>
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>		
</form>

<?php 
} /* content */

require_once('inc_main.php');
?>