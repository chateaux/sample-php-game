<?php
class FunctionClass
{
    var $error;
    function __construct()
    {
    }
    //api/rpc 
    function get_private_key_for_public_key( $public_key = '' )
    {
        // extract private key from database or cache store
        global $connectionDetail;
        return $connectionDetail['vendorkey']['private_key'];
    }
    //general function    
    function convertDateFormat( $date, $format = "d-M-Y" )
    {
        $date          = new DateTime( $date );
        $newDateString = $date->format( $format );
        return $newDateString;
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
    function getCurrencyDetails( $currency_type = "EUR" )
    {
        global $DBH;
        //  and trans_type='credit' 
        
        $stmt = $DBH->prepare( "select * from     CurrencyBaseValue where  code=:code" );
        $stmt->bindValue( ':code', $currency_type );
        $stmt->execute();
        $currency_array = array();
        while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
            $currency_array = $row;
        } //$row = $stmt->fetch( PDO::FETCH_ASSOC )
        return $currency_array;
    }
    function getCurrencyList( )
    {
        global $DBH;
        //  and trans_type='credit' 
        
        $stmt = $DBH->prepare( "select * from     CurrencyBaseValue where  status=1" );
        $stmt->execute();
        $currency_array = array();
        $currency_array = $stmt->fetchAll();
        return $currency_array;
    }
    // login access token  section
    function getAccessToken( $code )
    {
        global $connectionDetail;
        $parameters  = "code=" . $code . "&grant_type=authorization_code" . "&client_id=" . $connectionDetail['oauth']['clientId'] . "&client_secret=" . $connectionDetail['oauth']['clientSecret'] . "&redirect_uri=" . urlencode( $connectionDetail['oauth']['redirectUri'] );
        $token_url   = $connectionDetail['oauth']['endpoint'] . $connectionDetail['oauth']['urlAccessToken']; //. "/oauth";
        $curl_handle = curl_init();
        curl_setopt( $curl_handle, CURLOPT_URL, $token_url );
        curl_setopt( $curl_handle, CURLOPT_BUFFERSIZE, 1024 );
        curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl_handle, CURLOPT_SSL_VERIFYPEER, true );
        curl_setopt( $curl_handle, CURLOPT_SSL_VERIFYHOST, true );
        curl_setopt( $curl_handle, CURLOPT_POST, true );
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
        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => $connectionDetail['oauth']['clientId'],    // The client ID assigned to you by the provider
            'clientSecret'            => $connectionDetail['oauth']['clientSecret'],   // The client password assigned to you by the provider
            'redirectUri'             => $connectionDetail['oauth']['redirectUri'],
            'urlAuthorize'            => $connectionDetail['oauth']['endpoint'].$connectionDetail['oauth']['urlAuthorize'],
            'urlAccessToken'          => $connectionDetail['oauth']['endpoint'].$connectionDetail['oauth']['urlAccessToken'],
            'urlResourceOwnerDetails' => $connectionDetail['oauth']['endpoint'].$connectionDetail['oauth']['urlResourceOwnerDetails'],
        ]); 

        $newAccessToken = $provider->getAccessToken('refresh_token', [
            'refresh_token' => $refresh_token
        ]);
        return $newAccessToken;
        
        die();

    }
    function getResourceOwnerDetails( $access_token = '' )
    {
        global $connectionDetail;
        $token_url   = $connectionDetail['oauth']['endpoint'] . $connectionDetail['oauth']['urlResourceOwnerDetails'] . $access_token;
        $curl_handle = curl_init();
        curl_setopt( $curl_handle, CURLOPT_URL, $token_url );
        curl_setopt( $curl_handle, CURLOPT_HEADER, false );
        curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl_handle, CURLOPT_HTTPHEADER, array(
             'Content-Type:application/json' 
        ) );
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
    function isAccessTokenExpired()
    {
        
         
        if ( ( time() - $_SESSION['gamblingtec']['created'] ) > $_SESSION['gamblingtec']['expires_in'] ) {
            // session started more than 30 minutes ago
            //    session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
            $json_response = $this->getNewAccessToken( $_SESSION['gamblingtec']['refresh_token'], 'refresh_token' );
            $response      = json_decode( $json_response['content'], true );
            $access_token  = $response['access_token'];
            if ( isset( $access_token ) || $access_token != "" ) {
                $access_token                             = $response['access_token'];
                $expires_in                               = $response['expires_in'];
                $token_type                               = $response['token_type'];
                $scope                                    = $response['scope'];
                $refresh_token                            = $response['refresh_token'];
                $_SESSION['gamblingtec']['access_token']  = $access_token;
                $_SESSION['gamblingtec']['expires_in']    = $expires_in;
                $_SESSION['gamblingtec']['token_type']    = $token_type;
                $_SESSION['gamblingtec']['scope']         = $scope;
                $_SESSION['gamblingtec']['refresh_token'] = $refresh_token;
                $_SESSION['gamblingtec']['created']       = time();
            } //isset( $access_token ) || $access_token != ""
            else {
                header( "location: login.php" );
                exit;
            }
        } //( time() - $_SESSION['gamblingtec']['created'] ) > $_SESSION['gamblingtec']['expires_in']
    }
    //adding and updating users details in tables UserProxy
    function updateUserName( $postdata = array() )
    {
        global $DBH;
        $bindValue['uuid'] = $postdata['uuid'];
        $stmt              = $DBH->prepare( "select * from UserProxy where uuid=:uuid" );
        $stmt->bindValue( ':uuid', $postdata['uuid'] );
        $stmt->execute();
        $total_rows = $stmt->rowCount();
        if ( $total_rows <= 0 ) {
            $stmt = $DBH->prepare( "INSERT INTO UserProxy (uuid, username) VALUES (?, ?    )" );
            $stmt->execute([$postdata['uuid'], $postdata['username']]);
        } //$total_rows <= 0
        else {
            $bindValue['uuid']     = $postdata['uuid'];
            $bindValue['username'] = $postdata['username'];
            $stmt                  = $DBH->prepare( "update UserProxy set uuid=:uuid , username=:username  where uuid=:uuid" );
            $stmt->execute( $bindValue );
        }
    }
    // end login access token  section
    // game play section
    function getGameOpenId( $currency = 'EUR' )
    {
        /*
         $stmt = $DBH->prepare( "select * , now() as curent_date_time, TIMESTAMPDIFF(MINUTE,created_date,NOW()) as time_different from GameSession where game_session='open' and TIMESTAMPDIFF(MINUTE,created_date,NOW()) > 10
        " );
        $stmt->execute();
        $total_rows = $stmt->rowCount();
        if ( $total_rows > 0 ) { }
        */
        global $DBH;
        $currency      = ( $currency != '' ) ? $currency : 'EUR';
        $user_proxy_id = $_SESSION['gamblingtec']['uuid'];
        //and game_session='open'   and created_date >= NOW() + INTERVAL 10 MINUTE
        $stmt          = $DBH->prepare( "select * , now() as curent_date_time,   TIMESTAMPDIFF(MINUTE,created_date,NOW()) as time_different from GameSession where currency=:currency and user_proxy_id=:user_proxy_id order by id desc limit 0,1" );
        $stmt->bindValue( ':currency', $currency );
        $stmt->bindValue( ':user_proxy_id', $user_proxy_id );
        $stmt->execute();
        $total_rows = $stmt->rowCount();
        if ( $total_rows <= 0 ) {
            //$game_code = "asdsadasd";
            $stmt = $DBH->prepare( "select UUID() as uuid" );
            $stmt->execute();
            $result    = $stmt->fetch( PDO::FETCH_ASSOC );
            $game_code = $result['uuid'];
            $stmt      = $DBH->prepare( "INSERT INTO GameSession (created_date,game_code,currency, game_session,user_proxy_id) VALUES (now(),:game_code, :currency, :game_session,:user_proxy_id)" );
            $stmt->bindValue( ':game_code', $game_code );
            $stmt->bindValue( ':currency', $currency );
            $stmt->bindValue( ':user_proxy_id', $user_proxy_id );
            $stmt->bindValue( ':game_session', 'open' );
            $stmt->execute();
            $lastInsertId = $DBH->lastInsertId();
            //            $newId = $pdo->lastInsertId();
        } //$total_rows <= 0
        else {
            $result           = $stmt->fetch( PDO::FETCH_ASSOC );
            $game_code        = $result['game_code'];
            $created_date     = $result['created_date'];
            $curent_date_time = $result['curent_date_time'];
            $time_different   = $result['time_different'];
            $lastInsertId     = $result['id'];
            //$req_dump = print_r($result, true);
            //$fp = file_put_contents('game_code.txt', $req_dump, FILE_APPEND);
            if ( $time_different > 10 ) {
                $stmt2 = $DBH->prepare( "update GameSession set game_session='close' ,update_date=now() where game_code=:game_code and currency=:currency and user_proxy_id=:user_proxy_id        " );
                $stmt2->bindValue( ':currency', $currency );
                $stmt2->bindValue( ':game_code', $game_code );
                $stmt2->bindValue( ':user_proxy_id', $user_proxy_id );
                $stmt2->execute();
                $stmt = $DBH->prepare( "select UUID() as uuid" );
                $stmt->execute();
                $result    = $stmt->fetch( PDO::FETCH_ASSOC );
                $game_code = $result['uuid'];
                $stmt3     = $DBH->prepare( "INSERT INTO GameSession (created_date,game_code,currency, game_session,user_proxy_id) VALUES (now(),:game_code, :currency, :game_session,:user_proxy_id)" );
                $stmt3->bindValue( ':game_code', $game_code );
                $stmt3->bindValue( ':currency', $currency );
                $stmt3->bindValue( ':game_session', 'open' );
                $stmt3->bindValue( ':user_proxy_id', $user_proxy_id );
                $stmt3->execute();
                $lastInsertId = $DBH->lastInsertId();
            } //$time_different >= 10
        }
        return array(
            'game_code'      => $game_code,
            'last_insert_id' => $lastInsertId 
        );
    }
    function getBalanceDetails( $currency_type = "EUR" )
    {
        global $DBH;
        //$userUuid =  $arguments['uuid'];   
        $userUuid = $_SESSION['gamblingtec']['uuid'];
        //  and trans_type='credit' 
        $stmt     = $DBH->prepare( "select   sum(amount) as amount from     Transaction where user_proxy_id=:user_proxy_id and currency_type=:currency_type" );
        $stmt->bindValue( ':user_proxy_id', $userUuid );
        $stmt->bindValue( ':currency_type', $currency_type );
        $stmt->execute();
        //$balance_detail = $stmt->fetchAll();
        $balance_array = array();
        while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
            $balance_array = $row;
        } //$row = $stmt->fetch( PDO::FETCH_ASSOC )
        return $balance_array;
    }
    function getCurrentBalances($arguments)
    {
		global $DBH;
	  	$userUuid =  $arguments['uuid'];   
        	$stmt = $DBH->prepare("select  currency_type, sum(amount) as amount  ,cur.code,cur.left_symbol,cur.right_symbol,cur.exponent from 	Transaction AS tr, CurrencyBaseValue AS cur 
        WHERE tr.currency_type=cur.code AND   user_proxy_id=:user_proxy_id group by currency_type");
		$stmt->bindValue(':user_proxy_id', $userUuid); 
	  	$stmt->execute();
 		//$balance_detail = $stmt->fetchAll();
		$balance_array = array();
		$total_rows = $stmt->rowCount(); 
	 	if($total_rows>0){
			while ($row = $stmt->fetch( PDO::FETCH_ASSOC )) {
				
				$balance_array[$row->currency_type] = $row;
               // print_r($row);
				
			}
			return $balance_array;
		} 
		
		
         
    }
    function betGame( $bet_amount_withexponent, $gameOpenData, $currency = 'EUR' )
    {
        global $DBH;
        if($bet_amount_withexponent>0){
        $game_code               = $gameOpenData['game_code'];
        $game_session_id         = $gameOpenData['last_insert_id'];
        $userUuid                = $_SESSION['gamblingtec']['uuid'];
        $bet_amount_withexponent = $bet_amount_withexponent * -1;

        $stmt2   = $DBH->prepare( "INSERT INTO Transaction (trans_type,identity,status,  user_proxy_id, counterid, game_session_id, amount ,currency_type ) VALUES (?, ?,?,?,?,?,?    ,?)" );
       
        $stmt2->execute(['debit', 'bet', 'approved', $userUuid, $game_code, $game_session_id, $bet_amount_withexponent, $currency]);
        return 1;
        } else {
            return 0;
        }
    
    }
    function collectGame( $bet_amount_withexponent, $gameOpenData, $currency = 'EUR' )
    {
        global $DBH;
        global $connectionDetail;
        if($bet_amount_withexponent>0){
            $game_code        = $gameOpenData['game_code'];
            $game_session_id  = $gameOpenData['last_insert_id'];
            $userUuid         = $_SESSION['gamblingtec']['uuid'];
            $amount_won_grand = ( $bet_amount_withexponent * $connectionDetail['default']['winAmount'] );
            $stmt2 = $DBH->prepare( "INSERT INTO Transaction (trans_type,identity,status,  user_proxy_id, counterid, game_session_id, amount ,currency_type ) VALUES (?, ?,?,?,?,?,?    ,?)" );
          
            $stmt2->execute(['credit', 'collect', 'approved', $userUuid, $game_code, $game_session_id, $amount_won_grand, $currency]);
            return 1;

        } else {
            return 0;
        }
        
    }
    // balances  sql queries
    function getTransactions()
    {
        global $DBH;
        //$userUuid =  $arguments['uuid'];   
        $userUuid = $_SESSION['gamblingtec']['uuid'];
        //  and trans_type='credit' 
        $stmt     = $DBH->prepare( "SELECT tr.*,cur.code,cur.left_symbol,cur.right_symbol,cur.exponent FROM Transaction  AS tr, CurrencyBaseValue AS cur 
WHERE tr.currency_type=cur.code AND tr.user_proxy_id=:user_proxy_id" );
        $stmt->bindValue( ':user_proxy_id', $userUuid );
        $stmt->execute();
        //$balance_detail = $stmt->fetchAll();
        $balance_array = array();
        $balance_array = $stmt->fetchAll();
        return $balance_array;
    }
    //game post sale
    function closeInactiveGameSession( )
    {
        global $DBH;
        $stmt2 = $DBH->prepare( "update GameSession set game_session='close' ,update_date=now() where TIMESTAMPDIFF(MINUTE,created_date,NOW()) > 10  and game_session='open' " );
                    
        $stmt2->execute();
        
    }
    function getGameResultStats( )
    {
        global $DBH;
       
        //$userUuid = $_SESSION['gamblingtec']['uuid'];
        //currency=:currency_type and 
        $stmt = $DBH->prepare( "select   game_code,user_proxy_id,currency from     GameSession gs where     gs.game_post_sale_status='pending' and gs.game_session='close' " );
        //$stmt->bindValue( ':currency_type', $currency_type );
        $stmt->execute();
        //$balance_detail = $stmt->fetchAll();
        $balance_array = array();
        while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
            //echo "<br>".
            $game_code = $row['game_code'];
            $userUuid  = $row['user_proxy_id'];
            $currency_type  = $row['currency'];
            $stmt1     = $DBH->prepare( "select    sum(amount)  as amount from Transaction trans    where    trans.identity='bet' and trans.status='approved' and trans.user_proxy_id=:user_proxy_id and trans.currency_type=:currency_type   and trans.counterid=:game_code" );
            $stmt1->bindValue( ':user_proxy_id', $userUuid );
            $stmt1->bindValue( ':currency_type', $currency_type );
            $stmt1->bindValue( ':game_code', $game_code );
            $stmt1->execute();
            $row_amount = $stmt1->fetch( PDO::FETCH_ASSOC );
            $stmt2      = $DBH->prepare( "select    sum(amount)  as cost from Transaction trans    where    trans.identity='collect' and trans.status='approved' and trans.user_proxy_id=:user_proxy_id and trans.currency_type=:currency_type  and trans.counterid=:game_code" );
            $stmt2->bindValue( ':user_proxy_id', $userUuid );
            $stmt2->bindValue( ':currency_type', $currency_type );
            $stmt2->bindValue( ':game_code', $game_code );
            $stmt2->execute();
            $row_cost                                = $stmt2->fetch( PDO::FETCH_ASSOC );
            //$game_stats_array = array('amount'=>0,'cost'=>0,'net'=>0);
            $stats_array['game_code']                = $game_code;
            $stats_array['amount']                   = $row_amount['amount'] * -1;
            $stats_array['cost']                     = $row_cost['cost'] * 1;
            $stats_array['net']                      = $stats_array['amount'] - $stats_array['cost'];
            $stats_array['currency']                 = $currency_type;
            $game_stats_array[$userUuid][$game_code] = $stats_array;
        } //$row = $stmt->fetch( PDO::FETCH_ASSOC )
        return $game_stats_array;
    }
    function setGameResultPostSaleUpdate( $game_code, $response )
    {
        global $DBH;
        $stmt = $DBH->prepare( "update GameSession set game_post_sale_status=:status , game_post_sale_transaction_id=:transaction_id,post_sale_update_date=now()  where game_code=:game_code" );
        $bindValue['status']         = $response['status'];
        $bindValue['transaction_id'] = $response['transaction_id'];
        $bindValue['game_code']      = $game_code;
        $stmt->execute( $bindValue );
        return 1;
    }
    function requestGameResultPostSale( $token_id, $postData )
    {
        global $connectionDetail;
        global $DBH;
        $token_url   = $connectionDetail['oauth']['endpoint'] . $connectionDetail['oauth']['urlPostSale']; // "/api/v1/post-sales";
        $curl_handle = curl_init();
        curl_setopt( $curl_handle, CURLOPT_URL, $token_url );
        curl_setopt( $curl_handle, CURLOPT_HEADER, false );
        curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, true );
      
        curl_setopt( $curl_handle, CURLOPT_POST, true );
       
        curl_setopt( $curl_handle, CURLOPT_HTTPHEADER, array(
             "authorization: Bearer " . $token_id,
            "cache-control: no-cache",
            "content-type: application/json" 
        ) );
        
        curl_setopt( $curl_handle, CURLOPT_POSTFIELDS, json_encode( $postData ) );
        $content = curl_exec( $curl_handle );
       
        curl_close( $curl_handle );
        return $content;
	}
	function sendMailAPI( $token_id, $postData )
    {
        global $connectionDetail;
        global $DBH;
        $token_url   = $connectionDetail['oauth']['endpoint'] . $connectionDetail['oauth']['urlMail']."&scope=mail"; // "/api/v1/post-sales";
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
        $content = curl_exec( $curl_handle );
       
        curl_close( $curl_handle );
        return $content;
    }
    //end of game post sale
}