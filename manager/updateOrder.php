<?php
require_once($_SERVER['DOCUMENT_ROOT']."/common/access.php");
require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/print_base.php");

print_page_start();
print_manager_top(MANAGER_DD_SALES_REPORT);


function find_purchase() {

	if (empty($_POST['rid']) && isset($_POST['rid'])) { ?>

		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Error!</strong> Please input a receipt ID number.
		</div>

	<?php
	} else if (!empty($_POST['rid'])) {
		$rid = mysql_real_escape_string($_POST['rid']);

		$SQL = "SELECT receiptId, cid, expectedDate
				FROM Purchase
				WHERE receiptId = '$rid'";

		$result = mysql_query($SQL, $GLOBALS['link']);
		$num_rows = mysql_num_rows($result);

		if ($num_rows == 0) { ?>

			<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>Error!</strong> This receipt ID does not exist.
			</div>

		<?php
		} else {

			$SQL = "SELECT I.upc, I.title, PI.qty
					FROM Item I, PurchaseItem PI, Purchase P 
					WHERE P.receiptId = '$rid' AND PI.receiptId = P.receiptId AND PI.upc = I.upc";

			$result2 = mysql_query($SQL, $GLOBALS['link']);

			while($db_field = mysql_fetch_assoc($result)) { ?>
				<div class="panel">
				<table class="table">
				<thead>
					<tr>
						<td align="center"> Receipt ID </td>
						<td align="center"> Customer ID  </td>
						<td align="center"> Expected Date </td>
						<td align="center"> Items </td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="center"><?php echo $db_field["receiptId"]?></td>
						<td align="center"><?php echo $db_field["cid"]?></td>
						<td align="center"><?php echo $db_field["expectedDate"]?></td>
						<td>
							<div class="panel">
							<table class="table table-striped" align="center">
							<thead>
								<tr>
									<td> UPC </td>
									<td> Title </td>
									<td align="center"> Qty </td>
									<?php while($db_field2 = mysql_fetch_assoc($result2)) { ?>
								<tr>
									<td><?php echo $db_field2["upc"]?></td>
									<td><?php echo $db_field2["title"]?></td>
									<td align="center"><?php echo $db_field2["qty"]?></td>
								</tr>
									<?php } ?>
							</table>
							</div>
						</td>
						
					</tr>
				</tbody>
				</table>
				</div>
			<br>

			<form method="post" action="updateOrder.php">
				<div class="input-group" style="width: 300px">
					<span class="input-group-addon">Select a date:</span>
					<input type="date" class="form-control" name="date"/>
					<input type="hidden" name="rid" value="<?php echo $_POST["rid"] ?>"/>
					<span class="input-group-btn">
						<button type="submit" class="btn btn-primary">Update</button>
					</span>
				</div>
			</form>

			<?php
			}
		}
	}
}

function update_delivery_date() {

	if (empty($_POST["date"]) && isset($_POST["date"]) && isset($_POST["rid"])) { ?>

		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Error!</strong> Please input a delivery date.
		</div>

	<?php
	} else if (!empty($_POST["date"]) && isset($_POST["date"]) && isset($_POST["rid"])) {

		$date = mysql_real_escape_string($_POST["date"]);
		$rid = mysql_real_escape_string($_POST["rid"]);
		date_default_timezone_set('America/Los_Angeles');
		$curr_date = date('Y/m/d');
		
		$date = strtotime($date);
		$curr_date = strtotime($curr_date);

		if ($date > $curr_date) { ?>

			<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>Error!</strong> Invalid delivery date. Date cannot be greater than current date.
			</div>

		<?php
		} else {

			$SQL = "UPDATE Purchase
				SET deliveredDate = '$date'
				WHERE receiptId = '$rid'";

		$result = mysql_query($SQL, $GLOBALS['link']);

		if ($result) { ?>

			<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>Success!</strong> Delivery date set.
			</div>
<?php
		}
		}
	}
}
?>

<h2>Update Order</h2>

<p>
	
<form method="post" action="updateOrder.php">
	<div class="input-group" style="width: 300px">
  		<input type="text" class="form-control" name="rid" value="<?php if (isset($_POST['rid'])) { echo $_POST['rid']; } ?>" placeholder="Receipt ID"/>
  		<span class="input-group-btn">
  	  	<button type="submit" class="btn btn-default"> Submit </button>
  		</span>
	</div>
</form>

<?php
find_purchase();
update_delivery_date();	
print_page_end();
?>