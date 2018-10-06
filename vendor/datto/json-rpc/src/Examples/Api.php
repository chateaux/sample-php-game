<?php

/**
 * Copyright (C) 2015 Datto, Inc.
 *
 * This file is part of PHP JSON-RPC.
 *
 * PHP JSON-RPC is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * PHP JSON-RPC is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with PHP JSON-RPC. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Spencer Mortensen <smortensen@datto.com>
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0
 * @copyright 2015 Datto, Inc.
 */

namespace Datto\JsonRpc\Examples;

use Datto\JsonRpc\Evaluator;
use Datto\JsonRpc\Exceptions\ArgumentException;
use Datto\JsonRpc\Exceptions\MethodException;
use Datto\JsonRpc\Exceptions\ApplicationException;
 

class Api implements Evaluator
{
    public function evaluate($method, $arguments)
    {
        if ($method === 'deposit') {
            return self::deposit($arguments);
        }
		if ($method === 'withdraw') {
            return self::withdraw($arguments);
        }
		if ($method === 'getBalances') {
            return self::getBalances($arguments);
        }
        throw new MethodException();
    }

    private static function deposit($arguments)
    {
		 global $DBH;
		   	
         
		//print_r($arguments);
		$userUuid =  $arguments['userUuid'];
		$transactionUuid =  $arguments['transactionUuid'];
		$amount =  $arguments['amount'];
		$currency =  $arguments['currency'];
		
        if ( filter_var($amount, FILTER_VALIDATE_INT) == false ) {
             throw new ArgumentException();
        }
		if($amount<=0){
			throw new ArgumentException();
			}
	  if ( filter_var($userUuid, FILTER_SANITIZE_STRING) == false ) {
			throw new ArgumentException();
	   }
	   
	   if ( filter_var($transactionUuid, FILTER_SANITIZE_STRING) == false ) {
		throw new ArgumentException();
	   }

	   if ( filter_var($currency, FILTER_SANITIZE_STRING) == false ) {
		throw new ArgumentException();
	   }


	 	$bindValue1['user_proxy_id'] = $userUuid;
		$bindValue1['counterid'] = $transactionUuid;
	 	
		
		$stmt = $DBH->prepare("select * from 	Transaction where user_proxy_id=:user_proxy_id and counterid=:counterid");
		$stmt->bindValue(':user_proxy_id', $userUuid); 
		$stmt->bindValue(':counterid', $transactionUuid); 
		$stmt->execute();
 	  	$total_rows = $stmt->rowCount(); 
	 	 
		if($total_rows<=0){
			if($amount>0){
				$stmt2 = $DBH->prepare("INSERT INTO Transaction (trans_type,identity,status,  user_proxy_id, counterid, game_session_id, amount ,currency_type ) VALUES (?, ?,?,?,?,?,?	,?)");
				$stmt2->execute(['credit','deposit','approved',$userUuid, $transactionUuid,'0',$amount,$currency]);
			   
			}
			
 		}  
		 
		
        return self::balances($arguments);
    }
	 private static function withdraw($arguments)
    {
        global $DBH;
		   	
         
		//print_r($arguments);
		$userUuid 	=  $arguments['userUuid'];
		$transactionUuid =  $arguments['transactionUuid'];
		$amount 	=  $arguments['amount'];
		$currency 	=  $arguments['currency'];
		$amount 	= -1 * abs($amount);
		 
         if ( filter_var($amount, FILTER_VALIDATE_INT) == false ) {
             throw new ArgumentException();
        }
	  if($amount<=0){
		throw new ArgumentException();
		}
	  if ( filter_var($userUuid, FILTER_SANITIZE_STRING) == false ) {
			throw new ArgumentException();
	   }
	   
	   if ( filter_var($transactionUuid, FILTER_SANITIZE_STRING) == false ) {
		throw new ArgumentException();
	   }

	   if ( filter_var($currency, FILTER_SANITIZE_STRING) == false ) {
		throw new ArgumentException();
	   }
	
		//print_r($DBH);
		 
	 	$bindValue1['user_proxy_id'] = $userUuid;
		$bindValue1['counterid'] = $transactionUuid;
	 	
		
		$stmt = $DBH->prepare("select * from 	Transaction where user_proxy_id=:user_proxy_id and counterid=:counterid");
		$stmt->bindValue(':user_proxy_id', $userUuid); 
		$stmt->bindValue(':counterid', $transactionUuid); 
		$stmt->execute();
 	  	$total_rows = $stmt->rowCount(); 
	 	$req_dump = print_r($arguments, true);
		$fp = file_put_contents('debug/action.txt', $req_dump, FILE_APPEND);
		if($total_rows<=0){
			if($amount>0){
				$stmt2 = $DBH->prepare("INSERT INTO Transaction (trans_type,identity,status,  user_proxy_id, counterid, game_session_id, amount ,currency_type ) VALUES (?, ?,?,?,?,?,?	,?)");
				$stmt2->execute(['debit','withdrawal','approved',$userUuid, $transactionUuid,'0',$amount,$currency]);
			   
			}
			
 		}  
		 
		
        return self::balances($arguments);
    }
	 private static function balances($arguments)
    {
		global $DBH;
		$userUuid =  $arguments['userUuid'];   
        //and trans_type='credit' 
		$stmt = $DBH->prepare("select  currency_type, sum(amount) as amount from 	Transaction where user_proxy_id=:user_proxy_id  group by currency_type");
		$stmt->bindValue(':user_proxy_id', $userUuid); 
	  	$stmt->execute();
 		//$balance_detail = $stmt->fetchAll();
		$balance_array = array();
		while ($row = $stmt->fetchObject()) {
			 
			$balance_array[$row->currency_type] = $row->amount;
			 
			
		}
		//{"balances":{"EUR":"123400","XBT":"10286000"}}
		 $array_message = array('balances'=>$balance_array);
         return $array_message;
    }
	 private static function getBalances($arguments)
    {
		global $DBH;
		$userUuid =  $arguments['userUuid'];   
        //  and trans_type='credit' 
		$stmt = $DBH->prepare("select  currency_type, sum(amount) as amount from 	Transaction where user_proxy_id=:user_proxy_id group by currency_type");
		$stmt->bindValue(':user_proxy_id', $userUuid); 
	  	$stmt->execute();
 		//$balance_detail = $stmt->fetchAll();
		$balance_array = array();
		$total_rows = $stmt->rowCount(); 
	 	if($total_rows>0){
			while ($row = $stmt->fetchObject()) {
				
				$balance_array[$row->currency_type] = $row->amount;
				
				
			}
			return $balance_array;
		} else {
			//ImplementationException
			$code = 404;
			$message = "Invalid params";
			$data    = 'User does not exist';
			
			throw new ApplicationException($message, $code, $data);
		}
		
		
         
    }
}
