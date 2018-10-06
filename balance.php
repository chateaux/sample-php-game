<?php
session_start();
require_once("dbconnection.php");
include("functionClass.php");
 $functionClass = new FunctionClass(); 
 
if (!isset($_SESSION['gamblingtec']['access_token']) && $_SESSION['gamblingtec']['access_token']=="") {
   header("location: login.php");
}
require_once __DIR__.'/vendor/autoload.php';
$functionClass->isAccessTokenExpired();
//$currency = $_SESSION['gamblingtec']['currency'];
$getTransactions = $functionClass->getTransactions(); 
$current_balance = $functionClass->getCurrentBalances($_SESSION['gamblingtec']);
        
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" href="assets/favicon.ico">
<title>Game Template</title>

<!-- Bootstrap core CSS -->
<link href="assets/css/bootstrap.min.css" rel="stylesheet">
 <!-- Custom styles for this template -->
 <link href="assets/css/album.css" rel="stylesheet">
</head>

<body>
<?php include("header.php");?>

<main role="main" class="inner py-md-3">
 	 <div class="container">
       
       <div class="row">
  <div class="col-sm-4"><?php include("leftmenu.php");?></div>	
  <div class="col-sm-8"><h1 class="jumbotron-heading">Transactions</h1>
  
  
  <table class="table table-bordered">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Date</th>
      <th scope="col">Type</th>
      <th scope="col">Identity</th>
      <th scope="col">Amount</th>
    </tr>
  </thead>
  <tbody>
  <?php
  	foreach($getTransactions  as $key => $value){
		$amount_temp = $value['amount'];
		$amount_temp = $amount_temp / $value['exponent'];
	//	$amount_total = $amount_total + $amount_temp;
		if($amount_temp<=0){
			$amount = $amount_temp * -1;
			$amount_temp1 = ' - ';
			
		} else {
			$amount = $amount_temp;
			$amount_temp1 = ' + ';
		}
		 $currency_symbol = ($value['left_symbol'] != '') ? $value['left_symbol'] : $value['code'];
		 $amount_temp2 = $amount_temp1 . $currency_symbol . ' '. $amount;
  ?>
    <tr>
      <td ><?php echo $functionClass->convertDateFormat($value['created_date'],'d-M-Y')?></td>
      <td><?php echo $value['trans_type']?></td>
      <td><?php echo $value['identity']?></td>
      <td><?php echo $amount_temp2;?></td>
    </tr>
   <?php }?>
    
  </tbody>
</table>

 
  
  </div>
 
</div>
    </div>

</main>

<?php include("footer.php");?>

<!-- Bootstrap core JavaScript
    ================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> 
<script>window.jQuery || document.write('<script src="assets/js/jquery-slim.min.js"><\/script>')</script> 
<script src="assets/js/popper.min.js"></script> 
<script src="assets/js/bootstrap.min.js"></script> 
 
</body>
</html>
