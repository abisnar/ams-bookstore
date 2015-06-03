<?php

require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");
session_destroy();
header("Location: http://".$_SERVER['SERVER_NAME']."/index.php");

?>