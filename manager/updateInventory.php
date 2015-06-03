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

if(isset($_GET["error"])) {
	switch($_GET["error"]) {
		case 1: 
			$GLOBALS["error_msg"] = "Must select one of the choices in the dropdown!";
			break;
	}
} ?>

<h3>Add/Remove/Update Item</h3>

<div class="btn-group">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
    Choose an action <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
     <li><a tabindex="-1" href="add_item.php">Add Item</a></li>
    <li><a tabindex="-1" href="remove_item.php">Remove Item</a></li>
    <li><a tabindex="-1" href="update_item.php">Update Item</a></li>
  </ul>
</div>

<?php
if ((isset($_POST['dropdown']) == '1')){
	header("Location: add_item.php");
} else if ((isset($_POST['dropdown']) == '2')){
	header("Location: remove_item.php");
} else if ((isset($_POST['dropdown']) == '3')){
	header("Location: update_item.php");
}

print_page_end() ?>

