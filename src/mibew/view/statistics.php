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

require_once("inc_menu.php");
$page['title'] = getlocal("statistics.title");
$page['menuid'] = "statistics";

function tpl_content() { global $page, $mibewroot, $errors;
?>

<?php echo getlocal("statistics.description") ?>
<br />
<br />
<?php
require_once('inc_errors.php');
?>

<form name="statisticsForm" method="get" action="<?php echo $mibewroot ?>/operator/statistics.php">
	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">


	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal("statistics.dates") ?></div>
			<div class="fvaluenodesc">
				<div class="searchctrl">
					<?php echo getlocal("statistics.from") ?>
					<select name="startday"><?php foreach($page['availableDays'] as $k) { echo "<option value=\"" . safe_htmlspecialchars($k) . "\"".($k == form_value("startday") ? " selected=\"selected\"" : "").">" . safe_htmlspecialchars($k) . "</option>"; } ?></select>

					<select name="startmonth"><?php foreach($page['availableMonth'] as $k => $v) { echo "<option value=\"" . safe_htmlspecialchars($k) . "\"".($k == form_value("startmonth") ? " selected=\"selected\"" : "").">" . safe_htmlspecialchars($v) . "</option>"; } ?></select>
				</div>
				<div class="searchctrl">
					<?php echo getlocal("statistics.till") ?>
					<select name="endday"><?php foreach($page['availableDays'] as $k) { echo "<option value=\"" . safe_htmlspecialchars($k) . "\"".($k == form_value("endday") ? " selected=\"selected\"" : "").">" . safe_htmlspecialchars($k) . "</option>"; } ?></select>

					<select name="endmonth"><?php foreach($page['availableMonth'] as $k => $v) { echo "<option value=\"" . safe_htmlspecialchars($k) . "\"".($k == form_value("endmonth") ? " selected=\"selected\"" : "").">" . safe_htmlspecialchars($v) . "</option>"; } ?></select>
				</div>
				<div id="searchbutton">
					<input type="image" name="search" src="<?php echo $mibewroot . safe_htmlspecialchars(getlocal("image.button.search")) ?>" alt="<?php echo safe_htmlspecialchars(getlocal("button.search")) ?>"/>
				</div>
			</div>
			<br clear="all"/>
		</div>
	</div>

	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</form>

<?php if( $page['showresults'] ) { ?>

<br/>
<br/>

<div class="tabletitle">
<?php echo getlocal("report.bydate.title") ?>
</div>
<table class="statistics">
<thead>
<tr><th>
	<?php echo getlocal("report.bydate.1") ?>
</th><th>
	<?php echo getlocal("report.bydate.2") ?>
</th><th>
	<?php echo getlocal("report.bydate.3") ?>
</th><th>
	<?php echo getlocal("report.bydate.4") ?>
</th></tr>
</thead>
<tbody>
<?php if( $page['reportByDate'] ) { ?>
	<?php foreach( $page['reportByDate'] as $row ) { ?>
	<tr>
		<td><?php echo safe_htmlspecialchars($row['date']) ?></td>
		<td><?php echo safe_htmlspecialchars($row['threads']) ?></td>
		<td><?php echo safe_htmlspecialchars($row['agents']) ?></td>
		<td><?php echo safe_htmlspecialchars($row['users']) ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td><b><?php echo getlocal("report.total") ?></b></td>
		<td><?php echo safe_htmlspecialchars($page['reportByDateTotal']['threads']) ?></td>
		<td><?php echo safe_htmlspecialchars($page['reportByDateTotal']['agents']) ?></td>
		<td><?php echo safe_htmlspecialchars($page['reportByDateTotal']['users']) ?></td>
	</tr>
<?php } else { ?>
	<tr>
	<td colspan="4">
		<?php echo getlocal("report.no_items") ?>
	</td>
	</tr>
<?php } ?>
</tbody>
</table>

<br/>
<br/>

<div class="tabletitle"><?php echo getlocal("report.byoperator.title") ?></div>
<table class="statistics">
<thead>
<tr><th>
	<?php echo getlocal("report.byoperator.1") ?>
</th><th>
	<?php echo getlocal("report.byoperator.2") ?>
</th><th>
	<?php echo getlocal("report.byoperator.3") ?>
</th><th>
	<?php echo getlocal("report.byoperator.4") ?>
</th></tr>
</thead>
<tbody>
<?php if( $page['reportByAgent'] ) { ?>
	<?php foreach( $page['reportByAgent'] as $row ) { ?>
	<tr>
		<td><?php echo topage(safe_htmlspecialchars($row['name'])) ?></td>
		<td><?php echo safe_htmlspecialchars($row['threads']) ?></td>
		<td><?php echo safe_htmlspecialchars($row['msgs']) ?></td>
		<td><?php echo safe_htmlspecialchars($row['avglen']) ?></td>
	</tr>
	<?php } ?>
<?php } else { ?>
	<tr>
	<td colspan="4">
		<?php echo getlocal("report.no_items") ?>
	</td>
	</tr>
<?php } ?>
</tbody>
</table>

<?php } ?>

<?php
} /* content */

require_once('inc_main.php');
?>