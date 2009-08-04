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

function menuloc($id) {
	global $current_locale;
	if($current_locale == $id) {
		echo " class=\"active\"";
	}
	return "";
}
function tpl_menu() { global $page, $webimroot, $errors, $current_locale;
?>
			<li>
				<h2><b><?php echo getlocal("lang.choose") ?></b></h2>
				<ul class="locales">
<?php foreach($page['localeLinks'] as $id => $title) { ?>
					<li<?php menuloc($id)?> ><a href='?locale=<?php echo $id ?>'><?php echo $title ?></a></li>
<?php } ?>
				</ul>
			</li>
<?php 
}
?>