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
        enqueueScript('accounts-js', '/dist/js/user/wallet/accounts.js');
    } else {
        enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    }

    getHeader($lang['d_wallet_title']);

    if ($user->UserAuthStatus == 'accepted') {
        ?>
        <section style="padding-top: 110px">
            <div class="mj-wallet-head-blue">
                <div class="mj-wallet-blue">
                    <?= $lang['d_credit_cards_title']; ?>
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
            </div>
        </section>

        <section class="mj-witdraw-section">

            <div class="mj-accounts-list">
                <?php
                $flagEmpty = true;
                $creditCards = User::getCreditCardsList2($user->UserId, 'all');
                foreach ($creditCards->response as $card) {
                    if ($card->CurrencyId == (int)$_REQUEST['currencyId']) {
                        $flagEmpty = false;
                        ?>
                        <div class="mj-account-card mb-2">
                            <div class="mj-accounts-operations">
                                <div class="mj-account-delete">
                                    <div class="fa-trash-can"><?=$lang['u_delete'];?></div>
                                </div>
                            </div>
                            <div class="mj-account-card-title <?=$card->CardStatus;?>">
                                <div class="mj-account-info">
                                    <div data-tj-credit-title="<?= $card->CardId; ?>"><?= $card->CardBankName; ?></div>
                                    <div>
                                        <bdi><?= preg_replace('/(?<=\d)(?=(\d{4})+$)/', ' ', $card->CardNumber); ?></bdi>
                                    </div>
                                </div>
                                <div class="mj-account-bank-logo">
                                    <img src="<?= Utils::getBankIranInfo(substr($card->CardNumber, 0, 6))->icon; ?>"
                                         alt="<?= Utils::getBankIranInfo(substr($card->CardNumber, 0, 6))->name; ?>"
                                         width="34px">
                                </div>

                                <svg width="63" height="13" viewBox="0 0 63 13" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_818_507)">
                                        <path d="M49.7336 1H0C6.35681 1 11.9766 3.14994 15.2318 6.41296C15.3239 6.55019 15.4314 6.68742 15.5542 6.8094C15.7692 7.08386 16.0149 7.34308 16.2605 7.58704C19.5157 10.8501 25.1355 13 31.4923 13C37.8491 13 43.4689 10.8501 46.7241 7.60229C46.7241 7.58704 46.7241 7.58704 46.7241 7.58704C46.9544 7.37357 47.154 7.14485 47.3536 6.90089C47.5072 6.74841 47.6147 6.59593 47.7375 6.42821C47.7375 6.42821 47.7426 6.42313 47.7529 6.41296C51.008 3.14994 56.6125 1 62.9846 1H49.7183L49.7336 1Z"
                                              fill="white"/>
                                    </g>
                                    <path d="M36.3906 5.16016L32.5312 8.79102C32.4043 8.91797 32.252 8.96875 32.125 8.96875C31.9727 8.96875 31.8203 8.91797 31.6934 8.81641L27.834 5.16016C27.5801 4.93164 27.5801 4.55078 27.8086 4.29688C28.0371 4.04297 28.418 4.04297 28.6719 4.27148L32.125 7.52148L35.5527 4.27148C35.8066 4.04297 36.1875 4.04297 36.416 4.29688C36.6445 4.55078 36.6445 4.93164 36.3906 5.16016Z"
                                          fill="#9A9A9A"/>
                                    <defs>
                                        <clipPath id="clip0_818_507">
                                            <rect width="63" height="12" fill="white" transform="translate(0 1)"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </div>

                            <div class="mj-accounts-detail">
                                <div class="mj-account-info-detail">
                                    <span><?= $lang['card_account']; ?>:</span>
                                    <span><?= $card->CardAccountNumber; ?></span>
                                </div>
                                <div class="mj-account-info-detail my-2">
                                    <span><?= $lang['card_iban']; ?>:</span>
                                    <span><bdi><?= $card->CardIBAN; ?></bdi></span>
                                </div>
                            </div>


                        </div>
                        <?php
                    }
                }
                ?>
            </div>

            <?php if ($flagEmpty) { ?>
                <div class="mj-empty-accounts">
                    <div class="mj-empty-image">
                        <img src="/dist/images/wallet/empty-account.svg" alt="">
                        <?= $lang['u_card_notice_1']; ?>
                        <br>
                        <?= $lang['u_card_notice_2']; ?>
                    </div>
                </div>
            <?php } ?>

            <div class="mj-add-account-btn mt-4" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <?= $lang['d_add_credit_card_title']; ?>
            </div>
        </section>

        <!-- Modal add -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mj-wallet-modal">
                <div class="modal-content mj-modal-add-account">
                    <div class="modal-header mj-modal">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <h1 class="modal-title fs-5" id="exampleModalLabel"><?=$lang['d_add_credit_card_title'];?></h1>

                    </div>
                    <div class="modal-body" style="padding: 10px 15px;">
                        <div style="font-size: 12px;color: red" class="mb-3">
                            <?= $lang['u_card_notice_3']; ?>
                        </div>
                        <form class="mj-add-account-form" action="GET">
                            <label for="account-name"><?= $lang['wallet_name']; ?></label>
                            <input type="text"
                                   id="account-name"
                                   name="account-name"
                                   class="mj-add-account-input"
                                   placeholder="<?= $lang['wallet_name_placeholder']; ?>">

                            <label for="account-number"><?= $lang['card_account']; ?></label>
                            <input type="text"
                                   id="account-number"
                                   name="account-number"
                                   class="mj-add-account-input"
                                   placeholder="<?= $lang['card_account_placeholder']; ?>">

                            <label for="account-name"><?= $lang['card_number']; ?></label>
                            <div class="mj-credit-card-num" dir="ltr">
                                <input class="mj-credit-inputs"
                                       type="text"
                                       inputmode="decimal"
                                       maxlength="4"
                                       placeholder="6104"
                                       name="cart-number[]"
                                       id="cart-1">
                                <input class="mj-credit-inputs"
                                       type="text"
                                       inputmode="decimal"
                                       maxlength="4"
                                       placeholder="****"
                                       name="cart-number[]"
                                       id="cart-2">
                                <input class="mj-credit-inputs"
                                       type="text"
                                       inputmode="decimal"
                                       maxlength="4"
                                       placeholder="****"
                                       name="cart-number[]"
                                       id="cart-3">
                                <input class="mj-credit-inputs"
                                       type="text"
                                       inputmode="decimal"
                                       maxlength="4"
                                       placeholder="4411"
                                       name="cart-number[]"
                                       id="cart-4">
                            </div>
                            <label for="IBAN"><?= $lang['card_iban']; ?></label>
                            <div class="mj-IBAN-num">
                                <input type="text"
                                       id="cart-iban"
                                       name="cart-iban"
                                       inputmode="decimal"
                                       placeholder="7754020000008314257">
                                <span>-IR</span>
                            </div>


                        </form>
                    </div>
                    <div class="modal-footer mj-modal-footer">
                        <button type="button"
                                id="submit-card"
                                name="submit-card"
                                data-tj-currency="<?= (int)$_REQUEST['currencyId']; ?>"
                                class="btn btn-primary mj-add-account-button">
                            <?= $lang['d_cargo_additional_expenses_button']; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--Modal Delete-->
        <div class="modal fade" id="delete-card-modal" aria-hidden="true"
             aria-labelledby="delete-card"
             tabindex="-1">
            <div class="modal-dialog modal-dialog-centered width-100">
                <div class="modal-content text-center">
                    <div class="modal-header text-center">
                        <h5 class="modal-title" id="delete-card"><?= $lang['verifying'] ?></h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?= $lang['credit_card_delete_message'] ?>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button class="mj-btn-more mj-btn-cancel-yes px-4" id="btn-delete">
                            <?= $lang['d_btn_yes'] ?>
                        </button>
                        <button class="mj-btn-more mj-btn-cancel px-4" data-bs-dismiss="modal">
                            <?= $lang['d_btn_close'] ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('add-credit-card') ?>">
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