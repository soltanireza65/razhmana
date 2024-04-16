<?php

use MJ\Security\Security;
use MJ\Utils\Utils;

global $lang;

if (User::userIsLoggedIn()) {
    header('location: /');
} else {
    include_once 'header-footer.php';

    enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');

    enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
    enqueueStylesheet('select2-js', '/dist/libs/fontawesome/all.min.css');
    enqueueScript('select2-js', '/dist/libs/fontawesome/all.min.js');
    enqueueScript('login-init-js', '/dist/js/site/login.init.js');


    getLoginHeader($lang['d_login_title']);
    ?>
    <script type="text/javascript">
        let lang_vars = <?= json_encode($lang) ?>;
    </script>

    <main class="h-100">
        <div class="mj-login-logo d-flex align-items-center justify-content-center">
            <a href="/">
                <img src="<?= Utils::fileExist('/uploads/site/user-logo-light.svg', BOX_EMPTY) ?>" alt="logo-light">
            </a>
        </div>

        <div class="mj-bottom-sheet">
            <button data-close class="btn-close shadow-none d-none"></button>

            <div data-step="1">
                <h3 class="mj-header-title"><?= $lang['d_welcome_title'] ?></h3>
                <p class="mj-header-subtitle mt-4 mb-3"><?= $lang['d_welcome_subtitle'] ?></p>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-1">
                            <label for="phone" class="mj-form-label"><?= $lang['d_phone_number_label'] ?></label>
                            <div class="d-flex mj-input-box mj-country-code-box">
                                <input type="text" inputmode="tel" class="form-control mj-input" id="phone"
                                       name="phone" maxlength="10" lang="en"
                                       placeholder="<?= $lang['d_phone_number_placeholder'] ?>" dir="ltr">
                                <select id="country-code" name="country-code" data-width="100px" dir="ltr">
                                    <?php
                                    // $countries = json_decode(Utils::getFileValue('countries.json', null, false));
                                    $countriesData = Location::getAllCountriesFromLoginPage();
                                    $countries = $countriesData->response;
                                    $selected = '' ;
                                    if( substr(($_COOKIE['language'])  ,0,2) == 'en' ){
                                        $selected = 86 ;
                                    }elseif (substr(($_COOKIE['language'])  ,0,2) == 'ru'){
                                        $selected = 2 ;
                                    }elseif (substr(($_COOKIE['language'])  ,0,2) == 'fa'){
                                        $selected = 1 ;
                                    }elseif (substr(($_COOKIE['language'])  ,0,2) == 'tr'){
                                        $selected = 3 ;
                                    }
                                    foreach ($countries as $key => $country) {


                                        ?>
                                        <option data-image="<?= Utils::fileExist($country->country_flag, '/uploads/flags/empty.webp') ?>"
                                            <?=  $country->country_id  ==  $selected ? 'selected' : '' ?>
                                                value="<?= $country->country_display_code ?>"><?= $country->country_display_code ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <p class="mb-0 "><?= $lang['d_accept_laws'] ?></p>
                        </div>
                    </div>

                    <div class="col-12 mj-login-phone-btn">
                        <div class="mt-3">
                            <input type="hidden" id="token_login" name="token_login"
                                   value="<?= Security::initCSRF('login') ?>">
                            <button type="button" class="btn mj-btn mj-btn-primary shadow-none  w-100"
                                    data-next-step>
                                <?= $lang['d_next_step'] ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: none" data-step="2">
                <h3 class="mj-header-title"><?= $lang['d_welcome_title'] ?></h3>
                <p class="mj-header-subtitle mt-4 mb-3"><?= $lang['d_welcome_subtitle_2'] ?></p>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-5">
                            <div class="mj-otp-field" dir="ltr">
                                <input type="text" inputmode="decimal" maxlength="1" lang="en">
                                <input type="text" inputmode="decimal" maxlength="1" lang="en">
                                <input type="text" inputmode="decimal" maxlength="1" lang="en">
                                <input type="text" inputmode="decimal" maxlength="1" lang="en">
                                <input type="text" inputmode="decimal" maxlength="1" lang="en">
                                <input type="text" inputmode="decimal" maxlength="1" lang="en">
                            </div>
                        </div>
                    </div>
                    <div class="col-6 ">
                        <button id="next-step" type="button" class="btn mj-btn mj-btn-primary shadow-none w-100 mb-3 d-flex justify-content-center align-items-center"
                                >
                            <?= $lang['d_continue_otp'] ?>
                        </button>


                        <script>

                                const nextBtn = document.getElementById("next-step")
                                const spinnerIcon = document.getElementById("spinner-icon")
                                nextBtn.addEventListener('click',function() {
                                    // disable button
                                    nextBtn.disabled = true;
                                    nextBtn.innerHTML = '<div id="spinner-icon" class=" me-1 fa-spinner fa-spin"></div><?= $lang['d_continue_otp'] ?>'
                                    // add spinner to button
                                    setTimeout(function () {
                                        nextBtn.disabled = false;
                                        nextBtn.innerHTML = '<?= $lang['d_continue_otp'] ?>'
                                    }, 8000);
                                });

                        </script>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn mj-btn mj-btn-outline-primary shadow-none w-100 mb-3"
                                data-prev-step>
                            <?= $lang['d_change_phone'] ?>
                        </button>

                        <input type="hidden" id="token_otp" name="token_otp" value="<?= Security::initCSRF('otp'); ?>">
                    </div>

                </div>
            </div>

            <div style="display: none" data-step="3">
                <h3 class="mj-header-title"><?= $lang['d_welcome_title'] ?></h3>
                <p class="mj-header-subtitle mt-2 mb-3"><?=$lang['u_please_enter_name_and_last_name'];?></p>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-2 mj-username-input">
                            <input id="user_name" type="text" placeholder="<?=$lang['name'];?>">
                            <input id="user_lname" type="text" placeholder="<?=$lang['b_lastname'];?>">
                            <input id="user_referral" class="<?=isset($_REQUEST['referals']) ? 'd-none' : ''?>"  type="number" placeholder="<?=$lang['u_referral_code'];?>" <?=isset($_REQUEST['referals']) ? 'readonly' : ''?> value="<?= isset($_REQUEST['referals']) ? $_REQUEST['referals'] : ''?>" >
                        </div>

                        <div class="col-12">
                            <button id="register_new_user" type="button"
                                    class="btn mj-btn mj-btn-outline-primary shadow-none w-100 mb-3">
                                <?=$lang['businessman_login'];?>
                            </button>

                            <input type="hidden" id="token_otp" name="token_otp"
                                   value="<?= Security::initCSRF('otp'); ?>">
                            <input type="hidden" id="token_register" name="token_register"
                                   value="<?= Security::initCSRF('token_register'); ?>">
                        </div>
                    </div>
                </div>
            </div>
    </main>
    <?php
    getLoginFooter();
}