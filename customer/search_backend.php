<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/access.php");
has_access(CUSTOMER);
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");

function search_page() {
	
	// check that a search parameter was inputted
	if (isset($_GET["search1"])) {

		// Put the search and inputs into a single array
		$search_terms = array();
		foreach($_GET as $key => $value) {
		   if(preg_match("/search.*/", $key)) {
				$search_terms[preg_replace("/[^0-9]+/", "", $key)] = array ($value, "");
		   } else if(preg_match("/input.*/", $key)) {
				$search_terms[preg_replace("/[^0-9]+/", "", $key)][1] = $value;
		   }
		}
		
		// Check for duplicate search terms
		foreach($search_terms as $term) {
			foreach($search_terms as $term2) {
				if (($term[0] == $term2[0]) && ($term[1] != $term2[1]) && ($term2[1] == 0 || !empty($term2[1])) && ($term[1] == 0 || !empty($term[1]))) {
					$GLOBALS["error_msg"] = "Cannot search with two different $term[0]";
					return;
				}
			}
		}
		
		// Check for duplicate search terms
		$empty = 0;
		foreach($search_terms as $term) {
			if ($term[1] == 0 || !empty($term[1])) {
				$empty = 1;
			}
		}
		if($empty == 0) {
			$GLOBALS["error_msg"] = "No search terms provided!";
			return;
		}
		
		$lead_singer = 0;
		
		// Build the SQL query with all availbale search parameters
		$sql1 = "SELECT Item.upc, title, stock, type, price, category, name FROM Item, LeadSinger WHERE Item.upc = LeadSinger.upc AND";
		$sql2 = "SELECT upc, title, stock, type, price, category, NULL AS name FROM Item WHERE  Item.upc NOT IN (SELECT upc FROM LeadSinger) AND";
		foreach($search_terms as $term) {
			if ($term[1] == 0 || !empty($term[1])) {
				if ($term[0] == "upc" || $term[0] == "type" || $term[0] == "category") {
					$sql1 .=" Item.$term[0] = '$term[1]' AND";
					$sql2 .=" Item.$term[0] = '$term[1]' AND";
				} else if ($term[0] == "price") {
					$sql1.=" Item.$term[0] <= '$term[1]' AND";
					$sql2.=" Item.$term[0] <= '$term[1]' AND";
				} else if ($term[0] == "title") {
					$sql1.=" Item.$term[0] LIKE '%$term[1]%' AND";
					$sql2.=" Item.$term[0] LIKE '%$term[1]%' AND";
				} else if ($term[0] == "leadsinger") {
					$lead_singer = 1;
					$sql1.=" LeadSinger.name LIKE '%".$term[1]."%' AND";
				}
			}
		}
		
		$sql1 = substr($sql1, 0, -4);
		$sql2 = substr($sql2, 0, -4);			
		
		if(!$lead_singer) {
			$sql = "(".$sql1.") UNION (".$sql2.")";
		} else {
			$sql = $sql1;
		}
		
	
		
		$result = mysql_query($sql, $GLOBALS['link']) or die(mysql_errno($GLOBALS['link']).": ".mysql_error($link)."\n");
		$num_rows = mysql_num_rows($result);
		
		if($num_rows == 0) {
			echo "<i>0 items matched</i>";
		} else {
		
			echo '<div class="panel"><table class="table table-condensed table-ams">
					<thead>
						<tr>
							<td><b>Title</b></td>
							<td><b>Type</b></td>
							<td><b>Price</b></td>
							<td><b>Category</b></td>
							<td align="center"><b>Quantity</b></td>
						</tr>
					</thead>
					<tbody>
			';
		
			while($db_field = mysql_fetch_assoc($result)) {
				echo '<tr>
							<td>'.$db_field['title'];
				if(!is_null($db_field['name'])) { echo '<br><span style="font-size: 70%">by '.$db_field['name']."</span>"; }
				echo '</td>
							<td><div style="padding-top: 10px;"><span class="label label-light">'.$db_field['type'].'</span></div></td>
							<td><div style="padding-top: 10px;"><span class="label label-light">'.$db_field['category'].'</span></div></td>
							<td><div style="padding-top: 10px;"><span class="label label-success">CDN$ '.$db_field['price'].'</span></div></td>';
					if ($db_field['stock'] > 0) {
					echo '<td align="right">
					<form method="post" id="purchase_item" action="cart.php" style="margin-bottom:0px">
							<input type="hidden" name="upc" id="upc" value="'.$db_field['upc'].'">							
							<div class="input-group" style="width: 150px; min-height:35px;">
								<input type="input"  class="form-control" style="border-right-width: 0px;" name="quantity" id="upc_'.$db_field['upc'].'" value="1" size="3">
								  <span class="input-group-btn">
									<button type="submit" class="btn btn-ams" name="add_to_cart" id="add_to_cart">Add to cart</button>
								  </span>
							</div>
				   </form>
						  </td>
				        </tr>
				        ';
				  	} else {
				  		echo '<td align="right" ><div style="padding-top: 15px; min-height:40px;"><span class="label label-danger"> Out of Stock</span></div></td>
				  				</tr>
				  				';
					}			
			}
			
			echo '</tbody></table></div>';
		}
	}
}

echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>';
$error_msg = "";
search_page();

if(!empty($error_msg)) {
	echo "<div class=\"error\">Error: $error_msg</div>";
}

?>