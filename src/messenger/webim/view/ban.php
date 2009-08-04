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

$page['title'] = getlocal("page_ban.title");

function tpl_content() { global $page, $webimroot, $errors;
?>

<?php if( $page['saved'] ) { ?>
	<?php echo getlocal2("page_ban.sent",array($page['address'])) ?>

	<script type="text/javascript"><!--
		setTimeout( (function() { window.close(); }), 1500 );
	//--></script>
<?php } else { ?>

<?php echo getlocal("page_ban.intro") ?>
<br/>
<br/>
<?php 
require_once('inc_errors.php');
?>


<?php if( $page['thread'] ) { ?>
	<?php echo getlocal2("page_ban.thread",array(htmlspecialchars($page['thread']))) ?><br/>
	<br/>
<?php } ?>

<form name="banForm" method="post" action="<?php echo $webimroot ?>/operator/ban.php">
<input type="hidden" name="banId" value="<?php echo $page['banId'] ?>"/>
<?php if( $page['threadid'] ) { ?>
<input type="hidden" name="threadid" value="<?php echo $page['threadid'] ?>"/>
<?php } ?>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">
	
	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.address') ?><span class="required">*</span></div>
			<div class="fvalue">
				<input type="text" name="address" size="40" value="<?php echo form_value('address') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.address.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.ban_days') ?><span class="required">*</span></div>
			<div class="fvalue">
				<input type="text" name="days" size="4" value="<?php echo form_value('days') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.ban_days.description') ?></div>
			<br clear="all"/>
		</div>

		<div class="field">
			<div class="flabel"><?php echo getlocal('form.field.ban_comment') ?></div>
			<div class="fvalue">
				<input type="text" name="comment" size="40" value="<?php echo form_value('comment') ?>" class="formauth"/>
			</div>
			<div class="fdescr"> &mdash; <?php echo getlocal('form.field.ban_comment.description') ?></div>
			<br clear="all"/>
		</div>
		
		<div class="fbutton">
			<input type="image" name="save" value="" src='<?php echo $webimroot.getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
		</div>
	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
	
	<div class="asterisk">
		<?php echo getlocal("common.asterisk_explanation") ?>
	</div>
	
</form>
<?php } ?>


<?php 
} /* content */

require_once('inc_main.php');
?>