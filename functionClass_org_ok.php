<?php

class FunctionClass
{
    
    var $error;
    
    
    function __construct()
    {
        
        
        
	}
	//api/rpc 
	function get_private_key_for_public_key($public_key='') {
		// extract private key from database or cache store
	   global $connectionDetail;		
		
		return $connectionDetail['vendorkey']['private_key'];
   }

    function generateRandomString( $length = 10 )
    {
        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen( $characters );
        $randomString     = '';
        for ( $i = 0; $i < $length; $i++ ) {
            $randomString .= $characters[rand( 0, $charactersLength - 1 )];
        } //$i = 0; $i < $length; $i++
        return $randomString;
    }
    
    function getAccessToken( $code )
    {
        
        global $connectionDetail;
        $parameters = "code=" . $code . "&grant_type=authorization_code" . "&client_id=" . $connectionDetail['oauth']['clientId'] . "&client_secret=" . $connectionDetail['oauth']['clientSecret'] . "&redirect_uri=" . urlencode( $connectionDetail['oauth']['redirectUri'] );
        
        $token_url   = $connectionDetail['oauth']['endpoint'] . "/oauth";
        $curl_handle = curl_init();
        curl_setopt( $curl_handle, CURLOPT_URL, $token_url );
        curl_setopt( $curl_handle, CURLOPT_BUFFERSIZE, 1024 );
        curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $curl_handle, CURLOPT_SSL_VERIFYPEER, TRUE );
        curl_setopt( $curl_handle, CURLOPT_SSL_VERIFYHOST, TRUE );
        curl_setopt( $curl_handle, CURLOPT_POST, TRUE );
        curl_setopt( $curl_handle, CURLOPT_PROXY, '' );
        curl_setopt( $curl_handle, CURLOPT_SSLVERSION, 4 );
        curl_setopt( $curl_handle, CURLOPT_SSL_CIPHER_LIST, 'SSLv3' );
        
        curl_setopt( $curl_handle, CURLOPT_POSTFIELDS, $parameters );
        // $response = curl_exec($curl_handle);
        
        $content = curl_exec( $curl_handle );
        $err     = curl_errno( $curl_handle );
        $errmsg  = curl_error( $curl_handle );
        $header  = curl_getinfo( $curl_handle );
        curl_close( $curl_handle );
        
        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        
        return $header;
        
    }
    
    function getNewAccessToken( $refresh_token = '', $grant_type = 'refresh_token' )
    {
        global $connectionDetail;
        
        $postData = array(
             "grant_type" => $grant_type,
            'client_id' => $connectionDetail['oauth']['clientId'],
            'client_secret' => $connectionDetail['oauth']['clientSecret'],
            'refresh_token' => $refresh_token 
            
        );
       // echo "<br>" . http_build_query( $postData );
       // echo "<br>" . 
		$token_url = $connectionDetail['oauth']['endpoint'] . "/oauth";
        $curl_handle = curl_init();
        curl_setopt( $curl_handle, CURLOPT_URL, $token_url );
        curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl_handle, CURLOPT_POSTFIELDS, http_build_query( $postData ) );
        $response = curl_exec( $curl_handle );
        $header   = curl_getinfo( $curl_handle );
        curl_close( $curl_handle );
        
        
        $header['content'] = $response;
        
        return $header;
    }
    function getResourceOwnerDetails( $access_token = '', $grant_type = 'client_credentials' )
    {
        global $connectionDetail;
         $postData = array(
             "grant_type" => $grant_type,
			 "access_token" => $access_token
                
        );
    
		$token_url = $connectionDetail['oauth']['endpoint'] . "/api/v1/user?access_token=".$access_token;
        $curl_handle = curl_init();
        curl_setopt( $curl_handle, CURLOPT_URL, $token_url );
        curl_setopt( $curl_handle, CURLOPT_HEADER, false );
        curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER,array('Content-Type:application/json'));
        curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, true );
      //  curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($postData));
	   // curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
       
	    
        $content = curl_exec( $curl_handle );
        $err     = curl_errno( $curl_handle );
        $errmsg  = curl_error( $curl_handle );
        $header  = curl_getinfo( $curl_handle );
        curl_close( $curl_handle );
         
        
        $header['content'] = $content;
		
		
		//calling function to add uuid and username in db
		//$response = json_decode( $content, true );	
		//$addUsername = $this->updateUserName($response);	
       // print_r( $response );
        return $header;
	}

	function getAccessTokenForSale( $token_id )
    {
        
        global $connectionDetail;
		$parameters = "?grant_type=client_credentials" . "&client_id=" . $connectionDetail['oauth']['clientId'] . "&client_secret=" . $connectionDetail['oauth']['clientSecret'] . "&scope=post-sales" ;
		//{“amount”:”1000”,”cost”:”250”,”net”:”750”,”currency”:”EUR”,”game_code”...}
		$postData = array(
			"grant_type" => 'client_credentials',
			"client_id" => $connectionDetail['oauth']['clientId'],
			"client_secret" => $connectionDetail['oauth']['clientSecret'],
			"scope" => 'post-sales'
	   );
	   //?amount=1000&cost=700&net=300&currency=EUR&game_code=18973097-b81f-11e8-ac05-00163e0c3360&user_uuid=8b96093d-3bc7-4c3d-a46a-ea27bfeae166&is_credit=false
	   $postData2 = array(
		
		"amount" => 1000,
		"cost" => 700,
		"net" => 300,
		"currency" => 'EUR',
		"is_credit" => 'false',
		"game_transaction_id" => '18973097-b81f-11e8-ac05-00163e0c3360',
		"game_code" => $connectionDetail['oauth']['gameProviderUuid'],
		"user_uuid" => '8b96093d-3bc7-4c3d-a46a-ea27bfeae166'
		   
   );
      echo  $token_url   = $connectionDetail['oauth']['endpoint'] . "/api/v1/post-sales";
        $curl_handle = curl_init();
        curl_setopt( $curl_handle, CURLOPT_URL, $token_url );
       // curl_setopt( $curl_handle, CURLOPT_BUFFERSIZE, 1024 );
      //  curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, TRUE );
      //  curl_setopt( $curl_handle, CURLOPT_SSL_VERIFYPEER, TRUE );
     //   curl_setopt( $curl_handle, CURLOPT_SSL_VERIFYHOST, TRUE );
        curl_setopt( $curl_handle, CURLOPT_POST, TRUE );
     //   curl_setopt( $curl_handle, CURLOPT_PROXY, '' );
     //   curl_setopt( $curl_handle, CURLOPT_SSLVERSION, 4 );
    //    curl_setopt( $curl_handle, CURLOPT_SSL_CIPHER_LIST, 'SSLv3' );
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
			"authorization: Bearer 15efa27394e71e91f3736825491e0f779fa6c9c9",
			"cache-control: no-cache",
			"content-type: application/json",
			"postman-token: 91c2cba2-0e32-6ba2-60e0-6289034690c5"
		  ));
		
		//curl_setopt( $curl_handle, CURLOPT_POSTFIELDS, $parameters );
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, json_encode($postData2));
		 
        // $response = curl_exec($curl_handle);
        
        $content = curl_exec( $curl_handle );
        $err     = curl_errno( $curl_handle );
        $errmsg  = curl_error( $curl_handle );
        $header  = curl_getinfo( $curl_handle );
        curl_close( $curl_handle );
        
        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        
        return $content;
        
    }
	function sendGameResult( $access_token = '', $grant_type = 'client_credentials' )
    {
        global $connectionDetail;
         $postData = array(
             "grant_type" => $grant_type,
			 "access_token" => $access_token
                
        );
    
		$token_url = $connectionDetail['oauth']['endpoint'] . "/api/v1/user?access_token=".$access_token;
        $curl_handle = curl_init();
        curl_setopt( $curl_handle, CURLOPT_URL, $token_url );
        curl_setopt( $curl_handle, CURLOPT_HEADER, false );
        curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER,array('Content-Type:application/json'));
       
      //  curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($postData));
	   // curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
       
	    
        $content = curl_exec( $curl_handle );
        $err     = curl_errno( $curl_handle );
        $errmsg  = curl_error( $curl_handle );
        $header  = curl_getinfo( $curl_handle );
        curl_close( $curl_handle );
         
        
        $header['content'] = $content;
		
		
		//calling function to add uuid and username in db
		//$response = json_decode( $content, true );	
		//$addUsername = $this->updateUserName($response);	
       // print_r( $response );
        return $header;
    }
	function isAccessTokenExpired($arguments) {
		   if ((time() - $arguments['gamblingtec']['created']) > $arguments['gamblingtec']['expires_in']) {
			// session started more than 30 minutes ago
			//    session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
		 	    $json_response = $this->getNewAccessToken($arguments['gamblingtec']['refresh_token'],'refresh_token');
			    $response = json_decode( $json_response['content'], true );
   		 
				$access_token  = $response['access_token'];
			
				if ( isset( $access_token ) || $access_token != "" ) {
					$access_token  = $response['access_token'];
					$expires_in    = $response['expires_in'];
					$token_type    = $response['token_type'];
					$scope         = $response['scope'];
					$refresh_token = $response['refresh_token'];
					
					$_SESSION['gamblingtec']['access_token']  = $access_token;
					$_SESSION['gamblingtec']['expires_in']    = $expires_in;
					$_SESSION['gamblingtec']['token_type']    = $token_type;
					$_SESSION['gamblingtec']['scope']         = $scope;
					$_SESSION['gamblingtec']['refresh_token'] = $refresh_token;
					$_SESSION['gamblingtec']['created'] 	  = time();
				} else {
					header("location: login.php");
					exit;
				}
				
		 
			}
	 }
	 
	function getBalanceDetails( $arguments,$currency_type="EUR" )
    {
       global $DBH;
		$userUuid =  $arguments['uuid'];   
        //  and trans_type='credit' 
		$stmt = $DBH->prepare("select   sum(amount) as amount from 	Transaction where user_proxy_id=:user_proxy_id and currency_type=:currency_type");
		$stmt->bindValue(':user_proxy_id', $userUuid); 
		$stmt->bindValue(':currency_type', $currency_type); 
	  	$stmt->execute();
 		//$balance_detail = $stmt->fetchAll();
		$balance_array = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			 
			$balance_array = $row;
			 
			
		}
		
         return $balance_array;
    }
	function getCurrencyDetails( $currency_type="EUR" )
    {
       global $DBH;
		 
        //  and trans_type='credit' 
		$stmt = $DBH->prepare("select   * from 	CurrencyBaseValue where  code=:code");
		$stmt->bindValue(':code', $currency_type); 
		 
	  	$stmt->execute();
 		//$balance_detail = $stmt->fetchAll();
		$currency_array = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			 
			$currency_array = $row;
			 
			
		}
		
         return $currency_array;
    }
	
	// databse sql queries
	function getTransactions( $arguments )
    {
       global $DBH;
	 
	 	$userUuid =  $arguments['uuid'];   
	 
        //  and trans_type='credit' 
		 
		$stmt = $DBH->prepare("SELECT tr.*,cur.code,cur.left_symbol,cur.right_symbol,cur.exponent FROM Transaction  AS tr, CurrencyBaseValue AS cur 
WHERE tr.currency_type=cur.code AND tr.user_proxy_id=:user_proxy_id");

		$stmt->bindValue(':user_proxy_id', $userUuid); 
		 
	  	$stmt->execute();
 		//$balance_detail = $stmt->fetchAll();
		$balance_array = array();
		 $balance_array = $stmt->fetchAll();
			 
		 
         return $balance_array;
    } 
	//adding and updating users details in tables UserProxy
	function updateUserName($postdata=array() )
    {
        global $DBH;
		
	 	$bindValue['uuid'] = $postdata['uuid'];
		$stmt = $DBH->prepare("select * from UserProxy where uuid=:uuid");
		$stmt->bindValue(':uuid', $postdata['uuid']);
 		$stmt->execute(); 
		 
	  	$total_rows = $stmt->rowCount(); 
	 
		if($total_rows<=0){
 			
 			$stmt = $DBH->prepare("INSERT INTO UserProxy (uuid, username) VALUES (?, ?	)");
		 	$stmt->execute([$postdata['uuid'], $postdata['username']]);
			
		} else {
			$bindValue['uuid'] = $postdata['uuid'];
			$bindValue['username'] = $postdata['username'];
			$stmt = $DBH->prepare("update UserProxy set uuid=:uuid , username=:username  where uuid=:uuid");
		 	$stmt->execute($bindValue);

		}
					
    }
	function getGameOpenId($currency='EUR'){
		 global $DBH;
		
	 	$currency = ($currency!='')?$currency:'EUR'; 
		//and game_session='open'   and created_date >= NOW() + INTERVAL 10 MINUTE
		$stmt = $DBH->prepare("select * , now() as curent_date_time,   TIMESTAMPDIFF(MINUTE,created_date,NOW()) as time_different from GameSession where currency=:currency order by id desc limit 0,1");
		$stmt->bindValue(':currency', $currency);
		$stmt->execute(); 
		 
	  	$total_rows = $stmt->rowCount(); 
	 
		if($total_rows<=0){
 			//$game_code = "asdsadasd";
			$stmt = $DBH->prepare("select UUID() as uuid");
			$stmt->execute();
			$result	   = $stmt ->fetch(PDO::FETCH_ASSOC);
			$game_code = $result['uuid'];
 			$stmt = $DBH->prepare("INSERT INTO GameSession (game_code,currency, game_session) VALUES (:game_code, :currency, :game_session)");
			$stmt->bindValue(':game_code', $game_code);
			$stmt->bindValue(':currency', $currency);
			$stmt->bindValue(':game_session', 'open');
		 	$stmt->execute();
			$lastInsertId = $DBH->lastInsertId();
			
		//			$newId = $pdo->lastInsertId();
			
		} else {
			$result	   = $stmt ->fetch(PDO::FETCH_ASSOC);
			$game_code = $result['game_code'];
			$created_date = $result['created_date'];
			$curent_date_time = $result['curent_date_time'];
			$time_different = $result['time_different'];
			$lastInsertId = $result['id'];
			//$req_dump = print_r($result, true);
			//$fp = file_put_contents('game_code.txt', $req_dump, FILE_APPEND);
			if($time_different>=10){ 
				$stmt2 = $DBH->prepare("update GameSession set game_session='close' ,update_date=now() where game_code=:game_code and currency=:currency");
				$stmt2->bindValue(':currency', $currency);
				$stmt2->bindValue(':game_code', $game_code);
				$stmt2->execute();
				$stmt = $DBH->prepare("select UUID() as uuid");
				$stmt->execute();
				$result	   = $stmt ->fetch(PDO::FETCH_ASSOC);
				$game_code = $result['uuid'];
				$stmt3 = $DBH->prepare("INSERT INTO GameSession (game_code,currency, game_session) VALUES (:game_code, :currency, :game_session)");
				$stmt3->bindValue(':game_code', $game_code);
				$stmt3->bindValue(':currency', $currency);
				$stmt3->bindValue(':game_session', 'open');
				$stmt3->execute();
				$lastInsertId = $DBH->lastInsertId();
			}
			
		}
		
		return array('game_code'=>$game_code,'last_insert_id'=>$lastInsertId);
	}
	
	function convertDateFormat($date,$format="d-M-Y")
	{
		 
			$date = new DateTime($date);
			$newDateString = $date->format($format);
			return $newDateString;
	}
}

?>