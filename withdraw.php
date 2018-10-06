<?php
session_start();
require_once("dbconnection.php");
include("functionClass.php");
$functionClass = new FunctionClass(); 
if (!isset($_SESSION['gamblingtec']['access_token']) && $_SESSION['gamblingtec']['access_token']=="") {
//    header("location: login.php");
}
require_once __DIR__.'/vendor/autoload.php';
  
$functionClass->isAccessTokenExpired();

$withdraw_auth_url = "https://sandbox.gamblingtec.com/widget/balances"; 
  
        
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
  <div class="col-sm-8"> <h1 class="jumbotron-heading">Withdraw</h1>
  
  <p class="lead text-muted">Add some information about the game below, the author, or any other background context. Make it a few sentences long so folks can pick up some informative tidbits. Then, link them off to some social networking sites or contact information.</p>
      <p>
        <a href="<?php echo $withdraw_auth_url;?>" class="btn btn-primary my-2">Withdraw from Gambling Tec</a>
        
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
