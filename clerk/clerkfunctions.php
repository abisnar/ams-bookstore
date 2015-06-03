<?php

	$link = $GLOBALS['link'];
	
	function print_order(){
		?>
		<html>
			<table class="table table-bordered table-condensed table-ams" style="margin-bottom: 0px;">
				<thead>
					<tr>
						<td>UPC</td>
						<td>Title</td>
						<td>Price</td>
						<td>Quantity</td>
						<td align="right">Total</td>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ($_SESSION['items'] as $value) 
						{		
							?>
								<tr>
									<td><?php echo $value['upc'] ?></td>
									<td><?php echo $value['title'] ?></td>
									<td>CDN $<?php echo $value['price'] ?></td>
									<td><?php echo $value['quantity'] ?></td>
									<td align="right">CDN $<?php echo $value['quantity']*$value['price'] ?></td>
								</tr>
					<?php } ?> 

 				</tbody>
			</table>
		<div class="label label-ams label-ams-total pull-right" style="font-size: 100%"><?php echo "Total sale value: $" . $_SESSION['item_sum'] . "</div><br><br>";
	}

	function print_return(){
		?>
			<table class="table table-condensed table-ams">
				<thead>
					<tr>
						<td><b>UPC</b></td>
						<td><b>ReceiptId</b></td>
						<td><b>Price</b></td>
						<td><b>Quantity</b></td>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ($_SESSION['r_items'] as $value) 
						{		
							?>
								<tr>
									<td><?php echo $value['upc'] ?></td>
									<td><?php echo $value['title'] ?></td>
									<td>CDN $<?php echo $value['price'] ?></td>
									<td><?php echo $value['quantity'] ?></td>
									<td align="right">CDN $<?php echo $value['quantity']*$value['price'] ?></td>
								</tr>
					<?php } ?> 

 				</tbody>
			</table>
		<div class="label label-ams label-ams-total pull-right" style="font-size: 100%"><?php echo "Total return value: $" . $_SESSION['item_sum'] . "</div><br><br>";
	}
	
	function clear_order()
	{
		$_SESSION['items'] = array();
		$_SESSION['item_sum'] = 0;
	}
	function clear_return()
	{
		$_SESSION['rid'] = 0;
		$_SESSION['return_upc'] = 0;
		$_SESSION['return_qty'] = 0;
		$_SESSION['return_item_sum'] = 0;
		$_SESSION['card_no'] = 0;
	}

	function card_type($card)
	{
		$sum = $_SESSION['return_item_sum'];	
		if($_SESSION['payment_type'] == "card")
			echo "Please return $$sum to the user's card ending in $card.<br>";
		if($_SESSION['payment_type'] == "cash")
			echo "Please return $$sum to the user in cash.<br>";
	}



	function submit_card_order($cardNo, $expiryDate)
	{
		$last5 = substr($cardNo, -5); 
		$date = date("Y-m-d");
		$link = $GLOBALS['link'];

		//Create new purchase tuple in DB.
		$sql = "INSERT INTO `cs304`.`purchase` (`date`, `cardNo`, `expiry`) VALUES ('$date', '$cardNo', '$expiryDate')";
		mysql_query($sql, $GLOBALS["link"]);
		$recId = mysql_insert_id($link);
		// Add new purchaseItem tuple for each item in the order. Decrease stock level of that item by the quantity ordered. 
		foreach ($_SESSION['items'] as $value) {
			$qty = $value['quantity'];
			$upc = $value['upc'];
			$sql = "INSERT INTO `cs304`.`purchaseitem` VALUES ('$recId', '$upc', '$qty')";
			mysql_query($sql, $GLOBALS["link"]);
			// Retrieve current stock levels and decrement by the item order quantity. 
			$stock_val = "SELECT stock FROM item WHERE upc = '$upc'";
			$result = mysql_query($stock_val, $GLOBALS["link"]);
			$result = mysql_fetch_assoc($result);
			$new_stock = $result['stock'];
			$new_stock = $new_stock - $qty;
			$post_stock = "UPDATE item SET stock=$new_stock WHERE upc = '$upc'";
			mysql_query($post_stock, $GLOBALS["link"]);
		}
		?>
		<div class="alert alert-success">
			Thank you for your purchase!
		</div>

		<?php echo "<div class='panel'>Receipt number: $recId<br> Date: $date<br> Card Number: **************$last5<br>";
		print_order();
		clear_order();
		echo "</div>";
	}

	function submit_cash_order()
	{
		$link = $GLOBALS['link'];
		$date = date("Y-m-d");
		//Create new purchase tuple in DB.
		$sql = "INSERT INTO `cs304`.`purchase` (`date`) VALUES ('$date')";
		mysql_query($sql, $GLOBALS["link"]);
		$recId = mysql_insert_id($link);
		// Add new purchaseItem tuple for each item in the order. Decrease stock level of that item by the quantity ordered. 
		foreach ($_SESSION['items'] as $value) {
			$qty = $value['quantity'];
			$upc = $value['upc'];
			$sql = "INSERT INTO `cs304`.`purchaseitem` VALUES ('$recId', '$upc', '$qty')";
			mysql_query($sql, $GLOBALS["link"]);
			// Retrieve current stock levels and decrement by the item order quantity. 
			$stock_val = "SELECT stock FROM item WHERE upc = '$upc'";
			$result = mysql_query($stock_val, $GLOBALS["link"]);
			$result = mysql_fetch_assoc($result);
			$new_stock = $result['stock'];
			$new_stock = $new_stock - $qty;
			$post_stock = "UPDATE item SET stock=$new_stock WHERE upc = '$upc'";
			mysql_query($post_stock, $GLOBALS["link"]);
		} ?>
		<div class="alert alert-success">
			Thank you for your purchase!
		</div>
		<?php echo "<div class='panel'>Receipt ID: $recId <br> Date: $date <br>";
		print_order();
		clear_order();
		echo "</div>";
	}

?>