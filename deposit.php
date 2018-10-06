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

//$json_response = $functionClass->getResourceOwnerDetails($_SESSION['gamblingtec']['access_token'],'client_credentials');
/*
$response = json_decode( $json_response['content'], true );	

 //calling function to add uuid and username in db
 if(sizeof($response)>0){
	$addUsername = $functionClass->updateUserName($response);	
 }
*/
$deposite_url = "https://sandbox.gamblingtec.com/widget/launch/".$connectionDetail['oauth']['gameProviderUuid']."?type=deposit&return=http://samplegame.gtec.io/depositsuccess.php";
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
    <div class="row ">
      <div class="col-sm-4">
        <?php include("leftmenu.php");?>
      </div>
      <div class="col-sm-8">
        <h1 class="jumbotron-heading">Deposit</h1>
         <p class="lead text-muted">
             When making a deposit, your game must re-direct the user to the GamblingTec.com wallet pages where the user
             can make a payment by credit card or e-wallet and on completion, the funds will be transferred to the game.
         </p>
      <p>
        <a href="<?php echo $deposite_url;?>" class="btn btn-primary my-2">Deposit</a>
      </p>
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
