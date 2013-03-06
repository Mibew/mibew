<?php
/*
 * Copyright 2005-2013 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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