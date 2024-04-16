<?php

global $lang;

use MJ\Router\Router;
use MJ\Security\Security;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    User::checkUserSlugAccess();
    $cargo = Driver::getCargoDetail($_REQUEST['id']);
    if ($cargo->status == 200) {
        $cargo = $cargo->response;

        if (!in_array($cargo->CargoStatus, ['pending', 'canceled', 'rejected', 'expired'])) {
            include_once 'header-footer.php';

            enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');
//        enqueueStylesheet('dropzone-css', '/dist/libs/dropzone/min/dropzone.min.css');

//        enqueueScript('dropzone-js', '/dist/libs/dropzone/min/dropzone.min.js');
            enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
            enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
            enqueueScript('send-request-init', '/dist/js/driver/send-request.init.js');

            getHeader($lang['d_send_request_title']);

            $user = User::getUserInfo();

            ?>
            <main class="container" style="padding-bottom: 80px;">
                <?php
                if (!Driver::checkCanSendRequest($cargo->CargoId, $user->UserId)) {
                    ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="mj-alert mj-alert-with-icon mj-alert-warning mb-3">
                                <div class="mj-alert-icon">
                                    <img src="/dist/images/icons/circle-exclamation.svg" alt="exclamation">
                                </div>
                                <?= $lang['d_alert_authentication_3'] ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                if ($user->UserStatus != 'suspend' && Driver::checkCanSendRequest($cargo->CargoId, $user->UserId)) {
                    ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card mj-card">
                                <div class="card-body">
                                    <div class="">
                                        <div class="mb-3">
                                            <span class="mj-d-icon-title d-block mj-fw-700 mj-font-13 mb-1"><?= $lang['u_recommended_price_2'] ?></span>
                                            <p class="mj-d-cargo-item-desc mb-0">
                                            <span>
                                                <img src="/dist/images/icons/circle-exclamation-gray.svg" class="me-1"
                                                     alt="exclamation"/>
                                            </span>
                                                <?= $lang['d_send_request_sub_title'] ?>
                                            </p>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 d-none">
                                                <div class="mb-3">
                                                    <label for="car-type"
                                                           class="form-label mj-form-label mj-fw-500 mj-font-12 mb-1">
                                                        <?= $lang['d_send_request_select_car'] ?>
                                                    </label>
                                                    <div class="mj-custom-select cars-list mj-add-car-select">
                                                        <select class="is-invalid " name="cars-list" id="cars-list"
                                                                data-width="65%">
                                                            <option value="-1"><?= $lang['d_send_request_choose_car'] ?></option>
                                                            <?php
                                                            $cars = Driver::getMyCarsList($user->UserId);
                                                            foreach ($cars->response as $item) {
                                                                if ($item->CarStatus == 'accepted') {
                                                                    ?>
                                                                    <option value="<?= $item->CarId ?>"
                                                                            data-image="<?= $item->TypeImage ?>">
                                                                        <?=$item->CarPlaque . ' | ' . $item->car_name; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                        <a href="javascript:void (0);"
                                                           id="btn-add-car"
                                                           class="mj-d-add-car"><?= $lang['u_new_car']; ?></a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label for="price"
                                                           class="form-label mj-form-label mj-fw-500 mj-font-12 mb-1">
                                                        <?= $lang['u_cargo_price_driver'] ?>
                                                    </label>
                                                    <div class="d-flex align-items-center mj-input-filter-box">
                                                        <img src="/dist/images/icons/money-check-dollar-pen.svg"
                                                             alt="price"/>
                                                        <input type="text"
                                                               inputmode="numeric"
                                                               class="mj-input-filter mj-fw-700 mj-font-13 px-2 py-1"
                                                               id="price"
                                                               name="price"
                                                               lang="en"
                                                               placeholder="<?= $lang['d_send_request_price_placeholder'] ?>">
                                                        <div><?= $cargo->CargoMonetaryUnit ?></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-7 mx-auto">
                                                <div class="mb-3">
                                                    <input type="hidden" id="token" name="token"
                                                           value="<?= Security::initCSRF('request') ?>">
                                                    <button type="button"
                                                            class="mj-btn mj-btn-primary mj-fw-400 py-2 w-100"
                                                            id="submit-request" name="submit-request"
                                                            data-cargo="<?= $cargo->CargoId ?>"
                                                            style="min-height: unset;">
                                                        <?= $lang['u_recommended_price_2'] ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-none">
                                        <div class="mj-info-box text-center mb-3">
                                            <img src="/dist/images/icons/money-check-dollar-pen.svg"
                                                 alt="request-sent"/>
                                            <h5><?= $lang['d_send_request_sent'] ?></h5>
                                            <p class="mb-0">
                                                <?= $lang['d_send_request_sent_sub_title'] ?>
                                            </p>
                                        </div>
                                    </div>

                                    <?php
                                    if (empty($request) || (!empty($request) && $request->RequestStatus == 'pending')) {
                                        ?>
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="/dist/images/icons/circle-dollar.svg" class="mj-d-icon-box me-2"
                                                 alt="lowest-price">
                                            <span class="mj-d-icon-title"><?= $lang['d_cargo_lowest_price'] ?></span>
                                        </div>

                                        <div class="row">
                                            <?php
                                            if (isset($cargo->CargoMinRequest[0]) && $cargo->CargoMinRequest[0] != 0) {
                                                ?>
                                                <div class="col-6">
                                                    <div class="mb-2">
                                                        <span>
                                                            <?= number_format($cargo->CargoMinRequest[0]) ?>
                                                            <small><?= $cargo->CargoMonetaryUnit ?></small>
                                                        </span>
                                                    </div>
                                                </div>
                                                <?php
                                            } else {
                                                ?>
                                                <div class="col-6">
                                                    <div class="mb-2">
                                                        <span><?= $lang['d_lowest_price_recommended_none'] ?></span>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>

                                            <?php
                                            if (isset($cargo->CargoMinRequest[1]) && $cargo->CargoMinRequest[1] != 0) {
                                                ?>
                                                <div class="col-6">
                                                    <div class="mb-2">
                                                        <span>
                                                            <?= number_format($cargo->CargoMinRequest[1]) ?>
                                                            <small><?= $cargo->CargoMonetaryUnit ?></small>
                                                        </span>
                                                    </div>
                                                </div>
                                                <?php
                                            } else {
                                                ?>
                                                <div class="col-6">
                                                    <div class="mb-2">
                                                        <span class="mj-d-placeholder rounded-pill"></span>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <?php
                                            if (isset($cargo->CargoMinRequest[2]) && $cargo->CargoMinRequest[2] != 0) {
                                                ?>
                                                <div class="col-6">
                                                    <div class="mb-2">
                                                        <span>
                                                            <?= number_format($cargo->CargoMinRequest[2]) ?>
                                                            <small><?= $cargo->CargoMonetaryUnit ?></small>
                                                        </span>
                                                    </div>
                                                </div>
                                                <?php
                                            } else {
                                                ?>
                                                <div class="col-6">
                                                    <div class="mb-2">
                                                        <span class="mj-d-placeholder rounded-pill"></span>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>

                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <span class="mj-d-placeholder rounded-pill"></span>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <span class="mj-d-placeholder rounded-pill"></span>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <span class="mj-d-placeholder rounded-pill"></span>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <span class="mj-d-placeholder rounded-pill"></span>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <span class="mj-d-placeholder rounded-pill"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <div class="my-3">
                                        <lottie-player src="/dist/lottie/send-request.json" class="mx-auto"
                                                       style="max-width: 400px;" speed="1" loop
                                                       autoplay></lottie-player>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>

                <div class="modal fade" id="add-car-model" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header" style="border-bottom: 0;">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                        style="margin: 0;"></button>

                            </div>
                            <div class="modal-body" style="border-bottom:0 ;">

                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="car-name"
                                                   class="text-dark mj-fw-500 mj-font-12 mb-1">
                                                <?= $lang['d_my_cars_table_type'] ?>
                                                <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                            </label>
                                            <div class="mj-input-filter-box">
                                                <input type="text" inputmode="text" id="car-name" name="car-name"
                                                       class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                                       placeholder="<?= $lang['b_car_name_example'] ?>"
                                                       style="min-height: 38px;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="car-type"
                                                   class="form-label mj-form-label mj-fw-500 mj-font-12 mb-1">
                                                <?= $lang['d_my_cars_choose_car_type'] ?>
                                                <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                            </label>
                                            <div class="mj-custom-select car-type">
                                                <select class="is-invalid"
                                                        name="car-type"
                                                        id="car-type"
                                                        data-width="100%">
                                                    <option value="-1">
                                                    </option>
                                                    <?php
                                                    $carTypes = Driver::getCarTypes();
                                                    foreach ($carTypes->response as $item) {
                                                        ?>
                                                        <option data-image="<?=$item->TypeImage ; ?>" value="<?= $item->TypeId ?>"><?= $item->TypeName ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="plaque-type"
                                                   class="form-label mj-form-label mj-fw-500 mj-font-12 mb-1">
                                                <?= $lang['d_my_cars_choose_plaque_type'] ?>
                                                <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                            </label>
                                            <div class="mj-custom-select plaque-type">
                                                <select class="is-invalid"
                                                        name="plaque-type"
                                                        id="plaque-type"
                                                        data-width="100%"
                                                        data-placeholder="<?= $lang['d_my_cars_choose_plaque_type'] ?>">
                                                    <option value=""></option>
                                                    <?php
                                                    $plaqueTypes = json_decode(Utils::getFileValue('plaque_types.json', null, false));
                                                    $language = trim($_COOKIE['language']);
                                                    foreach ($plaqueTypes as $key => $type) {
                                                        ?>
                                                        <option value="<?= $key ?>"><?= $type->name->$language ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="plaque-type"
                                                   class="form-label mj-form-label mj-fw-500 mj-font-12 mb-1">
                                                <?= $lang['d_my_cars_table_plaque'] ?>
                                                <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                            </label>
                                            <div>
                                                <div class="row flex-row-reverse">
                                                    <div class="col">
                                                        <div class="mb-3">
                                                            <div class="mj-input-filter-box">
                                                                <input type="text"
                                                                       id="plaque-number"
                                                                       inputmode="text"
                                                                       class="mj-input-filter text-center mj-fw-700 mj-font-13 px-0 py-1"
                                                                       name="plaque-number"
                                                                       placeholder="<?=$lang['d_my_cars_plaque_type'];?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-7 mx-auto">
                                        <input type="hidden" id="token-new-car" name="token-new-car"
                                               value="<?= Security::initCSRF2() ?>">
                                        <button type="button" class="mj-btn-more py-2 w-100" id="submit-car"
                                                name="submit-car">
                                            <?= $lang['d_button_add'] ?>
                                        </button>
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

                                    <h6 class="mb-0"><?= str_replace('#ACTION#', $lang['submit_car'], $lang['b_info_processing']) ?>
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
            <?php
        } else {
            header('location:/cargo-ads/' . $cargo->CargoId);
        }
        getFooter('', false);
    } else {
        Router::trigger404();
    }
} else {
    header('location:/login');
}