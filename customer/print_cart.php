<?php

require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/access.php");
has_access(CUSTOMER);
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");

function print_cart($checkout) {
	$return_html = "";
	$item_num = 0;
	$total = 0;
	if (isset($_SESSION["cart"])) {
		foreach($_SESSION["cart"] as $upc=>$quantity) {
			if($quantity > 0) {
				$sql = "SELECT title, year, price, company FROM Item WHERE upc = '$upc'";
				$result = mysql_query($sql, $GLOBALS['link']);
				if(mysql_num_rows($result) > 0) {
					$db_field = mysql_fetch_assoc($result);
					$return_html .= "<li class='list-group-item'><div class='' style='height: 40px;'>
					<div class='item_desc col-lg-8' style='padding-left: 0px;' >
					<span class='title'>".$db_field['title']."</span> (".$db_field['year'].")";
					$total += $db_field['price']*$quantity;
					$item_num += $quantity;
					$sql = "SELECT name FROM LeadSinger WHERE upc = '$upc'";
					$result_lead = mysql_query($sql, $GLOBALS['link']);
					if(mysql_num_rows($result_lead) > 0) {
						$db_field_lead = mysql_fetch_assoc($result_lead);
						$return_html .= '<br><span style="font-size: 70%">by '.$db_field_lead['name']."</span>";
					}
					$return_html .= '</div>
					<div class="cart_num col-lg-4" style="padding-right: 0;">';
					if($checkout) {
						$return_html .= "<div class='pull-right' style='margin-top: 15px;'><span class='label label-ams'>Price: CDN$ ".$db_field['price']."</span>";
						$return_html .= "<span class='label label-ams' style='margin-left: 10px'>Quantity: ".$quantity."</span></div>";
					} else {
						$return_html .= '<form method="post" id="update_cart" action="cart.php" style="margin: 0px;">
								<input type="hidden" name="upc" id="upc" value="'.$upc.'">
								<div style="width:150px; float: right;">
								<div class="input-group">
								<input type="input"  class="form-control" style="border-right-width: 0px;" name="quantity" id="upc_'.$upc.'" value="'.$quantity.'" size="3">
								  <span class="input-group-btn">
									<button type="submit" class="btn btn-ams" name="update_cart" id="update_cart">Update cart</button>
								  </span>
								</div>
								</div>
								</form>';
					}
					$return_html .= '</div></div></li>';
				} else {
					unset($_SESSION['cart'][$upc]);
				}
			} else {
				unset($_SESSION['cart'][$upc]);
			}
		}
	} 
	$return_html = "<ul class='list-group' style='margin-bottom:0px;'><li class='list-group-item'><b>You have $item_num items in your cart</b></li>".$return_html."</ul>";
	if($item_num > 0) {
		$return_html.= "<div style='margin-right:30px;'><h4 style='margin: 0px;'><span class='label label-ams label-ams-total pull-right' style=''>Total: CDN\$ $total</span></h4></div>";
	}
	$GLOBALS['item_num'] = $item_num;
	return $return_html;
}
?>