<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

include('Crypto.php');

if(!checkloggedin()){
    header("Location: ".$link['LOGIN']);
    exit();
}

// manually set action for paytm payments
if (isset($_GET['access_token']) && isset($_GET['i']) && $_GET['i'] == 'ccavenue') {
    responseReturn();
}else{
    error($lang['PAGE_NOT_FOUND'], __LINE__, __FILE__, 1);
    exit();
}

/**
 * Execute purchase product after successful payment
 */
function responseReturn()
{
    global $config;
    $error = '';
    $access_token = filter_var($_GET["access_token"], FILTER_SANITIZE_STRING);

    $working_key = get_option('CCAVENUE_WORKING_KEY');        //Working Key should be provided here.
    $encResponse = filter_var($_POST["encResp"], FILTER_SANITIZE_STRING);            //This is the response sent by the CCAvenue Server
    $rcvdString = decrypt($encResponse, $working_key);        //Crypto Decryption used as per the specified working key.
    $order_status = "";
    $decryptValues = explode('&', $rcvdString);
    $dataSize = sizeof($decryptValues);

    for ($i = 0; $i < $dataSize; $i++) {
        $information = explode('=', $decryptValues[$i]);
        if ($i == 3) $order_status = $information[1];
    }

    if ($order_status === "Success") {
        payment_success_save_detail($access_token);
        exit();

    } else if ($order_status === "Aborted") {
        $error_msg = "Thank you for shopping with us.We will keep you posted regarding the status of your order through e-mail";
        payment_fail_save_detail($access_token);
        payment_error("error",$error_msg,$access_token);
        exit();
    } else if ($order_status === "Failure") {
        $error_msg = "Thank you for shopping with us.However,the transaction has been declined.";
        payment_fail_save_detail($access_token);
        payment_error("error",$error_msg,$access_token);
        exit();
    } else {
        $error_msg = "Security Error. Illegal access detected";
        payment_fail_save_detail($access_token);
        payment_error("error",$error_msg,$access_token);
        exit();
    }
}