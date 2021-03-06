<?php

require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/print_base.php");

if(has_access_only(CUSTOMER, 0, 0)) {
	header("Location: http://".$_SERVER["SERVER_NAME"]."/customer/index.php");
}

if(isset($_GET["success"])) {
	switch($_GET["success"]) {
		case 1: 
			$GLOBALS["success_msg"] = "You have successfully logged out.";
			break;
	}
}

print_page_start();

?>

	<h3>Welcome to <b>ams music.</b></h3>
	<p>We have a wide selection of music CD's and DVD's availbale!</p>
	
<?php

if(!has_access(CUSTOMER, 0, 0)) {
	echo '
		<p> To access the store please sign in.</p>

		<div style="margin-top: 10px;">
			<a href="common/login.php" class="btn btn-primary">Sign In</a><span style="margin-left: 10px; margin-right:7px;">or</span>
			<a href="customer/register.php" class="btn btn-ams">Register</a>
		</div>';
} else if (has_access(CLERK, 0, 0)) {
	echo '<div style="margin-top: 10px; min-height: 50px;">
			<div class="block margin-right-10 "><a href="customer/index.php" class="btn btn-primary">Access customer view</a></div>
			<div class="block margin-right-10"><a href="clerk/index.php" class="btn btn-primary">Access clerk view</a></div>';
			if(has_access(MANAGER, 0, 0)) {
				echo '<div class="block"><a href="manager/index.php" class="btn btn-primary">Access manager view</a></div>';
			}
	echo '</div>';
}
?>

	
<?php
print_page_end();
?>