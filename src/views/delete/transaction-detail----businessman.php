<?php

global $lang;

use MJ\Router\Router;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();
    $tx = User::getTransactionDetail($user->UserId, $_REQUEST['id']);
    if ($tx->status == 200) {
        $tx = $tx->response;
        include_once 'header-footer.php';

        enqueueScript('menu-init-js', '/dist/js/businessman/dashboard.init.js');

        getHeader($lang['d_transaction_detail_title']);

        ?>
        <main class="container" style="padding-bottom: 180px;">
            <div class="row mt-3">
                <div class="col-12">
                    <div class="mj-card">
                        <div class="card-body">
                            <div class="mj-about-header mb-4">
                                <a href="javascript:void(0);" class="d-flex align-items-center"
                                   onclick="history.back()">
                                    <img src="/dist/images/icons/caret-right.svg" class="mj-profile-items-icon me-2"
                                         alt="back">
                                    <span><?= $lang['back_prev'] ?></span>
                                </a>

                            </div>

                            <div class="mj-transaction-card">
                                <div class="mj-ticket-header text-dark mj-fw-500 mj-font-14 px-3">
                                    <?= $lang['d_transaction_detail_title'] ?>
                                </div>

                                <div class="card-body py-0">
                                    <div class="mj-tx-item d-flex align-items-center justify-content-between py-2">
                                        <span><?= $lang['transaction_list_date'] ?> :</span>
                                        <span dir="ltr"><?= ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('Y/m/d', $tx->TransactionTime) : date('Y-m-d', $tx->TransactionTime) ?></span>
                                    </div>

                                    <div class="mj-tx-item d-flex align-items-center justify-content-between py-2">
                                        <span><?= $lang['tx_authority'] ?> :</span>
                                        <span dir="ltr"><?= (!empty($tx->TransactionAuthority)) ? $tx->TransactionAuthority : '-' ?></span>
                                    </div>

                                    <div class="mj-tx-item d-flex align-items-center justify-content-between py-2">
                                        <span><?= $lang['tx_tacking_code'] ?> :</span>
                                        <span dir="ltr"><?= (!empty($tx->TransactionTrackingCode)) ? $tx->TransactionTrackingCode : '-' ?></span>
                                    </div>

                                    <div class="mj-tx-item d-flex align-items-center justify-content-between py-2">
                                        <span><?= $lang['tx_amount'] ?> :</span>
                                        <div>
                                            <span><?= number_format($tx->TransactionAmount) ?></span>
                                            <span style="font-weight: 100;font-size: 11px;"><?= $tx->TransactionCurrency ?></span>
                                        </div>
                                    </div>

                                    <div class="mj-tx-item d-flex align-items-center justify-content-between py-2">
                                        <span><?= $lang['tx_status'] ?> :</span>
                                        <span dir="ltr">
                                            <?php
                                            if ($tx->TransactionStatus == 'completed') {
                                                echo $lang['tx_status_completed'];
                                            } elseif (in_array($tx->TransactionStatus, ['pending', 'pending_deposit'])) {
                                                echo $lang['tx_status_pending'];
                                            } elseif (in_array($tx->TransactionStatus, ['rejected', 'rejected_deposit'])) {
                                                echo $lang['tx_status_rejected'];
                                            } elseif ($tx->TransactionStatus == 'expired') {
                                                echo $lang['tx_status_expired'];
                                            } elseif ($tx->TransactionStatus == 'unpaid') {
                                                echo $lang['tx_status_unpaid'];
                                            } elseif ($tx->TransactionStatus == 'paid') {
                                                echo $lang['tx_status_paid'];
                                            } else {
                                                echo $lang['tx_status_unknown'];
                                            }
                                            ?>
                                        </span>
                                    </div>

                                    <div class="mj-tx-item d-flex align-items-center justify-content-between py-2">
                                        <span><?= $lang['tx_type'] ?> :</span>
                                        <span dir="ltr">
                                            <?php
                                            if ($tx->TransactionType == 'deposit') {
                                                if ($tx->TransactionDepositType == 'receipt') {
                                                    echo $lang['tx_type_deposit_receipt'];
                                                } else {
                                                    echo $lang['tx_type_deposit_online'];
                                                }
                                            } else {
                                                echo $lang['wallet_withdraw'];
                                            }
                                            ?>
                                        </span>
                                    </div>

                                    <?php
                                    if ($tx->TransactionType == 'withdraw') {
                                        ?>
                                        <div class="mj-tx-item d-flex align-items-center justify-content-between py-2">
                                            <span><?= $lang['tx_destination'] ?> :</span>
                                            <span dir="ltr">
                                                <?= $tx->TransactionDestination->CardIBAN ?>
                                            </span>
                                        </div>
                                        <?php
                                    }

                                    if ($tx->TransactionDepositType == 'receipt') {
                                        ?>
                                        <div class="mj-tx-item py-2">
                                            <span><?= $lang['tx_receipt'] ?> :</span>
                                            <img class="d-block w-100 mt-2 mx-auto"
                                                 src="<?= Utils::fileExist($tx->TransactionOptions->receipt, BOX_EMPTY) ?>"
                                                 alt="receipt"
                                                 style="max-width: 400px;">
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mj-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="/dist/images/icons/headset.svg" class="mj-d-icon-box me-2" alt="support">
                                <div>
                                    <span class="mj-d-icon-title"><?= $lang['d_cargo_support'] ?></span>
                                    <p class="mj-d-cargo-item-desc mb-0">
                                        <?= $lang['d_cargo_support_sub_title'] ?>
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex align-items-center flex-nowrap overflow-auto">
                                <a href="tel:<?= Utils::getFileValue("settings.txt", 'support_call') ?>"
                                   class="mj-btn mj-d-btn-call me-2"
                                   style="flex: 0 0 auto; min-height: 34px;">
                                    <img src="/dist/images/icons/circle-phone.svg" class="me-1" alt="call"/>
                                    <?= $lang['d_cargo_call'] ?>
                                </a>

                                <a href="https://wa.me/<?= Utils::getFileValue("settings.txt", 'whatsapp') ?>"
                                   class="mj-btn mj-d-btn-whatsapp me-2"
                                   style="flex: 0 0 auto; min-height: 34px;">
                                    <img src="/dist/images/icons/whatsapp.svg" class="me-1" alt="whatsapp"/>
                                    <?= $lang['d_cargo_whatsapp'] ?>
                                </a>

                                <a href="/user/support" class="mj-btn mj-d-btn-ticekt me-2"
                                   style="flex: 0 0 auto; min-height: 34px;">
                                    <img src="/dist/images/icons/circle-envelope.svg" class="me-1" alt="ticket"/>
                                    <?= $lang['d_cargo_ticket'] ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php

        getFooter('', false);
    } else {
        Router::trigger404();
    }
} else {
    header('location: /login');
}