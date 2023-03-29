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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $logstxt = $data_json;
 $data_arr = json_decode($data_json, true);
  
    if (isset($data_arr['invoice_id'])) {
   
        $invoiceLinkBody = '{"send_to_recipient": false, "send_to_invoicer": false}';
        $get_invoice_url = paypalPost($invoice_endpoint . '/' . $data_arr['invoice_id'] . '/send', $invoiceLinkBody, $data_arr['client_id'],$data_arr['client_secret']);
        if (isset($get_invoice_url['response']['details'])) {
            if ($get_invoice_url['response']['details'][0]['issue'] == 'INVOICE_ALREADY_SHARED') {
                echo (json_encode(array('is_error' => false, 'invoive_url' => '', 'is_shared' => 'yes')));
            } else {
               echo (json_encode(array('is_error' => true, 'msg'=>$get_invoice_url['response']['details'])));
            }
        }
         file_put_contents('response.log',json_encode($get_invoice_url['response']). "\n", FILE_APPEND);
        if (isset($get_invoice_url['response']['href'])) {
            echo (json_encode(array('is_error' => false, 'invoive_url' => $get_invoice_url['response']['href'], 'is_shared' => 'yes')));
            // file_put_contents('pp_success_response.json', json_encode($get_invoice_url['response']));
        }

    }

}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $logstxt = $data_json; 
    if (isset($_GET['invoice_id'])) {
        $invoiceLinkBody = '{"send_to_recipient": false, "send_to_invoicer": false}';
        $get_invoice_status = paypalGet($invoice_endpoint . '/' . $_GET['invoice_id'], null, $_GET['client_id'], $_GET['client_secret']);
        if (isset($get_invoice_status['response']['name'])) {
            if ($get_invoice_status['response']['name'] == 'RESOURCE_NOT_FOUND') {
                echo (json_encode(array('is_error' => false, 'invoice_status' => 'NOT_FOUND', 'is_shared' => 'yes')));
            } else {
                echo (json_encode(array('is_error' => true)));
            }
        }  
        if (isset($get_invoice_status['response']['status'])) {
            echo (json_encode(array('is_error' => false, 'invoice_status' => $get_invoice_status['response']['status']))); 
        }

    }
}

?>