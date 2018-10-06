<?php
$currencyList = $functionClass->getCurrencyList();
?>
<header>
  <div class="collapse bg-dark" id="navbarHeader">
    <div class="container">
      <div class="row">
        <div class="col-sm-8 col-md-7 py-4">
          <h4 class="text-white">About</h4>
          <p class="text-muted">
              This is a simple application designed to show game developers how to integrate into the GamblingTec.com
              application.
          </p>
        </div>
        <div class="col-sm-4 offset-md-1 py-4" style="color">
          <h4 class="text-white">Contact</h4>
          <ul class="list-unstyled">
            <li><a href="https://gtec.curacaowebhosting.com/" class="text-white">Our support desk</a></li>
            <li><a href="https://www.twitter.com/gamblingtec" class="text-white">Find us on twitter</a></li>
            <li><a href="#" class="text-white">Skype: brendanjnash</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container d-flex justify-content-between">
      <a href="index.php" class="navbar-brand d-flex align-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
        <strong>Game</strong>
      </a>
      <ul class="nav">
  <li class="nav-item">
    <a class="nav-link active" href="game.php">Game</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="dashboard.php">Dashboard</a>
  </li>
  <li class="nav-item">
  
  <div class="dropdown">
  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?php echo $_SESSION['gamblingtec']['currency'];?>
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
    <?php
    foreach($currencyList as $key => $valueCurrency){
      ?>
    <button class="dropdown-item" type="button" onClick="window.location.href='currencyset.php?currencyType=<?php echo $valueCurrency['code']?>&redirectto=<?php echo basename($_SERVER['PHP_SELF'])?>'"> <?php echo $valueCurrency['title']?></button> 
      <?php
    }
    ?>
    
    
  </div>
</div>
  
  </li>
  
</ul>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </div>
</header>