<?php

global $lang;

use MJ\Security\Security;

if (User::userIsLoggedIn()) {
    include_once 'header-footer.php';
    enqueueScript('menu-init-js', '/dist/js/driver/dashboard.init.js');
    enqueueScript('add-card-init-js', '/dist/js/driver/add-card.init.js');
    getHeader($lang['d_add_credit_card_title']);
    ?>
    <main class="container" style="padding-bottom: 180px;">

        <div class="row mt-3">
            <div class="col-12">
                <div class="card mj-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="bank-name"
                                           class="form-label mj-form-label mj-fw-500 mj-font-12 mb-2">
                                        <?= $lang['bank_name'] ?>
                                    </label>
                                    <div class="mj-input-filter-box ">
                                        <input type="text" inputmode="text"
                                               class="mj-input-filter text-center mj-fw-700 mj-font-13 px-0 py-1 w-100"
                                               id="bank-name" name="bank-name" placeholder="<?= $lang['bank_name_example'] ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label mj-form-label mj-fw-500 mj-font-12 mb-2">
                                        <?= $lang['bank_card_number'] ?>
                                        <span class="mj-fw-100" style="color: darkred;"><?= $lang['bank_card_number_badge'] ?></span>
                                    </label>
                                    <div class="row flex-row-reverse mj-credit-num-row mx-n1">
                                        <div class="col px-1">
                                            <div class="">
                                                <div class="mj-input-filter-box px-1">
                                                    <input type="text" inputmode="numeric" maxlength="4"
                                                           class="mj-input-filter text-center mj-fw-700 mj-font-13 px-0 py-1"
                                                           id="cart-1" name="cart-number[]" placeholder="<?= $lang['bank_card_number_example'][0] ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col px-1">
                                            <div class="">
                                                <div class="mj-input-filter-box px-1">
                                                    <input type="text" inputmode="numeric" maxlength="4"
                                                           class="mj-input-filter text-center mj-fw-700 mj-font-13 px-0 py-1"
                                                           id="cart-2" name="cart-number[]" placeholder="<?= $lang['bank_card_number_example'][1] ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col px-1">
                                            <div class="">
                                                <div class="mj-input-filter-box px-1">
                                                    <input type="text" inputmode="numeric" maxlength="4"
                                                           class="mj-input-filter text-center mj-fw-700 mj-font-13 px-0 py-1"
                                                           id="cart-3" name="cart-number[]" placeholder="<?= $lang['bank_card_number_example'][2] ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col px-1">
                                            <div class="">
                                                <div class="mj-input-filter-box px-1">
                                                    <input type="text" inputmode="numeric" maxlength="4"
                                                           class="mj-input-filter text-center mj-fw-700 mj-font-13 px-0 py-1"
                                                           id="cart-4" name="cart-number[]" placeholder="<?= $lang['bank_card_number_example'][3] ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="account-number"
                                           class="form-label mj-form-label mj-fw-500 mj-font-12 mb-2">
                                        <?= $lang['bank_account'] ?>
                                    </label>
                                    <div class="mj-input-filter-box ">
                                        <input type="text" inputmode="numeric"
                                               class="mj-input-filter text-center mj-fw-700 mj-font-13 px-0 py-1 w-100"
                                               id="account-number" name="account-number" placeholder="<?= $lang['bank_account_example'] ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="cart-iban" class="form-label mj-form-label mj-fw-500 mj-font-12 mb-2">
                                        <?= $lang['bank_iban'] ?>
                                        <span class="mj-fw-100" style="color: darkred"><?= $lang['bank_iban_badge'] ?></span>
                                    </label>
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="mj-input-filter-box px-1">
                                                <input type="text" inputmode="numeric" maxlength="24"
                                                       class="mj-input-filter text-center mj-fw-700 mj-font-13 px-0 py-1 w-100"
                                                       id="cart-iban" name="cart-iban" placeholder="<?= $lang['bank_iban_example'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <span dir="ltr">IR-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div>
                                    <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('add-credit-card') ?>">
                                    <button type="button" id="submit-card" name="submit-card"
                                            class="mj-btn-more py-2 px-5 mx-auto">
                                        <?= $lang['d_button_add'] ?>
                                    </button>
                                </div>
                            </div>
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