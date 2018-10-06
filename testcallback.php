<?php
session_start();
require_once("dbconnection.php");
include("functionClass.php");
$functionClass = new FunctionClass();
 
echo "test ";

$getAccessTokenForSale = $functionClass->getAccessTokenForSale();

print_r($getAccessTokenForSale);
die();
$private_key = '501ee1944b81bb7018c7d10316854971';
$hex = hex2bin("501ee1944b81bb7018c7d10316854971");
//var_dump($hex);

$public_key = '501ee1944b81bb7018c7d10316854971';
if($_SERVER['HTTP_HOST'] == '127.0.0.1'){
	 $apiUrl = 'http://127.0.0.1/samplegame/callbackaction.php';
} else {
	$apiUrl = 'http://samplegame.gtec.io/callbackaction.php';
}
$method = "getBalances";

if($method=="getBalances"){
    $message = json_encode(
        array('jsonrpc' => '2.0', 'id' => 1, 'method' => 'getBalances', 'params' => array('userUuid'=>'8b96093d-3bc7-4c3d-a46a-ea27bfeae166'))
    );
	 
	//	 $hex = $private_key; 
	 $hex = hex2bin($private_key); 
    $sign = hash_hmac('sha256', $message, $hex);
    $requestHeaders = [
        'X-HMAC:sha256:' . $sign,
         'Content-type: application/json'
    ];
	
	 
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
    
    $response = curl_exec($ch);
    curl_close($ch);
	
   print_r($response);

}    
if($method=="withdraw"){

    
 $message = json_encode(
        array('jsonrpc' => '2.0', 'id' => 1, 'method' => 'withdraw', 'params' => array('userUuid'=>'8b96093d-3bc7-4c3d-a46a-ea27bfeae166','transactionUuid'=>'8bba5f34-0f2e-4344-9dfd-7209ec443cf2','amount'=>'1000','currency'=>'EUR'))
    );
	 
	//	 $hex = $private_key; 
	$hex = hex2bin($private_key); 
    $sign = hash_hmac('sha256', $message, $hex);
    $requestHeaders = [
        'X-HMAC:sha256:' . $sign,
         'Content-type: application/json'
    ];
	
	 
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
    
    $response = curl_exec($ch);
    curl_close($ch);
	
   print_r($response);
} 