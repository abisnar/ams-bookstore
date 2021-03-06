<?php

require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/access.php");
has_access(CUSTOMER);
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/print_base.php");

// Print error messages to user
if(isset($_GET["error"])) {
	switch($_GET["error"]) {
		case 0:
			$GLOBALS["error_msg"] = "Not enough item in stock!";
			break;
		case 1: 
			$GLOBALS["error_msg"] = "The item you requested does not exist!";
			break;
		case 2:
			$GLOBALS["error_msg"] = "Must be a customer to make online purchases. If you are a clerk/manager please sign up for a customer account to make purchases for yourself!";
			break;
	}
}

// Print success messages to users
if(isset($_GET["success"])) {
	switch($_GET["success"]) {
		case 0:
			$GLOBALS["success_msg"] = "Sucessfully registered! You can start searching for items below.";
			break;
	}
}


print_page_start();

?>
<h3>Search</h3>
<br>

<div id="container">	
	<div class="panel">
		<form method="get" id="search_form">
		<div>
		<br>
				<span onclick="return false;" id="addVar" class="label label-success">add search terms</span>
				<br><br>
				<input type="submit" value="Search" name="submit" class="btn btn-primary" id="search_submit">
		</div>
		</form>
	</div>
</div>
	
<script>
	// This section of javascript is responsible to dynamically adding and removing search terms.

	$(document).ready(function() {
		var startingNo = 0;
		var node = "";
		for(varCount=0;varCount<=startingNo;varCount++){
			var displayCount = varCount+1;
			node += printDropdown(displayCount, 0);
			runDropdown();
		}

		$('#search_form').prepend(node);
		
		$('#search_form').on('click', '.removeVar', function(){
			$(this).parent().remove();
		});

		$('#addVar').on('click', function(){
			//new node
			varCount++;
			node = printDropdown(varCount, 1);
			$(this).parent().before(node);
			runDropdown();
		});
		
		if(window.location.hash.length > 0) {
			$.ajax({
				   type: "GET",
				   url: "search_backend.php",
				   data: window.location.hash.substring(1, window.location.hash.length),
				   success: function(data)
				   {
					  $("#search_results").html(data);
				   }
			});
		}
		
		$("#search_submit").click(function() {

			var data = $("#search_form").serialize();
			window.location.hash = data;

			$.ajax({
				   type: "GET",
				   url: "search_backend.php",
				   data: data,
				   success: function(data)
				   {
					  $("#search_results").html(data);
				   }
				 });

			return false; // avoid to execute the actual submit of the form.
		});
		
	});
	
	
</script>

<div id="search_results">

</div>
<?php
print_page_end();
?>
