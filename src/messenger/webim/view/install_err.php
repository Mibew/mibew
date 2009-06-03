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

$page['title'] = getlocal("install.err.title");
$page['no_right_menu'] = true;
$page['fixedwrap'] = true;

function tpl_content() { global $page, $webimroot, $errors;
?>

<?php 
require_once('inc_errors.php');
?>
<?php echo getlocal("install.err.back") ?>

<?php 
} /* content */

require_once('../view/inc_main.php');
?>