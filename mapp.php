<?php

require "common.php";
$invoice_endpoint = '/v2/invoicing/invoices'; 

// Disable error reporting
error_reporting(0);
// Disable display of errors
ini_set('display_errors', 0);

header('Content-Type: application/json');
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$ipAddress = $_SERVER['REMOTE_ADDR'];
$domainName = $_SERVER['HTTP_HOST'];
$headers = getallheaders();
$request_auth_key = $headers['server_auth_key'];
$data_json = file_get_contents('php://input');
if($_SERVER['REQUEST_METHOD']=='POST'){
    $data_arr = json_decode($data_json,true);
    $request_data =$data_arr['data']; 
    $jaosnBody = json_encode($request_data); 
    $call_invoice =paypalPost($invoice_endpoint,$jaosnBody,$data_arr ['client_id'],$data_arr['client_secret']);
    if($call_invoice['isError']==true){ 
        // file_put_contents('pp_response.json',$call_invoice);
        echo (json_encode(array('is_error'=>true,'message'=>'Null Response')));
    }else{ 
        if(isset($call_invoice['response']['id'])){ 
            echo (json_encode(array('is_error'=>false,'invoice_id'=>$call_invoice['response']['id'],'invoice_url'=>$call_invoice['response']['detail']['metadata']['recipient_view_url'])));           
        }
    }

}


?>