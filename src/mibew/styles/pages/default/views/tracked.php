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

require_once(dirname(__FILE__).'/inc_main.php');
?>