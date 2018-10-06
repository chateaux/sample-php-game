<?php
session_start();
require_once("dbconnection.php");
include("functionClass.php");
$functionClass = new FunctionClass(); 
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

<main role="main">

  <section class="jumbotron text-center">
    <div class="container">
      <h1 class="jumbotron-heading">GamblingTec sample game</h1>
        <p class="lead text-muted">
          This game site has been created to assist game developers integrate into the GamblingTec.com API.
        </p>
        <p class="lead text-muted">
            You can find the sourcecode here: <a href="https://github.com/chateaux/sample-php-game">https://github.com/chateaux/sample-php-game</a>
        </p>
      <p>
        <a href="game.php" class="btn btn-primary my-2">Go To Game</a>
      </p>
    </div>
  </section>
 

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
