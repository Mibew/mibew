<?php
/*
 * Copyright 2005-2014 the original author or authors.
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

if(isset($page) && isset($page['localeLinks'])) {
	require_once('inc_locales.php');
}
$page['title'] = getlocal("install.title");
$page['fixedwrap'] = true;

function tpl_header() { global $page, $mibewroot, $jsver;
	if($page['soundcheck']) {
?>
<script type="text/javascript" language="javascript" src="<?php echo $mibewroot ?>/js/<?php echo $jsver ?>/common.js"></script>
<script type="text/javascript" language="javascript"><!--
var wroot ="<?php echo $mibewroot ?>";
//--></script>
<script type="text/javascript" language="javascript" src="<?php echo $mibewroot ?>/js/<?php echo $jsver ?>/soundcheck.js"></script>
<?php
	}
}

function tpl_content() { global $page, $mibewroot, $errors;
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
<a href="<?php echo $mibewroot ?>/license.php"><?php echo getlocal("install.license") ?></a>


<?php
} /* content */

require_once('../view/inc_main.php');
?>