<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2009 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
 * 
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once("inc_menu.php");
$page['title'] = getlocal("page_avatar.title");
$page['menuid'] = $page['opid'] == $page['currentopid'] ? "profile" : "operators";

function tpl_content() { global $page, $webimroot, $errors;
?>

<?php echo getlocal("page_avatar.intro") ?>
<br />
<br />
<?php 
require_once('inc_errors.php');
?>

<form name="avatarForm" method="post" action="<?php echo $webimroot ?>/operator/avatar.php" enctype="multipart/form-data">
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

	<div class="fieldForm">

<?php if( $page['avatar'] ) { ?>
		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.avatar.current') ?></div>
			<div class="fvalue">
				<img src="<?php echo $page['avatar'] ?>" alt="cannot load avatar"/><br/>
<?php if($page['canmodify']) { ?>
                <a class="formauth" href='<?php echo $webimroot ?>/operator/avatar.php?op=<?php echo $page['opid'] ?>&amp;delete=true'>
                    <?php echo getlocal("page_agent.clear_avatar") ?>
                </a>
<?php } ?>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.avatar.current.description') ?></div>
			<br clear="all"/>
		</div>
<?php } else if(!$page['canmodify']) { ?>
		<div class="field">
			<div class="fvaluenodesc">
				No avatar
			</div>
		</div>
<?php } ?>

<?php if($page['canmodify']) { ?>
		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.avatar.upload') ?><span class="required">*</span></div>
			<div class="fvalue">
				<input type="file" name="avatarFile" size="40" value="<?php echo form_value('avatarFile') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.avatar.upload.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="fbutton">
			<input type="image" name="save" value="" src='<?php echo $webimroot.getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
		</div>
<?php } ?>
	</div>

	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	</div>		

	<div class="asterisk">
		<?php echo getlocal("common.asterisk_explanation") ?>
	</div>
</form>

<?php 
} /* content */

require_once('inc_main.php');
?>