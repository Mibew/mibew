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

$page['title'] = getlocal("install.err.title");
$page['no_right_menu'] = true;
$page['fixedwrap'] = true;

function tpl_content() { global $page, $mibewroot, $errors;
?>

<?php
require_once('inc_errors.php');
?>
<?php echo getlocal("install.err.back") ?>

<?php
} /* content */

require_once('../view/inc_main.php');
?>