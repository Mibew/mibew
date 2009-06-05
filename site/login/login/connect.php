<?php
// db info
$hostname="localhost";
$mysql_login="root";
$mysql_password="";
$database="mibew";

if (!($db = mysql_connect($hostname, $mysql_login , $mysql_password))){
  die("Can't connect to mysql.");    
}else{
  if (!(mysql_select_db("$database",$db)))  {
    die("Can't connect to db.");
  }
}
?> 