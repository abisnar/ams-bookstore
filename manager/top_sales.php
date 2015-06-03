<?php
require_once($_SERVER['DOCUMENT_ROOT']."/common/access.php");
require_once($_SERVER['DOCUMENT_ROOT']."/settings/mysql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/session_start.php");
require_once($_SERVER['DOCUMENT_ROOT']."/common/print_base.php");
has_access(MANAGER);
print_page_start();
print_manager_top(MANAGER_DD_TOP_SALES);

function top_sales() {

  if (isset($_POST['start_date']) && isset($_POST['end_date']) && (empty($_POST["start_date"]) || empty($_POST["end_date"]))) { ?>

    <div class="alert alert-danger" style="width: 885px">
      <button type="button" class="close" data-dismiss="alert"> </button>
      <strong> Error! </strong> One or more dates not set.
    </div>

  <?php
  } else if (!empty($_POST["start_date"]) && !empty($_POST["end_date"])){

    $start_date = mysql_real_escape_string($_POST["start_date"]);
    $end_date = mysql_real_escape_string($_POST["end_date"]);

    if (empty($_POST['top'])) {
     
      $top_items = 5;
    
    } else

      $top_items = mysql_real_escape_string($_POST["top"]);

    if ($start_date > $end_date) { ?>

      <div class="alert alert-danger" style="width: 885px">
        <button type="button" class="close" data-dismiss="alert"> </button>
        <strong> Error! </strong> Start date must before end date.
      </div>

    <?php
    } else if ($top_items < 0) { ?>

      <div class="alert alert-danger" style="width: 885px">
        <button type="button" class="close" data-dismiss="alert"> </button>
        <strong> Error! </strong> Number of top items must be greater than zero.
      </div>

    <?php
    } else {

    $SQL = "SELECT I.upc, I.title, I.category, SUM(PI.qty) AS units_sold, I.stock
    FROM Item I, PurchaseItem PI, Purchase P
    WHERE I.upc = PI.upc AND P.receiptId = PI.receiptId AND P.date BETWEEN '$start_date' AND '$end_date'
    GROUP BY I.upc
    ORDER BY units_sold DESC"; 

    
    $result = mysql_query($SQL, $GLOBALS['link']); 

    echo '<div class="panel">
            <table class="table table-striped">
            <thead>
             <tr>
               <td> # </td>
               <td> UPC </td>
               <td> Name </td>
               <td> Category</td>
               <td align="center"> Units Sold </td>
               <td align="center"> Current Stock </td>
             </tr>
            </thead>
            <tbody>';

    $i = 0;
    while($i < $top_items && $db_field = mysql_fetch_assoc($result)) { 
     
      echo '<tr>
              <td>' .($i + 1). '</td> 
              <td>' .$db_field["upc"]. '</td>
              <td>' .$db_field["title"]. '</td>
              <td>' .$db_field["category"]. '</td>
              <td align="center">' .$db_field["units_sold"]. '</td>
              <td align="center">' .$db_field["stock"]. '</td>
            </tr>';
      $i++;
    }
        echo '</tbody>
            </table>
          </div>';
  }
  }
}
?>
            
 
<h2>Top Selling Items</h2>

<p>

Please select a time period: <br>

<form method="post" action="top_sales.php">
<div class="row">
  <div class="col-lg-4">
    <div class="input-group">
     <span class="input-group-addon">From:</span>
      <input type="date" class="form-control" name="start_date" value="<?php if (isset($_POST['start_date'])) { echo $_POST['start_date']; } ?>"> 
    </div>
  </div>
  <div class="col-lg-4">
    <div class="input-group">
    <span class="input-group-addon">To:</span>
      <input type="date" class="form-control" name="end_date" value="<?php if (isset($_POST['end_date'])) { echo $_POST['end_date']; } ?>"> 
    </div>
  </div>
    <div class="col-lg-4">
    <div class="input-group">
      <input type="number" class="form-control" placeholder="# of Items..." name="top">
      <span class="input-group-btn">
        <button class="btn btn-default" type="submit" name="submit">Submit</button>
      </span>
    </div>
  </div>
</div>	
</form>

<?php
top_sales();
print_page_end();
?>
