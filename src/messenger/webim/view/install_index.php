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

if(isset($page) && isset($page['localeLinks'])) {
	require_once('inc_locales.php');
}
$page['title'] = getlocal("install.title");
$page['fixedwrap'] = true;

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
<a href="<?php echo $webimroot ?>/epl-v10.html" target="_blank"><?php echo getlocal("install.license") ?></a>


<?php 
} /* content */

require_once('../view/inc_main.php');
?>