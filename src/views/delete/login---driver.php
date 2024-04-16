<?php

use MJ\Security\Security;
use MJ\Utils\Utils;

global $lang;

if (User::userIsLoggedIn()) {
    header('location: /driver');
} else {
    include_once 'header-footer.php';

    enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');

    enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
    enqueueScript('login-init-js', '/dist/js/driver/login.init.js');

    getLoginHeader($lang['d_login_title']);


    ?>
    <script type="text/javascript">
        let lang_vars = <?= json_encode($lang) ?>;
    </script>

    <main class="h-100">
        <div class="mj-login-logo d-flex align-items-center justify-content-center">
            <img src="<?= Utils::fileExist('/uploads/site/user-logo-light.svg', BOX_EMPTY) ?>" alt="logo-light">
        </div>

        <div class="mj-bottom-sheet">
            <button data-close class="btn-close shadow-none d-none"></button>

            <div data-step="1">
                <h3 class="mj-header-title"><?= $lang['d_welcome_title'] ?></h3>
                <p class="mj-header-subtitle mt-4 mb-5"><?= $lang['d_welcome_subtitle'] ?></p>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-1">
                            <label for="phone" class="mj-form-label"><?= $lang['d_phone_number_label'] ?></label>
                            <div class="d-flex mj-input-box mj-country-code-box">
                                <input type="text" inputmode="tel" class="form-control mj-input" id="phone"
                                       name="phone" maxlength="10"
                                       placeholder="<?= $lang['d_phone_number_placeholder'] ?>" dir="ltr">
                                <select id="country-code" name="country-code" data-width="100px" dir="ltr">
                                    <?php
                                    $countries = json_decode(Utils::getFileValue('countries.json', null, false));
                                    foreach ($countries as $key => $country) {
                                        ?>
                                        <option data-image="<?= Utils::fileExist($country->flag,'/uploads/flags/empty.webp') ?>" <?= ($key == 0) ? 'selected' : '' ?>
                                                value="<?= $country->displayCode ?>"><?= $country->displayCode ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <p class="mb-0"><?= $lang['d_accept_laws'] ?></p>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mt-3">
                            <input type="hidden" id="token_login" name="token_login"
                                   value="<?= Security::initCSRF('login') ?>">
                            <button type="button" class="btn mj-btn mj-btn-primary shadow-none d-none w-100"
                                    data-next-step>
                                <?= $lang['d_next_step'] ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: none" data-step="2">
                <h3 class="mj-header-title"><?= $lang['d_welcome_title'] ?></h3>
                <p class="mj-header-subtitle mt-4 mb-5"><?= $lang['d_welcome_subtitle_2'] ?></p>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-5">
                            <div class="mj-otp-field" dir="ltr">
                                <input type="text" inputmode="decimal" maxlength="1">
                                <input type="text" inputmode="decimal" maxlength="1">
                                <input type="text" inputmode="decimal" maxlength="1">
                                <input type="text" inputmode="decimal" maxlength="1">
                                <input type="text" inputmode="decimal" maxlength="1">
                                <input type="text" inputmode="decimal" maxlength="1">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="button" class="btn mj-btn mj-btn-outline-primary shadow-none w-100 mb-3"
                                data-prev-step>
                            <?= $lang['d_change_phone'] ?>
                        </button>

                        <input type="hidden" id="token_otp" name="token_otp" value="<?= Security::initCSRF('otp'); ?>">
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php

    getLoginFooter();
}