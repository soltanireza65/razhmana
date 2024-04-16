<?php

global $lang;

use MJ\Security\Security;

if (User::userIsLoggedIn()) {
    User::checkUserSlugAccess();
    $user = User::getUserInfo();

    include_once 'header-footer.php';

    enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');
    enqueueStylesheet('dropzone-css', '/dist/libs/dropzone/min/dropzone.min.css');
    enqueueStylesheet('persian-datepicker-css', '/dist/libs/persian-calendar/persian-datepicker.min.css');

    enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
    enqueueScript('dropzone-js', '/dist/libs/dropzone/min/dropzone.min.js');
    enqueueScript('persian-date-js', '/dist/libs/persian-calendar/persian-date.min.js');
    enqueueScript('persian-datepicker-js', '/dist/libs/persian-calendar/persian-datepicker.min.js');
    enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    enqueueScript('add-cargo-init-js', '/dist/js/businessman/add-cargo.init.js');
    getHeader($lang['b_submit_cargo'], true);
    ?>
    <main style="padding-bottom: 35px !important; ">
        <div class="container">
            <?php
            if ($user->UserStatus != 'suspend') {
                ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mj-b-stepper-card">
                            <img id="truck-addcargo" src="/dist/images/truck-image.png" alt="">
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

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="cargo-type"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_type'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-custom-select cargo-type">
                                            <select class="form-select my-1 mb-3"
                                                    id="cargo-type"
                                                    name="cargo-type"
                                                    data-width="100%"
                                                    data-placeholder="<?= $lang['b_select_cargo_type'] ?>">
                                                <option value=""></option>
                                                <?php
                                                $cargoTypes = Businessman::getCargoTypes();
                                                foreach ($cargoTypes->response as $item) {
                                                    ?>
                                                    <option value="<?= $item->CategoryId ?>"><?= $item->CategoryName ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="cargo-car-type"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_car_type'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-custom-select cargo-car-type">
                                            <select class="form-select my-1 mb-3"
                                                    id="cargo-car-type"
                                                    name="cargo-car-type"
                                                    data-width="100%"
                                                    data-placeholder="<?= $lang['b_select_car_type'] ?>">
                                                <option value=""></option>
                                                <?php
                                                $carTypes = Driver::getCarTypes();
                                                foreach ($carTypes->response as $item) {
                                                    ?>
                                                    <option value="<?= $item->TypeId ?>"><?= $item->TypeName ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
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
                                            <input type="text" readonly="readonly"
                                                   id="cargo-start-date"
                                                   name="cargo-start-date"
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

                        <div class="form-step">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="cargo-weight"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_weight'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <small class="d-block mj-font-11 mb-1"><?= $lang['b_cargo_weight_desc'] ?></small>
                                        <div class="mj-input-filter-box">
                                            <input type="text"
                                                   inputmode="decimal"
                                                   data-max="25"
                                                   class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                                   id="cargo-weight"
                                                   name="cargo-weight"
                                                   placeholder="<?= $lang['b_cargo_weight_placeholder'] ?>"
                                                   style="min-height: 38px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="cargo-needed-car"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_car_count'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <small class="d-block mj-font-11 mb-1"><?= $lang['b_cargo_car_count_desc'] ?></small>
                                        <div class="mj-input-filter-box">
                                            <input type="number"
                                                   inputmode="numeric"
                                                   value="1"
                                                   class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                                   id="cargo-needed-car"
                                                   name="cargo-needed-car"
                                                   placeholder="<?= $lang['b_cargo_car_count_placeholder'] ?>"
                                                   style="min-height: 38px;direction: rtl;">
                                        </div>
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

                                <div class="col-12 d-none">
                                    <div class="form-check form-switch pb-3 ps-0 d-flex justify-content-start align-items-center">
                                        <label class="text-dark mj-fw-500 mj-font-12 mj-greed-road-label"
                                               for="green-street">
                                            <div class="mj-green-road-blob"></div>
                                            <?= $lang['b_green_street_label'] ?>
                                        </label>
                                        <input type="checkbox" class="form-check-input" id="green-street"
                                               style="width:3em;margin-left: 20%;">
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="cargo-customs-of-origin"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_source_customs'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-custom-select cargo-customs-of-origin">
                                            <select class="form-select width-95 my-1 mb-3"
                                                    id="cargo-customs-of-origin"
                                                    name="cargo-customs-of-origin"
                                                    data-width="100%"
                                                    data-placeholder="<?= $lang['u_select_one_customs'] ?>">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="cargo-destination-customs"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_desc_customs'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-custom-select cargo-destination-customs">
                                            <select class="form-select width-95 my-1 mb-3"
                                                    id="cargo-destination-customs"
                                                    name="cargo-destination-customs"
                                                    data-placeholder="<?= $lang['u_select_one_customs'] ?>"
                                                    data-width="100%">
                                                <option value=""></option>
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

                        <div class="form-step">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="cargo-recommended-price"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_recommended_price_title'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-input-filter-box d-flex" style="padding: 4px 16px 4px 4px">
                                            <input type="text" inputmode="numeric"
                                                   class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                                   id="cargo-recommended-price" name="cargo-recommended-price"
                                                   placeholder="<?= $lang['b_cargo_recommended_price'] ?>"
                                                   style="min-height: 38px;">

                                            <select class="mj-custom-form-select" id="cargo-monetary-unit"
                                                    name="cargo-monetary-unit">
                                                <option value="-1" selected><?= $lang['b_currency_type'] ?></option>
                                                <?php
                                                $currencies = Driver::getCurrencyList();
                                                foreach ($currencies->response as $item) {
                                                    ?>
                                                    <option value="<?= $item->CurrencyId ?>"><?= $item->CurrencyName ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <small class="d-block mj-font-11 mb-1 text-danger"
                                               style="padding: 4px 16px 4px 4px"><?= $lang['b_price_zero_cargo_desc'] ?></small>

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
                                    <div class="mb-3">
                                        <label class="text-dark mj-fw-500 mj-font-12 mb-1"><?= $lang['b_your_message'] ?></label>
                                        <form action="#" method="post" class="dropzone mj-dropzone" id="cargoImages"
                                              data-plugin="dropzone" data-previews-container="#file-previews"
                                              data-upload-preview-template="#uploadPreviewTemplate">
                                            <div class="fallback">
                                                <input type="file" name="file">
                                            </div>

                                            <div class="dz-message needsclick">
                                                <img src="/dist/images/icons/folder-plus.svg" class="mb-2" alt="">
                                                <h5><?= $lang['b_cargo_images_upload_desc'] ?></h5>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="row dropzone-previews mt-3" id="file-previews"></div>

                                    <div class="d-none" id="uploadPreviewTemplate">
                                        <div class="col-12">
                                            <div class="card shadow-none border">
                                                <div class="p-2">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <img data-dz-thumbnail src="#"
                                                                 class="avatar-sm rounded" alt="">
                                                        </div>
                                                        <div class="col">
                                                            <strong class="text-muted" data-dz-name></strong>
                                                            <p class="mb-0" data-dz-size></p>
                                                            <div class="progress">
                                                                <div class="progress-bar progress-bar-striped"
                                                                     role="progressbar" data-dz-progress
                                                                     aria-valuemin="0"
                                                                     aria-valuemax="100"></div>

                                                            </div>
                                                            <p class="text-center mj-dropzone-progress">
                                                                <?= $lang['dropzone_inprogress'] ?>
                                                            </p>
                                                        </div>
                                                        <div class="col-auto">
                                                            <a href="javascript:void(0);"
                                                               class="btn btn-lg btn-link text-danger shadow-none"
                                                               data-dz-remove>
                                                                <img src="/dist/images/icons/close-cargo.svg" alt="">
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <input type="hidden" id="token" name="token"
                                               value="<?= Security::initCSRF('add-cargo') ?>">
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
                <?php
            } else if ($user->UserStatus == 'suspend') {
                ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="mj-alert mj-alert-with-icon mj-alert-warning mb-3">
                            <div class="mj-alert-icon">
                                <img src="/dist/images/icons/circle-exclamation.svg" alt="exclamation">
                            </div>

                            <div class="d-flex align-items-center justify-content-between w-100 pe-1">
                                <?= str_replace('#ACTION#', $lang['b_submit_cargo'], $lang['b_alert_suspend']) ?>

                                <a href="/user/laws"
                                   class="btn btn-xs mj-btn-more mj-font-10 mj-fw-300 shadow-none"><?= $lang['more_desc'] ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </main>
    <?php
    getFooter('', false, false);
}
else {
    header('location: /login');
}