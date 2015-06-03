<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
require_once($_SERVER['DOCUMENT_ROOT']."/common/access.php");
require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/print_base.php");
has_access(MANAGER);
print_page_start();
print_manager_top(MANAGER_DD_UPDATE_INVENTORY);

function remove_Item() {
	// Check that form fields are filled
	if (!empty($_POST["UPC"])){
		// query to check to make sure that item is in database
		$UPC = mysql_real_escape_string($_POST["UPC"]);
		$itemCheck = "SELECT * FROM Item WHERE upc = '$UPC'";
		$iCheckResult = mysql_query($itemCheck);
		$num_rows = mysql_num_rows($iCheckResult);
		// If there is a row in this query... Item exists therefore delete from Database
		if ($num_rows != 0){
					$delete = "DELETE FROM
					Item
					WHERE upc = '$UPC'";
			mysql_query($delete);
			echo "Item #$UPC and its fields have been successfully deleted from the database";
		} else{
			echo '<div class="alert-danger">
				<button class="close" data-dismiss="alert"> </button>
				<strong> Error!</strong> Item is not in the Database. Please Add New Item or Check UPC Value.
				</div>';
		}
	} else {
		echo '<div class="alert-danger">
				<button class="close" data-dismiss="alert"> </button>
				<strong> Error!</strong> Please provide a UPC Value.
				</div>';
	}
}

if (!empty($_POST['remove-submit'])) {
	remove_Item();
}

?>
<h3>Remove Item</h3>
	<form method="post" action="remove_item.php">
		<input type="text" class="form-control" placeholder="UPC" style="width: 300px" name="UPC"<?php if(isset($UPC)){ echo " value=$UPC";} ?>><br>
		<input type="submit" class="btn btn-success" name="remove-submit">
	</form>

<?php 
print_page_end();
?>

