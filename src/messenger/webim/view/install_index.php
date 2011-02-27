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

if(isset($page) && isset($page['localeLinks'])) {
	require_once('inc_locales.php');
}
$page['title'] = getlocal("install.title");
$page['fixedwrap'] = true;

function tpl_header() { global $page, $webimroot, $jsver;
	if($page['soundcheck']) {
?>
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/<?php echo $jsver ?>/common.js"></script>
<script type="text/javascript" language="javascript"><!--
var wroot ="<?php echo $webimroot ?>";
//--></script>
<script type="text/javascript" language="javascript" src="<?php echo $webimroot ?>/js/<?php echo $jsver ?>/soundcheck.js"></script>
<?php
	}
}

function tpl_content() { global $page, $webimroot, $errors;
?>
<?php echo getlocal("install.message") ?>
<br/>
<br/>

<?php 
require_once('inc_errors.php');
?>

<?php if( $page['done'] ) { ?>
<div id="install">
<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">
<?php echo getlocal("install.done") ?>
<ul>
<?php foreach( $page['done'] as $info ) { ?>
<li><?php echo $info ?></li>
<?php } ?>
</ul>
<?php if( $page['nextstep'] ) { ?>
<br/><br/>
<?php echo getlocal("install.next") ?>
<ul>
<li>
<?php if( $page['nextnotice'] ) { ?><?php echo $page['nextnotice'] ?><br/><br/><?php } ?>
<a href="<?php echo $page['nextstepurl'] ?>"><?php echo $page['nextstep'] ?></a>
</li>
</ul>
<?php } ?>
</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</div>
<?php } ?>

<br/>
<a href="<?php echo $webimroot ?>/license.php"><?php echo getlocal("install.license") ?></a>


<?php 
} /* content */

require_once('../view/inc_main.php');
?>