<?php
$current_balance = $functionClass->getCurrentBalances($_SESSION['gamblingtec']);

?>
<div class="row py-md-3">
 <div class="col-md-12">
  <ul class="list-group">
  <li class="list-group-item"><a href="dashboard.php">Dashboard</a></li>
  <li class="list-group-item"><a href="deposit.php">Deposit</a></li>
  <li class="list-group-item"><a href="withdraw.php">Withdraw</a></li>
  <li class="list-group-item"><a href="balance.php">Transactions</a></li>
   <li class="list-group-item"><a href="logout.php">Logout</a></li>
</ul>
</div>
</div>
<div class="row py-md-3">
 <div class="col-md-12">
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