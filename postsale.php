<?php
session_start();
require_once("dbconnection.php");
require_once __DIR__ . '/vendor/autoload.php';


include("functionClass.php");
$functionClass = new FunctionClass();

$currency = $connectionDetail['default']['currency'];

$provider = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId' => $connectionDetail['oauth']['clientId'],    // The client ID assigned to you by the provider
    'clientSecret' => $connectionDetail['oauth']['clientSecret'],   // The client password assigned to you by the provider
    'redirectUri' => $connectionDetail['oauth']['redirectUri'],
    'urlAuthorize' => $connectionDetail['oauth']['endpoint'] . $connectionDetail['oauth']['urlAuthorize'],
    'urlAccessToken' => $connectionDetail['oauth']['endpoint'] . $connectionDetail['oauth']['urlAccessToken'],
    'urlResourceOwnerDetails' => $connectionDetail['oauth']['endpoint'] . $connectionDetail['oauth']['urlResourceOwnerDetails']
]);

try {

    // Try to get an access token using the resource owner password credentials grant.
    $options = array('scope' => 'post-sales');
    $accessToken = $provider->getAccessToken('client_credentials', $options);
    //echo 'Access Token: ' . "<br>" .
        $token_id = $accessToken->getToken();
 
    $closeInactiveGameSession = $functionClass->closeInactiveGameSession();
    $game_stats_data = $functionClass->getGameResultStats();
    echo "<pre>";
// print_r($game_stats_data);

    $postData = array();
    foreach ($game_stats_data as $key_user_uuid => $values_stats) {

        foreach ($values_stats as $key_game_id => $value_game_stats) {
            $postData = array();
            $postData['amount'] = $value_game_stats['amount'];
            $postData['cost'] = $value_game_stats['cost'];
            $postData['net'] = $value_game_stats['net'];
            $postData['currency'] = $value_game_stats['currency'];
            $postData['is_credit'] = 'false';
            $postData['game_transaction_id'] = $key_game_id;
            $postData['game_code'] = $connectionDetail['oauth']['gameProviderUuid'];
            $postData['user_uuid'] = $key_user_uuid;
             print_r($postData);
            $responseGameResuTPostSale_json = $functionClass->requestGameResultPostSale($token_id, $postData);
            $responseGameResuTPostSale = json_decode($responseGameResuTPostSale_json, true);
            print_r($responseGameResuTPostSale);
            if ($responseGameResuTPostSale['status'] == "success" && isset($responseGameResuTPostSale['transaction_id'])) {
                $gameResultPostSaleUpdate = $functionClass->setGameResultPostSaleUpdate($key_game_id, $responseGameResuTPostSale);
            }
        }


    }




} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

    // Failed to get the access token
    exit($e->getMessage());

}

?>