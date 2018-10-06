<?php
session_start();
require_once("dbconnection.php");
include("functionClass.php");
$functionClass = new FunctionClass(); 
$_SESSION['gamblingtec']['currency'] = $connectionDetail['default']['currency'];
if(isset($_GET['currencyType']) && $_GET['currencyType']!="") {
  $currencyType = filter_var($_GET['currencyType'], FILTER_SANITIZE_STRING );
  $redirectto = filter_var($_GET['redirectto'], FILTER_SANITIZE_STRING );
  $currencyDetails = $functionClass->getCurrencyDetails($currencyType); 
  if(sizeof($currencyDetails)>0){
    $_SESSION['gamblingtec']['currency'] = $currencyDetails['code'];
    header("location: ".$redirectto);
  } 
  
}
?>