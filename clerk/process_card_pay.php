<?php
require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/access.php");
has_access(CLERK);
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");
require_once($_SERVER['DOCUMENT_ROOT']."/clerk/clerkfunctions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/print_base.php");
print_page_start();

$cardNo = $_POST['cardNo'];
$cardEx = $_POST['cardEx'];
submit_card_order($cardNo, $cardEx);

?>
<a href="index.php" class="btn btn-ams">Return to index.</a>
<?php print_page_end(); ?>