<?php
session_start();
require_once("dbconnection.php");


unset($_SESSION['gamblingtec']['access_token']);
unset($_SESSION['gamblingtec']['expires_in']);
unset($_SESSION['gamblingtec']['token_type']);
unset($_SESSION['gamblingtec']['scope']);
unset($_SESSION['gamblingtec']['refresh_token']);
unset($_SESSION['gamblingtec']['created']);
unset($_SESSION['gamblingtec']['created']);
unset($_SESSION['gamblingtec']['created']);

 
session_destroy();

header("Location: index.php");
exit; 

//print_r($json_response);

 