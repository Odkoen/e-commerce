<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

include('Crypto.php');

if(!checkloggedin()){
    header("Location: ".$link['LOGIN']);
    exit();
}

if (isset($_SESSION['quickad'][$access_token]['payment_type'])) {
    $currency = $config['currency_code'];
    $title = $_SESSION['quickad'][$access_token]['name'];
    $amount = $_SESSION['quickad'][$access_token]['amount'];

    $_SESSION['quickad'][$access_token]['merchantOrderId'] = $access_token;

    $user_id = $_SESSION['user']['id'];
    $userdata = get_user_data(null,$user_id);
    $user_name = filter_var($userdata['name'], FILTER_SANITIZE_STRING);
    $user_email = filter_var($userdata['email'], FILTER_SANITIZE_STRING);
    $phone = filter_var($userdata['phone'], FILTER_SANITIZE_STRING);
    $address = filter_var($userdata['address'], FILTER_SANITIZE_STRING);
    $country = filter_var($userdata['country'], FILTER_SANITIZE_STRING);

    //URL
    $merchant_id=get_option('CCAVENUE_MERCHANT_KEY');//Shared by CCAVENUES
    $access_code=get_option('CCAVENUE_ACCESS_CODE');//Shared by CCAVENUES
    $working_key=get_option('CCAVENUE_WORKING_KEY');//Shared by CCAVENUES

    $_POST['tid'] = time().rand(111,999);
    $_POST['merchant_id'] = $merchant_id;
    $_POST['order_id'] = uniqid();
    $_POST['amount'] = $amount;
    $_POST['currency'] = 'INR';
    $_POST['redirect_url'] = $link['IPN']."/ccavenue/".$access_token;
    $_POST['cancel_url'] = $link['PAYMENT'].$access_token."/ccavenue/cancel";
    $_POST['language'] = 'EN';
    //Additional
    $_POST['billing_name'] = $user_name;
    $_POST['billing_email'] = $user_email;
    $_POST['billing_tel'] = $phone;
    $_POST['billing_address'] = $address;
    $_POST['billing_country'] = $country;

    $merchant_data='';

    foreach ($_POST as $key => $value){
        $merchant_data.=$key.'='.$value.'&';
    }

    $encrypted_data=encrypt($merchant_data,$working_key); // Method for encrypting the data.

    $production_url='https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction&encRequest='.$encrypted_data.'&access_code='.$access_code;
    $url = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
    ?>
    <html>
    <head>
        <title>Redirecting...</title>
    </head>
    <body>
    <p>Please do not refresh this page...</p>
    <form method="post" name="redirect" action="<?php echo htmlspecialchars($url,ENT_QUOTES ) ?>">
        <?php
        echo "<input type=hidden name=encRequest value='".htmlspecialchars($encrypted_data,ENT_QUOTES)."'>";
        echo "<input type=hidden name=access_code value='".htmlspecialchars($access_code,ENT_QUOTES )."'>";
        ?>
    </form>
    <script language='javascript'>document.redirect.submit();</script>
    </body>
    </html>
    <?php
    exit;
}
else {
    error($lang['INVALID_TRANSACTION'], __LINE__, __FILE__, 1);
    exit();
}

?>