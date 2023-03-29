<?php 
function getApiConfig($reqValue){
    $is_sanboxmode = false;
    $access_token_file = 'paypal_access_token.txt';
    if($is_sanboxmode){
        $api_endpoint ="https://api-m.sandbox.paypal.com";
    }else{
        $api_endpoint ="https://api-m.paypal.com";
    }
    $apiConfig = array("api_endpoint"=>$api_endpoint, "access_token_file" => $access_token_file,"server_authkey");
    if($reqValue=='all'){
        return $apiConfig;
    }else{
        return $apiConfig[$reqValue];
    }
}

?>