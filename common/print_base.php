<?php
require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/access.php");

function print_header() {
	echo '<div class="header" style="margin-top: 30px;">';
	echo '<div class="header-left">';
	echo '<a href="http://'.$_SERVER['SERVER_NAME'].'/index.php" class="btn btn-ams">ams music.</a>';
	echo '</div>';
	if(has_access(CUSTOMER, 0, 0)) {
		echo '<div class="header-name">';
			echo '<div class="btn-group">';
			echo '<button class="btn btn-ams dropdown-toggle" data-toggle="dropdown" />';
			echo 'signed in as <b>'.$_SESSION['username'].'</b><span class="caret caret-black"></span>';
			echo '</button>';
			echo '<ul class="dropdown-menu">';
			if(has_access(MANAGER, 0, 0)) {
				echo '<li><a href="http://'.$_SERVER['SERVER_NAME'].'/manager/index.php">manager view</a></li>';
			}
			if(has_access(CLERK, 0, 0)) {
				echo '<li><a href="http://'.$_SERVER['SERVER_NAME'].'/clerk/index.php">clerk view</a></li>';
				echo '<li><a href="http://'.$_SERVER['SERVER_NAME'].'/customer/index.php">customer view</a></li>';
				echo '<li class="divider"></li>';
			}
			echo '<li><a href="http://'.$_SERVER['SERVER_NAME'].'/common/logout.php">log out</a></li>';
			echo '</ul>';
			echo '</div>';
		echo '</div>';
	}
	echo '<div class="header-cart">';
	if(has_access_only(CUSTOMER, 0, 0)) {
		$item_num = 0;
		if(isset($_SESSION["cart"])) {
			foreach($_SESSION["cart"] as $upc=>$quantity) {
				if ($quantity > 0) {
					$item_num += $quantity;
				}
			}
		} 
		echo "<a href='cart.php' class='btn btn-ams'>view cart ($item_num)</a>";
	}
	echo '</div>';
	echo '</div>';
}

function print_page_start() {
	echo '<html>
		<head>
			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
			<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
			<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0-rc1/js/bootstrap.min.js"></script>
			<script src="//'.$_SERVER['SERVER_NAME'].'/dropdown.js"></script>
			<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0-rc1/css/bootstrap.min.css">
			<link rel="stylesheet" type="text/css" media="screen" href="//tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css">
			<link href="//'.$_SERVER['SERVER_NAME'].'/style.css" rel="stylesheet" type="text/css" />
		</head>
		<body>
			<div class="container" id="container">';
			print_header();
	echo '		<div class="body img-rounded" id="body">';
	if (!empty($GLOBALS['error_msg'])) { 
		echo '<div class="error alert alert-danger">';
		echo '<button type="button" class="close" data-dismiss="alert">x</button>';
		echo $GLOBALS['error_msg'];
		echo '</div>';
	}
	if (!empty($GLOBALS['success_msg'])) { 
		echo '<div class="error alert alert-success">';
		echo '<button type="button" class="close" data-dismiss="alert">x</button>';
		echo $GLOBALS['success_msg'];
		echo '</div>';
	}

}

function print_page_end() {
echo '		</div>
		</div>
	</body>
</html>';
}

function print_manager_top($active) {
	echo '<h1>Manager</h1>
	<ul class="breadcrumb" style="width: 885px">
		<li class="'.(($active == MANAGER_DD_INDEX) ? "active" : "").'"><a href="http://'.$_SERVER['SERVER_NAME'].'/manager/index.php">Home</a></li>
		<li class="'.(($active == MANAGER_DD_UPDATE_INVENTORY) ? "active" : "").'"><a href="http://'.$_SERVER['SERVER_NAME'].'/manager/updateInventory.php">Add/Remove/Update Item</a></li>
		<li class="'.(($active == MANAGER_DD_SALES_REPORT) ? "active" : "").'"><a href="http://'.$_SERVER['SERVER_NAME'].'/manager/sales_report.php">Print Daily Sales Report</a></li>
		<li class="'.(($active == MANAGER_DD_UPDATE_ORDER) ? "active" : "").'"><a href="http://'.$_SERVER['SERVER_NAME'].'/manager/updateOrder.php">Update Order</a></li>
		<li class="'.(($active == MANAGER_DD_TOP_SALES) ? "active" : "").'"><a href="http://'.$_SERVER['SERVER_NAME'].'/manager/top_sales.php">Print Top Selling Items</a></li>
	</ul>';
}


?>