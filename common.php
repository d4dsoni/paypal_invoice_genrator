<?php

require "env.php";
function genrateToken($client_id, $client_secret)
{
    $access_token_file = getApiConfig('access_token_file');
    $api_config = getApiConfig('all');
    $api_endpoint = $api_config['api_endpoint'] . '/v1/oauth2/token';
    // Set up the authorization header
    $headers = array(
        'Accept: application/json',
        'Accept-Language: en_US',
        'Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret)
    );
    // Set up the API request data
    $data = http_build_query(array('grant_type' => 'client_credentials'));
    // Send the request to PayPal
    $ch = curl_init();
    curl_setopt_array(
        $ch,
        array(
            CURLOPT_URL => $api_endpoint,
            CURLOPT_RETURNTRANSFER => true, 
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            // CURLOPT_VERBOSE => 1,
            // CURLOPT_STDERR => $fp,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers,
        )
    );
    $response = curl_exec($ch);
    curl_close($ch); 
    if (!$response) {
    } else {
        $json_response = json_decode($response, true);
        if (isset($json_response['access_token'])) {
            file_put_contents($access_token_file, $json_response['access_token']);
        } else {
        }
    }
}
// genrateToken();

function paypalPost($url, $JsonBody, $client_id, $client_secret)
{
    $access_token_file = getApiConfig('access_token_file');
    // Try to get the access token from the file
    if (file_exists($access_token_file)) {
        $access_token = file_get_contents($access_token_file);
    }
    if (empty($access_token)) {
        genrateToken($client_id, $client_secret);
    }
    $api_endpoint = getApiConfig('api_endpoint') . $url;
    // Try to get the access token from the file
    if (file_exists($access_token_file)) {
        $access_token = file_get_contents($access_token_file);
    }
    // $fp = fopen(dirname(__FILE__) . '/errorlog.txt', 'w');
    $ch = curl_init();
    curl_setopt_array(
        $ch,
        array(
            CURLOPT_URL => $api_endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_VERBOSE => 1,
            // CURLOPT_STDERR => $fp,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $JsonBody,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Prefer: return=representation',
                'Authorization: Bearer ' . $access_token
            ),
        )
    );
    // Set up the invoice data
    $response = curl_exec($ch);
    // file_put_contents('response.log', $response, FILE_APPEND);
    curl_close($ch);
    if (!$response) {
        return array('isError' => true, 'message' => 'Null Response', 'response' => $response, 'code' => 203);
    } else {
        $json_response = json_decode($response, true);
        if (array_key_exists("error", $json_response)) {
            if ($json_response['error'] == 'invalid_token') {
                genrateToken($client_id, $client_secret);
                return array('isError' => true, 'message' => 'invalid_token (Regenrating...)', 'response' => $json_response, 'code' => 403);
            } else {
                return array('isError' => true, 'message' => 'invalid_token (Regenrating...)', 'response' => $json_response, 'code' => 400);
            }
        } else {
            return array('isError' => false, 'message' => 'Success!', 'response' => $json_response, 'code' => 200);
        }
    }
}
function paypalGet($url, $JsonBody, $client_id, $client_secret)
{
    $access_token_file = getApiConfig('access_token_file');
    // Try to get the access token from the file
    if (file_exists($access_token_file)) {
        $access_token = file_get_contents($access_token_file);
    }
    if (empty($access_token)) {
        genrateToken($client_id, $client_secret);
    }
    $api_endpoint = getApiConfig('api_endpoint') . $url;
    // file_put_contents('response.log', $api_endpoint . "\n", FILE_APPEND);
    // Try to get the access token from the file
    if (file_exists($access_token_file)) {
        $access_token = file_get_contents($access_token_file);
    }
    // $fp = fopen(dirname(__FILE__) . '/errorlog.txt', 'w');
    $ch = curl_init();
    curl_setopt_array(
        $ch,
        array(
            CURLOPT_URL => $api_endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_VERBOSE => 1,
            // CURLOPT_STDERR => $fp,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $JsonBody,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Prefer: return=representation',
                'Authorization: Bearer ' . $access_token
            ),
        )
    );
    // Set up the invoice data
    $response = curl_exec($ch);
    // file_put_contents('response.log', $response, FILE_APPEND);
    curl_close($ch);
    if (!$response) {
        return array('isError' => true, 'message' => 'Null Response', 'response' => $response, 'code' => 203);
    } else {
        $json_response = json_decode($response, true);
        if (array_key_exists("error", $json_response)) {
            if ($json_response['error'] == 'invalid_token') {
                genrateToken($client_id, $client_secret);
                return array('isError' => true, 'message' => 'invalid_token (Regenrating...)', 'response' => $json_response, 'code' => 403);
            } else {
                return array('isError' => true, 'message' => 'invalid_token (Regenrating...)', 'response' => $json_response, 'code' => 400);
            }
        } else {
            return array('isError' => false, 'message' => 'Success!', 'response' => $json_response, 'code' => 200);
        }
    }
}
?>