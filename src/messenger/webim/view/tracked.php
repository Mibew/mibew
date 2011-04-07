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
 *    Fedor Fetisov - tracking and inviting implementation
 */

$page['title'] = getlocal("tracked.path");

function tpl_content() { global $page;
?>

<?php echo getlocal("tracked.intro") ?>

<br/><br/>

<div class="logpane">


<div class="header">

		<div class="wlabel">
			<?php echo getlocal("tracked.visitor.came.from") ?>:
		</div> 
		<div class="wvalue">
<?php if ($page['entry']) { ?>
			<a href="<?php echo $page['entry'] ?>"><?php echo $page['entry'] ?></a>
<?php } else { ?>
			<?php echo getlocal("tracked.empty.referrer") ?>
<?php } ?>
		</div>
		<br clear="all"/>
</div>

<div class="message">

<table class="list">
<thead>
<tr class="header">
<th>
	<?php echo getlocal("tracked.date") ?>
</th><th>
	<?php echo getlocal("tracked.link") ?>
</th>
</tr>
</thead>
<tbody>
<?php
if(count($page['history']) > 0) { 
	foreach( $page['history'] as $step ) {
?>
<tr>
	<td class="notlast">
<?php echo $step['date']; ?>
	</td>
	<td>
<a href="<?php echo $step['link']; ?>"><?php echo $step['link']; ?></a>
	</td>
</tr>
<?php
	}
}
?>
</tbody>
</table>

</div>
</div>


<?php
} /* content */

require_once('inc_main.php');
?>