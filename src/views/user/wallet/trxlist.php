<?php

global $lang;

use MJ\Security\Security;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();
    if ($user->UserAuthStatus == 'accepted') {
        User::checkBalanceIsGenerated($user->UserId);
        $balance = User::getBalance($user->UserId);
    }

    include_once getcwd() . '/views/user/header-footer.php';


    enqueueStylesheet('FA-css', '/dist/libs/fontawesome/all.css');

    enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    enqueueScript('FA-js', '/dist/libs/fontawesome/all.min.js');
    enqueueScript('trxlist-js', '/dist/js/user/wallet/trxlist.js');

    $currencyId = (int)$_REQUEST['currencyId'];
    getHeader($lang['d_wallet_title']);

    if ($user->UserAuthStatus == 'accepted') {
        ?>
        <section style="padding-top: 110px">

            <div class="mj-wallet-head-blue">
                <div class="mj-wallet-blue">
                    <?= $lang['d_transactions_title'] ?>
                </div>
                <svg viewBox="0 0 1920 145" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1920 0C1920 77.8627 1490.19 141 960 141C429.81 141 0 77.8627 0 0H1920Z" fill="#3CA4F6"/>
                </svg>
                <div class="mj-transaction-list">
                    <div class="mj-trx-head">
                        <div class="mj-trx-list-title"><?= $lang['list_transactions']; ?> :</div>
                        <div class="mj-trx-operation-btns">
                            <div class="mj-search-btn me-2">
                                <div class="fa-search"></div>
                            </div>
                            <div class="mj-filter-btn">
                                <div class="fa-sliders"></div>
                                <div class="mj-filter-dropdown">
                                    <a href="/user/wallet/trxlist/<?= $currencyId; ?>/all">
                                        <div class="mj-filter-option">
                                            <div class="mj-filter-opt-color all"></div>
                                            <div class="mj-filter-opt-title"><?= $lang['u_wallet_filter_all']; ?></div>
                                        </div>
                                    </a>

                                    <a href="/user/wallet/trxlist/<?= $currencyId; ?>/pending">
                                        <div class="mj-filter-option">
                                            <div class="mj-filter-opt-color pending"></div>
                                            <div class="mj-filter-opt-title"><?= $lang['u_filter_pending']; ?></div>
                                        </div>
                                    </a>
                                    <a href="/user/wallet/trxlist/<?= $currencyId; ?>/completed">
                                        <div class="mj-filter-option">
                                            <div class="mj-filter-opt-color abort"></div>
                                            <div class="mj-filter-opt-title"><?= $lang['u_filter_paid']; ?></div>
                                        </div>
                                    </a>
                                    <a href="/user/wallet/trxlist/<?= $currencyId; ?>/rejected">
                                        <div class="mj-filter-option">
                                            <div class="mj-filter-opt-color reject"></div>
                                            <div class="mj-filter-opt-title"><?= $lang['u_filter_rejected']; ?></div>
                                        </div>
                                    </a>


                                    <a href="/user/wallet/trxlist/<?= $currencyId; ?>/paid">
                                        <div class="mj-filter-option">
                                            <div class="mj-filter-opt-color pending"></div>
                                            <div class="mj-filter-opt-title"><?= $lang['u_filter_by_paid_2']; ?></div>
                                        </div>
                                    </a>
                                    <a href="/user/wallet/trxlist/<?= $currencyId; ?>/unpaid">
                                        <div class="mj-filter-option">
                                            <div class="mj-filter-opt-color reject"></div>
                                            <div class="mj-filter-opt-title"><?= $lang['u_filter_by_unpaid']; ?></div>
                                        </div>
                                    </a>

                                    <a href="/user/wallet/trxlist/<?= $currencyId; ?>/pending_deposit">
                                        <div class="mj-filter-option">
                                            <div class="mj-filter-opt-color pending"></div>
                                            <div class="mj-filter-opt-title"><?= $lang['u_filter_by_pending_deposit']; ?></div>
                                        </div>
                                    </a>
                                    <a href="/user/wallet/trxlist/<?= $currencyId; ?>/rejected_deposit">
                                        <div class="mj-filter-option">
                                            <div class="mj-filter-opt-color reject"></div>
                                            <div class="mj-filter-opt-title"><?= $lang['u_filter_by_rejected_deposit']; ?></div>
                                        </div>
                                    </a>


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mj-trx-search">
                        <form action="" class="mj-trx-serach-form">
                            <input type="text" id="tx-search"
                                   placeholder="<?= $lang['transaction_list_search_placeholder'] ?>">
                            <button type="button">
                                <div class="fa-search"></div>
                            </button>
                        </form>
                    </div>


                    <img id="trx-image" src="/dist/images/wallet/trx-img.svg" alt="">
                    <div class="mj-transaction-items"
                         data-tj-currency="<?= $currencyId; ?>"
                         data-tj-status="<?= (isset($_REQUEST['status']) && in_array($_REQUEST['status'], ['all', 'pending', 'completed', 'rejected', 'paid', 'unpaid', 'pending_deposit', 'rejected_deposit'])) ? $_REQUEST['status'] : 'all'; ?>">


                    </div>


                    <div class="mj-trx-list-load d-none">
                        <lottie-player src="/dist/lottie/wallet-load.json" background="transparent" speed="1" loop
                                       autoplay></lottie-player>
                    </div>
                </div>

            </div>


        </section>

        <div class="modal fade" id="show-modal" tabindex="-1" aria-labelledby="modalshow" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mj-wallet-modal">
                <div class="modal-content mj-modal-add-account">
                    <div class="modal-header mj-modal">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <h1 class="modal-title fs-5" id="modalshow"><?= $lang['d_transaction_detail_title']; ?></h1>
                    </div>
                    <div class="modal-body" style="padding: 10px 15px;">


                    </div>
                    <div class="modal-footer mj-modal-footer">
                        <button type="button"
                                data-bs-dismiss="modal"
                                class="btn btn-soft-warning">
                            <?= $lang['d_btn_close']; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

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