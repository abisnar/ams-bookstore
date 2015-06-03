<?php

require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/access.php");
has_access_only(CUSTOMER);
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/print_base.php");

print_page_start();

if(isset($_GET["days"]) && isset($_GET["rid"])) {
	echo "<p>Your purchase was successful! The delivery with take approximately ".$_GET["days"]." days. Your order confirmation number is #".$_GET["rid"];
	echo "</p><a class='btn btn-ams' href=\"index.php\">Return to store!</a>";		
	print_page_end();
	exit;
}

?>
<h1>Checkout</h1>
<script>
$(document).ready(function () {
		$("#error").hide();
		
		$("#submit_purchase").click(function() {
			var data = $("#purchase_form").serialize();
			$.ajax({
				   type: "POST",
				   url: "purchase.php",
				   data: data,
				   success: function(data) {
						var code = data.substr(0,1);
						if(code == "0") {
							var lines = data.split(',');
							window.location.replace("checkout.php?days=" + lines[1] + "&rid=" + lines[2]);
						} else if(code == "1") {
							$("#error").html("Your cart is empty or you have not provided all the required information" );
						} else if(code == "2") {
							$("#error").html("Your cart is empty!");
						} else if(code == "3") {
							$("#error").html("Please choose one of Visa, American Express, or Mastercard. Other cards are not supported");
						} else if(code == "4") {
							$("#error").html("Parsing credit card number failed. Please try again (must be at least 14 digits and numeric)");
						} else if(code == "5") {
							$("#error").html("Invalid expiry date! " + data);
						} else {
							$("#error").html("Unknown error!" + data);
						}
						
						if(!(code == "0")) {
							$("#error").show();
						}
				   }
				 });

			return false; // avoid to execute the actual submit of the form.
		});	

});
</script>

<?php

require_once("print_cart.php");
echo print_cart(true);

if($GLOBALS['item_num'] < 1) {
	header("Location: cart.php?error=1");
}

?>
<br>
<br>
<br>
<div class="error alert alert-danger" id="error">';
	<button type="button" class="close" data-dismiss="alert">x</button>';
</div>
<div class="panel">
<div class="panel-heading">
Please enter your credit card information to complete your 
	purchase. 
</div>

<form method="post" action="purchase.php" id="purchase_form">
	<div class="input-group" style="width: 400px;">
	<span class="input-group-addon">Card Type: </span>
	<select name="card_type" class="form-control">
		<option value="visa">VISA</option>
		<option value="mc">Mastercard</option>
		<option value="ae">American Express</option>
	</select>
	</div>
	<br>   
	<input type="text" class="form-control" placeholder="Card No." style="width: 400px" name="card_no">
	<br>
	<div class="input-group" style="width: 400px;">
	<select class="form-control" style="width: 200px; border" id="card_exp_m" name="card_exp_m">
        <option value="1">January</option>
        <option value="2">February</option>
        <option value="3">March</option>
        <option value="4">April</option>
        <option value="5">May</option>
        <option value="6">June</option>
        <option value="7">July</option>
        <option value="8">August</option>
        <option value="9">September</option>
        <option value="10">October</option>
        <option value="11">November</option>
        <option value="12">December</option>
	</select>
	<select class="form-control" placeholder="Expiry Date" style="width: 200px" id="card_exp_y" name="card_exp_y">
		<option value="2013">2013</option>
		<option value="2014">2014</option>
		<option value="2015">2015</option>
		<option value="2016">2016</option>
		<option value="2017">2017</option>
		<option value="2018">2018</option>
		<option value="2019">2019</option>
		<option value="2020">2020</option>
	</select>
	</div>
	<br>
	<input type="submit" class="btn btn-success" name="submit_purchase" id="submit_purchase" value="Purchase">
</form>
</div>
<?php print_page_end(); ?>