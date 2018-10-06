<?php
session_start();
require_once("dbconnection.php");
require_once __DIR__ . '/vendor/autoload.php';


include("functionClass.php");
$functionClass = new FunctionClass();

$currency = $connectionDetail['default']['currency'];

$provider = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => $connectionDetail['oauth']['clientId'],       // The client ID       assigned to you by the provider
    'clientSecret'            => $connectionDetail['oauth']['clientSecret'],   // The client password assigned to you by the provider
    'redirectUri'             => $connectionDetail['oauth']['redirectUri'],
    'urlAuthorize'            => $connectionDetail['oauth']['endpoint'] . $connectionDetail['oauth']['urlAuthorize'],
    'urlAccessToken'          => $connectionDetail['oauth']['endpoint'] . $connectionDetail['oauth']['urlAccessToken'],
    'urlResourceOwnerDetails' => $connectionDetail['oauth']['endpoint'] . $connectionDetail['oauth']['urlResourceOwnerDetails']
]);

try {

    // Try to get an access token using the resource owner password credentials grant.
    $options             = array('scope' => 'mail');
    $accessToken         = $provider->getAccessToken('client_credentials');
    echo 'Access Token   : ' . "<br>" .
    $token_id            = $accessToken->getToken();


    //mail ontnet
    $mess_body_html  = file_get_contents("mailtemplate/mailhtml.html");
    $mess_body_plain = file_get_contents("mailtemplate/mailplain.txt");
    //end of mail content
    $postData = array();
    $postData['subject'] = "{{!}}Your subject here!";
    $postData['html'] = $mess_body_html;
    $postData['plaintext'] = $mess_body_plain;
    $postData['recipients'] = array('8b96093d-3bc7-4c3d-a46a-ea27bfeae166','8bc8821e-3bfc-49ed-8ace-efc5ff5665f7');
    $postData['run_now'] = 'true';
    //$response_json = $functionClass->sendMailAPI($token_id, $postData);

    echo $token_url   = $connectionDetail['oauth']['endpoint'] . $connectionDetail['oauth']['urlMail']; // "/api/v1/post-sales";
    $curl_handle = curl_init();
    curl_setopt( $curl_handle, CURLOPT_URL, $token_url );
    curl_setopt( $curl_handle, CURLOPT_HEADER, false );
    curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, true );
  
    curl_setopt( $curl_handle, CURLOPT_POST, true );
   
    curl_setopt( $curl_handle, CURLOPT_HTTPHEADER, array(
         "authorization: Bearer " . $token_id,
     
        "content-type: application/json" 
    ) );
    
    curl_setopt( $curl_handle, CURLOPT_POSTFIELDS, json_encode( $postData ) );
    $response_json = curl_exec( $curl_handle );
   
    curl_close( $curl_handle );

    print_r($response_json);
  
 
} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

    // Failed to get the access token
    exit($e->getMessage());

}

?>