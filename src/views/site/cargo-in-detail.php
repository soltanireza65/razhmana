<?php

global $lang, $Settings;

use MJ\Router\Router;
use MJ\Security\Security;
use MJ\Utils\Utils;

include_once 'header-footer.php';
$cargo = Driver::getCargoInDetail($_REQUEST['id']);
$language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
if ($cargo->status == 200) {
    $cargo = $cargo->response;
    if (!in_array($cargo->CargoStatus, ['pending', 'canceled', 'rejected'])) {
        $user = null;
        if (User::userIsLoggedIn()) {
            $user = User::getUserInfo();
        }
        if (!is_null($user)) {
            $request = Driver::getMyRequestInForCargo($cargo->CargoId, $user->UserId);
        }
        $requests = Businessman::getCargoInDrivers($cargo->BusinessmanId, $cargo->CargoId);


        enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
        enqueueStylesheet('ol-css', '/dist/libs/ol/ol.css'); //
        enqueueScript('ol-js', '/dist/libs/ol/dist/ol.js');//
        enqueueScript('map-js', '/map/assets/index.4eb0d7de.js');

        enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
        enqueueScript('elm', '/dist/libs/ele-pep/elem.js');
        enqueueScript('map-js', '/map/assets/index.11536adb.js');
        enqueueScript('cargo-in-detail', '/dist/js/site/cargo-in-detail.js');

        getHeader($lang['d_cargo_detail_title']);

        $sourceOutput= $cargo->CargoOriginCity;
        $destOutput= $cargo->CargoDestinationCity;
        ?>

        <div id="map-detail"
             data-source-lat="<?=$cargo->CargoOriginCityInfo->city_long?>"
             data-source-long="<?=$cargo->CargoOriginCityInfo->city_lat?>"
             data-dest-lat="<?= $cargo->CargoDestinationCityInfo->city_long?>"
             data-dest-long="<?= $cargo->CargoDestinationCityInfo->city_lat?>"
             data-source-icon="https://ntirapp.local/dist/images/pin.png"
             data-dest-icon="https://ntirapp.local/dist/images/pingreen.png" data-zoom="3" data-img-size="28"
             data-road-color="var(--primary)" ></div>



        <!-- call owner modal start  -->
        <div class="mj-cargo-owner-modal-info modal " id="staticBackdrop" data-bs-backdrop="static"
             data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="mj-cargo-owner-info-head">
                            <div class="fa-phone fa-beat"></div>
                        </div>
                        <h5 class="mj-cargo-owner-info-welcome"><?=$lang['u_call_owner_modal_title']?></h5>
                        <div class="mt-2 " style="text-align: right;margin-bottom: 5px;width: 95%;display: block"><?=$lang['u_call_owner_modal_mobile']?></div>
                        <div class="mj-cargo-owner-info-list">
                            <span style="color: #303030;font-size: 16px;" dir="ltr"><?=Utils::getFileValue("settings.txt",'support_call');?></span>
                            <div>
                                <a href="tel:<?=Utils::getFileValue("settings.txt",'support_call');?>">
                                    <div class="fa-mobile-button"></div>
                                    <span><?=$lang['u_call_owner_modal_calling']?></span>
                                </a>
                            </div>
                        </div>
                        <div class="mt-2" style="    text-align: right;margin-bottom: 5px;width: 95%;display: block"><?=$lang['u_call_owner_modal_mobile']?></div>
                        <div class="mj-cargo-owner-info-list">
                            <span style="color: #303030;font-size: 16px;" dir="ltr"><?=Utils::getFileValue("settings.txt",'support_call_2');?></span>
                            <div>
                                <a href="tel:<?=Utils::getFileValue("settings.txt",'support_call_2');?>">
                                    <div class="fa-phone"></div>
                                    <span><?=$lang['u_call_owner_modal_calling']?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <?=$lang['u_close_call_owner_modal']?>!
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- call owner modal end  -->
        <main class="container" style="padding-bottom: 70px;">
            <?php
            if ($cargo->CargoStatus == 'completed' || (!empty($request) && $request->RequestStatus == 'completed')) {
                ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mj-alert mj-alert-with-icon mj-alert-success mb-3">
                            <div class="mj-alert-icon">
                                <img src="/dist/images/icons/circle-exclamation.svg" alt="exclamation">
                            </div>
                            <?= $lang['d_alert_completed_request'] ?>
                        </div>
                    </div>
                </div>
                <?php
            }


            if (!empty($request) && $request->RequestStatus == 'pending') {
                ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mj-alert mj-alert-with-icon mj-alert-warning mb-3">
                            <div class="mj-alert-icon">
                                <img src="/dist/images/icons/circle-exclamation.svg" alt="exclamation">
                            </div>
                            <?= $lang['d_alert_pending_request'] ?>
                        </div>
                    </div>
                </div>
                <?php
            }

            if (!empty($request)) {
                ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mj-alert mj-alert-with-icon mj-alert-warning mb-3">
                            <div class="mj-alert-icon">
                                <img src="/dist/images/icons/circle-exclamation.svg" alt="exclamation">
                            </div>
                            <?php
                            $requestPrice = str_replace('#AMOUNT#', number_format($request->RequestPrice), $lang['d_alert_request']);
                            $requestPrice = str_replace('#CURRENCY#', $cargo->CargoMonetaryUnit, $requestPrice);
                            echo $requestPrice;
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }

            if (!empty($request) && in_array($request->RequestStatus, ['pending', 'accepted'])) {
                ?>
                <div class="row">
                    <div class="col-12 mb-3">
                        <button type="button" class="mj-btn-more w-100" style="min-height: 42px"
                                data-bs-toggle="modal"
                                data-bs-target="#cancel-request-modal">
                            <?= $lang['d_cancel_request'] ?>
                        </button>
                    </div>
                </div>

                <div class="modal fade" id="cancel-request-modal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <?= $lang['d_cancel_request'] ?>
                                </h5>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="reason"
                                                   class="text-dark mj-fw-500 mj-font-12 mb-1"><?= $lang['d_cancel_request_label'] ?>
                                                :</label>
                                            <div class="mj-input-filter-box">
                                                <textarea class="mj-input-filter mj-fw-400 mj-font-12" id="reason"
                                                          name="reason" rows="4"
                                                          placeholder="<?= $lang['d_cancel_request_placeholder'] ?>"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex justify-content-center">
                                            <input type="hidden" id="token-cancel" name="token-cancel"
                                                   value="<?= Security::initCSRF('cancel-request-in') ?>">
                                            <button class="mj-btn-more mj-btn-cancel-yes px-4 me-1"
                                                    id="submit-cancel"
                                                    name="submit-cancel"
                                                    data-request="<?= $request->RequestId ?>">
                                                <?= $lang['d_btn_yes'] ?>
                                            </button>
                                            <button class="mj-btn-more mj-btn-cancel px-4 ms-1"
                                                    data-bs-dismiss="modal">
                                                <?= $lang['d_btn_close'] ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }

            if (!empty($request) && ($request->RequestStatus == 'progress' || $request->RequestStatus == 'accepted')) {
                ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mj-info-box mb-3">
                            <h4 class="mj-fw-500 mj-font-13 mt-0 mb-3"><?= $lang['d_businessman_info'] ?>:</h4>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="mj-fw-600 mj-font-13 mb-1"><?php
                                        //$cargo->BusinessmanDisplayName
                                        echo $lang['d_cargo_owner_call'];
                                        ?></div>
                                    <div class="mj-fw-600 mj-font-13">
                                        <bdi><?php
                                            // $cargo->BusinessmanMobile
                                            echo Utils::getFileValue("settings.txt", 'support_call');
                                            ?> </bdi>
                                    </div>
                                </div>
                                <a href="tel:<?php
                                // $cargo->BusinessmanMobile
                                echo Utils::getFileValue("settings.txt", 'support_call');
                                ?>" class="mj-btn-2 mj-btn-info">
                                    <img src="/dist/images/icons/circle-phone.svg" class="me-2" alt="direct-call">
                                    <?= $lang['d_direct_call'] ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }

            if (!empty($request) && $request->RequestStatus == 'accepted') {
                ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mj-info-box mb-3">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <button type="button" class="mj-btn-2 mj-btn-primary shadow-none w-100"
                                            onclick="window.location.href='/driver/start-transportation-in/<?= $request->RequestId ?>'">
                                        <?= $lang['d_start_loading'] ?>
                                    </button>
                                </div>

                                <div class="col-6">
                                    <div class="text-center mj-text-danger mj-fw-500 mj-font-12">
                                        <?= $lang['d_alert_start_loading'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="row">
                <div class="col-12">
                    <?php
                    if (User::userIsLoggedIn()) {
                        if ($user->UserStatus != 'suspend' && !in_array($cargo->CargoStatus, ['pending', 'rejected', 'completed'])) {
                            if (empty($request) || (!empty($request) && !in_array($request->RequestStatus, ['pending', 'accepted', 'progress', 'completed']))) {

                                if (isset($_COOKIE['user-type']) && in_array($_COOKIE['user-type'], ['driver'])) { ?>
                                    <button type="button" class="mj-d-floating-button mj-d-floating-button-top"
                                            onclick="window.location.replace('/driver/send-request-in/<?= $cargo->CargoId ?>')"><?= $lang['d_send_request'] ?></button>
                                <?php } elseif (isset($_COOKIE['user-type']) && in_array($_COOKIE['user-type'], ['guest'])) { ?>

                                <?php } elseif (isset($_COOKIE['user-type']) && in_array($_COOKIE['user-type'], ['businessman'])) { ?>

                                <?php }


                            }
                        }

                        if (!empty($request) && $request->RequestStatus == 'progress') {
                            if ($user->UserType == 'driver') {
                                ?>
                                <button type="button"
                                        onclick="window.location.href = '/driver/end-transportation-in/<?= $request->RequestId ?>'"
                                        class="mj-d-floating-button mj-d-floating-button-top"><?= $lang['d_cargo_end_transportation_button'] ?></button>
                                <?php
                            }
                        }

                        if (!empty($request) && in_array($request->RequestStatus, ['accepted', 'progress', 'completed'])) {
                            if ($user->UserType == 'driver') {
                                ?>
                                <div class="mj-d-cargo-card mb-3">
                                    <input type="hidden" id="token-complaint" name="token-complaint"
                                           value="<?= Security::initCSRF('submit-complaint-in') ?>">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="/dist/images/icons/headset.svg" class="mj-d-icon-box me-2"
                                                 alt="support">
                                            <div>
                                                <span class="mj-d-icon-title"><?= $lang['d_cargo_support'] ?></span>
                                                <p class="mj-d-cargo-item-desc mb-0">
                                                    <?= $lang['d_cargo_support_sub_title'] ?>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mj-support-links-cargo">
                                            <a href="tel:<?= Utils::getFileValue("settings.txt", 'support_call') ?>"
                                               class="mj-btn mj-d-btn-call me-2"
                                               style="flex: 0 0 auto; min-height: 34px;">
                                                <img src="/dist/images/icons/circle-phone.svg" class="me-1" alt="call"/>
                                                <?= $lang['d_cargo_call'] ?>
                                            </a>

                                            <a href="https://wa.me/<?= Utils::getFileValue("settings.txt", 'whatsapp') ?>"
                                               class="mj-btn mj-d-btn-whatsapp me-2"
                                               style="flex: 0 0 auto; min-height: 34px;">
                                                <img src="/dist/images/icons/whatsapp.svg" class="me-1" alt="whatsapp"/>
                                                <?= $lang['d_cargo_whatsapp'] ?>
                                            </a>

                                            <a href="/user/support" class="mj-btn mj-d-btn-ticekt me-2"
                                               style="flex: 0 0 auto; min-height: 34px;">
                                                <img src="/dist/images/icons/circle-envelope.svg" class="me-1"
                                                     alt="ticket"/>
                                                <?= $lang['d_cargo_ticket'] ?>
                                            </a>

                                            <?php
                                            if (Complaint::checkCanSendComplaintIn($cargo->CargoId, $request->RequestId, $user->UserId, $cargo->BusinessmanId)) {
                                                ?>
                                                <a href="javascript:void(0);" class="mj-btn mj-d-btn-complaint me-2"
                                                   style="flex: 0 0 auto; min-height: 34px;"
                                                   data-cargo="<?= $cargo->CargoId ?>"
                                                   data-request="<?= $request->RequestId ?>"
                                                   data-businessman="<?= $cargo->BusinessmanId ?>"
                                                   onclick="submitComplaint(this);">
                                                    <img src="/dist/images/icons/whatsapp.svg" class="me-1"
                                                         alt="complaint"/>
                                                    <?= $lang['d_cargo_complaint'] ?>
                                                </a>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    } else {
                        ?>
                        <button type="button" class="mj-d-floating-button mj-d-floating-button-top"
                            <?= isset($_COOKIE['user-login']) ? '' : 'onclick="setCookie(\'login-back-url\' ,\'/cargo-ads/' . $_REQUEST['id'] . '\' ) ; window.location.replace(\'/login\')"' ?>
                        ><?= $lang['login']; ?></button>
                        <?php
                    }
                    ?>

                    <div class="mj-d-cargo-card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="mj-d-cargo-item-category me-2"
                                     style="background: <?= $cargo->CategoryColor ?>">
                                    <img src="<?= Utils::fileExist($cargo->CategoryIcon, BOX_EMPTY) ?>"
                                         alt="<?= $cargo->CategoryName ?>">
                                    <span><?= $cargo->CategoryName ?></span>
                                </div>
                                <div class="flex-fill">
                                    <h2 class="mj-d-cargo-item-header mt-0 mb-2"><?php

                                        echo $cargo->CargoName; ?></h2>
                                    <div class="mj-d-cargo-item-price-box d-flex align-items-center justify-content-between">
                                        <span><?= $lang['d_cargo_price'] ?>:</span>
                                        <span>
                                            <?php if ($cargo->CargoRecomendedPrice == 0) {
                                                echo $lang['u_agreement'];
                                            } else {
                                                echo number_format($cargo->CargoRecomendedPrice);
                                                ?>
                                                <small><?= $cargo->CargoMonetaryUnit ?></small>
                                            <?php } ?>
                                    </span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/country.svg"
                                             class="mj-d-cargo-item-icon me-2" alt="origin"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['country'] ?>:</div>
                                            <div class="mj-d-cargo-item-value"><?= $cargo->CargoOriginCountry->CountryName ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/calendar-star.svg"
                                             class="mj-d-cargo-item-icon me-2" alt="loading-time"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['d_cargo_loading_time'] ?>:
                                            </div>
                                            <div class="mj-d-cargo-item-value"><?= ($language == 'fa_IR') ? Utils::jDate('Y/m/d', $cargo->CargoStartTransportation) : date('Y-m-d', $cargo->CargoStartTransportation) ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/arrow-up-left-from-circle.svg"
                                             class="mj-d-cargo-item-icon me-2" alt="origin"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['d_cargo_origin'] ?>:</div>
                                            <div class="mj-d-cargo-item-value"><?= $sourceOutput ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/weight-scale.svg"
                                             class="mj-d-cargo-item-icon me-2" alt="weight"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['d_cargo_weight'] ?>:</div>
                                            <div class="mj-d-cargo-item-value">
                                                <?= $cargo->CargoWeight ?> <?= $lang['d_cargo_weight_unit'] ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/arrow-down-left-from-circle.svg"
                                             class="mj-d-cargo-item-icon me-2" alt="destination"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['d_cargo_destination'] ?>:
                                            </div>
                                            <div class="mj-d-cargo-item-value"><?= $destOutput ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/maximize.svg"
                                             class="mj-d-cargo-item-icon me-2" alt="volume"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['u_request_car_type'] ?>:
                                            </div>
                                            <div class="mj-d-cargo-item-value">
                                                <?= $cargo->CargoCarType ?> </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/barcode.svg"
                                             class="mj-d-cargo-item-icon me-2" alt="volume"/>
                                        <div>
                                            <div class="mj-d-cargo-item-title"><?= $lang['u_code_cargo'] ?>:
                                            </div>
                                            <div class="mj-d-cargo-item-value">
                                                <?= $cargo->CargoId ?> </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="cargo-detail col-6">
                                    <div class="d-flex align-items-center mb-1">
                                        <img src="/dist/images/icons/truck-numbers.svg"
                                             class="mj-d-cargo-item-icon me-2" alt="car-count"/>
                                        <div class="w-100">
                                            <div class="mj-d-cargo-item-title"><?= $lang['b_cargo_car_needed'] ?>
                                                (<?= count($requests->response) . ' / ' . $cargo->CargoCarCount ?>)
                                            </div>
                                            <div class="progress mt-1" dir="ltr">
                                                <div class="progress-bar <?= ($cargo->CargoStatus == 'completed') ? 'bg-success' : '' ?>"
                                                     aria-valuemin="0" aria-valuemax="<?= $cargo->CargoCarCount ?>"
                                                     aria-valuenow="0"
                                                     style="width: <?= (count($requests->response) / $cargo->CargoCarCount) * 100 ?>%">
                                                    <?= count($requests->response) . ' / ' . $cargo->CargoCarCount ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- start owner btn -->
                    <div class="mj-d-cargo-card mb-3">
                        <div class="card-body">
                            <a class="d-flex align-items-center mj-cargo-owner-call "
                               href="javascript:void(0);">
                                <img src="/dist/images/icons/phone.svg" class="mj-d-icon-box me-2"
                                     alt="route-on-the-map">
                                <span class=""><?= $lang['d_cargo_owner_call'] ?></span>
                            </a>
                        </div>
                    </div>
                    <!-- end owner btn -->
                    <div class="mj-d-cargo-card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <img src="/dist/images/icons/folder-image.svg" class="mj-d-icon-box me-2"
                                         alt="gallery">
                                    <span class="mj-d-icon-title"><?= $lang['d_cargo_gallery'] ?></span>
                                </div>

                                <div class="d-flex flex-row-reverse">
                                    <?php
                                    if (!empty($cargo->CargoImages)) {
                                        foreach ($cargo->CargoImages as $key => $image) {
                                            if ($key == 3) {
                                                break;
                                            }
                                            if ($key != 2) {
                                                ?>
                                                <a href="#gallery-modal" data-bs-toggle="modal"
                                                   data-gallery>
                                                    <img src="<?= Utils::fileExist($image, BOX_EMPTY) ?>"
                                                         class="mj-d-cargo-img ms-2"
                                                         alt="<?= $image ?>">
                                                </a>
                                                <?php
                                            } else {
                                                ?>
                                                <a href="#gallery-modal" data-bs-toggle="modal"
                                                   class="mj-d-cargo-img-overlay"
                                                   data-gallery>
                                                    <img src="<?= Utils::fileExist($image, BOX_EMPTY) ?>"
                                                         class="mj-d-cargo-img"
                                                         alt="<?= $image ?>">
                                                    <img src="/dist/images/icons/ellipsis.svg" data-more alt="more">
                                                </a>
                                                <?php
                                            }
                                        }
                                    } else {
                                        ?>
                                        <a href="#gallery-modal" data-bs-toggle="modal"
                                           class="mj-d-cargo-img-overlay"
                                           data-gallery>
                                            <img src="<?= Utils::fileExist($cargo->CategoryImage, BOX_EMPTY) ?>"
                                                 class="mj-d-cargo-img"
                                                 alt="<?= $cargo->CategoryImage ?>">
                                            <img src="/dist/images/icons/ellipsis.svg" data-more alt="more">
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </div>

                                <div class="modal fade" id="gallery-modal" data-bs-backdrop="static"
                                     data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-transparent border-0 shadow-none">
                                            <div class="modal-body">
                                                <button type="button"
                                                        class="btn mj-btn-danger mj-btn-close shadow-none rounded-3 mb-3"
                                                        data-bs-dismiss="modal">
                                                    <img src="/dist/images/icons/close.svg" width="12" height="12"
                                                         class="me-1" alt="exit">
                                                    <?= $lang['exit'] ?>
                                                </button>

                                                <div class="swiper gallery-swiper" dir="ltr">
                                                    <div class="swiper-wrapper">
                                                        <?php
                                                        if (!empty($cargo->CargoImages)) {
                                                            foreach ($cargo->CargoImages as $image) {
                                                                ?>
                                                                <div class="swiper-slide mj-d-cargo-gallery-img-box">
                                                                    <img src="<?= Utils::fileExist($image, BOX_EMPTY) ?>"
                                                                         class="mj-d-cargo-gallery-img"
                                                                         alt="<?= $image ?>"/>
                                                                </div>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <div class="swiper-slide mj-d-cargo-gallery-img-box">
                                                                <img src="<?= Utils::fileExist($cargo->CategoryImage, BOX_EMPTY) ?>"
                                                                     class="mj-d-cargo-gallery-img"
                                                                     alt="<?= $cargo->CategoryImage ?>"/>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="swiper-pagination"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                 <?php
                    $slugdesc = 'CargoDescription_' . $language;
                    if (!empty($cargo->$slugdesc)) {
                        ?>

                        <div class="mj-d-cargo-card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="/dist/images/icons/align-center.svg" class="mj-b-icon-box me-2"
                                         alt="description">
                                    <span class="mj-b-icon-title"><?= $lang['b_details_cargo_desc'] ?></span>
                                </div>

                                <div class="mj-b-cargo-item-desc">
                                    <?= $cargo->$slugdesc ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>


                    <?php
                    if (empty($request) || (!empty($request) && in_array($request->RequestStatus, ['pending', 'rejected', 'canceled']))) {
                        ?>
                        <div class="mj-d-cargo-card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="/dist/images/icons/headset.svg" class="mj-d-icon-box me-2"
                                         alt="support">
                                    <div>
                                        <span class="mj-d-icon-title"><?= $lang['d_cargo_support'] ?></span>
                                        <p class="mj-d-cargo-item-desc mb-0">
                                            <?= $lang['d_cargo_support_sub_title'] ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="mj-support-links-cargo">
                                    <a href="tel:<?= Utils::getFileValue("settings.txt", 'support_call') ?>"
                                       class="mj-btn mj-d-btn-call me-2"
                                       style="flex: 0 0 auto; min-height: 34px;">
                                        <img src="/dist/images/icons/circle-phone.svg" class="me-1" alt="call"/>
                                        <?= $lang['d_cargo_call'] ?>
                                    </a>

                                    <a href="https://wa.me/<?= Utils::getFileValue("settings.txt", 'whatsapp') ?>"
                                       class="mj-btn mj-d-btn-whatsapp me-2"
                                       style="flex: 0 0 auto; min-height: 34px;">
                                        <img src="/dist/images/icons/whatsapp.svg" class="me-1" alt="whatsapp"/>
                                        <?= $lang['d_cargo_whatsapp'] ?>
                                    </a>

                                    <a href="/user/support" class="mj-btn mj-d-btn-ticekt me-2"
                                       style="flex: 0 0 auto; min-height: 34px;">
                                        <img src="/dist/images/icons/circle-envelope.svg" class="me-1"
                                             alt="ticket"/>
                                        <?= $lang['d_cargo_ticket'] ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <div class="mj-d-cargo-card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="/dist/images/icons/location-dot.svg" class="mj-d-icon-box me-2"
                                     alt="route-on-the-map">
                                <span class="mj-d-icon-title"><?= $lang['d_cargo_route_on_the_map'] ?></span>
                            </div>
                            <script type="text/javascript">
                                let requestLocationIcon = '/dist/images/location.png';
                                let requestLocations = [
                                    {
                                        "lat": "<?= $cargo->CargoOriginCityInfo->city_lat ?>",
                                        "long": "<?= $cargo->CargoOriginCityInfo->city_long ?>"
                                    },
                                    {
                                        "lat": "<?= $cargo->CargoDestinationCityInfo->city_lat ?>",
                                        "long": "<?= $cargo->CargoDestinationCityInfo->city_long ?>"
                                    }
                                ];
                            </script>
                            <div class="mj-d-cargo-item-map">
                                <div id="map" class="map " style="position: relative; height: 300px;width: 100% ">
                                    <div id="popup"></div>
                                </div>
                                <div id="map-direction-share-btn">
                                    <a href="https://www.google.com/maps/dir/?api=1&origin=<?= $cargo->CargoOriginCityInfo->city_lat ?>.','<?= $cargo->CargoOriginCityInfo->city_long ?>&destination=<?= $cargo->CargoDestinationCityInfo->city_lat . ',' . $cargo->CargoDestinationCityInfo->city_long ?>&travelmode=car"
                                       class="btn btn-primary">
                                        <img src="/dist/images/icons/location-dot(blue).svg" alt="">
                                        <?= $lang['u_show_on_map']; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    if (empty($request) || (!empty($request) && $request->RequestStatus == 'pending')) {
                        if (User::userIsLoggedIn()) {
                            if ($user->UserType == 'driver') {
                                ?>
                                <div class="mj-d-cargo-card mb-3">
                                    <div class="card-body">
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
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    }

                    if (!empty($request) && $request->RequestStatus == 'progress') {

                        ?>
                        <div class="mj-d-cargo-card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between my-2 p-3"
                                     style="background: #e0eeff;border-radius: 11px;">
                                    <span><?= $lang['u_agreement_price'] ?> </span>
                                    <span class="mj-b-icon-title">  <?= number_format($request->RequestPrice) . ' ' . $cargo->CargoMonetaryUnit ?></span>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <img src="/dist/images/icons/circle-dollar.svg" class="mj-d-icon-box me-2"
                                         alt="additional-expenses">
                                    <div>
                                        <span class="mj-d-icon-title"><?= $lang['d_cargo_additional_expenses'] ?></span>
                                        <p class="mj-d-cargo-item-desc mb-0">
                                            <?= $lang['d_cargo_additional_expenses_sub_title'] ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-2">
                                            <div class="mj-input-filter-box d-flex py-2">
                                                <input type="text" class="mj-input-filter mj-font-12 px-0"
                                                       id="title"
                                                       name="title"
                                                       placeholder="<?= $lang['d_cargo_additional_expenses_input_title_placeholder'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-2">
                                            <div class="mj-input-filter-box d-flex"
                                                 style="padding: 4px 16px 4px 4px">
                                                <input type="text" class="mj-input-filter mj-font-12 px-0"
                                                       id="price" name="price" inputmode="numeric"
                                                       placeholder="<?= $lang['d_cargo_additional_expenses_input_price_placeholder'] ?>">
                                                <select class="mj-custom-form-select" id="currency-unit"
                                                        name="currency-unit">
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
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <input type="hidden" id="token" name="token"
                                                   value="<?= Security::initCSRF('extra-expenses-in') ?>">
                                            <button type="button" class="mj-btn mj-btn-primary mj-fw-400 py-2 w-100"
                                                    id="submit-expenses" name="submit-expenses"
                                                    data-cargo="<?= $cargo->CargoId ?>"
                                                    data-request="<?= $request->RequestId ?>"
                                                    style="min-height: unset">
                                                <img src="/dist/images/icons/circle-plus.svg" class="me-1"
                                                     alt="submit">
                                                <?= $lang['d_cargo_additional_expenses_button'] ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <p class="mj-text-danger mj-fw-300 mj-font-11">
                                    <?= $lang['d_cargo_additional_expenses_alert'] ?>
                                </p>

                                <div class="table-responsive">
                                    <table class="table mj-table mj-table-stripped mj-table-row-number">
                                        <tbody>
                                        <?php
                                        $expenses = Driver::getExtraExpensesInList($request->RequestId);
                                        foreach ($expenses->response as $key => $item) {
                                            ?>
                                            <tr>
                                                <td><?= $key + 1 ?></td>
                                                <td><?= $item->ExpenseName ?></td>
                                                <td>
                                                    <?= number_format($item->ExpenseAmount) ?>
                                                    <?= $item->ExpenseCurrency ?>
                                                </td>
                                                <td>
                                                    <span class="mj-badge <?php
                                                    if ($item->ExpenseStatus == 'pending') {
                                                        echo 'mj-badge-warning';
                                                    } elseif ($item->ExpenseStatus == 'accepted') {
                                                        echo 'mj-badge-success';
                                                    } elseif ($item->ExpenseStatus == 'rejected') {
                                                        echo 'mj-badge-danger';
                                                    }
                                                    ?>">
                                                        <?php
                                                        if ($item->ExpenseStatus == 'pending') {
                                                            echo $lang['d_status_additional_expenses_pending'];
                                                        } elseif ($item->ExpenseStatus == 'accepted') {
                                                            echo $lang['d_status_additional_expenses_accepted'];
                                                        } elseif ($item->ExpenseStatus == 'rejected') {
                                                            echo $lang['d_status_additional_expenses_rejected'];
                                                        }
                                                        ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php
                    }

                    if (!empty($request) && $request->RequestStatus == 'completed') {
                        ?>
                        <div class="mj-d-cargo-card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="/dist/images/icons/folder-image.svg" class="mj-d-icon-box me-2"
                                         alt="additional-images-and-expenses">
                                    <span class="mj-d-icon-title"><?= $lang['d_cargo_additional_images_and_expenses'] ?></span>
                                </div>

                                <div class="mj-d-image-slider">
                                    <?php
                                    foreach ($request->RequestImages as $image) {
                                        ?>
                                        <div class="mj-d-image-slider-box">
                                            <img src="<?= $image ?>" alt="/<?= $image ?>">
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>

                                <div class="row">
                                    <?php
                                    $expenses = Driver::getExtraExpensesInList($request->RequestId);
                                    foreach ($expenses->response as $item) {
                                        if ($item->ExpenseStatus == 'accepted') {
                                            ?>
                                            <div class="cargo-detail col-6">
                                                <div class="mb-2">
                                                    <div class="mj-d-cargo-item-title"><?= $item->ExpenseName ?>:
                                                    </div>
                                                    <div class="mj-d-cargo-item-value">
                                                        <?= number_format($item->ExpenseAmount) ?>
                                                        <?= $item->ExpenseCurrency ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <!-- start share btn -->
                    <div class="mj-d-cargo-card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="/dist/images/icons/share.svg" class="mj-d-icon-box me-2"
                                     alt="route-on-the-map">
                                <span class="mj-d-icon-title"><?= $lang['share_cargo'] ?></span>
                            </div>
                            <div class="mj-share-links-cargo">
                                <a href="javascript:void(0)" class="mj-btn mj-d-btn-call me-2"
                                   style="flex: 0 0 auto; min-height: 34px;" id="share">
                                    <img src="/dist/images/icons/share-white.svg" class="me-1" alt="share">
                                    <?= $lang['share_cargo'] ?> </a>

                                <a href="whatsapp://send?text=<?=  $cargo->CargoName . $Settings['share_text']; ?>%0A<?= SITE_URL; ?>/cargo-in-ads/<?= $_REQUEST['id'] ?>"
                                   class="mj-btn mj-d-btn-whatsapp me-2"
                                   style="flex: 0 0 auto; min-height: 34px;"
                                   target="_blank">
                                    <img src="/dist/images/icons/whatsapp.svg" class="me-1" alt="whatsapp">
                                    <?= $lang['share_cargo_whatsapp'] ?></a>

                                <a href="https://t.me/share/url?url=<?= SITE_URL; ?>/cargo-in-ads/<?= $_REQUEST['id'] ?>&text=<?=  $cargo->CargoName . $Settings['share_text']; ?>"
                                   class="mj-btn mj-d-btn-telegram me-2"
                                   target="_blank"
                                   style="flex: 0 0 auto; min-height: 34px;">
                                    <img src="/dist/images/icons/telegram.svg" class="me-1" alt="telegram">
                                    <?= $lang['share_cargo_telegram'] ?> </a>
                            </div>
                        </div>
                    </div>
                    <!-- end share btn -->
                </div>
            </div>
        </main>
        <a id="share-data" href="javascript:void(0);" class="d-none" data-link="<?= SITE_URL; ?>/cargo-in/<?= $_REQUEST['id'] ?>"
           data-title="<?=  $cargo->CargoName ?>" data-share-setting="<?= $Settings['share_text'] ?>"
           data-source="<?= $cargo->CargoOriginCountry->CountryName." - ".$sourceOutput ?>" data-dest="<?= $destOutput ?>"
           data-weight="<?= $cargo->CargoWeight ?> <?= $lang['d_cargo_weight_unit'] ?>"></a>
        <?php
    } else {
        header('location: /driver');
    }
    getFooter('', false);
} else {
    Router::trigger404();
}

