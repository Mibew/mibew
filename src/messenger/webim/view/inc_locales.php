<?php 
function menuloc($id) {
	global $current_locale;
	if($current_locale == $id) {
		echo " class=\"active\"";
	}
	return "";
}
function tpl_menu() { global $page, $webimroot, $errors, $current_locale;
?>
<?php if(isset($page) && isset($page['localeLinks'])) { ?>
			<li>
				<h2><b><?php echo getlocal("lang.choose") ?></b></h2>
				<ul class="locales">
<?php foreach($page['localeLinks'] as $id => $title) { ?>
					<li<?php menuloc($id)?> ><a href='?locale=<?php echo $id ?>'><?php echo $title ?></a></li>
<?php } ?>
				</ul>
			</li>
<?php } ?>

<?php 
}
?>