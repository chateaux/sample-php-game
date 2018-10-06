<?php
session_start();
require_once("dbconnection.php");
require_once __DIR__.'/vendor/autoload.php';

include("functionClass.php");
$functionClass = new FunctionClass(); 
 
$provider = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => $connectionDetail['oauth']['clientId'],    // The client ID assigned to you by the provider
    'clientSecret'            => $connectionDetail['oauth']['clientSecret'],   // The client password assigned to you by the provider
    'redirectUri'             => $connectionDetail['oauth']['redirectUri'],
    'urlAuthorize'            => $connectionDetail['oauth']['endpoint']."/oauth/authorize",
    'urlAccessToken'          => $connectionDetail['oauth']['endpoint']."/oauth",
    'urlResourceOwnerDetails' => $connectionDetail['oauth']['endpoint']."/api/v1/post-sales",
]);

try {

    // Try to get an access token using the resource owner password credentials grant.
    $options = array('scope' => 'post-sales');
    $accessToken = $provider->getAccessToken('client_credentials',$options);
    echo 'Access Token: ' . "<br>" .
    $token_id  =  $accessToken->getToken();
    //echo 'Refresh Token: ' . $accessToken->getRefreshToken();
    //echo 'Expired in: ' . "<br>" .
  //   $accessToken->getExpires() . "<br>";
   // echo 'Already expired? ' . ($accessToken->hasExpired() ? 'expired' : 'not expired') . "<br>";
    $responseGameResuTPostSale = $functionClass->requestGameResultPostSale($token_id);

   print_r($responseGameResuTPostSale);
   die();
    /*
    Access Token: 8790ad8d42ba8d771ea8105c3d7df700e4bc8cc6
Expired in: 1537139329
https://sandbox.gamblingtec.com/api/v1/post-sales{"status":"success","transaction_id":"8bcd3aea-6075-49f3-af28-179235d9ffc1"}1

Access Token: d8899733fb0543694f9b4f18302722764e013004
Expired in: 1537139388
https://sandbox.gamblingtec.com/api/v1/post-sales{"type":"http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html","title":"Conflict","status":409,"detail":"Existing sale id: 18973097-b81f-11e8-ac05-00163e0c3360 for game: Sample Game"}1

    $accessToken = $provider->getAccessToken('password', [
        'username' => 'd3systems17@gmail.com',
        'password' => '1111111'
    ]);
*/

       // print_r($accessToken);
} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

    // Failed to get the access token
    exit($e->getMessage());

}
        
?>