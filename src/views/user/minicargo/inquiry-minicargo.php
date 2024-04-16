<?php

global $lang;

use MJ\Security\Security;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();

    $CargoCategories = Ground::getAllCargoCategoryByStatus();

    $language = 'fa_IR';
    if (isset($_COOKIE['language'])) {
        $language = $_COOKIE['language'];
    }

    include_once getcwd() . '/views/businessman/header-footer.php';

    enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');
    enqueueStylesheet('persian-datepicker-css', '/dist/libs/persian-calendar/persian-datepicker.min.css');

    enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
    enqueueScript('persian-date-js', '/dist/libs/persian-calendar/persian-date.min.js');
    enqueueScript('persian-datepicker-js', '/dist/libs/persian-calendar/persian-datepicker.min.js');
    enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    enqueueScript('inquiry-init', '/dist/js/user/minicargo/inquiry-minicargo.init.js');

    getHeader($lang['u_inquiry_minicargo'], true);
    ?>
    <main style="padding-bottom: 0 !important; ">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="mj-b-stepper-card mj-cargo-minicargo-stepper">
                        <img src="/dist/images/customs.svg" alt="">
                        <div class="mj-b-progressbar">
                            <div class="mj-b-progress" id="progress"></div>
                            <div class="mj-b-progress-step mj-b-progress-step-active"></div>
                            <div class="mj-b-progress-step"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-step form-step-active">
                        <div class="row">
                            <div class="col-6">
                                <div class="mj-add-account-btn" id="btn-add">
                                    <?= $lang['u_commodity']; ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <button type="button"
                                        id="submit_cargo_step1"
                                        class="btn-x btn-next border-0"><?= $lang['next_level'] ?></button>
                            </div>
                            <div class="col-12" id="commodity-list-empty">
                                <lottie-player src="/dist/lottie/emptycargo.json" background="transparent" speed="1"
                                               style="width: 250px; height: 250px;" loop autoplay></lottie-player>
                                <div>لطفا کالای خود را وارد کنید</div>
                            </div>
                            <div class="col-12" id="commodity-list">

                            </div>
                        </div>


                    </div>
                </div>


                <div class="form-step ">
                    <div class="row">


                        <div class="col-12 mb-3">
                            <label for="cargo-start-date"
                                   class="text-dark mj-fw-500 mj-font-12 mb-1">
                                <?= $lang['u_inquiry_minicargo_date'] ?>
                                <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                            </label>
                            <div class="mj-input-filter-box">
                                <input type="text"
                                       id="cargo-start-date"
                                       name="cargo-start-date"
                                       class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                       style="min-height: 38px;"
                                       value="<?= date('Y/m/d') ?>">

                                <input type="hidden" id="cargo-start-date-ts" name="cargo-start-date-ts">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="cargo-origin-country"
                                       class="text-dark mj-fw-500 mj-font-12 mb-1">
                                    <?= $lang['b_cargo_source_country'] ?>
                                    <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                </label>
                                <div class="mj-custom-select cargo-origin-country">
                                    <select class="form-select width-95 my-1 mb-3"
                                            id="cargo-origin-country"
                                            name="cargo-origin-country"
                                            data-width="100%"
                                            data-placeholder="<?= $lang['b_cargo_select_country'] ?>">
                                        <option value=""></option>
                                        <?php
                                        $countries = Location::getCountriesList();
                                        foreach ($countries->response as $item) {
                                            ?>
                                            <option value="<?= $item->CountryId ?>"><?= $item->CountryName ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="mb-3">
                                <label for="cargo-dest-country"
                                       class="text-dark mj-fw-500 mj-font-12 mb-1">
                                    <?= $lang['b_cargo_dest_country'] ?>
                                    <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                </label>
                                <div class="mj-custom-select cargo-dest-country">
                                    <select class="form-select width-95 my-1 mb-3"
                                            id="cargo-dest-country"
                                            name="cargo-dest-country"
                                            data-width="100%"
                                            data-placeholder="<?= $lang['b_cargo_select_country'] ?>">
                                        <option value=""></option>
                                        <?php
                                        foreach ($countries->response as $item) {
                                            ?>
                                            <option value="<?= $item->CountryId ?>"><?= $item->CountryName ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="mb-3">
                                <label for="cargo-origin"
                                       class="text-dark mj-fw-500 mj-font-12 mb-1">
                                    <?= $lang['b_cargo_source_city'] ?>
                                    <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                </label>
                                <div class="mj-custom-select cargo-origin">
                                    <select class="form-select width-95 my-1 mb-3"
                                            id="cargo-origin"
                                            name="cargo-origin"
                                            data-width="100%"
                                            data-placeholder="<?= $lang['b_cargo_select_cities'] ?>">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="mb-3">
                                <label for="cargo-destination"
                                       class="text-dark mj-fw-500 mj-font-12 mb-1">
                                    <?= $lang['b_cargo_dest_city'] ?>
                                    <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                </label>
                                <div class="mj-custom-select cargo-destination">
                                    <select class="form-select width-95 my-1 mb-3"
                                            id="cargo-destination"
                                            name="cargo-destination"
                                            data-width="100%"
                                            data-placeholder="<?= $lang['b_cargo_select_cities'] ?>">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <style>

                            </style>
                            <span style="color: red"> <?= $lang['info'] ?>:</span>
                            <span style="font-size: 13px; color: #303030">
                                <?= $lang['u_minicargo_arrangement-info'] ?>
                            </span>
                            <div class="mj-input-filter-box2 d-flex">
                                <input type="checkbox" id="cargo_arrangement">
                                <label for="cargo_arrangement">
                                    <?= $lang['u_inquiry_minicargo_arrangement'] ?>


                                </label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="cargo-description"
                                   class="text-dark mj-fw-500 mj-font-12 mb-1"><?= $lang['b_more_desc'] ?></label>
                            <div class="mj-input-filter-box">
                                            <textarea class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                                      id="cargo-description"
                                                      name="cargo-description" placeholder="<?= $lang['b_more_desc'] ?>"
                                                      rows="5"></textarea>
                            </div>
                        </div>

                        <div class="col-12 mb-4">
                            <div class="d-flex align-items-center">
                                <input type="hidden" id="token" name="token"
                                       value="<?= Security::initCSRF2() ?>">
                                <button type="button"
                                        class="mj-btn-cancel btn-prev text-white border-0 w-100 me-1"
                                        style="min-height: 44px; border-radius: 10px;"><?= $lang['previous_level'] ?></button>
                                <button type="button"
                                        id="submit-cargo"
                                        name="submit-cargo"
                                        class="mj-btn-more btn-next w-100 ms-1"
                                        style="min-height: 44px; border-radius: 10px;"><?= $lang['b_submit_cargo'] ?></button>
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

                            <h6 class="mb-0"><?= str_replace('#ACTION#', $lang['b_submit_cargo'], $lang['b_info_processing']) ?>
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

        <!-- Start Modal add -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true" style="bottom:0 !important ">
            <div class="modal-dialog modal-fullscreen mj-wallet-modal">
                <div class="modal-content mj-modal-add-account mj-cargo-custom-modal">
                    <div class="modal-header mj-modal">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <h1 class="modal-title fs-5"><?= $lang['u_commodity']; ?></h1>
                    </div>

                    <div class="modal-body" style="padding: 10px 15px;">

                        <div class="mj-add-account-form">
                            <label for="commodity-name"><?= $lang['a_name_commodity']; ?></label>
                            <input type="text"
                                   id="commodity-name"
                                   name="commodity-name"
                                   class="mj-add-cargo-custom-input"
                                   placeholder="<?= $lang['u_enter_name_commodity']; ?>">

                            <label for="commodity-category"><?= $lang['a_category_commodity']; ?></label>
                            <div class="mj-custom-select mj-custom-select-cargo commodity-category">
                                <select class="form-select my-1 mb-3"
                                        id="commodity-category"
                                        name="commodity-category"
                                        data-width="100%"
                                        data-placeholder="<?= $lang['u_select_category_commodity']; ?>">
                                    <option value=""><?= $lang['u_select_category_commodity']; ?></option>
                                    <?php
                                    foreach ($CargoCategories->response as $item) {
                                        ?>
                                        <option
                                            value="<?= $item->category_id ?>"><?= array_column(json_decode($item->category_name), 'value', 'slug')[$language] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>


                            <label for="commodity-weight"><?= $lang['a_weight']; ?></label>
                            <div class="mj-input-custom-cargo d-flex commodity-weight-parent">
                                <input type="number"
                                       inputmode="numeric"
                                       class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                       id="commodity-weight"
                                       name="commodity-weight"
                                       placeholder="<?= $lang['u_enter_weight']; ?>"
                                       style="min-height: 38px;">

                                <select class="mj-custom-form-select"
                                        id="commodity-weight-slug"
                                        name="commodity-weight-slug" data-width="30%">
                                    <option value="kg" selected>kg</option>
                                    <option value="ton">Ton</option>

                                </select>
                            </div>

                            <label for="commodity-volume"><?= $lang['a_volume']; ?></label>
                            <input type="text"
                                   id="commodity-volume"
                                   name="commodity-volume"
                                   class="mj-add-cargo-custom-input"
                                   placeholder="<?= $lang['u_enter_volume']; ?>">
                        </div>
                        <div class="mj-custom-cargo-footer">
                            <button class="mj-upgrade-button"
                                    id="btn-commodity-add"
                                    type="button">
                                <?= $lang['add']; ?>
                            </button>
                            <button class="mj-upgrade-button"
                                    id="btn_close"
                                    type="button">
                                <?= $lang['closes']; ?>
                            </button>
                        </div>
                    </div>

                    <!--                    <div class="modal-footer">-->
                    <!---->
                    <!--                     -->
                    <!--                    </div>-->
                </div>
            </div>
        </div>
        <!-- End Modal add -->


        </div>
    </main>
    <?php
    getFooter('', false, false);

} else {
    header('location: /login');
}