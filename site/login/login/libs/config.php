<?php
	define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_DATABASE', 'mibew');
//Maynot be needed it is deleteable, not sure if it's needed for profile page.
 $link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
$db = mysql_select_db(DB_DATABASE);
$result = mysql_query("SELECT * FROM members") or die(mysql_error());


?>