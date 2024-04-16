<?php
global $lang, $Settings;

use MJ\Security\Security;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();

    include_once 'header-footer.php';

    enqueueScript('profile-js', '/dist/js/user/profile.init.js');

    getHeader($lang['d_profile_title']);


    $can_change_user_type_businessman = User::canChangeUserTypeBusinessman();
    $can_change_user_type_driver = User::canChangeUserTypeDriver();
    ?>
    <main class="container mj-container" style="padding-bottom: 10px !important;">
        <div class="col-12 mj-card p-2">
            <div class="row mb-3">
                <div class="mj-profile-detail d-flex align-items-center justify-content-between px-2">
                    <div class="mj-username">
                        <input type="hidden" id="token-avatar" name="token-avatar"
                               value="<?= Security::initCSRF('avatar') ?>">
                        <input type="file" id="user-avatar" name="user-avatar" accept="image/*" hidden>
                        <label for="user-avatar">
                            <img src="<?= Utils::fileExist($user->UserAvatar, '/dist/images/icons/profile-2.png') ?>"
                                 class="mj-user-avatar" alt="<?= $user->UserDisplayName ?>">
                        </label>
                        <div class="mj-username-details">
                            <span id="user-name-text"><?php
                                echo $user->UserDisplayName;
                                if ($user->UserType == 'businessman') {
                                    echo ' (' . $lang['businessman'] . ')';
                                } elseif ($user->UserType == 'driver') {
                                    echo ' (' . $lang['driver'] . ')';
                                } else {
                                    echo ' (' . $lang['u_general'] . ')';
                                } ?></span>
                            <span id="user-name-num"><bdi><?= $user->UserMobile ?></bdi></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row mb-3">
                <?php
                if (isset($_COOKIE['user-type']) && $_COOKIE['user-type'] == 'driver') {
                    ?>
                    <div class="mj-authorize-btn">
                        <a href="/driver/auth"><?= $lang['d_auth_title'] ?></a>
                    </div>
                    <?php
                } elseif (isset($_COOKIE['user-type']) && $_COOKIE['user-type'] == 'businessman') {
                    ?>
                    <div class="mj-authorize-btn">
                        <a href="/businessman/auth"><?= $lang['d_auth_title'] ?></a>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="mj-authorize-btn">
                        <a href="/user/auth"><?= $lang['d_auth_title'] ?></a>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="row mb-3">
                <div class="mj-profile-awards d-flex justify-content-between align-items-center">
                    <div class="mj-card-in  text-center">
                        <img class="mb-2" src="/dist/images/icons/badge-dollar.svg" alt="gifts">
                        <span><span><?= $lang['profile_gifts'] ?></span></span>
                        <div class="mj-devider"></div>
                        <span class="mj-awards-num"><?= number_format($user->UserGift) ?></span>
                    </div>
                    <div class="mj-card-in text-center">
                        <img class="mb-2" src="/dist/images/icons/stars.svg" alt="scores">
                        <span><span><?= $lang['profile_scores'] ?></span></span>
                        <div class="mj-devider"></div>
                        <span class="mj-awards-num"><?= number_format($user->UserScore) ?></span>
                    </div>
                </div>
            </div>
            <div class="row mb-3 mj-profile-wallet">
                <a href="/user/wallet">
                    <div class="mj-profile-wallet-row d-flex justify-content-between align-items-center">
                        <div class="mj-wallet d-flex align-items-center">
                            <img class="me-2 mj-profile-items-icon" src="/dist/images/icons/wallet(blue).svg"
                                 alt="wallet">
                            <span><?= $lang['d_wallet_title'] ?></span>
                        </div>
                        <img style="width: 7px; margin-left: 10px" src="/dist/images/icons/caret-left.svg"
                             alt="caret-left">
                    </div>
                </a>

            </div>
            <div class="row mb-3 mj-profile-wallet">
                <a href="/user/invite">
                    <div class="mj-profile-wallet-row d-flex justify-content-between align-items-center">
                        <div class="mj-wallet d-flex align-items-center">
                            <img class="me-2 mj-profile-items-icon" src="/dist/images/invite.svg"
                                 alt="wallet">
                            <span><?= $lang['user-invite'] ?></span>
                        </div>
                        <img style="width: 7px; margin-left: 10px" src="/dist/images/icons/caret-left.svg"
                             alt="caret-left">
                    </div>
                </a>

            </div>
            <div class="row mb-3 mj-profile-wallet">
                <?php
                if (isset($_COOKIE['user-type']) && $_COOKIE['user-type'] == 'businessman') {
                    ?>
                    <!--<a href="https://businessman.ntirapp.com/buser" target="_blank">-->
                    <!--    <div class="mj-profile-wallet-row d-flex justify-content-between align-items-center">-->
                    <!--        <div class="mj-wallet d-flex align-items-center">-->
                    <!--            <img class="me-2 mj-profile-items-icon" src="/dist/images/invite.svg"-->
                    <!--                 alt="wallet">-->
                    <!--            <span><?= $lang['organization_section'] ?></span>-->
                    <!--        </div>-->
                    <!--        <img style="width: 7px; margin-left: 10px" src="/dist/images/icons/caret-left.svg"-->
                    <!--             alt="caret-left">-->
                    <!--    </div>-->
                    <!--</a>-->
                    <?php
                }
                ?>
            </div>

            <div class="row ">
                <a class="mb-3 btnChangeLang" href="/lang">
                    <div class="mj-profile-wallet-row d-flex justify-content-between align-items-center">
                        <div class="mj-wallet d-flex align-items-center">
                            <img class="me-2 mj-profile-items-icon" src="/dist/images/language.svg" alt="about">
                            <span>
                                <?php
                                $language = 'fa_IR';
                                if (isset($_COOKIE['language'])) {
                                    $language = $_COOKIE['language'];
                                }
                                echo strtoupper(explode("_", $language)[0]); ?>
                            </span>
                        </div>
                        <img style="width: 7px; margin-left: 10px" src="/dist/images/icons/caret-left.svg"
                             alt="caret-left">
                    </div>
                </a>
            </div>
            <div class="row mb-3">
                <a class="mb-3" href="/user/faq">
                    <div class="mj-profile-wallet-row d-flex justify-content-between align-items-center">
                        <div class="mj-wallet d-flex align-items-center">
                            <img class="me-2 mj-profile-items-icon" src="/dist/images/icons/faq.svg" alt="faq">
                            <span><?= $lang['d_faq_title'] ?></span>
                        </div>
                        <img style="width: 7px; margin-left: 10px" src="/dist/images/icons/caret-left.svg"
                             alt="caret-left">
                    </div>
                </a>
                <a class="mb-3" href="/user/laws">
                    <div class="mj-profile-wallet-row d-flex justify-content-between align-items-center">
                        <div class="mj-wallet d-flex align-items-center">
                            <img class="me-2 mj-profile-items-icon" src="/dist/images/icons/laws.svg" alt="laws">
                            <span><?= $lang['d_laws_title'] ?></span>
                        </div>
                        <img style="width: 7px; margin-left: 10px" src="/dist/images/icons/caret-left.svg"
                             alt="caret-left">
                    </div>
                </a>
                <a class="mb-3" href="/user/about">
                    <div class="mj-profile-wallet-row d-flex justify-content-between align-items-center">
                        <div class="mj-wallet d-flex align-items-center">
                            <img class="me-2 mj-profile-items-icon" src="/dist/images/icons/about-us.svg" alt="about">
                            <span><?= $lang['d_about_title'] ?></span>
                        </div>
                        <img style="width: 7px; margin-left: 10px" src="/dist/images/icons/caret-left.svg"
                             alt="caret-left">
                    </div>
                </a>
                <a class="mb-3" href="/user/contact-us">
                    <div class="mj-profile-wallet-row d-flex justify-content-between align-items-center">
                        <div class="mj-wallet d-flex align-items-center">
                            <img class="me-2 mj-profile-items-icon" src="/dist/images/phone.svg" alt="about">
                            <span><?= $lang['u_contact_us_2'] ?></span>
                        </div>
                        <img style="width: 7px; margin-left: 10px" src="/dist/images/icons/caret-left.svg"
                             alt="caret-left">
                    </div>
                </a>

                <?php if ($can_change_user_type_businessman == true) { ?>
                    <a class="mb-3" href="javascript:void(0);" id="change-user-type-businessman">
                        <div class="mj-profile-wallet-row d-flex justify-content-between align-items-center">
                            <div class="mj-wallet d-flex align-items-center">
                                <img class="me-2 mj-profile-items-icon" src="/dist/images/switch.svg" alt="about">
                                <span><?= $lang['change_user_type_businessman'] ?></span>
                            </div>
                            <img style="width: 7px; margin-left: 10px" src="/dist/images/icons/caret-left.svg"
                                 alt="caret-left">
                        </div>
                    </a>
                <?php } ?>
                <?php if ($can_change_user_type_driver == true) { ?>
                    <a class="mb-3" href="javascript:void(0);" id="change-user-type-driver">
                        <div class="mj-profile-wallet-row d-flex justify-content-between align-items-center">
                            <div class="mj-wallet d-flex align-items-center">
                                <img class="me-2 mj-profile-items-icon" src="/dist/images/switch.svg" alt="about">
                                <span><?= $lang['change_user_type_driver'] ?></span>
                            </div>
                            <img style="width: 7px; margin-left: 10px" src="/dist/images/icons/caret-left.svg"
                                 alt="caret-left">
                        </div>
                    </a>
                <?php } ?>
                <!--<a class="mb-3" href="/developer">
                    <div class="mj-profile-wallet-row d-flex justify-content-between align-items-center">
                        <div class="mj-wallet d-flex align-items-center">
                            <img class="me-2 mj-profile-items-icon" src="/dist/images/icons/code.svg" alt="about">
                            <span>< ?= $lang['design'] ?></span>
                        </div>
                        <img style="width: 7px; margin-left: 10px" src="/dist/images/icons/caret-left.svg"
                             alt="caret-left">
                    </div>
                </a>-->
                <a class="mb-1" href="javascript:void(0);" id="logout">
                    <div class="mj-profile-wallet-row d-flex justify-content-between align-items-center">
                        <div class="mj-wallet d-flex align-items-center">
                            <img class="me-2 mj-profile-items-icon" src="/dist/images/icons/exit.svg" alt="about">
                            <span><?= $lang['logout'] ?></span>
                        </div>

                    </div>
                </a>
            </div>
            <p class=" align-items-center text-center website_version">
                <?= $Settings['website_version'] ?>
            </p>

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

                            <h6 class="mb-0"><?= str_replace('#ACTION#', $lang['upload_avatar'], $lang['b_info_processing']) ?>
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

    <input type="hidden" id="token_change_user_type" name="token_change_user_type"
           value="<?= Security::initCSRF('token_change_user_type') ?>">

    <?php
    getFooter('', false);
} else {
    header('location: /login');
}