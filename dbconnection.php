<?php
// Create connection
# connect to the database
if ( function_exists( 'date_default_timezone_set' ) )
date_default_timezone_set("Europe/London");

define('DEBUG',true);
$connectionDetail = parse_ini_file("dbconfig.ini",true);
$host = $connectionDetail['database']['host'];
$dbname = $connectionDetail['database']['dbname'];
$user = $connectionDetail['database']['user'];
$pass = $connectionDetail['database']['pass'];
//print_r($connectionDetail);

try {
  $DBH = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
  $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
 
  
}
catch(PDOException $e) {
    echo "I'm sorry, Dave. I'm afraid I can't do that.";
	die();
   // file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
}

$_SESSION['gamblingtec']['currency'] = ($_SESSION['gamblingtec']['currency'] != '' ) ? $_SESSION['gamblingtec']['currency'] : $connectionDetail['default']['currency'];