<?php

require_once 'Jibit.class.php';

// Your Api Key :
$apiKey = 'test';
// Your Api Secret :
$apiSecret = 'test';

/** @var Jibit $jibit */
$jibit = new Jibit($apiKey, $apiSecret);

// Making payment request
// you should save the order details in DB, you need if for verify
$requestResult = $jibit->paymentRequest(10000, time(), '09142393101', 'https://ntirapp.com/purchases/ntirapp-www/callback');

var_dump($requestResult);
if (!empty($requestResult['pspSwitchingUrl'])) {
    //successful result and redirect to PG
    header('Location: ' . $requestResult['pspSwitchingUrl']);
}
if (!empty($requestResult['errors'])) {
    //fail result and show the error
    echo $requestResult['errors'][0]['code'] . ' ' . $requestResult['errors'][0]['message'];
}



