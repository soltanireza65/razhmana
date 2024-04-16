<?php
//payment
use MJ\Security\Security;
if (User::userIsLoggedIn()) {


    require_once SITE_ROOT  .'/core/Jibit.class.php';
// Your Api Key :
    $apiKey = 'W6HLqqtums';
// Your Api Secret :
    $apiSecret = 'i7BwXPEDSWftfJLFBdniRzsWCMEYUg7Fo3_8HeJ6q5Rksx2KSz';

    $jibit = new Jibit($apiKey, $apiSecret);

// Making payment request
// you should save the order details in DB, you need if for verify
    $refNumber = time();
    $amount = isset($_REQUEST['amount']) ? $_REQUEST['amount'] : 0;
    $requestResult = $jibit->paymentRequest($amount, $refNumber, 'ntirapp_users', "https://ntirapp.com/purchases/$refNumber/callback");
    $user_id = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
    if (!empty($requestResult['pspSwitchingUrl'])) {
//successful result and redirect to PG
        Transactions::depositOnline($user_id, $refNumber, $amount, 1);
        header('Location: ' . $requestResult['pspSwitchingUrl']);
    }
    if (!empty($requestResult['errors'])) {
//fail result and show the error
        echo $requestResult['errors'][0]['code'] . ' ' . $requestResult['errors'][0]['message'];
    }
}else{
    header('Location: ' . '/');
}