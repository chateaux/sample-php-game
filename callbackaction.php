<?php
session_start();
require_once("dbconnection.php");
include("functionClass.php");
$functionClass = new FunctionClass();
require_once __DIR__ . '/vendor/autoload.php';
use Datto\JsonRpc\Server;
use Datto\JsonRpc\Examples\Api;

$server = new Server(new Api());
$private_key = $functionClass->get_private_key_for_public_key();
$hex_private_key = hex2bin($private_key);
// User hit the end point API with $data, $signature  
if (DEBUG == 1) {
    $req_dump = print_r($_SERVER, true);
    $fp = file_put_contents('debug/request.txt', $req_dump, FILE_APPEND);
} //DEBUG == 1
$received_signature = explode(":", $_SERVER['HTTP_X_HMAC']);
if ($_SERVER['CONTENT_TYPE'] == "application/json") {
    $data_json = file_get_contents('php://input');
    if (DEBUG == 1) {
        $req_dump = print_r($data_json, true);
        $fp = file_put_contents('debug/request.txt', $data_json, FILE_APPEND);
    } //DEBUG == 1
} //$_SERVER['CONTENT_TYPE'] == "application/json"
else {
    $request = json_decode($data_json, true);
    $id = $request['id'];
    $error = array(
        'code' => '32602',
        'message' => 'Invalid params',
        'data' => 'Invalid Content type'
    );
    $array_message = array(
        'jsonrpc' => '2.0',
        'id' => $id,
        'error' => $error
    );
    $data_json = json_encode($array_message);
    die();
    
}
$computed_signature = hash_hmac('sha256', $data_json, $hex_private_key);
if ($received_signature[1] == $computed_signature) {
    $reply = $server->reply($data_json);
    $computed_respond_signature = hash_hmac('sha256', $reply, $hex_private_key);
    header('Content-Type: application/json');
    header('X-HMAC:sha256:' . $computed_respond_signature);
    echo $reply;
    if (DEBUG == 1) {
        $fp = file_put_contents('debug/response.txt', $reply, FILE_APPEND);
    } //DEBUG == 1
    die();
} //$received_signature[1] == $computed_signature
else {
    $request = json_decode($data_json, true);
    $id = $request['id'];
    $error = array(
        'code' => '32602',
        'message' => 'Invalid params',
        'data' => 'Invalid signature'
    );
    $array_message = array(
        'jsonrpc' => '2.0',
        'id' => $id,
        'error' => $error
    );
    $data_json = json_encode($array_message);
    echo $data_json;
    die();
}