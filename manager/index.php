<?php
require_once($_SERVER['DOCUMENT_ROOT']."/common/access.php");
require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/print_base.php");
has_access(MANAGER);
print_page_start();
print_manager_top(MANAGER_DD_INDEX);
?>
<h2> Announcements </h2>
<p>
	Any important manager related notices will appear here for any accessing manager to attend to 
	or be aware of.
		
<?php 
print_page_end() ?>
