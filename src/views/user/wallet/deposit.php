<?php

global $lang;

use MJ\Security\Security;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();

    if ($user->UserAuthStatus == 'accepted') {
        User::checkBalanceIsGenerated($user->UserId);
        $balance = User::getBalance($user->UserId);


        $BalanceAvailable = 0;
        $BalanceInWithdraw = 0;
        $BalanceCurrency = '';
        foreach ($balance->response as $item) {
            if ($item->CurrencyId == (int)$_REQUEST['currencyId']) {
                $BalanceAvailable = $item->BalanceAvailable;
                $BalanceCurrency = $item->BalanceCurrency;
                $BalanceInWithdraw = $item->BalanceInWithdraw;
            }
        }
        if (empty($BalanceCurrency) || is_null($BalanceCurrency)) {
            header('location: /user/wallet');
        }
    }

    include_once getcwd() . '/views/user/header-footer.php';

    if ($user->UserAuthStatus == 'accepted') {
        enqueueStylesheet('FA-css', '/dist/libs/fontawesome/all.css');

        enqueueScript('FA-js', '/dist/libs/fontawesome/all.min.js');
        enqueueScript('deposit-js', '/dist/js/user/wallet/deposit.js');
    } else {
        enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    }

    getHeader($lang['d_wallet_title']);

    if ($user->UserAuthStatus == 'accepted') {
        ?>
        <section style="padding-top: 110px; ">
            <div class="mj-wallet-head-blue">
                <div class="mj-wallet-blue">
                    <?= $lang['u_deposit'] . " " . $BalanceCurrency; ?>
                </div>
                <svg viewBox="0 0 1920 145" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1920 0C1920 77.8627 1490.19 141 960 141C429.81 141 0 77.8627 0 0H1920Z" fill="#3CA4F6"/>
                </svg>
                <div class="mj-wallet-rial-balance-withdraw">
                    <div class="mj-balance">
                        <div class="mj-balance-title"><?= $lang['u_cash_enable_withdraw']; ?> :</div>
                        <div class="mj-balance-price">
                            <span><?= number_format($BalanceAvailable); ?></span>
                            <span><?= $BalanceCurrency; ?></span>
                        </div>
                        <div class="mj-more-balance-text mt-2">
                            <div class="mj-balance-title2"><?= $lang['u_balance_in_withdraw']; ?></div>
                            <div class="mj-balance-price2">
                                <span><?= number_format($BalanceInWithdraw); ?></span>
                                <span><?= $BalanceCurrency; ?></span>
                            </div>
                        </div>
                    </div>
                    <img src="<?= Utils::getCurrencyImage((int)$_REQUEST['currencyId'])->icon; ?>"
                         alt="<?= $BalanceCurrency; ?>">
                    <img id="show-balance-more" src="/dist/images/wallet/down-arrow.svg" alt="">
                </div>
        </section>
        <section class="mj-deposit-section">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active mj-deposit-btn-1"
                            id="pills-home-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#pills-home"
                            type="button"
                            role="tab"
                            aria-controls="pills-home"
                            aria-selected="true"><?= $lang['u_deposit_online']; ?>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link mj-deposit-btn-1"
                            id="pills-profile-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#pills-profile"
                            type="button"
                            role="tab"
                            aria-controls="pills-profile"
                            aria-selected="false"><?= $lang['wallet_deposit_receipt']; ?>
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active"
                     id="pills-home"
                     role="tabpanel"
                     aria-labelledby="pills-home-tab"
                     tabindex="0">
                    <div class="mj-online-deposit">
                        <form class="mj-online-deposit-form">
                            <label for="deposit-online"><?= $lang['wallet_deposit_amount']; ?> :</label>
                            <div class="mj-deposit-input">
                                <input type="text"
                                       inputmode="decimal"
                                       id="deposit-online"
                                       lang="en"
                                       name="deposit-online"
                                       placeholder="<?= $lang['u_enter_the_desired_amount']; ?>"
                                       minlength="1">
                                <span><?= $BalanceCurrency; ?></span>
                            </div>
                            <button id="submit-online"
                                    name="submit-online"
                                    data-tj-currency="<?= (int)$_REQUEST['currencyId']; ?>"
                                    type="button"><?= $lang['u_payment']; ?></button>
                        </form>
                    </div>
                </div>

                <div class="tab-pane fade"
                     id="pills-profile"
                     role="tabpanel"
                     aria-labelledby="pills-profile-tab"
                     tabindex="0">
                    <div class="mj-offline-deposit ">
                        <form class="mj-offline-deposit-form">
                            <label for="amount-offline"><?= $lang['wallet_deposit_amount']; ?> :</label>
                            <div class="mj-deposit-input mb-2">
                                <input type="text"
                                       lang="en"
                                       inputmode="decimal"
                                       name="amount-offline"
                                       id="amount-offline"
                                       placeholder="<?= $lang['u_wallet_withdraw_amount_placeholder']; ?>"
                                       minlength="5">
                                <span><?= $BalanceCurrency; ?></span>
                            </div>

                            <label for="authority-offline"><?= $lang['wallet_deposit_authority']; ?> :</label>
                            <div class="mj-deposit-input mb-2">
                                <input type="text"
                                       lang="en"
                                       inputmode="decimal"
                                       name="authority-offline"
                                       id="authority-offline"
                                       placeholder="<?= $lang['card_account_placeholder']; ?>"
                                       minlength="5">
                            </div>


                            <label><?= $lang['wallet_deposit_receipt']; ?> :</label>
                            <div class="d-flex align-items-center" id="upload-receipt-div">
                                <div class="mj-input-filter-box flex-fill pe-1">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="mj-font-12 me-1"><?= $lang['wallet_deposit_receipt_message'] ?></div>
                                        <label class="mj-btn-more mj-font-12 py-2 px-3 ms-1">
                                            <?= $lang['d_choose_image'] ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <input type="file"
                                   class="d-none"
                                   id="file-input"
                                   name="file-input"/>
                            <div class="d-flex justify-content-center">
                                <img width="100px" id="respect-img" src="<?= BOX_EMPTY; ?>">
                            </div>

                            <div class="d-flex justify-content-center">
                                <button name="submit-respect"
                                        id="submit-respect"
                                        data-tj-currency="<?= (int)$_REQUEST['currencyId']; ?>"
                                        type="button"><?= $lang['u_deposit']; ?></button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </section>
        <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('deposit') ?>">
        <?php
    } else {
        ?>
        <main class="container">
            <div class="text-center">
                <lottie-player src="/dist/lottie/auth-wallet.json" class="mx-auto"
                               style="max-width: 400px;" speed="1" loop
                               autoplay></lottie-player>
                <p class="text-center text-info font-17"><?= $lang['auth_imperfect']; ?></p>
                <div class="mj-home-gt-blog-btn d-flex justify-content-center">
                    <a href="/user/auth"><?= $lang['return_to_auth_page']; ?></a>
                </div>
            </div>
        </main>
        <div class="modal fade" id="modal-alert-auth" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="text-center my-3">
                            <i class="fas fa-fingerprint text-primary mb-4" style="font-size: 72px;"></i>

                            <h5 class="text-dark mj-fw-600 mj-font-14 mt-0 mb-4">
                                <?= $lang['required_auth'] ?>
                            </h5>

                            <div class="d-flex align-items-center justify-content-center">
                                <a href="/user/auth" class="mj-btn-more px-4 me-1">
                                    <?= $lang['d_auth_title'] ?>
                                </a>

                                <a href="javascript:void(0);" data-bs-dismiss="modal"
                                   class="mj-btn-more mj-btn-cancel px-4 ms-1">
                                    <?= $lang['d_btn_close'] ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    getFooter('', false);
} else {
    header('location: /login');
}