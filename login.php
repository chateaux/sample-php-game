<?php
session_start();
require_once( "dbconnection.php" );

require_once __DIR__.'/vendor/autoload.php';

include("functionClass.php");
$functionClass = new FunctionClass(); 
$provider = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => $connectionDetail['oauth']['clientId'],    // The client ID assigned to you by the provider
    'clientSecret'            => $connectionDetail['oauth']['clientSecret'],   // The client password assigned to you by the provider
    'redirectUri'             => $connectionDetail['oauth']['redirectUri'],
    'urlAuthorize'            => $connectionDetail['oauth']['endpoint'].$connectionDetail['oauth']['urlAuthorize'],
    'urlAccessToken'          => $connectionDetail['oauth']['endpoint'].$connectionDetail['oauth']['urlAccessToken'],
    'urlResourceOwnerDetails' => $connectionDetail['oauth']['endpoint'].$connectionDetail['oauth']['urlResourceOwnerDetails'],
]); 
$code = (isset($_GET['code']))?$_GET['code']:'';

if ( !isset( $code ) || $code == "" ) {
    //   die("Error - code parameter missing from request!");
    
    if ( isset( $_SESSION['gamblingtec']['access_token1'] ) ) {
       // header( 'Location: game.php' );
    } //isset( $_SESSION['gamblingtec']['access_token'] )
    else {
        $options = array('scope' => 'transparent');
          
 
        // Get the state generated for you and store it to the session.
           
    
           // $auth_url = $connectionDetail['oauth']['endpoint'] . "/oauth/authorize?scope=transparent&response_type=code&state=" . $state . "&client_id=" . $connectionDetail['oauth']['clientId'] . "&redirect_uri=" . urlencode( $connectionDetail['oauth']['redirectUri'] );
           
            $auth_url = $provider->getAuthorizationUrl($options);
            $_SESSION['gamblingtec']['oauth2state'] = $state = $provider->getState();
        
          //  header( "location: " . $auth_url );
		    //exit;
    }
    
    
} //!isset( $code ) || $code == ""
elseif (empty($_GET['state']) || (isset($_SESSION['gamblingtec']['oauth2state']) && $_GET['state'] !== $_SESSION['gamblingtec']['oauth2state'])) {

    if (isset($_SESSION['gamblingtec']['oauth2state'])) {
        unset($_SESSION['gamblingtec']['oauth2state']);
    }
    echo '  <meta http-equiv="refresh" content="10;url=login.php"> ';
    exit('Invalid state');
    

} else {
    
    /*
    $json_response = $functionClass->getAccessToken( $code, 'authorization_code' );
    
    
    
    $response = json_decode( $json_response['content'], true );
   // print_r( $response );
    
    $access_token  = $response['access_token'];
    $expires_in    = $response['expires_in'];
    $token_type    = $response['token_type'];
    $scope         = $response['scope'];
    $refresh_token = $response['refresh_token'];
    */
    $accessToken = $provider->getAccessToken('authorization_code', [
        'code' => $code
    ]);
   // print_r($accessToken);
    $access_token  = $accessToken->getToken() ;
    $refresh_token = $accessToken->getRefreshToken() ;
    $expires_in    = $accessToken->getExpires() ;
    $values        = $accessToken->getValues() ;
    $scope         = $values['scope'];
    $token_type    = $values['token_type'];

    if ( !isset( $access_token ) || $access_token == "" ) {
        echo '  <meta http-equiv="refresh" content="10;url=login.php"> ';
        die( "Error - access token missing from response!" );
    } //!isset( $access_token ) || $access_token == ""
    
    
    
      
        // The provider provides a way to get an authenticated API request for
        // the service, using the access token; it returns an object conforming
        // to Psr\Http\Message\RequestInterface.
        
        
        $_SESSION['gamblingtec']['access_token']  = $access_token;
        $_SESSION['gamblingtec']['expires_in']    = $expires_in;
        $_SESSION['gamblingtec']['token_type']    = $token_type;
        $_SESSION['gamblingtec']['scope']         = $scope;
        $_SESSION['gamblingtec']['refresh_token'] = $refresh_token;
		
        $_SESSION['gamblingtec']['created'] = time();
        
        
       // $json_response = $functionClass->getResourceOwnerDetails($_SESSION['gamblingtec']['access_token'],'client_credentials');
        $json_response = $functionClass->getResourceOwnerDetails($access_token);
		$response = json_decode( $json_response['content'], true );	
		//calling function to add uuid and username in db
		$_SESSION['gamblingtec']['uuid']     = $response['uuid'];
        $_SESSION['gamblingtec']['username'] = $response['username'];

		$_SESSION['gamblingtec']['currency'] = $connectionDetail['default']['currency'];
		$addUsername = $functionClass->updateUserName($response);	
	 	 header( 'Location: game.php' ) ;
		 exit;
    
}

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
<?php
include( "header.php" );
?>

<main role="main">
  <section class="jumbotron text-center">
    <div class="container">
      <h1 class="jumbotron-heading">Login</h1>
      <p class="lead text-muted">
          To play the game you first need to login to your account and we use OAuth2.0 for Authentication with the
          GamblingTec.com platform.
      </p>
      <p>
        <a href="<?php echo $auth_url;?>" class="btn btn-primary my-2">Login with Gambling Tec</a>
         
      </p>
    </div>
  </section>
</main>

<?php
include( "footer.php" );
?>

<!-- Bootstrap core JavaScript
    ================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> 
<script>window.jQuery || document.write('<script src="assets/js/jquery-slim.min.js"><\/script>')</script> 
<script src="assets/js/popper.min.js"></script> 
<script src="assets/js/bootstrap.min.js"></script> 
 
</body>
</html>