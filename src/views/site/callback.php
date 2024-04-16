<?php

require_once './core/Jibit.class.php';
global $lang, $Settings;

use MJ\Security\Security;
use MJ\Utils\Utils;

include_once 'header-footer.php';

enqueueScript('menu-init-js', '/dist/libs/lottie/lottie-player.js');

getLoginHeader($lang['payment_status'], 1, 'desc', false);


//get data from query string

$amount = $_POST['amount'];
$purchaseId = $_POST['purchaseId'];
$refNum = $_REQUEST['ref_id'];
$state = $_POST['status'];

if (isset($_POST['amount']) && isset($_REQUEST['ref_id']) && isset($_POST['status'])) {


// Your Api Key :
    $apiKey = 'W6HLqqtums';
// Your Api Secret :
    $apiSecret = 'i7BwXPEDSWftfJLFBdniRzsWCMEYUg7Fo3_8HeJ6q5Rksx2KSz';


    /** @var Jibit $jibit */
    $jibit = new Jibit($apiKey, $apiSecret);


// Making payment verify
    $requestResult = $jibit->paymentVerify($purchaseId);
    file_put_contents('./db/transactions/' . $refNum . '.json', json_encode($_POST) . json_encode($requestResult), FILE_APPEND);

    if (!empty($_POST['status']) && $_POST['status'] === 'SUCCESSFUL') {
        //successful result
        echo 'Successful! refNum:' . $refNum . PHP_EOL;

        //show session detail
        $order = $jibit->getOrderById($refNum);
        if (!empty($order['elements'][0]['pspMaskedCardNumber'])) {
            echo 'payer card pan mask: ' . $order['elements'][0]['pspMaskedCardNumber'];
        }

        Transactions::updateDepositOnline($refNum, $amount / 10, "paid");
        ?>
        <style>
            @media (min-width: 768px)
            body[data-leftbar-size="condensed"]:not([data-layout="compact"]) {
                min-height: unset !important;
            }
        </style>
        <script src="/dist/libs/lottie/lottie-player.js"></script>
        <main class="container mj-callback-body" style="padding-bottom: 180px;">
            <div class="mj-callback-message-card">
                <div class="mj-callback-animation">
                    <lottie-player src="/dist/lottie/done.json" background="transparent" speed="1"
                                   style="width: 200px; height: 200px;" loop autoplay></lottie-player>
                </div>
                <div class="mj-callback-texts">
                    <div><?= $lang['u_online_payment_success_title'] ?></div>
                    <p><?= $lang['u_online_payment_success_desc'] ?></p>
                    <div><?= $lang['u_online_payment_success_timer'] ?></div>
                </div>
                <a href="https://ntirapp.com" ><?=$lang['return_to_home']?> </a>
            </div>
        </main>
        <?php

        return false;
    } else {

        Transactions::updateDepositOnline($refNum, $amount / 10, "rejected_deposit");

        ?>
        <style>
            @media (min-width: 768px)
            body[data-leftbar-size="condensed"]:not([data-layout="compact"]) {
                min-height: unset !important;
            }
        </style>
        <script src="/dist/libs/lottie/lottie-player.js"></script>
        <main class="container mj-callback-body" style="padding-bottom: 180px;">
            <div class="mj-callback-message-card">
                <div class="mj-callback-animation">
                    <lottie-player src="/dist/lottie/reject.json" background="transparent" speed="1"
                                   style="width: 200px; height: 200px;" loop autoplay></lottie-player>
                </div>
                <div class="mj-callback-texts">
                    <div><?= $lang['u_online_payment_failed_title'] ?></div>
                    <p><?= $lang['u_online_payment_failed_desc'] ?></p>
                    <div><?= $lang['u_online_payment_failed_timer'] ?></div>
                </div>
                <a href="ntirapp://open"><?=$lang['return_to_home']?> </a>

            </div>
        </main>
        <script>
            setTimeout(function () {
                window.location.replace("/user/wallet/trxlist/1/all")
            }, 2000)
        </script>
        <?php
    }
//fail result and show the error


    getFooter('', false);
} else {
    header('Location: ' . SITE_URL);
}