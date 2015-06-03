<?php

require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");

function print_error($print) {
	if($print == 1) {
		echo "You do not have correct privilages to access this page!<br>";
		echo '<a href="http://'.$_SERVER['SERVER_NAME'].'/'.($_SESSION["access"] > CUSTOMER ? $_SESSION["username"] : "customer").'/index.php">&lt;&lt; Return Home</a>';
		die();
	} else {
		return false;
	}
}

function has_access($req_level, $print=1, $redirbool=1) {
	if(!isset($_SESSION["username"]) || !isset($_SESSION["access"])) {
		if($redirbool == 1) {
			header("Location: http://".$_SERVER['SERVER_NAME']."/common/login.php?error=1");
			exit;
		} else {
			return false;
		}
	} else {
		if($_SESSION["access"] < $req_level) {
			print_error($print);
			return false;
		}
	}
	return true;
}

function has_access_only($req_level, $print=1, $redirbool=1) {
	if(has_access($req_level, NULL, $print, $redirbool)) {
		if($_SESSION["access"] != $req_level) {
			print_error($print);
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}

?>