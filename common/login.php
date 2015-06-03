<?php

require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/print_base.php");

if(isset($_GET["error"])) {
	switch($_GET["error"]) {
		case 1: 
			$GLOBALS["error_msg"] = "You have to be logged in to view this page!";
			break;
	}
}

if (isset($_GET["redirect"])) {
	$GLOBALS["redirect"] = $_GET["redirect"];
}

function login_redirect($username, $access, $redirect) {
	$_SESSION[USERNAME] = $username;
	$_SESSION[ACCESS] = $access;
	if(!empty($GLOBALS["redirect"])) {
		header("Location: ".$GLOBALS["redirect"]);
	} else {
		header("Location: $redirect");
	}
	die();
}

if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["user_type"]))
{
	$username = $_POST["username"];
	$password = $_POST["password"];
	$password_hash = md5($password);
	$user_type = $_POST["user_type"];
	
	if ($user_type == CUSTOMER) {
		$sql = "SELECT cid FROM Customer WHERE cid = '$username' AND password = '$password_hash'";
			
		$result = mysql_query($sql, $link) or die(mysql_errno($link).": ".mysql_error($link)."\n");
		$assoc = mysql_fetch_assoc($result);
		$num_rows = mysql_num_rows($result);
		
		if($num_rows > 0) {
			login_redirect($username, CUSTOMER, "http://".$_SERVER['SERVER_NAME']."/customer/index.php");
		} else {
			$error_msg = "Wrong Credentials!";
		}
	} else if ($user_type == CLERK && $username == CLERK_UNAME) {
		if ($password == CLERK_PASSWORD) {
			login_redirect($username, CLERK, "http://".$_SERVER['SERVER_NAME']."/clerk/index.php");
		} else {
			$error_msg = "Wrong Credentials!";
		}
	} else if ($user_type == MANAGER && $username == MANAGER_UNAME) {
		if ($password == MANAGER_PASSWORD) {
			login_redirect($username, MANAGER, "http://".$_SERVER['SERVER_NAME']."/manager/index.php");
		} else {
			$error_msg = "Wrong Credentials!";
		}
	}	
}

print_page_start();

?>
    <script type="text/javascript">
		$(document).ready(function() {
			<?php if(!isset($user_type) || ($user_type == CUSTOMER)) {echo '$("#user_type_div").hide();'; } ?>
			$("#admin_link").on('click', function(){
				$("#user_type_div").show();
			});
		});
    </script>
<h3  style="margin-bottom: 15px;">Login to ams music.</h3>
<form action="login.php" method="POST">
	<div id="user_type_div">
		<div class="input-group">
		<span class="input-group-addon">User Type</span>
		<select name="user_type" class="form-control" style="width: 210px">
		  <option value="<?php echo CUSTOMER ?>"<?php if(isset($user_type) && $user_type == CUSTOMER){ echo ' selected="selected"';} ?>">Customer</option>
		  <option value="<?php echo CLERK ?>"<?php if(isset($user_type) && $user_type == CLERK){ echo ' selected="selected"';} ?>>Clerk</option>
		  <option value="<?php echo MANAGER ?>"<?php if(isset($user_type) && $user_type == MANAGER){ echo ' selected="selected"';} ?>>Manager</option>
		</select>
		</div>
	</div><br>
	<input type="text" class="form-control" placeholder="Username" style="width: 300px" name="username"<?php if(isset($username)){ echo " value=$username";} ?>><br>
	<input type="password" class="form-control" placeholder="Password" style="width: 300px" name="password"><br>
	<?php if(isset($GLOBALS["redirect"])) { echo '<input type="hidden" name="redirect" value="'.$GLOBALS["redirect"].'">';} ?>
	<div style="width: 200px; height: 30px;">
	<div class="col-lg-4" style="padding-left:0px;">
	<input type="submit" class="btn btn-primary pull-left" style="margin-left: 0px;" value="Login">
	</div>
	<div class="col-lg-8" style="padding-left:5px;">
	No account? <a href="../customer/register.php" class="">Register here</a>
	</div>
	</div>
</form>
<br>
<a id="admin_link" href="#"><span class="label label-default">Clerk/Manager Login</span></a>
<?php
print_page_end();
?>