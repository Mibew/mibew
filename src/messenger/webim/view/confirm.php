<?php
/*
 * This file is part of Mibew Messenger project.
 *
 * Copyright (c) 2005-2009 Mibew Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
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