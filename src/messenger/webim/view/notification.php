<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2010 Mibew Messenger Community
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

$page['title'] = getlocal("notification.title");

function tpl_content() { global $page, $webimroot, $errors;
$notification = $page['notification'];
?>

<?php echo getlocal("notification.intro") ?>

<br/><br/>

<div class="logpane">
<div class="header">

		<div class="wlabel">
			<?php echo getlocal("notification.label.to") ?>:
		</div> 
		<div class="wvalue">
			<?php echo topage(htmlspecialchars($notification['vcto'])) ?>
		</div>
		<br clear="all"/>

		<div class="wlabel">
			<?php echo getlocal("notification.label.time") ?>:
		</div>
		<div class="wvalue">
			<?php echo date_to_text($notification['created']) ?>
		</div>
		<br clear="all"/>
		
		<div class="wlabel">
			<?php echo getlocal("notification.label.subj") ?>:
		</div>
		<div class="wvalue">
			<?php echo topage(htmlspecialchars($notification['vcsubject'])) ?>
		</div>
		<br clear="all"/>
</div>

<div class="message">
<?php echo topage(prepare_html_message(htmlspecialchars($notification['tmessage']))) ?>
</div>
</div>

<br />
<a href="<?php echo $webimroot ?>/operator/notifications.php">
	<?php echo getlocal("notification.back_to_list") ?></a>
<br />


<?php 
} /* content */

require_once('inc_main.php');
?>