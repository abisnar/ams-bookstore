<?php
require_once($_SERVER['DOCUMENT_ROOT']."/common/print_base.php");
require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/access.php");
has_access(CLERK);
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");
require_once($_SERVER['DOCUMENT_ROOT']."/clerk/clerkfunctions.php");

print_page_start();

$sum = 0.00;
$item = array();

if(isset($_SESSION['items'])) 
{
	$items = $_SESSION['items'];
} 
else $items = array();


if(isset($_SESSION['item_sum'])) 
{
	$sum = $_SESSION['item_sum'];
} 
else $sum = 0;

function check_empty()
{
	if(empty($_SESSION['items']))
	{
		echo "Add items to cart before proceeding to payment.<br>";
	}
	else 
		{
			echo '<form method="post" action="payment.php">
					<button type="submit" class="btn btn-primary">Proceed to Payment Processing</button>
				</form>';
		}
}

?>
<h1>Process a Sale</h1>
	<form method="post" action="purchase.php">
		<div class="form-group" style="width: 300px">
		    <input type="text" class="form-control" id="enter_upc" placeholder="Enter Item UPC" name="upc">
		</div>
		<div class="form-group" style="width: 300px">
	 		<input type="text" class="form-control" id="qty" placeholder="Quantity" name="quantity">
  		</div>
  			<button type="submit" class="btn btn-primary">Add</button>

	</form>


		<?php


		//Check that a search parameter is inputted
			if (isset($_POST["upc"])) {

				//set search parameters
				$upc = mysql_real_escape_string($_POST["upc"]);

				$SQL = "SELECT * FROM item WHERE upc = '$upc'";
				$result = mysql_query($SQL, $GLOBALS["link"]);
				
				if($result == false || mysql_num_rows($result) <= 0)
					echo "Item does not exist. <br>";
				else 
				{	

					$item = mysql_fetch_assoc($result);
					
					// Default to 1 if no quantity set.
					$quantity = isset($_POST['quantity']) && $_POST['quantity'] > 0 ? $_POST["quantity"] : 1 ;
					// check stock from DB
					$stock = $item['stock'];
					// Check if the item is already in the order. If so, check stock levels and combine quantities.
					if(isset($items[$item['upc']]))
					{
						$upc = $item['upc'];
						$existing_item = $items[$item['upc']];
						$old_quant = $existing_item['quantity'];
						$new_quant = $quantity + $old_quant;
						$available_quant = $stock - $old_quant;
						if($new_quant > $stock)
						{
							echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">x</button>Not enough stock for this order. There are '.$available_quant.' units available. </div>';
						}
						else 
						{
							$item['quantity'] = $new_quant;
							$items[$item['upc']] = $item;
							$sum += ($item['price'] * $quantity);
							echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">x</button>Item added to purchase.</div>';
						}
					}
					else
						{
						$item['quantity'] = $quantity;

						// No quantity selected, or insufficient quantity.
						if (isset($quantity) && $quantity > $stock){
							echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">x</button>Not enough stock for this order. There are '.$stock.' units available. </div>';
						}
						// We have sufficient stock, add to the order.
						else
						{	
							$sum += ($item['price'] * $item['quantity']);
							$items[$item['upc']] = $item;
							echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">x</button>Item added to purchase.</div>';
						}
					}
				}
			}
	

	$_SESSION['items'] = $items;
	$_SESSION['item_sum'] = $sum;
	

	print_order();
	check_empty();
	?>

	<a href="index.php" class="btn btn-ams">Cancel purchase and return to index.</a>
<?php
print_page_end();
?>

