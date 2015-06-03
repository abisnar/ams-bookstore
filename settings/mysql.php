<?php
require_once("config.php");

$link = mysql_connect($server, $user_name, $password);
mysql_select_db($database, $link);

?>