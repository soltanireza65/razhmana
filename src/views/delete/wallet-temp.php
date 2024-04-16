<?php

global $lang;

use MJ\Security\Security;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();
    $balance = User::getBalance($user->UserId);
    include_once 'header-footer.php';

    enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');

    enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
    enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    enqueueScript('menu-init-js', '/dist/js/driver/dashboard.init.js');
    enqueueScript('wallet-init-js', '/dist/js/driver/wallet.init.js');

    getHeader($lang['d_wallet_title']);

    ?>
    <main class="container" style="padding-bottom: 180px;">
        <div class="row mt-3">
            <div class="col-12">
                <div class="mj-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <img src="/dist/images/icons/wallet(green).svg" class="mj-d-icon-box me-2" alt="wallet">
                            <span class="mj-d-icon-title"><?= $lang['d_wallet_title'] ?></span>
                        </div>

                        <?php
                        foreach ($balance->response as $item) {
                            ?>
                            <div class="mj-wallet-balance bg-light d-flex justify-content-between p-3 mb-3">
                                <span><?= $lang['wallet_balance'] ?> :</span>
                                <div class="mj-wallet-balance-value">
                                    <span><?= number_format($item->BalanceAvailable) ?></span>
                                    <span style="font-weight: 100; font-size: 11px"><?= $item->BalanceCurrency ?></span>
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                        <span class="d-block mj-fw-500 mj-font-13 mb-1"><?= $lang['wallet_deposit'] ?> :</span>

                        <div class="mj-wallet-add-value bg-light p-3">
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item d-none" role="presentation">
                                    <button class="nav-link" id="online-tab-btn" data-bs-toggle="pill"
                                            data-bs-target="#tab-online" type="button" role="tab"
                                            aria-controls="tab-online" aria-selected="false">
                                        <?= $lang['wallet_deposit_online'] ?>
                                    </button>
                                </li>
                                <li class="nav-item w-100" role="presentation">
                                    <button class="nav-link active" id="receipt-tab-btn" data-bs-toggle="pill"
                                            data-bs-target="#tab-receipt" type="button" role="tab"
                                            aria-controls="tab-receipt" aria-selected="true">
                                        <?= $lang['wallet_deposit_receipt'] ?>
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade" id="tab-online" role="tabpanel"
                                     aria-labelledby="online-tab-btn">
                                    <div class="mj-value-default">
                                        <a href="javascript.void(0)">500,000 تومان</a>
                                        <a href="javascript.void(0)">1,000,000 تومان</a>
                                        <a href="javascript.void(0)">5,000,000 تومان</a>
                                    </div>
                                    <div class="mj-wallet-input">
                                        <div class="mj-plus-btn">+</div>
                                        <div class="mj-add-value-input">
                                            <input type="number" value="1"/>
                                        </div>
                                        <div class="mj-minus-btn">-</div>
                                    </div>
                                    <div class="mj-add-value-btn mt-3 text-center">
                                        <a href="#">پرداخت</a>
                                    </div>
                                </div>

                                <div class="tab-pane fade show active" id="tab-receipt" role="tabpanel"
                                     aria-labelledby="receipt-tab-btn">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="deposit-amount" class="mj-fw-500 mj-font-12 mb-1">
                                                    <?= $lang['wallet_deposit_amount'] ?> :
                                                </label>
                                                <div class="mj-input-filter-box d-flex"
                                                     style="padding: 4px 16px 4px 4px">
                                                    <input type="text" inputmode="tel" id="deposit-amount"
                                                           name="deposit-amount"
                                                           class="mj-input-filter text-dark mj-fw-400 mj-font-13"
                                                           style="min-height: 38px;">

                                                    <select class="mj-custom-form-select" id="currency-unit"
                                                            name="currency-unit">
                                                        <?php
                                                        $currencies = Driver::getCurrencyList();
                                                        foreach ($currencies->response as $item) {
                                                            ?>
                                                            <option value="<?= $item->CurrencyId ?>"><?= $item->CurrencyName ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="tx-authority" class="mj-fw-500 mj-font-12 mb-1">
                                                    <?= $lang['wallet_deposit_authority'] ?> :
                                                </label>
                                                <div class="mj-input-filter-box">
                                                    <input type="text" inputmode="tel" id="tx-authority"
                                                           name="tx-authority"
                                                           class="mj-input-filter text-dark mj-fw-400 mj-font-13"
                                                           style="min-height: 38px;">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-3">
                                                <div class="mj-input-filter-box pe-1">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="mj-font-12 me-1">
                                                            <?= $lang['wallet_deposit_receipt_message'] ?>
                                                        </div>
                                                        <input type="file" id="receipt" name="receipt" hidden>
                                                        <label for="receipt"
                                                               class="mj-btn-more mj-font-12 py-2 px-3 ms-1">
                                                            <?= $lang['choose_file'] ?>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="mj-wallet-alert">
                                                    <span>
                                                        <img src="/dist/images/icons/circle-infon.svg" class="me-1"
                                                             alt="exclamation">
                                                        <?= $lang['wallet_deposit_receipt_alert'] ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div>
                                                <input type="hidden" id="token-receipt" name="token-receipt"
                                                       value="<?= Security::initCSRF('deposit-receipt') ?>">
                                                <div class="mj-add-value-btn text-center">
                                                    <a href="javascript:void(0);" id="submit-receipt">
                                                        <?= $lang['wallet_deposit_submit_receipt'] ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php

                foreach ($balance->response as $index=>$item) {
                    ?>
                    <div class="mj-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <img src="/dist/images/icons/inbox-out.svg" class="mj-d-icon-box me-2" alt="wallet">
                                <span class="mj-d-icon-title">
                                    <?= $lang['wallet_withdraw'] . ' ' . $item->BalanceCurrency ?>
                                </span>
                            </div>

                            <div class="mj-wallet-balance bg-light justify-content-between p-3">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="withdraw-amount" class="mj-fw-500 mj-font-12 mb-1">
                                                <?= $lang['wallet_withdraw_amount'] ?> :
                                            </label>
                                            <div class="mj-input-filter-box mb-2">
                                                <input type="text" inputmode="tel" id="withdraw-amount"
                                                       name="withdraw-amount"
                                                       class="mj-input-filter text-dark mj-fw-400 mj-font-13"
                                                       style="min-height: 38px;">
                                            </div>

                                            <div class="mj-wallet-balance-value align-items-center d-flex">
                                                <span class="pe-1"><?= $lang['wallet_balance'] ?> :</span>
                                                <a href="javascript:void(0);" data-select-all>
                                                    <span><?= number_format($item->BalanceAvailable) ?></span>
                                                    <span style="font-weight: 100; font-size: 11px;padding-right: 5px;"><?= $item->BalanceCurrency ?></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="withdraw-dest" class="mj-fw-500 mj-font-12 mb-1">
                                                <?= $lang['wallet_withdraw_destination'] ?> :
                                            </label>
                                            <div class="mj-custom-select mb-2">
                                                <select name="withdraw-dest-<?=$index?>" id="withdraw-dest-<?=$index?>"
                                                        data-width="100%" class="withdraw-dest">
                                                    <option value="-1">
                                                        <?= $lang['wallet_withdraw_destination_placeholder'] ?>
                                                    </option>
                                                    <?php
                                                    $creditCards = User::getCreditCardsList($user->UserId, 'accepted');
                                                    foreach ($creditCards->response as $card) {
                                                        ?>
                                                        <option value="<?= $card->CardId ?>"><?= $card->CardAccountNumber ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="mj-add-credit-card">
                                                <a href="/driver/add-card"><?= $lang['wallet_add_card'] ?></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div>
                                            <input type="hidden" id="token-withdraw" name="token-withdraw"
                                                   value="<?= Security::initCSRF('withdraw-request') ?>">
                                            <div class="mj-add-value-btn text-center">
                                                <a href="javascript:void(0);" id="submit-withdraw"
                                                   data-currency="<?= $item->CurrencyId ?>">
                                                    <?= $lang['wallet_withdraw_submit'] ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>




                <div class="mj-card">
                    <div class="card-body">
                        <div class="mj-wallet-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="/dist/images/icons/inbox-out.svg" class="mj-d-icon-box me-2" alt="wallet">
                                <span class="mj-d-icon-title"><?= $lang['wallet_transactions_history'] ?></span>
                            </div>

                            <div class="mj-add-value-btn text-center">
                                <a class="px-3"
                                   href="/driver/transactions"><?= $lang['wallet_transactions_history'] ?></a>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="mj-card">
                    <div class="card-body">
                        <div class="mj-wallet-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="/dist/images/icons/inbox-out.svg" class="mj-d-icon-box me-2" alt="wallet">
                                <span class="mj-d-icon-title"><?= $lang['credit_cards_list'] ?></span>
                            </div>

                            <div class="mj-add-value-btn text-center">
                                <a class="px-3" href="/driver/credit-cards"><?= $lang['credit_cards_list'] ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-processing" data-bs-backdrop="static" data-bs-keyboard="false"
             role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="text-center my-3">
                            <lottie-player src="/dist/lottie/loading.json" class="mx-auto"
                                           style="max-width: 400px;" speed="1" loop
                                           autoplay></lottie-player>

                            <h6 class="mb-0"><?= str_replace('#ACTION#', $lang['wallet_deposit_submit_receipt'], $lang['b_info_processing']) ?>
                                ...</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-submitted"
             role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="text-center my-3" id="submitting-alert">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php

    getFooter('', false);
} else {
    header('location: /login');
}