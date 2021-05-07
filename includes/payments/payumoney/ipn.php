<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

if(!checkloggedin()){
    header("Location: ".$link['LOGIN']);
    exit();
}
if (isset($_SESSION['quickad'][$access_token]['payment_type'])) {
    $postdata = $_POST;
    $msg = '';
    if (isset($postdata['key'])) {

        $payumoney_merchant_key = get_option('payumoney_merchant_key');
        $payumoney_merchant_salt = get_option('payumoney_merchant_salt');

        $salt				=   $payumoney_merchant_salt;
        $key				=   filter_var($postdata['key'], FILTER_SANITIZE_STRING);
        $txnid 				= 	filter_var($postdata['txnid'], FILTER_SANITIZE_STRING);
        $amount      		= 	filter_var($postdata['amount'], FILTER_SANITIZE_STRING);
        $productInfo  		= 	filter_var($postdata['productinfo'], FILTER_SANITIZE_STRING);
        $firstname    		= 	filter_var($postdata['firstname'], FILTER_SANITIZE_STRING);
        $email        		=	filter_var($postdata['email'], FILTER_SANITIZE_STRING);
        $udf5				=   filter_var($postdata['udf5'], FILTER_SANITIZE_STRING);
        $mihpayid			=	filter_var($postdata['mihpayid'], FILTER_SANITIZE_STRING);
        $status				= 	filter_var($postdata['status'], FILTER_SANITIZE_STRING);
        $resphash			= 	filter_var($postdata['hash'], FILTER_SANITIZE_STRING);
        //Calculate response hash to verify
        $keyString 	  		=  	$key.'|'.$txnid.'|'.$amount.'|'.$productInfo.'|'.$firstname.'|'.$email.'|||||'.$udf5.'|||||';
        $keyArray 	  		= 	explode("|",$keyString);
        $reverseKeyArray 	= 	array_reverse($keyArray);
        $reverseKeyString	=	implode("|",$reverseKeyArray);
        $CalcHashString 	= 	strtolower(hash('sha512', $salt.'|'.$status.'|'.$reverseKeyString));


        if ($status == 'success'  && $resphash == $CalcHashString) {
            $msg = "Transaction Successful and Hash Verified...";
            //Do success order processing here...
            payment_success_save_detail($access_token);
            exit();
        }
        else {
            //tampered or failed
            $msg = "Payment failed for Hasn not verified...";
            payment_fail_save_detail($access_token);
            mail($config['admin_email'],'Paystack error in '.$config['site_title'],'Paystack error in '.$config['site_title'].', status from Payumoney');

            $error_msg = "Transaction was not successful: Last Payumoney gateway response was: ".$msg;
            payment_error("error",$error_msg,$access_token);
            exit();
        }
    }
}else {
    error($lang['INVALID_TRANSACTION'], __LINE__, __FILE__, 1);
    exit();
}
?>