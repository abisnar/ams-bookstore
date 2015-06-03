<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
require_once($_SERVER['DOCUMENT_ROOT']."/common/access.php");
require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/print_base.php");
has_access(MANAGER);
print_page_start();
print_manager_top(MANAGER_DD_UPDATE_INVENTORY);

//gets called by later function depending on what form inputs are set
function update($i){
//----------Set input variables ----------------------
if (isset($_POST["UPC"])){
	$UPC = mysql_real_escape_string($_POST["UPC"]);
}
if (isset($_POST["Title"])){
	$title = mysql_real_escape_string($_POST["Title"]);
}
if (isset($_POST["Category"])){
	$category = mysql_real_escape_string($_POST["Category"]);
}
if (isset($_POST["Type"])){
	$type = mysql_real_escape_string($_POST["Type"]);
}
if (isset($_POST["Company"])){
	$company = mysql_real_escape_string($_POST["Company"]);
}
if (isset($_POST["Year"])){
	$year = mysql_real_escape_string($_POST["Year"]);
}
if (isset($_POST["Price"])){
	$price = mysql_real_escape_string($_POST["Price"]);
}
if (isset($_POST["Quantity"])){
	$stock = mysql_real_escape_string($_POST["Quantity"]);
}
if (isset($_POST["SongTitle"])){
	$songTitle = mysql_real_escape_string($_POST["SongTitle"]);
}
if (isset($_POST["SingerName"])){
	$singerName = mysql_real_escape_string($_POST["SingerName"]);
}
//----------------------------------------------------------------
// Switch function that assigns the coresponding query given the form filled out
	switch($i){

		// -----------------CASE 1: Update Item Title ---------------------------------
		case 1:
		//Check to make sure that the Title and UPC fields are not empty
		if (!empty($_POST["Title"]) && !empty($_POST["UPC"])){
			$checkTitle = "SELECT * FROM Item WHERE upc = '$UPC'";
			$checkExists = mysql_query($checkTitle);
			$num_rows = mysql_num_rows($checkExists);
		// Checks to make sure that the Item exists in our database
			if ($num_rows != 0){

			$SQL = "UPDATE Item
					SET title = '$title'
					WHERE upc = '$UPC'";

		// Our mySQL Query
			$result = mysql_query($SQL, $GLOBALS['link']);
			echo "The Title: ";echo $title; echo " has been successfully changed in UPC: "; echo $UPC; echo " successfully.";
		} else {
		echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> UPC Does Not Exist in database.
		</div>';
		}
		} else{
			echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> UPC and/or Title is empty. Please enter both a UPC and Title.
		</div>';
		}

		break;

		// ------------CASE 2: Add Songs to CD or DVD -------------------------------
		
		case 2:

		// check Fields are not empty
		if (!empty($_POST["SongTitle"]) && !empty($_POST["UPC"])){
			$checkDatabase = "SELECT upc FROM Item WHERE upc = '$UPC'";
			$upcResult = mysql_query($checkDatabase);
			$num_rows = mysql_num_rows($upcResult);
		// Checks to make sure that the Item exists in our database
			if ($num_rows != 0){
			$SQL = "INSERT into HasSong
					VALUES ('$UPC', '$songTitle')";
			$result = mysql_query($SQL);

			echo "The Song: ";echo $songTitle; echo " has been successfully added to UPC: "; echo $UPC; echo ".";
		}else {
			echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> UPC Does Not Exist in database.
		</div>';
		}
		}else{
			echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> UPC and/or Song Title is empty. Please enter both a UPC and a Song Title.
		</div>';
		}
		break;
		// ---------------CASE 3: REMOVE SONG FROM ITEM --------------------------------------------------
		case 3:
		//Checks Fields are not empty
		if (!empty($_POST["SongTitle"]) && !empty($_POST["UPC"])){
			$checkForSong = "SELECT * from HasSong WHERE upc = '$UPC' AND title = '$songTitle'";
			$songResult = mysql_query($checkForSong);
			$num_rows = mysql_num_rows($songResult);

		// Checks to make sure that the Item exists in our database
			if ($num_rows != 0){

				$SQL = "DELETE from HasSong
					WHERE upc = '$UPC'AND SongTitle ='$songTitle'";
			$result = mysql_query($SQL);
			echo "The Song: ";echo $songTitle; echo " has been successfully removed from UPC: "; echo $UPC; echo ".";

			}else{
			echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> The Song does not exist in the Database. Please ensure to write the correct Song Title and UPC.
		</div>';
		}
		}else{
			echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> UPC and/or Song Title is empty. Please enter both a UPC and a Song Title.
		</div>';
		}
		break;

		//--------------------CASE 4: ADD LEAD SINGER TO AN ITEM ----------------------------
		
		case 4:
		// checks to ensure fields are not empty
		if (!empty($_POST["SingerName"]) && !empty($_POST["UPC"])){
			$checkUPC = "SELECT * from Item WHERE upc = '$UPC'";
			$checkExists = mysql_query($checkUPC);
			$num_rows = mysql_num_rows($checkExists);

			// checks to make sure the item exists in our database
			if ($num_rows != 0){
			$SQL = "INSERT into LeadSinger VALUES ('$UPC', '$singerName')";
			$result = mysql_query($SQL);
			echo "The Singer: ";echo $singerName; echo " has been successfully assigned to UPC: "; echo $UPC; echo ".";
		} else { echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> The Item does not exist in the Database.
		</div>';
		}
		} else {
			echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> UPC and/or Singer field is empty. Please enter both a UPC and a Singer.
		</div>';
		}
		break;

		//---------------CASE 5: REMOVE LEAD SINGER ---------------------------------

		case 5:
		// checks the fields of the form
		if (!empty($_POST["SingerName"]) && !empty($_POST["UPC"])){
			$checkForName = "SELECT * from LeadSinger WHERE upc = '$UPC' AND name = '$singerName'";
			$singerResult = mysql_query($checkForName);
			$num_rows = mysql_num_rows($singerResult);

			// makes sure that the item is in the database
			if ($num_rows == 0){
			echo '<div class="alert-danger">
			<button class="close" data-dismiss="alert"> </button>
			<strong> Error!</strong> The provided singer is not associated with the given UPC value. Please check your fields.
			</div>';
			} else {
					$SQL = "DELETE from LeadSinger
							WHERE upc = '$UPC'and name = '$singerName'";
					$result = mysql_query($SQL);
					echo "The Singer: ";echo $_POST["SingerName"]; echo " has been successfully removed from UPC: "; echo $UPC; echo ".";
					} 
			} else {
					echo '<div class="alert-danger">
						<button class="close" data-dismiss="alert"> </button>
						<strong> Error!</strong> UPC and/or Singer field is empty. Please enter both a UPC and a Singer.
							</div>';
					}
		break;

		//-------------CASE 6: UPDATE ITEM TYPE ---------------------------------------

		case 6:

		// checks to see if the fields are not empty
		if (!empty($_POST["Type"]) && !empty($_POST["UPC"])){
			$checkUPC = "SELECT * from Item WHERE upc = '$UPC'";
			$checkExists = mysql_query($checkUPC);
			$num_rows = mysql_num_rows($checkExists);
 
			// checks database to see if item exists before it modifies
			if ($num_rows != 0){

			$SQL = "UPDATE Item
					SET Type = '$type'
					WHERE upc = '$UPC'";
			$result = mysql_query($SQL);
			echo "The Type has been successfully changed to: ";echo $_POST["Type"]; echo " in UPC: "; echo $UPC; echo ".";
		} else {
			echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> UPC Does Not Exist in database.
		</div>';
		}
		} else {
			echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> UPC and/or Type is empty. Please enter both a UPC and a Type.
		</div>';
		}
		break;

		//------------CASE 7: CHANGE STOCK--------------------------------------
		
		case 7:
		// checks the fields of the forms to make sure they are not empty
		if (!empty($_POST["Quantity"]) && !empty($_POST["UPC"])){
			$checkUPC = "SELECT * from Item WHERE upc = '$UPC'";
			$checkExists = mysql_query($checkUPC);
			$num_rows = mysql_num_rows($checkExists);

			// checks to make sure that the item exists in the database
			if ($num_rows != 0){
			$SQL = "UPDATE Item
					SET stock = '$stock'
					WHERE upc = '$UPC'";
			$result = mysql_query($SQL);
			echo "The Stock number has been successfully updated to : "; echo $_POST["Quantity"]; echo " in UPC: "; echo $UPC; echo ".";
		}else{
			echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> Item does not exist in our database.
		</div>';
		}
		}else{
			echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> UPC and/or Quantity field is empty. Please enter both a UPC and a Quantity > 0.
		</div>';
		}
		break;
		
		//----------------------CASE 8: CHANGE PRICE ----------------------------------------------

		case 8:

		// checks the fields of the form
		if (!empty($_POST["Price"]) && !empty($_POST["UPC"])){
			$checkUPC = "SELECT * from Item WHERE upc = '$UPC'";
			$checkExists = mysql_query($checkUPC);
			$num_rows = mysql_num_rows($checkExists);

			// checks to ensure item exists in the database
			if ($num_rows != 0){

			$SQL = "UPDATE Item
					SET price = '$price'
					WHERE upc = '$UPC'";
			$result = mysql_query($SQL);
			echo "The Price has been successfully updated to : "; echo $_POST["Price"]; echo " in UPC: "; echo $UPC; echo ".";
		}else{
			 '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> Item does not exist in our database.
		</div>';
		}
		}else{
			echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> UPC and/or Quantity field is empty. Please enter both a UPC and a Quantity > 0.
		</div>';
		}
		break;

		//--------------------CASE 9: CHANGE CATEGORY--------------------------------------------------

		case 9:

		//check fields of the form
		if (!empty($_POST["Category"]) && !empty($_POST["UPC"])){
			
			$checkUPC = "SELECT * from Item WHERE upc = '$UPC'";
			$checkExists = mysql_query($checkUPC);
			$num_rows = mysql_num_rows($checkExists);

			// checks to make sure that the item is in the database
			if ($num_rows != 0){
			$SQL = "UPDATE Item
					SET category = '$category'
					WHERE upc = '$UPC'";
			$result = mysql_query($SQL);
			echo "The Category has been successfully updated to : "; echo $_POST["Category"]; echo " in UPC: "; echo $UPC; echo ".";
		} else{ 
			'<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> Item does not exist in our database.
		</div>';
			}
		}
	else {
		echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> UPC and/or Category is empty. Please enter both a UPC and a Category.
		</div>';
	}
		break;

		//------------------------CASE 10: CHANGE YEAR----------------------------------------------------------

		case 10:
		// Check Form Fields not empty
		if (!empty($_POST["Year"]) && !empty($_POST["UPC"])){
			$checkTitle = "SELECT * FROM Item WHERE upc = '$UPC'";
			$checkExists = mysql_query($checkTitle);
			$num_rows = mysql_num_rows($checkExists);
		// Checks to make sure that the Item exists in our database
			if ($num_rows != 0){

			$SQL = "UPDATE Item
					SET year = '$year'
					WHERE upc = '$UPC'";

		// Our mySQL Query
			$result = mysql_query($SQL, $GLOBALS['link']);
			echo "The Year has been successfully changed to: ";echo $company; echo " in UPC: "; echo $UPC; echo ".";
		} else {
		echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> Item UPC Does Not Exist in database.
		</div>';
		}
		} else{
			echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> UPC and/or Company is empty. Please enter both a UPC and Year.
		</div>';
		}
		break;

		//-----------------------CASE 11: CHANGE COMPANY -----------------------------------------------
		
		case 11:
		// Check Form Fields not empty
		if (!empty($_POST["Company"]) && !empty($_POST["UPC"])){
			$checkTitle = "SELECT * FROM Item WHERE upc = '$UPC'";
			$checkExists = mysql_query($checkTitle);
			$num_rows = mysql_num_rows($checkExists);
		// Checks to make sure that the Item exists in our database
			if ($num_rows != 0){

			$SQL = "UPDATE Item
					SET company = '$company'
					WHERE upc = '$UPC'";

		// Our mySQL Query
			$result = mysql_query($SQL, $GLOBALS['link']);
			echo "The Company name has been successfully changed to: ";echo $company; echo " in UPC: "; echo $UPC; echo ".";
		} else {
		echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> Item UPC Does Not Exist in database.
		</div>';
		}
		} else{
			echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> UPC and/or Company is empty. Please enter both a UPC and Company.
		</div>';
		}
		break;

		default:
		echo "did not find case";
	}
}

// This calls update(i) according to the inputs filled
// When Change Item Title form is filled out call update(1)
if (!empty($_POST['action']) && $_POST['action'] == "1") {
	update(1);

// When Add Song Title form is filled out call update(2)
}else if (!empty($_POST['action']) && $_POST['action'] == "2"){
	update(2);

// When Delete Song Title form is filled out call update(3)
}else if (!empty($_POST['action']) && $_POST['action'] == "3") {
	update(3);

// When Add Lead Singer form is filled out call update(4)
}else if (!empty($_POST['action']) && $_POST['action'] == "4") {
	update(4);

// When Delete Lead Singer form is filled out call update(5)
}else if (!empty($_POST['action']) && $_POST['action'] == "5") {
	update(5);

// When Change Item Title form is filled out call update(6)
}else if (!empty($_POST['action']) && $_POST['action'] == "6") {
		//make sure the type is either cd or dvd
			if (mysql_real_escape_string($_POST["Type"]) == "cd" ||
			 mysql_real_escape_string($_POST["Type"]) == "dvd") {
				update(6);
		} else{
		echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> Type needs or be cd or dvd.
		</div>';
		}

// When Change Item Type form is filled out call update(7)
}else if (!empty($_POST['action']) && $_POST['action'] == "7") {
	update(7);


// When Change Item Price form is filled out call update(8)
}else if (!empty($_POST['action']) && $_POST['action'] == "8") {
	update(8);

// When Change Item Category form is filled out call update(9)
}else if (!empty($_POST['action']) && $_POST['action'] == "9") {

	//check to make sure that the Category is of right type
	if (mysql_real_escape_string($_POST["Category"]) == "rock" || mysql_real_escape_string($_POST["Category"]) == "pop" || 
		mysql_real_escape_string($_POST["Category"]) == "rap" || mysql_real_escape_string($_POST["Category"]) == "country" ||
		mysql_real_escape_string($_POST["Category"]) =="classical" ||mysql_real_escape_string($_POST["Category"]) =="new age" ||
		mysql_real_escape_string($_POST["Category"]) == "instrumental"){
		update(9);
	}
	else{
		echo '<div class="alert-danger">
		<button class="close" data-dismiss="alert"> </button>
		<strong> Error!</strong> Invalid Category Type.
		</div>';
		}


// When Change Item Year form is filled out call update(10)
}else if (!empty($_POST['action']) && $_POST['action'] == "10") {
	update(10);

// When Change Item Company form is filled out call update(1)
}else if (!empty($_POST['action']) && $_POST['action'] == "11") {
	update(11);
}

?>


<h2>Update Item</h2>


<h3>Change Item Title</h3>
<form method="post" action="update_item.php" class="form-inline" id="1">
	<input type="text" style="width: 300px" class="form-control" placeholder="UPC" name="UPC"<?php if(isset($UPC)){ echo " value=$UPC";} ?>>
	<input type="text" style="width: 300px" class="form-control" placeholder="Title" name="Title"<?php if(isset($title)){ echo " value=$title";} ?>>
	<input type="hidden" name="action" value="1">
<button type="submit" class="btn btn-primary"name="changeTitle">OK</button>

</form>

<h3>Add Song Title</h3>
<form method="post" action="update_item.php" class="form-inline" id="2">
	<input type="text" style="width: 300px" class="form-control" placeholder="UPC" name="UPC"<?php if(isset($UPC)){ echo " value=$UPC";} ?>>
	<input type="text" style="width: 300px" class="form-control" placeholder="Song Title" name="SongTitle"<?php if(isset($songTitle)){echo "value= $songTitle";} ?>>
	<input type="hidden" name="action" value="2">
<button type="submit" class="btn btn-primary"name="addSong">OK</button>
</form>

<h3>Delete Song Title</h3>
<form method="post" action="update_item.php" class="form-inline" id="3">
	<input type="text" style="width: 300px" class="form-control" placeholder="UPC" name="UPC"<?php if(isset($UPC)){ echo " value=$UPC";} ?>>
	<input type="text" style="width: 300px" class="form-control" placeholder="Song Title" name="SongTitle"<?php if(isset($songTitle)){echo "value= $songTitle";} ?>>
	<input type="hidden" name="action" value="3">
<button type="submit" class="btn btn-primary"name="removeSong">OK</button>
</form>

<h3>Add Lead Singer</h3>
<form method="post" action="update_item.php" class="form-inline" id="4">
	<input type="text" style="width: 300px" class="form-control" placeholder="UPC" name="UPC"<?php if(isset($UPC)){ echo " value=$UPC";} ?>>
	<input type="text" style="width: 300px" class="form-control" placeholder="Singer Name" name="SingerName"<?php if(isset($singerName)){echo "value=$singerName";} ?>>
	<input type="hidden" name="action" value="4">
<button type="submit" class="btn btn-primary">OK</button>
</form>

<h3>Remove Lead Singer</h3>
<form method="post" action="update_item.php" class="form-inline" id="5">
	<input type="text" style="width: 300px" class="form-control" placeholder="UPC" name="UPC"<?php if(isset($UPC)){ echo " value=$UPC";} ?>>
	<input type="text" style="width: 300px" class="form-control" placeholder="Singer Name" name="SingerName"<?php if(isset($singerName)){echo "value=$singerName";} ?>>
	<input type="hidden" name="action" value="5">
<button type="submit" class="btn btn-primary">OK</button>
</form>

<h3>Change Item Type</h3>
<form method="post" action="update_item.php" class="form-inline" id="6">
	<input type="text" style="width: 300px" class="form-control" placeholder="UPC" name="UPC"<?php if(isset($UPC)){ echo " value=$UPC";} ?>>
	<input type="text" style="width: 300px" class="form-control" placeholder="Type" name="Type"<?php if(isset($type)){ echo " value=$title";} ?>>
	<input type="hidden" name="action" value="6">
<button type="submit" class="btn btn-primary">OK</button>
</form>

<h3>Update Stock Quantity</h3>
<form method="post" action="update_item.php" class="form-inline" id="7">
	<input type="text" style="width: 300px" class="form-control" placeholder="UPC" name="UPC"<?php if(isset($UPC)){ echo " value=$UPC";} ?>>
	<input type="text" style="width: 300px" class="form-control" placeholder="Quantity" name="Quantity"<?php if(isset($stock)){ echo " value=$stock";} ?>>
	<input type="hidden" name="action" value="7">
<button type="submit" class="btn btn-primary">OK</button>
</form>

<h3>Change Price</h3>
<form method="post" action="update_item.php" class="form-inline" id="8">
	<input type="text" style="width: 300px" class="form-control" placeholder="UPC" name="UPC"<?php if(isset($UPC)){ echo " value=$UPC";} ?>>
	<input type="text" style="width: 300px" class="form-control" placeholder="Price" name="Price"<?php if(isset($price)){ echo " value=$price";} ?>>
	<input type="hidden" name="action" value="8">
<button type="submit" class="btn btn-primary">OK</button>
</form>

<h3>Change Item Category</h3>
<form method="post" action="update_item.php" class="form-inline" id="9">
	<input type="text" style="width: 300px" class="form-control" placeholder="UPC" name="UPC"<?php if(isset($UPC)){ echo " value=$UPC";} ?>>
	<input type="text" style="width: 300px" class="form-control" placeholder="Category" name="Category"<?php if(isset($category)){ echo " value=$category";} ?>>
	<input type="hidden" name="action" value="9">
<button type="submit" class="btn btn-primary">OK</button>
</form>

<h3>Change Year</h3>
<form method="post" action="update_item.php" class="form-inline" id="10">
	<input type="text" style="width: 300px" class="form-control" placeholder="UPC" name="UPC"<?php if(isset($UPC)){ echo " value=$UPC";} ?>>
	<input type="text" style="width: 300px" class="form-control" placeholder="UPC" name="Year"<?php if(isset($year)){ echo " value=$year";} ?>>
	<input type="hidden" name="action" value="10">
<button type="submit" class="btn btn-primary">OK</button>
</form>

<h3>Change Item Company</h3>
<form method="post" action="update_item.php" class="form-inline" id="11">
	<input type="text" style="width: 300px" class="form-control" placeholder="UPC" name="UPC"<?php if(isset($UPC)){ echo " value=$UPC";} ?>>
	<input type="text" style="width: 300px" class="form-control" placeholder="Company" name="Company"<?php if(isset($company)){ echo " value=$company";} ?>>
	<input type="hidden" name="action" value="11">
<button type="submit" class="btn btn-primary">OK</button>
</form>
<?php
print_page_end();
?>
