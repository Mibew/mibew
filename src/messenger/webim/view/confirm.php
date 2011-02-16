<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
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

$page['title'] = getlocal("confirm.take.head");

function tpl_content() { global $page, $webimroot;
?>

<div id="confirmpane">
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

		<?php echo getlocal2("confirm.take.message",array($page['user'], $page['agent'])) ?><br/><br/>
		<br/>

		<div>
		<table class="nicebutton"><tr>
			<td><a href="<?php echo $page['link'] ?>">
				<img src='<?php echo $webimroot ?>/images/submit.gif' width="40" height="35" border="0" alt="" /></a></td>
			<td class="submit"><a href="<?php echo $page['link'] ?>">
				<?php echo getlocal("confirm.take.yes") ?></a></td>
			<td><a href="<?php echo $page['link'] ?>">
				<img src='<?php echo $webimroot ?>/images/submitrest.gif' width="10" height="35" border="0" alt="" /></a></td>
		</tr></table>

		<table class="nicebutton"><tr>
			<td><a href="javascript:window.close();">
				<img src='<?php echo $webimroot ?>/images/submit.gif' width="40" height="35" border="0" alt="" /></a></td>
			<td class="submit"><a href="javascript:window.close();">
				<?php echo getlocal("confirm.take.no") ?></a></td>
			<td><a href="javascript:window.close();">
				<img src='<?php echo $webimroot ?>/images/submitrest.gif' width="10" height="35" border="0" alt="" /></a></td>
		</tr></table>
		
		<br clear="all"/>
		</div>
				
	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</div>		

<?php 
} /* content */

require_once('inc_main.php');
?>