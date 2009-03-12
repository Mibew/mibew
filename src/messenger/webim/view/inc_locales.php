<?php 
function menuloc($id) {
	global $current_locale;
	if($current_locale == $id) {
		return " class=\"active\"";
	}
	return "";
}

$page['right_menu'] = ""; 

if(isset($page) && isset($page['localeLinks'])) {
	$page['right_menu'] .= "<li>\n<h2><b>locales</b></h2>\n<ul class=\"locales\">\n";
	foreach($page['localeLinks'] as $id => $title) {
		$page['right_menu'] .= "<li".menuloc($id)."><a href=\"?locale=$id\">$title</a></li>\n";
	}
	$page['right_menu'] .= "</ul>\n</li>";
}
?>