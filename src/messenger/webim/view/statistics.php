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

require_once("inc_tabbar.php");
require_once("inc_menu.php");

$page['title'] = getlocal("statistics.title");
$page['menuid'] = "statistics";

function tpl_content() { global $page, $webimroot, $errors;
?>

<?php echo getlocal2("statistics.description.full", array(date_to_text($page['last_cron_run']), $page['cron_path'])) ?>
<br />
<br />
<?php 
require_once('inc_errors.php');
?>

<form name="statisticsForm" method="get" action="<?php echo $webimroot ?>/operator/statistics.php">
<input type="hidden" name="type" value="<?php echo $page['type'] ?>" />

	<?php print_tabbar(); ?>

	<div class="mform"><div class="formtop"><div class="formtopi"></div></div><div class="forminner">

	<div class="fieldForm">
		<div class="field">
			<div class="flabel"><?php echo getlocal("statistics.dates") ?></div>
			<div class="fvaluenodesc">
				<div class="searchctrl">
					<?php echo getlocal("statistics.from") ?>
					<select name="startday"><?php foreach($page['availableDays'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("startday") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
			
					<select name="startmonth"><?php foreach($page['availableMonth'] as $k => $v) { echo "<option value=\"".$k."\"".($k == form_value("startmonth") ? " selected=\"selected\"" : "").">".$v."</option>"; } ?></select>
				</div>
				<div class="searchctrl">
					<?php echo getlocal("statistics.till") ?>
					<select name="endday"><?php foreach($page['availableDays'] as $k) { echo "<option value=\"".$k."\"".($k == form_value("endday") ? " selected=\"selected\"" : "").">".$k."</option>"; } ?></select>
			
					<select name="endmonth"><?php foreach($page['availableMonth'] as $k => $v) { echo "<option value=\"".$k."\"".($k == form_value("endmonth") ? " selected=\"selected\"" : "").">".$v."</option>"; } ?></select>
				</div>
				<div id="searchbutton">
					<input type="image" name="search" src='<?php echo $webimroot.getlocal("image.button.search") ?>' alt='<?php echo getlocal("button.search") ?>'/>
				</div>
			</div>
			<br clear="all"/>
		</div>
	</div>

	</div><div class="formbottom"><div class="formbottomi"></div></div></div>
</form>

<?php if( $page['showresults'] ) { ?>

<?php if($page['showbydate']) { ?>
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
	<?php echo getlocal("report.bydate.7") ?>
</th><th>
	<?php echo getlocal("report.bydate.3") ?>
</th><th>
	<?php echo getlocal("report.bydate.4") ?>
</th><th>
	<?php echo getlocal("report.bydate.5") ?>
</th><th>
	<?php echo getlocal("report.bydate.6") ?>
</th>
<?php if ($page['show_invitations_info']) { ?>
<th>
	<?php echo getlocal("report.bydate.8") ?>
</th><th>
	<?php echo getlocal("report.bydate.9") ?>
</th><th>
	<?php echo getlocal("report.bydate.10") ?>
</th><th>
	<?php echo getlocal("report.bydate.11") ?>
</th>
<?php } ?>
</tr>
</thead>
<tbody>
<?php if( $page['reportByDate'] ) { ?>
	<?php foreach( $page['reportByDate'] as $row ) { ?>
	<tr>
		<td><?php echo $row['date'] ?></td>
		<td><?php echo $row['threads'] ?></td>
		<td><?php echo $row['missedthreads'] ?></td>
		<td><?php echo $row['agents'] ?></td>
		<td><?php echo $row['users'] ?></td>
		<td><?php echo $row['avgwaitingtime'] ?></td>
		<td><?php echo $row['avgchattime'] ?></td>
		<?php if ($page['show_invitations_info']) { ?>
		<td><?php echo $row['sentinvitations'] ?></td>
		<td><?php echo $row['acceptedinvitations'] ?></td>
		<td><?php echo $row['rejectedinvitations'] ?></td>
		<td><?php echo $row['ignoredinvitations'] ?></td>
		<?php } ?>
	</tr>
	<?php } ?>
	<tr>
		<td><b><?php echo getlocal("report.total") ?></b></td>
		<td><?php echo $page['reportByDateTotal']['threads'] ?></td>
		<td><?php echo $page['reportByDateTotal']['missedthreads'] ?></td>
		<td><?php echo $page['reportByDateTotal']['agents'] ?></td>
		<td><?php echo $page['reportByDateTotal']['users'] ?></td>
		<td><?php echo $page['reportByDateTotal']['avgwaitingtime'] ?></td>
		<td><?php echo $page['reportByDateTotal']['avgchattime'] ?></td>
		<?php if ($page['show_invitations_info']) { ?>
		<td><?php echo $page['reportByDateTotal']['sentinvitations'] ?></td>
		<td><?php echo $page['reportByDateTotal']['acceptedinvitations'] ?></td>
		<td><?php echo $page['reportByDateTotal']['rejectedinvitations'] ?></td>
		<td><?php echo $page['reportByDateTotal']['ignoredinvitations'] ?></td>
		<?php } ?>
	</tr>
<?php } else { ?>
	<tr>
	<td colspan="<?php echo($page['show_invitations_info'] ? 11 : 7); ?>">
		<?php echo getlocal("report.no_items") ?>
	</td>
	</tr>
<?php } ?>
</tbody>
</table>
<?php } ?>

<?php if($page['showbyagent']) { ?>
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
</th>
<?php if ($page['show_invitations_info']) { ?>
<th>
	<?php echo getlocal("report.byoperator.5") ?>
</th>
<th>
	<?php echo getlocal("report.byoperator.6") ?>
</th>
<th>
	<?php echo getlocal("report.byoperator.7") ?>
</th>
<th>
	<?php echo getlocal("report.byoperator.8") ?>
</th>
<?php } ?>
</tr>
</thead>
<tbody>
<?php if( $page['reportByAgent'] ) { ?>
	<?php foreach( $page['reportByAgent'] as $row ) { ?>
	<tr>
		<td><a href="<?php echo $webimroot ?>/operator/history.php?q=<?php echo topage(htmlspecialchars($row['name'])) ?>&type=operator"><?php echo topage(htmlspecialchars($row['name'])) ?></a></td>
		<td><?php echo $row['threads'] ?></td>
		<td><?php echo $row['msgs'] ?></td>
    	<td><?php echo $row['avglen'] ?></td>
		<?php if ($page['show_invitations_info']) { ?>
		<td><?php echo $row['sentinvitations'] ?></td>
		<td><?php echo $row['acceptedinvitations'] ?></td>
		<td><?php echo $row['rejectedinvitations'] ?></td>
		<td><?php echo $row['ignoredinvitations'] ?></td>
		<?php } ?>
	</tr>
	<?php } ?>
<?php } else { ?>
	<tr>
	<td colspan="<?php echo($page['show_invitations_info'] ? 8 : 4); ?>">
		<?php echo getlocal("report.no_items") ?>
	</td>
	</tr>
<?php } ?>
</tbody>
</table>
<?php } ?>

<?php if($page['showbypage']) { ?>
<br/>
<br/>

<div class="tabletitle"><?php echo getlocal("report.bypage.title") ?></div>
<table class="statistics">
<thead>
<tr><th>
	<?php echo getlocal("report.bypage.1") ?>
</th><th>
	<?php echo getlocal("report.bypage.2") ?>
</th><th>
	<?php echo getlocal("report.bypage.3") ?>
</th></tr>
</thead>
<tbody>
<?php if( $page['reportByPage'] ) { ?>
	<?php foreach( $page['reportByPage'] as $row ) { ?>
	<tr>
		<td><a href="<?php echo htmlspecialchars($row['address']) ?>"><?php echo htmlspecialchars($row['address']) ?></a></td>
		<td><?php echo $row['visittimes'] ?></td>
		<td><?php echo $row['chattimes'] ?></td>
	</tr>
	<?php } ?>
<?php } else { ?>
	<tr>
	<td colspan="3">
		<?php echo getlocal("report.no_items") ?>
	</td>
	</tr>
<?php } ?>
</tbody>
</table>
<?php } ?>

<?php } ?>

<?php 
} /* content */

require_once('inc_main.php');
?>