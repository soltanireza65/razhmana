<?php

global $lang;

use MJ\Security\Security;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();

    include_once getcwd() . '/views/businessman/header-footer.php';

    enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');
    enqueueStylesheet('persian-datepicker-css', '/dist/libs/persian-calendar/persian-datepicker.min.css');

    enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
    enqueueScript('persian-date-js', '/dist/libs/persian-calendar/persian-date.min.js');
    enqueueScript('persian-datepicker-js', '/dist/libs/persian-calendar/persian-datepicker.min.js');
    enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    enqueueScript('inquiry-init', '/dist/js/businessman/inquiry-ship.init.js');

    getHeader($lang['b_inquiry_ship'] , true);
    ?>
    <main style="padding-bottom: 0 !important; ">
        <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="mj-b-stepper-card">
                            <img id="ship" src="/dist/images/ship-inquirypng.png" alt="">
                            <div class="mj-b-progressbar">
                                <div class="mj-b-progress" id="progress"></div>
                                <div class="mj-b-progress-step mj-b-progress-step-active"></div>
                                <div class="mj-b-progress-step"></div>
                                <div class="mj-b-progress-step"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-step form-step-active">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="cargo-name"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_name'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-input-filter-box">
                                            <input type="text" inputmode="text" id="cargo-name" name="cargo-name"
                                                   class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                                   placeholder="<?= $lang['b_example_cargo_type'] ?>"
                                                   style="min-height: 38px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="cargo-type"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_type'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-custom-select cargo-type">
                                            <select class="form-select my-1 mb-3" id="cargo-type" name="cargo-type"
                                                    data-width="100%">
                                                <option value="-1" selected><?= $lang['b_select_ship_category'] ?></option>
                                                <?php
                                                $shipCategories = Ship::getAllShipCategoryByStatus();
                                                foreach ($shipCategories->response as $item) {
                                                    ?>
                                                    <option value="<?= $item->category_id ?>"><?=array_column(json_decode($item->category_name), 'value', 'slug')[$_COOKIE['language']] ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="cargo-packing-type"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_packing_type'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-custom-select cargo-packing-type">
                                            <select class="form-select my-1 mb-3" id="cargo-packing-type" name="cargo-packing-type"
                                                    data-width="100%">
                                                <option value="-1" selected><?= $lang['b_packing_type_select'] ?></option>
                                                <?php
                                                $shipPackings = Ship::getAllPackingByStatus();
                                                foreach ($shipPackings->response as $item) {
                                                    ?>
                                                    <option value="<?= $item->packing_id ?>"><?=array_column(json_decode($item->packing_name), 'value', 'slug')['fa_IR'] ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="cargo-car-type"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_ship_container_type'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-custom-select cargo-car-type">
                                            <select class="form-select my-1 mb-3" id="cargo-car-type"
                                                    name="cargo-car-type"
                                                    data-width="100%">
                                                <option value="-1" selected><?= $lang['b_select_ship_container_type'] ?></option>
                                                <?php
                                                $containerType = Ship::getAllContainerByStatus();
                                                foreach ($containerType->response as $item) {
                                                    ?>
                                                    <option value="<?= $item->container_id ?>"><?=array_column(json_decode($item->container_name), 'value', 'slug')[$_COOKIE['language']] ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="cargo-container-count"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_ship_container_count'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-input-filter-box">
                                            <input type="text"
                                                   inputmode="decimal"
                                                   id="cargo-container-count"
                                                   name="cargo-container-count"
                                                   class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                                   placeholder="<?= $lang['b_ship_container_count'] ?>"
                                                   value="1"
                                                   style="min-height: 38px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="cargo-start-date"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_date'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-input-filter-box">
                                            <input type="text" id="cargo-start-date" name="cargo-start-date"  readonly="readonly"
                                                   class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                                   style="min-height: 38px;" value="<?= date('Y/m/d') ?>">

                                            <input type="hidden" id="cargo-start-date-ts" name="cargo-start-date-ts">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="">
                                        <button type="button" id="submit_cargo_step1"
                                                class="btn-x btn-next border-0"><?= $lang['next_level'] ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-step ">
                            <div class="row">



                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="cargo-origin-country"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_source_country'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-custom-select cargo-origin-country">
                                            <select class="form-select width-95 my-1 mb-3" id="cargo-origin-country"
                                                    name="cargo-origin-country" data-width="100%">
                                                <option value="-1"
                                                        selected><?= $lang['b_cargo_select_country'] ?></option>
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
                                            <select class="form-select width-95 my-1 mb-3" id="cargo-dest-country"
                                                    name="cargo-dest-country" data-width="100%">
                                                <option value="-1"
                                                        selected><?= $lang['b_cargo_select_country'] ?></option>
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
                                            <select class="form-select width-95 my-1 mb-3" id="cargo-origin"
                                                    name="cargo-origin" data-width="100%">
                                                <option value="-1"
                                                        selected><?= $lang['b_cargo_select_cities'] ?></option>
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
                                            <select class="form-select width-95 my-1 mb-3" id="cargo-destination"
                                                    name="cargo-destination" data-width="100%">
                                                <option value="-1"
                                                        selected><?= $lang['b_cargo_select_cities'] ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="source-port"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_source_port'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-custom-select cargo-customs-of-origin">
                                            <select class="form-select width-95 my-1 mb-3" id="source-port"
                                                    name="source-port" data-width="100%">
                                                <option value="-1"
                                                        selected><?= $lang['b_select_cargo_desc_port'] ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="dest-port"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_desc_port'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-custom-select cargo-destination-customs">
                                            <select class="form-select width-95 my-1 mb-3"
                                                    id="dest-port"
                                                    name="dest-port" data-width="100%">
                                                <option value="-1"
                                                        selected><?= $lang['b_select_cargo_desc_port'] ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <button type="button"
                                                class="mj-btn-cancel btn-prev text-white border-0 w-100 me-1"
                                                style="min-height: 44px; border-radius: 10px;"><?= $lang['previous_level'] ?></button>
                                        <button type="button" id="submit_cargo_step2"
                                                class="mj-btn-more btn-next w-100 ms-1"
                                                style="min-height: 44px; border-radius: 10px;"><?= $lang['next_level'] ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-step ">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="cargo-weight"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_weight'] ?>
                                        </label>
                                        <div class="d-flex align-items-center mj-input-filter-box">
                                            <input type="text"
                                                   inputmode="decimal"
                                                   data-max="25"
                                                   class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                                   id="cargo-weight"
                                                   name="cargo-weight"
                                                   lang="en"
                                                   placeholder="<?= $lang['b_cargo_weight_placeholder'] ?>"
                                                   style="min-height: 38px;">
                                            <div> <?= $lang['u_ton'] ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="cargo-volume"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_inquiry_volume'] ?>
                                        </label>
                                        <div class="d-flex align-items-center mj-input-filter-box">
                                            <input type="text"
                                                   inputmode="decimal"
                                                   data-max="25"
                                                   class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                                   id="cargo-volume"
                                                   name="cargo-volume"
                                                   lang="en"
                                                   placeholder="<?= $lang['b_cargo_weight_placeholder'] ?>"
                                                   style="min-height: 38px;">
                                            <div> <?= $lang['u_km3'] ?></div>
                                        </div>
                                    </div>
                                </div>



                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="cargo-description"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1"><?= $lang['b_more_desc'] ?></label>
                                        <div class="mj-input-filter-box">
                                            <textarea class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                                      id="cargo-description"
                                                      name="cargo-description" placeholder="<?= $lang['b_more_desc'] ?>"
                                                      rows="5"></textarea>
                                        </div>
                                    </div>
                                </div>



                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <input type="hidden" id="token" name="token"
                                               value="<?= Security::initCSRF('inquiry-ship') ?>">
                                        <button type="button"
                                                class="mj-btn-cancel btn-prev text-white border-0 w-100 me-1"
                                                style="min-height: 44px; border-radius: 10px;"><?= $lang['previous_level'] ?></button>
                                        <button type="button" id="submit-cargo" name="submit-cargo"
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
        </div>
    </main>
    <?php
    getFooter('', false, false);
} else {
    header('location: /login');
}