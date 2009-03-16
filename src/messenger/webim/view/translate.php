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

$page['title'] = getlocal("page.translate.title");

function tpl_content() { global $page, $webimroot, $errors;
?>

	<?php if( $page['saved'] ) { ?>
	<?php echo getlocal("page.translate.done") ?>

	<script><!--
		setTimeout( (function() { window.close(); }), 500 );
	//--></script>
<?php } ?>
<?php if( !$page['saved'] ) { ?>

<?php echo getlocal("page.translate.one") ?>
<br/>
<br/>
<?php 
require_once('inc_errors.php');
?>

<form name="translateForm" method="post" action="<?php echo $webimroot ?>/operator/translate.php">
<input type="hidden" name="key" value="<?php echo $page['key'] ?>"/>
<input type="hidden" name="target" value="<?php echo $page['target'] ?>"/>
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo $page['title1'] ?></div>
			<div class="fvaluenodesc">
				<textarea name="original" disabled="disabled" cols="20" rows="5" class="wide"><?php echo $page['formoriginal'] ?></textarea>
			</div>
		</div>

		<div class="field">
			<div class="flabel"><?php echo $page['title2'] ?></div>
			<div class="fvaluenodesc">
				<textarea name="translation" cols="20" rows="5" class="wide"><?php echo $page['formtranslation'] ?></textarea>
			</div>
		</div>
	
		<div class="fbutton">
			<input type="image" name="save" value="" src='<?php echo $webimroot.getlocal("image.button.save") ?>' alt='<?php echo getlocal("button.save") ?>'/>
		</div>
	</div>
	
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</form>

<?php } ?>

<?php 
} /* content */

require_once('inc_main.php');
?>