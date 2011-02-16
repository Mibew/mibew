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

$page['title'] = getlocal("license.title");
$page['no_right_menu'] = true;
$page['fixedwrap'] = true;

function tpl_content() { global $page, $webimroot, $errors;
?>

<p>Mibew Messenger is distributed under the terms of the Eclipse Public License (or
the General Public License, this means that you can choose one of two, and use it
accordingly) with the following special exception.</p>

<br/>

<b>License exception:</b>
<p>No one may remove, alter or hide any copyright notices or links to the community
site ("http://mibew.org") contained within the Program. Any derivative work
must include this license exception.</p>

<br/>

<p>Eclipse Public License:<br/>
<a href="<?php echo $webimroot ?>/epl-v10.html">Local version</a> or <a href="http://www.eclipse.org/legal/epl-v10.html">http://www.eclipse.org/legal/epl-v10.html</a>
</p>

<br/>

<p>
General Public License:<br/>
<a href="<?php echo $webimroot ?>/gpl-2.0.txt">Local version</a> or <a href="http://www.gnu.org/copyleft/gpl.html">http://www.gnu.org/copyleft/gpl.html</a>
</p>

<?php 
} /* content */

require_once('inc_main.php');
?>