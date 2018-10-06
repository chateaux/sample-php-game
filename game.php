<?php
session_start();
require_once("dbconnection.php");
include("functionClass.php");
$functionClass = new FunctionClass(); 

$currency = ( $_SESSION['gamblingtec']['currency'] != '' ) ? $_SESSION['gamblingtec']['currency'] : 'EUR';
require_once __DIR__.'/vendor/autoload.php';
  
if (!isset($_SESSION['gamblingtec']['access_token']) && $_SESSION['gamblingtec']['access_token']=="") {
   header("location: login.php");
}
   
    //$current_balance  = $functionClass->getCurrentBalances($_SESSION['gamblingtec']);  
    $functionClass->isAccessTokenExpired();
    $currencyDetails = $functionClass->getCurrencyDetails($currency); 

    $closeInactiveGameSession = $functionClass->closeInactiveGameSession();
    
    $error_message = "";
	  
    
	  if(isset($_POST['mode']) && $_POST['mode']=="gamesubmit"){
		 $bet_amount = filter_var($_POST['bet'], FILTER_SANITIZE_NUMBER_INT);
		 $gameColorValue = filter_var($_POST['gameColorValue'], FILTER_SANITIZE_NUMBER_INT);
		 
	 	 $bet_amount = ($bet_amount>0) ? $bet_amount : '1'; 
		 if($gameColorValue<=0){
			$error_message = "Please select color";
		 }
		 if($bet_amount<=0){
			$error_message = "Please enter your bet amount";
		 } 
		
		 if($error_message==""){
			$bet_amount_withexponent = $bet_amount * $currencyDetails['exponent']; 
			
			$currency_symbol = ($currencyDetails['left_symbol'] != '') ? $currencyDetails['left_symbol'] : $currencyDetails['code'];
			
			
		  
			$balanceDetails = $functionClass->getBalanceDetails($currency); 
			 
      
			if($balanceDetails['amount'] > 0 and  $balanceDetails['amount'] >= $bet_amount_withexponent){
        $gameOpenData = $functionClass->getGameOpenId($currency); 
        $betGame =   $functionClass->betGame($bet_amount_withexponent,$gameOpenData,$currency);
      
      $amount_won_only =   ($bet_amount * $connectionDetail['default']['winAmount']);
	   
       $isWinner = mt_rand (1,2);
       
			if($gameColorValue==$isWinner){
        $collectGame =   $functionClass->collectGame($bet_amount_withexponent,$gameOpenData,$currency);

			  $_SESSION['gamblingtec']['gameresult'] = "Congratulation you have won the game with  amount  $currency_symbol ".$amount_won_only;
			} else {
			  $_SESSION['gamblingtec']['gameresult'] = "Ohh! you lost the game , better luck next time.";
			}
			} else {
				 $error_message = "You don't have a sufficient amount in ".$currency." to play the game. Please add some funds <a href='deposit.php'>Click here</a>";
			}
			//header("location:game.php");
		 }
	}
//print_r($json_response);
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
 
<main role="main" class="inner text-center bodygame">
<?php if(isset($_SESSION['gamblingtec']['gameresult']) && $_SESSION['gamblingtec']['gameresult']!=""){
	?>
    	<div class="alert alert-success" role="alert">
         <?php echo $_SESSION['gamblingtec']['gameresult'];?>
        </div>
    <?php
	$_SESSION['gamblingtec']['gameresult'] = "";
	unset($_SESSION['gamblingtec']['gameresult']);

}?>
<?php if(isset($error_message) && $error_message!=""){
	?>
    	<div class="alert alert-danger" role="alert">
         <?php echo $error_message;?>
        </div>
    <?php
	$_SESSION['gamblingtec']['gameresult'] = "";
	unset($_SESSION['gamblingtec']['gameresult']);

}?>

       <div class="row py-md-3">
       <div class="col-md-9">
</div>
 <div class="col-md-3">
 <?php
    if(sizeof($current_balance)>0){
            ?>

                    <div class="card">
                    <div class="card-header">
                        Current Balance
                    </div>
                    <div class="card-body">
                           <?php
                             
                             foreach($current_balance as $key => $value){
                                 $amount_temp = $value['amount'];
                                 $amount = $amount_temp / $value['exponent'];
                                
                     ?> 
                            <div class="row">
                            <div class="col-sm-3"><?php echo $value['currency_type']?></div>	
                            <div class="col-sm-3"><?php echo $amount?></div>
                            </div> 
                            <?php } ?>
                    </div>
                    </div>
          
       
    
<?php } ?>
                             </div>
                             </div>
<h1 class="cover-heading">The simple Black or White games!</h1>
<p class="lead">
    This game is really simple, select either the black or white square, enter your bet amount, then click the play button.
</p>
  <form id="frmGame" name= "frmGame" action="" method="post">
 <input type="hidden" name="gameColorValue" id="gameColorValue" value="-1"  >
 <input type="hidden" name="mode" id="mode" value="gamesubmit" >
  <!-- Example row of columns -->
  <div class="row">
    <div class="col">
      <h2>Black</h2>
      <p id="p1"  class="  "  >
        <img src="assets/images/black.jpg" id="1" class=" gameColor  border-white"  >
       
      </p>
    </div>
    <div class="col">
      <h2>White</h2>
      <p id="p2" class=" ">
        <img src="assets/images/white.jpg" id="2" class=" gameColor  border-dark"   >
        
      </p>
    </div>
  </div>
   <div class="row">
    <div class="col-md-6">
      <div class="form-group row">
    <label for="mybet" class="col-sm-3 col-form-label">Enter your bet <php echo $currency??></label>
    <div class="col-sm-3">
       <input type="text"   class="form-control" id="bet" name="bet" value="1">
    </div>
  </div>

    </div>
  </div>  
  <div class="row">
    <div class="col-md-12">
      <div class="  text-center">
        <button class="btn btn-primary my-2" id="gamePlayBtn" name="gamePlayBtn" type="submit"  >Play</button>
      </div>
    </div>
    <hr>
  </div>
 </form>
  </main>
<?php include("footer.php");?>


<!-- Bootstrap core JavaScript
    ================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> 
<script>window.jQuery || document.write('<script src="assets/js/jquery-slim.min.js"><\/script>')</script> 
<script src="assets/js/popper.min.js"></script> 
<script src="assets/js/bootstrap.min.js"></script> 
<script>
 

 $(".gameColor").click(function(){
  
  // $("p").css("color", "red");
  var selctedColorId = $(this).attr("id");
  $("#gameColorValue").val(selctedColorId);
     $("#p1").removeClass("border border-success");
     $("#p2").removeClass("border border-success");  
    if(selctedColorId==1){
     $("#p1").addClass("border border-success");
	 
   } else if(selctedColorId==2){
     $("#p2").addClass("border border-success");
   }
  
 });
 
 </script>
</body>
</html>
