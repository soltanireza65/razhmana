<?php

global $lang;

use MJ\Security\Security;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();
    if ($user->UserAuthStatus == 'accepted') {
        User::checkBalanceIsGenerated($user->UserId);
        $balance = User::getBalance($user->UserId);
    }
    include_once getcwd() . '/views/user/header-footer.php';

    enqueueStylesheet('FA-css', '/dist/libs/fontawesome/all.css');

    enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    enqueueScript('fontawesome-js', '/dist/libs/fontawesome/all.min.js');

    getHeader($lang['d_wallet_title']);

    if ($user->UserAuthStatus == 'accepted') {
        ?>
        <section style="padding-top: 110px">
            <div class="mj-wallet-head-blue">
                <div class="mj-wallet-blue">
                    <?= $lang['d_wallet_title'] ?>
                </div>
                <svg viewBox="0 0 1920 145" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1920 0C1920 77.8627 1490.19 141 960 141C429.81 141 0 77.8627 0 0H1920Z" fill="#3CA4F6"/>
                </svg>
                <div class="mj-wallet-rial-balance">
                    <div class="mj-balance">
                        <?php
                        foreach ($balance->response as $item) {
                            if ($item->CurrencyId == 1) {
                                ?>
                                <div class="mj-balance-title"><?= $lang['wallet_balance'] ?> :</div>
                                <div class="mj-balance-price">
                                    <span><?= number_format($item->BalanceAvailable) ?></span>
                                    <span><?= $item->BalanceCurrency ?></span>
                                </div>
                                <?php
                                break;
                            }
                        }
                        ?>
                    </div>
                    <img src="/dist/images/wallet/IRT.svg" alt="">
                </div>
            </div>
        </section>
        <section class="mj-operation-section">
            <div class="mj-tx-operations">
                <div class="mj-deposit">
                    <div class="mj-deposit-btn">
                        <a href="/user/wallet/deposit/1">
                            <img src="/dist/images/wallet/deposit.svg" alt="">
                        </a>
                    </div>
                    <div class="mj-operation-label"><?=$lang['u_deposit'];?></div>
                </div>
                <div class="mj-withdraw">
                    <div class="mj-withdraw-btn">
                        <a href="/user/wallet/withdraw/1">
                            <img src="/dist/images/wallet/withdraw.svg" alt="">
                        </a>
                    </div>
                    <div class="mj-operation-label"><?=$lang['withdraw'];?></div>
                </div>
                <div class="mj-transactions">
                    <div class="mj-trx-btn">
                        <a href="/user/wallet/trxlist/1">
                            <img src="/dist/images/wallet/trxs.svg" alt="">
                        </a>
                    </div>
                    <div class="mj-operation-label"><?=$lang['d_transactions_title'];?></div>
                </div>
                <div class="mj-accounts">
                    <div class="mj-accounts-btn">
                        <a href="/user/wallet/accounts/1">
                            <img src="/dist/images/wallet/accounts.svg" alt="">
                        </a>
                    </div>
                    <div class="mj-operation-label"><?=$lang['u_accounts'];?></div>
                </div>
            </div>
        </section>


        <?php
        foreach ($balance->response as $item) {
            if ($item->CurrencyId == 3) {
                ?>
                <div class="mj-dollar-account mj-foreign-currency mb-2">
                    <div><?= $lang['u_cash'] . " " . $item->BalanceCurrency; ?> :</div>
                    <div><?=$lang['u_soon'];?></div>
                    <img src="/dist/images/wallet/dollar.svg" alt="dollar">
                </div>
                <?php
                break;
            }
        } ?>

        <?php
        foreach ($balance->response as $item) {
            if ($item->CurrencyId == 4) {
                ?>
                <div class="mj-euro-account mj-foreign-currency">
                    <div><?= $lang['u_cash'] . " " . $item->BalanceCurrency; ?> :</div>
                    <div><?=$lang['u_soon'];?></div>
                    <img src="/dist/images/wallet/euro.svg" alt="euro">
                </div>
                <?php
                break;
            }
        } ?>
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