<?php
	
require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/access.php");
has_access(CLERK);
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/print_base.php");
require_once($_SERVER['DOCUMENT_ROOT']."/clerk/clerkfunctions.php");

$return_sum = 0.00;
$return_item = array();
$link = $GLOBALS['link'];

$_SESSION['rid'] = 0;
$_SESSION['return_upc'] = 0;
$_SESSION['return_qty'] = 0;
$_SESSION['return_item_sum'] = 0;
$_SESSION['card_no'] = 0;
	
print_page_start();

?>
<h1>Process a Return</h1>

<body>
	<form method="post" action="return.php">
	  <input type="text" class="form-control" placeholder="Receipt ID" style="width: 300px" name="rid" /input><br>
	  <input type="text" class="form-control" placeholder="Item UPC" style="width: 300px" name="upc" /inpit><br>
	  <input type="text" class="form-control" placeholder="Return Quantity" style="width: 300px" name="qty" /inpit><br>
  	  <input type="submit" class="btn btn-primary" name="Submit" /input>
	</form>
<?php
	if(!isset($_POST["rid"]))
	{
		echo "Please enter a receipt number.<br>";
	}
	if(!isset($_POST["upc"]))
	{
		echo "Please enter an item upc.<br>";
	}
	if(!isset($_POST["qty"]))
	{
		echo "Please enter a quantity.<br>";
	}
	else
	{

		//set search parameters
		$rid = mysql_real_escape_string($_POST["rid"]);
		$upc = mysql_real_escape_string($_POST["upc"]);
		$qty = mysql_real_escape_string($_POST["qty"]);
		$SQL = "SELECT * FROM Purchase WHERE receiptId = $rid";
		$result = mysql_query($SQL, $GLOBALS["link"]);
		if($result == false || mysql_num_rows($result) <= 0)
			echo "Receipt does not exist. <br>";
		else
		{	
			$original_order = mysql_fetch_assoc($result);
			//Check to see if purchase date was within 15 days.
			$purchaseDate = $original_order['date'];
			$purchaseDate = new DateTime($purchaseDate);
			$now = new DateTime("now");
			$now->setTime("0", "0", "0");
			$difference = $purchaseDate->diff($now);
			$difference = $difference->days;
			
			if($difference > 15)
			{
				echo "The purchase was made $difference days ago, and is no longer valid for return.<br> Only purchases made within the last 15 days can be returned.<br>";
			}
			else
			{

				// Extract the quantity of the item from the original purchase
				$original_quantity = "SELECT PI.qty FROM purchaseItem PI WHERE PI.receiptId = $rid AND PI.upc = $upc";
				$original_quantity = mysql_query($original_quantity, $GLOBALS["link"]);
				//Check to make sure the original purchase exists.
				if($original_quantity == false || mysql_num_rows($original_quantity) <= 0)
				{
					echo "The item $upc was not found in the order. <br>";
				}
				else
				{	// If it exists, continue
					$original_quantity = mysql_fetch_assoc($original_quantity);
					$original_quantity = $original_quantity['qty'];
					echo "The original quantity of item $upc purchased was $original_quantity.<br>";


					// Ensure the return quantity does not exceed the original purchase quantity.
					if($original_quantity < $qty)
					{
						echo "Higher quantity selected than was in original purchase.<br>";
					}
					else 
					{	
						// Check to see if previous returns exist for this item and receipt combination. 
						// If so, find the return amount and check that that value plus the return quantity
						// does not exceed the original purchase quantity. 
						$prev_ret_quant = $sql = "SELECT RI.qty, RI.retId\n"
					    . "FROM returntable R, returnItem RI\n"
					    . "WHERE R.receiptId = $rid \n"
					    . "AND R.retId = RI.retId\n"
					    . "AND RI.upc = $upc\n";
						$prev_ret_quant = mysql_query($prev_ret_quant, $GLOBALS["link"]);
						$prev_quant = 0;	
						if(mysql_num_rows($prev_ret_quant) > 0)
							{
								while($db_field = mysql_fetch_assoc($prev_ret_quant)) 
								{
									$prev_quant += $db_field['qty'];
								}
								echo "Previous returned amount of this item on this receipt is: $prev_quant<br>";
							}

						$total_returns = $qty + $prev_quant;

						if($total_returns > $original_quantity)
						{
							echo "There are previous returns for this item on this receipt. These, combined with the requested return quantity, exceed the original purchase amount.<br>";
						}
						else
						{	
							// Store the session information, calculate the price to be returned, and move to process order. 
								//Check if return payment type should be cash or card.
							$_SESSION['payment_type'] = "card";
							$cardNo = $original_order['cardNo'];

							// If no card information in db, assign return type to cash.
							if(is_null($cardNo) && is_null($original_order['expiry']))
							{	
							echo "Cash return. <br>";
							$_SESSION['payment_type'] = "cash";
							$_SESSION['card_no'] = substr($cardNo, -5);
							}
							$SQL = "SELECT price FROM Item WHERE upc = $upc";
							$result = mysql_query($SQL, $GLOBALS["link"]);
							$result = mysql_fetch_assoc($result);
							$return_sum = $result['price'] * $qty;
							$_SESSION['rid'] = $rid;
							$_SESSION['return_upc'] = $upc;
							$_SESSION['return_qty'] = $qty;
							$_SESSION['return_item_sum'] = $return_sum;
							echo "Process a refund for $qty unit(s) of item $upc from receipt $rid? <br> Total return value is: $$return_sum<br>"; // Final tally of return.
							?>
								<form method="post" action="process_ret.php">
							  	<input type="submit" name="Submit" value="Confirm" /input>
								</form>
							<?php
						}
					}
				}
			}
		}

	}
?>
<a href="index.php">Cancel return and return to index.</a>
<?php
print_page_end();

?>
