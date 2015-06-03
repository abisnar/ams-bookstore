<?php
	require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/common/access.php");
	has_access(CLERK);
	require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/clerk/clerkfunctions.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/common/print_base.php");

	print_page_start();
	$sum = $_SESSION['return_item_sum'];
	$card = $_SESSION['card_no'];
	$qty = $_SESSION['return_qty'];
	$upc = $_SESSION['return_upc'];
	$rid = $_SESSION['rid'];
	$date = date("Y-m-d");

	// Create new ReturnTable entry.
	$sql = "INSERT INTO `cs304`.`returntable` (date, receiptId) VALUES ('$date', '$rid')";
	mysql_query($sql, $GLOBALS["link"]);
	$retId = mysql_insert_id($link);

	//Create new returnItem entry
	$sql = "INSERT INTO `cs304`.`returnitem` VALUES ('$retId', '$upc', '$qty')";
	mysql_query($sql, $GLOBALS["link"]);

	//Add stock back to item
	$sql = "UPDATE Item SET stock=stock + $qty WHERE upc='$upc'";
	mysql_query($sql, $link);

	//Print return receipt.
	echo "You have returned $qty x item $upc from receipt number $rid.<br>";
	card_type($card);
	
	?>
<html> 
	<a href="index.php">Return to index.</a>
</html>
<?php
	print_page_end();
?>