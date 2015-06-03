<?php

require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/access.php");
has_access_only(CUSTOMER);
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");

function process_purchase() {
	if (isset($_POST["card_type"]) && isset($_POST["card_no"]) && isset($_POST["card_exp_m"]) && isset($_POST["card_exp_y"]) && isset($_SESSION["cart"])) {
		$itemNum = 0;
		foreach($_SESSION["cart"] as $upc=>$quantity) {
			$itemNum += $quantity;
		}
		if ($itemNum < 0) { return 2; }
		$card_type = $_POST["card_type"];
		$card_no = $_POST["card_no"];
		$card_exp_m = $_POST["card_exp_m"];
		$card_exp_y = $_POST["card_exp_y"];
		if ($card_type != "visa" && $card_type != "mc" && $card_type != "ae") { return 3; }
		if (strlen($card_no) < 14 || !is_numeric($card_no)) {return 4; } // not a real credit card check, for the sake of simplicity
		if (($card_exp_m < date("n") && $card_exp_y == date("y")) 
			|| $card_exp_y < date("y")) {
			return 5;
		}
		
		$link = $GLOBALS['link'];
		$sql = "INSERT INTO Purchase (date, cid, cardNo, expiry, expectedDate, deliveredDate) VALUES (
			NOW(), 
			'".$_SESSION['username']."',
			'$card_no',
			STR_TO_DATE('$card_exp_m $card_exp_y', '%m %Y'), 
			NOW() + INTERVAL (SELECT FLOOR((SELECT COUNT( * ) FROM  Purchase as P2
							  WHERE P2.deliveredDate IS NULL AND NOT P2.cid IS NULL) / ".DELIVERIES_PER_DAY.") + 1)
			DAY, 
			NULL)";
			
		// Inset the purchase into the table
		mysql_query($sql, $link) or die(mysql_errno($link).": ".mysql_error($link)."\n");
		$recieptId = mysql_insert_id($link);

		$sql = "SELECT expectedDate FROM Purchase WHERE receiptId = '$recieptId'";
		$result = mysql_query($sql, $link) or die(mysql_errno($link).": ".mysql_error($link)."\n");
		$expectedDate = mysql_fetch_assoc($result)['expectedDate'];
		
		// Add each item to purchase
		foreach($_SESSION["cart"] as $upc=>$quantity) {
			if($quantity > 0) {
				$sql = "INSERT INTO PurchaseItem VALUES ($recieptId, $upc, $quantity)";
				mysql_query($sql, $link) or die(mysql_errno($link).": ".mysql_error($link)."\n");
				$sql = "UPDATE Item SET stock=stock - $quantity WHERE upc='$upc'";
				mysql_query($sql, $link) or die(mysql_errno($link).": ".mysql_error($link)."\n");
			}
		}	
		
		unset($_SESSION["cart"]);
		$expectedDate = new DateTime($expectedDate);
		$now = new DateTime("now");
		$now->setTime("0", "0", "0");
		$difference = $expectedDate->diff($now);
		
		return "0,".$difference->days.",$recieptId";
	} else {
		return 1;
	}
}

echo process_purchase();

?>