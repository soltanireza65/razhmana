<?php

require_once 'Jibit.class.php';


if (empty($_POST['amount']) || empty($_POST['purchaseId']) || empty($_POST['status'])) {
    echo 'No data found.';
    return false;
}


//get data from query string
$amount = $_POST['amount'];
$refNum = $_POST['purchaseId'];
$state = $_POST['status'];



// Your Api Key :
$apiKey = 'W6HLqqtums';
// Your Api Secret :
$apiSecret = 'i7BwXPEDSWftfJLFBdniRzsWCMEYUg7Fo3_8HeJ6q5Rksx2KSz';


/** @var Jibit $jibit */
$jibit = new Jibit($apiKey, $apiSecret);



// Making payment verify
$requestResult = $jibit->paymentVerify($refNum);
if (!empty($requestResult['status']) && $requestResult['status'] === 'SUCCESSFUL') {
    //successful result
    echo 'Successful! refNum:' . $refNum .PHP_EOL;

    //show session detail
    $order = $jibit->getOrderById($refNum);
    if (!empty($order['elements'][0]['pspMaskedCardNumber'])){
        echo 'payer card pan mask: ' .$order['elements'][0]['pspMaskedCardNumber'];
    }

    return false;
}
//fail result and show the error
echo 'Payment fail.';



