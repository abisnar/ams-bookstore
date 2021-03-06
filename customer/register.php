<?php

require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/print_base.php");

function register() {
	if (isset($_POST["username"]) && isset($_POST["password"]) && 
		isset($_POST["password_conf"]) && isset($_POST["name"]) && isset($_POST["address"]) &&
		isset($_POST["phone"])) {

		// check if username exists within the system
		$username = mysql_real_escape_string($_POST["username"]);
		if(empty($username)) {
			$GLOBALS["error_msg"] = "Username cannot be empty!";
			return;
		}
		
		// Make sure that the username is not clerk or manager
		if($username == "clerk" || $username == "manager") {
			$GLOBALS["error_msg"] = "Username already exists!";
			return;
		}
		
		// Form SQL query to check if the username already exists
		$SQL = "SELECT * FROM Customer WHERE cid = '$username'";
		$result = mysql_query($SQL, $GLOBALS["link"]) or die(mysql_errno($GLOBALS["link"]).": ".mysql_error($GLOBALS["link"])."\n");
		$num_rows = mysql_num_rows($result);

		// if no rows return, the username does not exist and the customer can be created
		if($num_rows == 0) {
			$password = md5(mysql_real_escape_string($_POST["password"])); // hash the password
			$password_conf = md5(mysql_real_escape_string($_POST["password_conf"]));
			if ($password != $password_conf) {
				$GLOBALS["error_msg"] = "Passwords do not match!";
				return;
			}
			$name = mysql_real_escape_string($_POST["name"]);
			$address = mysql_real_escape_string($_POST["address"]);
			$phone = mysql_real_escape_string($_POST["phone"]);
			if(strlen($password) < 3 && strlen($password) > 8) {
				$GLOBALS["error_msg"] = "Passoword must be between 3 and 8 chars!";
				return;
			}
			// Checks to prevent sql errors with users entering large length data
			if(strlen($name) > 30 && strlen($address) > 80 && strlen($phone) > 13) {
				$GLOBALS["error_msg"] = "Bad data provided!";
				return;
			}
			$SQL = "INSERT INTO Customer VALUES ('$username', '$password', '$name', '$address', '$phone')";
			mysql_query($SQL, $GLOBALS["link"]);
			// User has been added to the Customer table
			$_SESSION['username']=$username;
			$_SESSION['access']=CUSTOMER;
			// Redirect to the customer index page
			header("Location: index.php?success=0");
			exit;
		} else {
			$GLOBALS["error_msg"] = "Username already exists.";
		}
	}
}

$error_msg = "";
register();
mysql_close($GLOBALS["link"]);


print_page_start();

?>
<h3>Register New Account</h3>
	<form method="post" action="register.php">
		<input type="text" class="form-control" placeholder="Username" style="width: 300px" name="username"<?php if(isset($username)){ echo " value=$username";} ?>><br>
		<input type="password" class="form-control" placeholder="Password" style="width: 300px" name="password"><br>
		<input type="password" class="form-control" placeholder="Confirm Password" style="width: 300px" name="password_conf"> <br>
		<input type="text" class="form-control" placeholder="Name" style="width: 300px" name="name"<?php if(isset($name)){ echo " value=$name";} ?>><br>
		<input type="text" class="form-control" placeholder="Address" style="width: 300px" name="address"<?php if(isset($address)){ echo " value=$address";} ?>><br>
		<input type="text" class="form-control" placeholder="Phone" style="width: 300px" name="phone"<?php if(isset($phone)){ echo " value=$phone";} ?>><br>
		<input type="submit" class="btn btn-primary" name="submit">	
	</form>
