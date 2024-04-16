<?php

global $lang;

use MJ\Security\Security;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    User::checkUserSlugAccess();
    $user = User::getUserInfo();
    $auth = User::getUserAuthOptions($user->UserId)->response;
    include_once 'header-footer.php';
    enqueueStylesheet('dropzone-css', '/dist/libs/dropzone/min/dropzone.min.css');
    enqueueStylesheet('fontawesome-css', '/dist/libs/fontawesome/all.css');
    enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    enqueueScript('fontawesome-js', '/dist/libs/fontawesome/all.min.js');
    enqueueScript('dropzone-js', '/dist/libs/dropzone/min/dropzone.min.js');
    enqueueScript('auth-init-js', '/dist/js/driver/auth.init.js');

    getHeader($lang['d_auth_title']);
    ?>
    <style>
        .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .modal-content {
            background: #FFFFFF;
            border: 1px solid #D5ECFF;
            box-shadow: 0px 4px 25px rgba(0, 130, 231, 0.12);
            border-radius: 20px;
            text-align: center;
        }

        .mj-auth-info-head {
            background: #209E71;
            border-radius: 50%;
            font-family: FontAwesome;
            width: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 50px;
            color: #fff;
        }

        .mj-auth-info-list {
            width: 100%;
            background: #DCEFFE;
            border-radius: 15px;
            padding: 10px 2px;
            text-align: right;
            margin-block: 15px;
        }

        .mj-auth-info-list ul {
            margin: 0 !important;
            color: #303030;
        }

        .mj-auth-info-list ul li {
            margin-bottom: 7px;
        }

        .mj-auth-info-list ul li::marker {
            color: red;
        }

        .mj-auth-info-list ul li span {
            font-weight: bold;
            text-decoration: underline var(--primary);

        }

        .mj-auth-info-welcome {
            text-align: right;
        }

        .mj-auth-info-welcome span {
            color: red;
            font-family: FontAwesome;
            font-weight: 300;
        }

        .mj-auth-modal-info .btn-secondary {
            width: 100%;
            height: 48px;
            border-radius: 10px;
            background: var(--primary);
            border: none !important;
        }

        .mj-auth-modal-info .btn-secondary:hover {
            width: 100%;
            height: 48px;
            border-radius: 10px;
            background: var(--primary);
            border: none !important;
        }
    </style>
    <main class="container" style="padding-bottom: 180px;">
        <div class="row">
            <div class="col-12 mj-card">
                <div class="card-body">
                    <ul class="nav nav-pills mb-3 mj-message-nav" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link mj-nav-link active" id="pills-auth-required-tab"
                                    data-bs-toggle="pill"
                                    data-bs-target="#pills-auth-required" type="button" role="tab"
                                    aria-controls="pills-auth-required"
                                    aria-selected="true">
                                <?= $lang['u_authorization_1'] ?>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link mj-nav-link" id="pills-auth-optional-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-auth-optional" type="button" role="tab"
                                    aria-controls="pills-auth-optional" aria-selected="false">
                                <?= $lang['u_authorization_2'] ?>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">

                        <div class="tab-pane fade show active" id="pills-auth-required" role="tabpanel"
                             aria-labelledby="pills-auth-required-tab">
                            <?php
                            if ($user->UserAuthStatus == "pending") {
                                ?>
                                <div class="text-center">
                                    <lottie-player src="/dist/lottie/auth-pending.json" class="mx-auto"
                                                   style="max-width: 400px;" speed="1" loop
                                                   autoplay></lottie-player>
                                    <h6 class="mb-0"><?= $lang['u_authorize_pending']; ?></h6>
                                </div>
                                <?php
                            } elseif ($user->UserAuthStatus == "accepted") {
                                ?>
                                <div class="text-center">
                                    <lottie-player src="/dist/lottie/auth-accepted.json" class="mx-auto"
                                                   style="max-width: 400px;" speed="1" loop
                                                   autoplay></lottie-player>
                                    <h6 class="mb-0"><?= $lang['u_authorize_accepted']; ?></h6>
                                </div>
                                <?php
                            } else {
                                ?>

                                <div class="mj-auth-modal-info modal show fade" id="staticBackdrop"
                                     data-bs-backdrop="static"
                                     data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">

                                            <div class="modal-body">
                                                <div class="mj-auth-info-head">
                                                    <div class="fa-fingerprint fa-beat"></div>
                                                </div>
                                                <h5 class="mj-auth-info-welcome"><?= $lang['u_auth_1']; ?></h5>
                                                <span class="mj-auth-info-welcome"><?= $lang['u_auth_2']; ?></span>
                                                <div class="mj-auth-info-list">
                                                    <ul>
                                                        <li><span><?= $lang['u_name_last_name']; ?></span>
                                                            <?= $lang['u_auth_3']; ?>
                                                        </li>
                                                        <li><span><?= $lang['u_code_melle']; ?></span>
                                                            <?= $lang['u_auth_4']; ?>
                                                        </li>
                                                        <li><span><?= $lang['a_nationality']; ?></span>
                                                            <?= $lang['u_auth_5']; ?>
                                                        </li>

                                                    </ul>
                                                </div>
                                                <span class="mj-auth-info-welcome"><span
                                                            class="fa-info-circle me-1 "></span><?= $lang['u_auth_6']; ?></span>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <?= $lang['u_understand']; ?>!
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mj-radio mb-3">
                                        <input name="req-choose-input"
                                               type="radio"
                                               id="req-radio-id-card" <?= ($user->UserTypeCard == "id-card" || $user->UserTypeCard == null) ? "checked" : ""; ?>>
                                        <label class="mj-radio-label"
                                               for="req-radio-id-card"><?= $lang['iran_id_card'] ?></label>
                                    </div>

                                    <div class="mj-radio mb-3">
                                        <input type="radio"
                                               type="radio"
                                               name="req-choose-input"
                                               id="req-radio-passport" <?= ($user->UserTypeCard == "passport") ? "checked" : ""; ?>>

                                        <label class="mj-radio-label"
                                               for="req-radio-passport"><?= $lang['foreign_passport'] ?></label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="req-first-name" class="mj-font-12 mb-1">
                                            <?= $lang['auth_first_name'] ?>
                                            <span class="text-danger mj-fw-300"><?= $lang['required'] ?></span>
                                        </label>

                                        <div class="d-flex align-items-center">
                                            <div class="mj-input-filter-box flex-fill">
                                                <input type="text" inputmode="text"
                                                       class="mj-input-filter mj-fw-500 mj-font-12 px-0"
                                                       id="req-first-name" name="req-first-name"
                                                       style="min-height: 32px;"
                                                       data-required
                                                       value="<?= $user->UserFirstName ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="req-last-name" class="mj-font-12 mb-1">
                                            <?= $lang['auth_last_name'] ?>
                                            <span class="text-danger mj-fw-300"><?= $lang['required'] ?></span>
                                        </label>

                                        <div class="d-flex align-items-center">
                                            <div class="mj-input-filter-box flex-fill">
                                                <input type="text" inputmode="text"
                                                       class="mj-input-filter mj-fw-500 mj-font-12 px-0"
                                                       id="req-last-name" name="req-last-name" style="min-height: 32px;"
                                                       data-required
                                                       value="<?= $user->UserLastName ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 <?= ($user->UserTypeCard == "id-card" || $user->UserTypeCard == null) ? "" : "d-none"; ?>">
                                    <div class="mb-3">
                                        <label for="req-id-card" class="mj-font-12 mb-1">
                                            <?= $lang['auth_id_card_number'] ?>
                                            <span class="text-danger mj-fw-300"><?= $lang['required'] ?></span>
                                        </label>

                                        <div class="d-flex align-items-center">
                                            <div class="mj-input-filter-box flex-fill">
                                                <input type="text" inputmode="decimal" lang="en"
                                                       maxlength="10"
                                                       class="mj-input-filter mj-fw-500 mj-font-12 px-0"
                                                       id="req-id-card" name="req-id-card" style="min-height: 32px;"
                                                       data-required
                                                       value="<?= $user->UserNumberCard ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 <?= ($user->UserTypeCard == "passport") ? "" : "d-none"; ?>">
                                    <div class="mb-3">
                                        <label for="req-passport" class="mj-font-12 mb-1">
                                            <?= $lang['auth_passport'] ?>
                                            <span class="text-danger mj-fw-300"><?= $lang['required'] ?></span>
                                        </label>

                                        <div class="d-flex align-items-center">
                                            <div class="mj-input-filter-box flex-fill">
                                                <input type="text" inputmode="text"
                                                       class="mj-input-filter mj-fw-500 mj-font-12 px-0"
                                                       id="req-passport" name="req-passport" style="min-height: 32px;"
                                                       data-required
                                                       value="<?= $user->UserNumberCard ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="button"
                                            name="submit_auth_required"
                                            id="submit_auth_required"
                                            class="mj-btn-more py-2 w-100">
                                        <?= $lang['auth_submit'] ?>
                                    </button>
                                </div>
                                <?php
                            }
                            ?>
                        </div>

                        <div class="tab-pane fade" id="pills-auth-optional" role="tabpanel"
                             aria-labelledby="pills-auth-optional-tab">

                            <?php
                            $flag = false;
                            if ((isset($auth['birthday-city']->OptionStatus) && $auth['birthday-city']->OptionStatus == "rejected") || !isset($auth['birthday-city'])) {
                                $flag = true;
                                ?>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="op-birthday-city" class="mj-font-12 mb-1">
                                            <?= $lang["auth_birthday_city"] ?>
                                        </label>
                                        <div class="d-flex align-items-center">
                                            <div class="mj-input-filter-box flex-fill">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="mj-input-filter mj-fw-500 mj-font-12 px-0"
                                                       id="op-birthday-city"
                                                       name="op-birthday-city"
                                                       style="min-height: 32px;"
                                                       value="">
                                            </div>
                                            <div class="d-flex flex-column align-items-center ps-2">
                                                <div class="fa-star align-middle font-14 text-warning"></div>
                                                <?= Utils::getFileValue("settings.txt", "auth_birthday-city"); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            if ((isset($auth['birthday-date']->OptionStatus) && $auth['birthday-date']->OptionStatus == "rejected") || !isset($auth['birthday-date'])) {
                                $flag = true;
                                ?>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="op-birthday-date" class="mj-font-12 mb-1">
                                            <?= $lang["auth_birthday_date"] ?>
                                        </label>
                                        <div class="d-flex align-items-center">
                                            <div class="mj-input-filter-box flex-fill">
                                                <input type="text"
                                                       inputmode="decimal"
                                                       class="mj-input-filter mj-fw-500 mj-font-12 px-0"
                                                       id="op-birthday-date"
                                                       name="op-birthday-date"
                                                       style="min-height: 32px;"
                                                       value="">
                                            </div>
                                            <div class="d-flex flex-column align-items-center ps-2">
                                                <div class="fa-star align-middle font-14 text-warning"></div>
                                                <?= Utils::getFileValue("settings.txt", "auth_birthday-date"); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            if ((isset($auth['phone-national']->OptionStatus) && $auth['phone-national']->OptionStatus == "rejected") || !isset($auth['phone-national'])) {
                                $flag = true;
                                ?>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="op-phone-national" class="mj-font-12 mb-1">
                                            <?= $lang["auth_phone_national"] ?>
                                        </label>
                                        <div class="d-flex align-items-center">
                                            <div class="mj-input-filter-box flex-fill">
                                                <input type="text"
                                                       inputmode="tel"
                                                       lang="en"
                                                       class="mj-input-filter mj-fw-500 mj-font-12 px-0"
                                                       id="op-phone-national"
                                                       name="op-phone-national"
                                                       style="min-height: 32px;"
                                                       value="">
                                            </div>
                                            <div class="d-flex flex-column align-items-center ps-2">
                                                <div class="fa-star align-middle font-14 text-warning"></div>
                                                <?= Utils::getFileValue("settings.txt", "auth_phone-national"); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            if ((isset($auth['address']->OptionStatus) && $auth['address']->OptionStatus == "rejected") || !isset($auth['address'])) {
                                $flag = true;
                                ?>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="op-address" class="mj-font-12 mb-1">
                                            <?= $lang["auth_address"] ?>
                                        </label>
                                        <div class="d-flex align-items-center">
                                            <div class="mj-input-filter-box flex-fill">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="mj-input-filter mj-fw-500 mj-font-12 px-0"
                                                       id="op-address"
                                                       name="op-address"
                                                       style="min-height: 32px;"
                                                       value="">
                                            </div>
                                            <div class="d-flex flex-column align-items-center ps-2">
                                                <div class="fa-star align-middle font-14 text-warning"></div>
                                                <?= Utils::getFileValue("settings.txt", "auth_address"); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            if ((isset($auth['phone']->OptionStatus) && $auth['phone']->OptionStatus == "rejected") || !isset($auth['phone'])) {
                                $flag = true;
                                ?>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="op-phone" class="mj-font-12 mb-1">
                                            <?= $lang["auth_phone"] ?>
                                        </label>
                                        <div class="d-flex align-items-center">
                                            <div class="mj-input-filter-box flex-fill">
                                                <input type="text"
                                                       inputmode="tel"
                                                       lang="en"
                                                       class="mj-input-filter mj-fw-500 mj-font-12 px-0"
                                                       id="op-phone"
                                                       name="op-phone"
                                                       style="min-height: 32px;"
                                                       value="">
                                            </div>
                                            <div class="d-flex flex-column align-items-center ps-2">
                                                <div class="fa-star align-middle font-14 text-warning"></div>
                                                <?= Utils::getFileValue("settings.txt", "auth_phone"); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            if ((isset($auth['insurance-type']->OptionStatus) && $auth['insurance-type']->OptionStatus == "rejected") || !isset($auth['insurance-type'])) {
                                $flag = true;
                                ?>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="op-insurance-type" class="mj-font-12 mb-1">
                                            <?= $lang["auth_insurance_type"] ?>
                                        </label>
                                        <div class="d-flex align-items-center">
                                            <div class="mj-input-filter-box flex-fill">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="mj-input-filter mj-fw-500 mj-font-12 px-0"
                                                       id="op-insurance-type"
                                                       name="op-insurance-type"
                                                       style="min-height: 32px;"
                                                       value="">
                                            </div>
                                            <div class="d-flex flex-column align-items-center ps-2">
                                                <div class="fa-star align-middle font-14 text-warning"></div>
                                                <?= Utils::getFileValue("settings.txt", "auth_insurance-type"); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            if ((isset($auth['insurance-number']->OptionStatus) && $auth['insurance-number']->OptionStatus == "rejected") || !isset($auth['insurance-number'])) {
                                $flag = true;
                                ?>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="op-insurance-number" class="mj-font-12 mb-1">
                                            <?= $lang["auth_insurance_number"] ?>
                                        </label>
                                        <div class="d-flex align-items-center">
                                            <div class="mj-input-filter-box flex-fill">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="mj-input-filter mj-fw-500 mj-font-12 px-0"
                                                       id="op-insurance-number"
                                                       name="op-insurance-number"
                                                       style="min-height: 32px;"
                                                       value="">
                                            </div>
                                            <div class="d-flex flex-column align-items-center ps-2">
                                                <div class="fa-star align-middle font-14 text-warning"></div>
                                                <?= Utils::getFileValue("settings.txt", "auth_insurance-number"); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            if ((isset($auth['id-card-image']->OptionStatus) && $auth['id-card-image']->OptionStatus == "rejected") || !isset($auth['id-card-image'])) {
                                $flag = true;
                                ?>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="opIdCard" class="mj-font-12 mb-1">
                                            <?= $lang["auth_id_card"] ?>
                                        </label>
                                        <div class="d-flex align-items-center">
                                            <form action="#"
                                                  method="post"
                                                  class="dropzone tj-dropzone mj-input-filter-box flex-fill"
                                                  id="opIdCard"
                                                  name="opIdCard"
                                                  data-plugin="dropzone"
                                                  data-previews-container="#file-previews"
                                                  data-upload-preview-template="#uploadPreviewTemplate">
                                                <div class="fallback">
                                                    <input type="file" name="file">
                                                </div>

                                                <div class="dz-message needsclick">
                                                    <img src="/dist/images/icons/folder-plus.svg" class="mb-2" alt="">
                                                    <h5><?= $lang['auth_image_upload_desc'] ?></h5>
                                                </div>
                                            </form>
                                            <div class="d-flex flex-column align-items-center ps-2">
                                                <div class="fa-star align-middle font-14 text-warning"></div>
                                                <?= Utils::getFileValue("settings.txt", "auth_id-card-image"); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            if ((isset($auth['passport-image']->OptionStatus) && $auth['passport-image']->OptionStatus == "rejected") || !isset($auth['passport-image'])) {
                                $flag = true;
                                ?>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="opPassport" class="mj-font-12 mb-1">
                                            <?= $lang["auth_passport_image"] ?>
                                        </label>
                                        <div class="d-flex align-items-center">
                                            <form action="#"
                                                  method="post"
                                                  class="dropzone tj-dropzone mj-input-filter-box flex-fill"
                                                  id="opPassport"
                                                  name="opPassport"
                                                  data-plugin="dropzone"
                                                  data-previews-container="#file-previews"
                                                  data-upload-preview-template="#uploadPreviewTemplate">
                                                <div class="fallback">
                                                    <input type="file" name="file">
                                                </div>

                                                <div class="dz-message needsclick">
                                                    <img src="/dist/images/icons/folder-plus.svg" class="mb-2" alt="">
                                                    <h5><?= $lang['auth_image_upload_desc'] ?></h5>
                                                </div>
                                            </form>
                                            <div class="d-flex flex-column align-items-center ps-2">
                                                <div class="fa-star align-middle font-14 text-warning"></div>
                                                <?= Utils::getFileValue("settings.txt", "auth_passport-image"); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            if ((isset($auth['car-card-image']->OptionStatus) && $auth['car-card-image']->OptionStatus == "rejected") || !isset($auth['car-card-image'])) {
                                $flag = true;
                                ?>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="opCarCard" class="mj-font-12 mb-1">
                                            <?= $lang["auth_car_card"] ?>
                                        </label>
                                        <div class="d-flex align-items-center">
                                            <form action="#"
                                                  method="post"
                                                  class="dropzone tj-dropzone mj-input-filter-box flex-fill"
                                                  id="opCarCard"
                                                  name="opCarCard"
                                                  data-plugin="dropzone"
                                                  data-previews-container="#file-previews"
                                                  data-upload-preview-template="#uploadPreviewTemplate">
                                                <div class="fallback">
                                                    <input type="file" name="file">
                                                </div>

                                                <div class="dz-message needsclick">
                                                    <img src="/dist/images/icons/folder-plus.svg" class="mb-2" alt="">
                                                    <h5><?= $lang['auth_image_upload_desc'] ?></h5>
                                                </div>
                                            </form>
                                            <div class="d-flex flex-column align-items-center ps-2">
                                                <div class="fa-star align-middle font-14 text-warning"></div>
                                                <?= Utils::getFileValue("settings.txt", "auth_car-card-image"); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            if ($flag) {
                                ?>
                                <div class="col-12">
                                    <button type="button"
                                            name="submit_auth_optional"
                                            id="submit_auth_optional"
                                            class="mj-btn-more py-2 w-100">
                                        <?= $lang['auth_submit'] ?>
                                    </button>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="text-center">
                                    <lottie-player src="/dist/lottie/auth-accepted.json" class="mx-auto"
                                                   style="max-width: 400px;" speed="1" loop
                                                   autoplay></lottie-player>

                                </div
                                <?php
                            }
                            ?>
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

                            <h6 class="mb-0"><?= str_replace('#ACTION#', $lang['auth_notice_upload'], $lang['b_info_processing']) ?>
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
        <input type="hidden" id="token" name="token" value="<?= Security::initCSRF2() ?>">
    </main>
    <?php

    getFooter('', false);
} else {
    header('location: /login');
}