<?php

global $lang, $Settings;

use MJ\Router\Router;
use MJ\Security\Security;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    User::checkUserSlugAccess();
    $user = User::getUserInfo();
    $cargo = Businessman::getCargoInDetail($user->UserId, $_REQUEST['id']);
    if ($cargo->status == 200) {
        $cargo = $cargo->response;
        $requests = Businessman::getCargoInDrivers($user->UserId, $cargo->CargoId);

        include_once 'header-footer.php';

        enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
        enqueueStylesheet('ol-css', '/dist/libs/ol/ol.css'); //
        enqueueScript('ol-js', '/dist/libs/ol/dist/ol.js');//
        enqueueScript('map-js', '/map/assets/index.4eb0d7de.js');

        enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
        enqueueScript('elm', '/dist/libs/ele-pep/elem.js');
        enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
        enqueueScript('cargo-in-detail-js', '/dist/js/businessman/cargo-in-detail.js');

        getHeader($lang['b_title_cargo_in_detail']);

        $destOutput =  $cargo->CargoDestinationCity ;
        $sourceOutput = $cargo->CargoOriginCity;
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
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
        <main style="padding-bottom: 70px !important;">
            <div class="container">

                <?php
                if ($cargo->CargoStatus == 'pending') {
                    ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="mj-alert mj-alert-with-icon mj-alert-warning mb-3">
                                <div class="mj-alert-icon">
                                    <img src="/dist/images/icons/circle-exclamation.svg" alt="exclamation">
                                </div>
                                <?= $lang['b_cargo_pending_request'] ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <a href="/businessman/edit-cargo-in/<?= $cargo->CargoId ?>" class="mj-btn-more w-100"
                               style="min-height: 42px">
                                <?= $lang['b_edit_cargo'] ?>
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <button type="button" class="mj-btn-2 mj-btn-danger w-100" style="min-height: 42px"
                                    data-bs-toggle="modal"
                                    data-bs-target="#cancel-request-modal">
                                <?= $lang['b_cancel_cargo'] ?>
                            </button>
                        </div>
                    </div>

                    <div class="modal fade" id="cancel-request-modal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <?= $lang['b_cancel_cargo'] ?>
                                    </h5>
                                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="reason"
                                                       class="text-dark mj-fw-500 mj-font-12 mb-1"><?= $lang['b_cancel_cargo_label'] ?>
                                                    :</label>
                                                <div class="mj-input-filter-box">
                                                <textarea class="mj-input-filter mj-fw-400 mj-font-12" id="reason"
                                                          name="reason" rows="4"
                                                          placeholder="<?= $lang['b_cancel_cargo_placeholder'] ?>"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="d-flex justify-content-center">
                                                <input type="hidden" id="token-cancel" name="token-cancel"
                                                       value="<?= Security::initCSRF('cancel-cargo-in') ?>">
                                                <button class="mj-btn-more mj-btn-cancel-yes px-4 me-1"
                                                        id="submit-cancel"
                                                        name="submit-cancel"
                                                        data-cargo="<?= $cargo->CargoId ?>">
                                                    <?= $lang['b_btn_yes'] ?>
                                                </button>
                                                <button class="mj-btn-more mj-btn-cancel px-4 ms-1"
                                                        data-bs-dismiss="modal">
                                                    <?= $lang['b_btn_close'] ?>
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

                if (in_array($cargo->CargoStatus, ['accepted', 'progress'])) {
                    ?>
                    <div class="row">
                        <?php
                        foreach ($requests->response as $request) {
                            ?>
                            <div class="col-12">
                                <div class="mj-info-box mj-b-info-box mb-3">

                                    <div>
                                        <h4 class="mj-fw-500 mj-font-13 mt-0 mb-3"><?= $lang['b_cargo_driver_info'] ?></h4>
                                        <div class="mj-fw-600 mj-font-13 mb-1"><?php echo $lang['d_cargo_owner_call']; // $lang['b_cargo_driver_info'] ?></div>
                                        <?php if ($cargo->CargoStatus != 'completed') { ?>
                                            <div class="text-start mj-fw-600 mj-font-13"
                                                 dir="ltr"><?php
                                                // $request->UserMobile
                                                echo Utils::getFileValue("settings.txt", 'support_call');
                                                ?></div>
                                        <?php } ?>
                                    </div>

                                    <?php
                                    if (Complaint::checkCanSendComplaintIn($cargo->CargoId, $request->RequestId, $user->UserId, $request->UserId)) {
                                        ?>
                                        <div class="d-flex align-items-center justify-content-between mj-b-info-btn-card">
                                            <a href="javascript:void(0)" class="w-100 mj-b-btn-1 text-white"
                                               data-cargo="<?= $cargo->CargoId ?>"
                                               data-request="<?= $request->RequestId ?>"
                                               data-driver="<?= $request->UserId ?>"
                                               onclick="submitComplaint(this);">
                                                <img src="/dist/images/icons/whatsapp.svg" class="me-1"
                                                     alt="complaint"/>
                                                <?= $lang['d_cargo_complaint'] ?>
                                            </a>
                                            <?php if ($cargo->CargoStatus != 'completed') { ?>
                                                <a href="tel:<?=Utils::getFileValue("settings.txt", 'support_call'); ?>"
                                                   class="w-100 mj-btn-2 mj-btn-info">
                                                    <img src="/dist/images/icons/circle-phone.svg" class="me-2"
                                                         alt="call">
                                                    <?= $lang['b_cargo_direct_call'] ?>
                                                </a>
                                            <?php } ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>


                    <?php
                    if (count($requests->response) < $cargo->CargoCarCount) {
                        ?>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <a href="/businessman/suggestions-in/<?= $cargo->CargoId ?>" class="mj-btn-more w-100"
                                   style="min-height: 42px">
                                    <?= $lang['b_see_all_request'] ?>
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                }

                if ($cargo->CargoStatus == 'completed') {
                    ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="mj-alert mj-alert-with-icon mj-alert-success mb-3">
                                <div class="mj-alert-icon">
                                    <img src="/dist/images/icons/circle-exclamation.svg" alt="exclamation">
                                </div>
                                <?= $lang['b_cargo_completed'] ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }

                ?>

                <div class="row">
                    <div class="col-12">
                        <div class="mj-b-cargo-card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="mj-b-cargo-item-category me-2"
                                         style="background: linear-gradient(180deg,  <?= $cargo->CategoryColor . 'b0' ?> ,<?= $cargo->CategoryColor ?>)">
                                        <img src="<?= Utils::fileExist($cargo->CategoryIcon, BOX_EMPTY); ?>"
                                             alt="">
                                        <span><?= $cargo->CategoryName ?></span>
                                    </div>
                                    <div class="flex-fill">
                                        <h2 class="mj-b-cargo-item-header mt-0 mb-2"><?= $cargo->CargoName ?></h2>
                                        <div class="mj-b-cargo-item-price-box d-flex align-items-center justify-content-between">
                                            <span><?= $lang['b_recommended_price'] ?></span>
                                            <span>
                                              <?php if ($cargo->CargoRecommendedPrice == 0) {
                                                  echo $lang['u_agreement'];
                                              } else {
                                                  echo number_format($cargo->CargoRecommendedPrice);
                                                  ?>
                                                  <small><?= $cargo->CargoMonetaryUnit ?></small>
                                              <?php } ?>
                                          </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-1">
                                            <img src="/dist/images/icons/house-signal-1.svg"
                                                 class="mj-b-cargo-item-icon me-2" alt="country"/>
                                            <div>
                                                <div class="mj-b-cargo-item-title"><?= $lang['country'] ?></div>
                                                <div class="mj-b-cargo-item-value"><?= $cargo->CargoOriginCountry->CountryName ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="/dist/images/icons/calendar-star.svg"
                                                 class="mj-b-cargo-item-icon me-2" alt="start-date"/>
                                            <div>
                                                <div class="mj-b-cargo-item-title"><?= $lang['b_cargo_start_date'] ?></div>
                                                <div class="mj-b-cargo-item-value"><?= ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('Y/m/d', $cargo->CargoStartTransportation) : date('Y-m-d', $cargo->CargoStartTransportation) ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="/dist/images/icons/arrow-up-left-from-circle.svg"
                                                 class="mj-b-cargo-item-icon me-2" alt="origin"/>
                                            <div>
                                                <div class="mj-b-cargo-item-title"><?= $lang['b_cargo_source_city'] ?></div>
                                                <div class="mj-b-cargo-item-value"><?= $sourceOutput ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="/dist/images/icons/weight-scale.svg"
                                                 class="mj-b-cargo-item-icon me-2" alt="weight"/>
                                            <div>
                                                <div class="mj-b-cargo-item-title"><?= $lang['b_details_cargo_weight'] ?></div>
                                                <div class="mj-b-cargo-item-value"><?= $cargo->CargoWeight ?> <?= $lang['d_cargo_weight_unit'] ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="/dist/images/icons/arrow-down-left-from-circle.svg"
                                                 class="mj-b-cargo-item-icon me-2" alt="destination"/>
                                            <div>
                                                <div class="mj-b-cargo-item-title"><?= $lang['b_cargo_dest_city'] ?></div>
                                                <div class="mj-b-cargo-item-value"><?= $destOutput ?></div>
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
                                            <img src="/dist/images/icons/maximize.svg"
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
                                                 class="mj-b-cargo-item-icon me-2" alt="car-count"/>
                                            <div class="w-100">
                                                <div class="mj-b-cargo-item-title"><?= $lang['b_cargo_car_needed'] ?>
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

                        <div class="mj-b-cargo-card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <img src="/dist/images/icons/folder-image.svg" class="mj-b-icon-box me-2"
                                             alt="gallery">
                                        <span class="mj-b-icon-title"><?= $lang['b_details_cargo_gallery'] ?></span>
                                    </div>

                                    <div class="d-flex flex-row-reverse">
                                        <?php
                                        if ($cargo->CargoImages) {
                                            foreach ($cargo->CargoImages as $key => $image) {
                                                if ($key == 3) {
                                                    break;
                                                }

                                                if ($key != 2) {
                                                    ?>
                                                    <a href="#gallery-modal" data-bs-toggle="modal"
                                                       data-gallery>
                                                        <img src="<?= Utils::fileExist($image, BOX_EMPTY); ?>"
                                                             class="mj-b-cargo-img ms-2"
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
                                                             class="me-1" alt="">
                                                        <?= $lang['b_cargo_exit'] ?>
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
                        if (!empty($cargo->CargoDescription)) {
                            ?>
                            <div class="mj-b-cargo-card mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/align-center.svg" class="mj-b-icon-box me-2"
                                             alt="description">
                                        <span class="mj-b-icon-title"><?= $lang['b_details_cargo_desc'] ?></span>
                                    </div>

                                    <div class="mj-b-cargo-item-desc">
                                        <?= $cargo->CargoDescription ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
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

                                <div class=" mj-support-links-cargo">
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
                                        <img src="/dist/images/icons/circle-envelope.svg" class="me-1" alt="ticket"/>
                                        <?= $lang['d_cargo_ticket'] ?>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!--           MAP             -->
                        <div class="mj-b-cargo-card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="/dist/images/icons/location-dot.svg" class="mj-b-icon-box me-2" alt="map">
                                    <span class="mj-b-icon-title"><?= $lang['b_cargo_display_on_map'] ?></span>
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
                        <!--           MAP             -->

                        <?php
                        if (!empty($requests->response)) {
                            ?>
                            <input type="hidden" id="token-expenses" name="token-expenses"
                                   value="<?= Security::initCSRF('expenses-in') ?>">
                            <input type="hidden" id="token-rate" name="token-rate"
                                   value="<?= Security::initCSRF('rate-in') ?>">
                            <input type="hidden" id="token-complaint" name="token-complaint"
                                   value="<?= Security::initCSRF('submit-complaint-in') ?>">
                            <?php
                        }

                        foreach ($requests->response as $request) {
                            ?>
                            <div class="mj-b-cargo-card mb-3">
                                <div class="card-body">

                                    <div class="d-flex align-items-center justify-content-between my-2 p-3"
                                         style="background: #e0eeff;border-radius: 11px;">
                                        <span><?= $lang['u_agreement_price'] ?> </span>
                                        <span class="mj-b-icon-title">  <?= number_format($request->RequestPrice) . ' ' . $request->CurrencyName ?></span>
                                    </div>

                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/circle-dollar.svg" class="mj-b-icon-box me-2"
                                             alt="extra-expenses">
                                        <div>
                                            <span class="mj-b-icon-title"><?= $lang['b_cargo_extra_cost'] ?>
<!--                                                --><?php //= $request->UserDisplayName ?>
                                            </span>
                                            <br>

                                            <p class="mj-b-cargo-item-desc mb-0">
                                                <?= $lang['b_cargo_extra_cost_desc'] ?>
                                            </p>

                                        </div>
                                    </div>

                                    <p class="mj-text-danger mj-fw-300 mj-font-11">
                                        <?= $lang['b_cargo_extra_cost_problem_desc'] ?>
                                    </p>

                                    <div class="table-responsive mj-b-table-responsive">
                                        <table class="table mj-table mj-table-stripped mj-table-row-number">
                                            <tbody>
                                            <?php
                                            $expenses = Businessman::getRequestExtraExpensesIn($request->RequestId);
                                            foreach ($expenses->response as $key => $expense) {
                                                ?>
                                                <tr class="align-middle">
                                                    <td><?= $key + 1 ?></td>
                                                    <td><?= $expense->ExpenseName ?></td>
                                                    <td><?= number_format($expense->ExpensePrice) ?> <?= $expense->CurrencyName ?></td>
                                                    <td>
                                                        <?php
                                                        if ($expense->ExpenseStatus == 'pending') {
                                                            ?>
                                                            <button class="mj-b-approve-btn me-1" type="button"
                                                                    data-expense="<?= $expense->ExpenseId ?>"
                                                                    data-request="<?= $request->RequestId ?>"
                                                                    data-cargo="<?= $cargo->CargoId ?>"
                                                                    data-driver="<?= $request->UserId ?>"
                                                                    data-status="accepted"
                                                                    data-btn-expenses>
                                                                <?= $lang['b_accept'] ?>
                                                            </button>
                                                            <button class="mj-b-reject-btn ms-1" type="button"
                                                                    data-expense="<?= $expense->ExpenseId ?>"
                                                                    data-request="<?= $request->RequestId ?>"
                                                                    data-cargo="<?= $cargo->CargoId ?>"
                                                                    data-driver="<?= $request->UserId ?>"
                                                                    data-status="rejected"
                                                                    data-btn-expenses>
                                                                <?= $lang['b_reject'] ?>
                                                            </button>
                                                            <?php
                                                        } elseif ($expense->ExpenseStatus == 'accepted') {
                                                            ?>
                                                            <span class="mj-badge mj-badge-success"><?= $lang['accepted'] ?></span>
                                                            <?php
                                                        } elseif ($expense->ExpenseStatus == 'rejected') {
                                                            ?>
                                                            <span class="mj-badge mj-badge-danger"><?= $lang['rejected'] ?></span>
                                                            <?php
                                                        }
                                                        ?>
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


                        foreach ($requests->response as $request) {
                            ?>
                            <div class="mj-b-cargo-card mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/folder-image.svg" class="mj-b-icon-box me-2"
                                             alt="receipt-images">
                                        <span class="mj-b-icon-title"><?= $lang['b_additional_images'] ?>
<!--                                            --><?php //= $request->UserDisplayName ?>
                                        </span>
                                    </div>

                                    <div class="mj-b-image-slider">
                                        <?php
                                        if (!empty($request->RequestReceipt)) {
                                            ?>
                                            <div class="mj-b-image-slider-box" data-bs-toggle="modal"
                                                 data-bs-target="#modal-images-<?= $request->RequestId ?>">
                                                <img src="<?= Utils::fileExist($request->RequestReceipt, BOX_EMPTY) ?>"
                                                     alt="<?= $request->RequestReceipt ?>">
                                            </div>
                                            <?php
                                        }

                                        if (!empty($request->RequestImages)) {
                                            foreach ($request->RequestImages as $image) {
                                                ?>
                                                <div class="mj-b-image-slider-box" data-bs-toggle="modal"
                                                     data-bs-target="#modal-images-<?= $request->RequestId ?>">
                                                    <img src="<?= Utils::fileExist($image, BOX_EMPTY) ?>"
                                                         alt="<?= $image ?>">
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>

                                    <div class="row">
                                        <?php
                                        $expenses = Businessman::getRequestExtraExpenses($request->RequestId);
                                        foreach ($expenses->response as $expense) {
                                            if ($expense->ExpenseStatus == 'accepted') {
                                                ?>
                                                <div class="cargo-detail col-6">
                                                    <div class="mb-2">
                                                        <div class="mj-b-cargo-item-title"><?= $expense->ExpenseName ?></div>
                                                        <div class="mj-b-cargo-item-value"><?= number_format($expense->ExpensePrice) ?> <?= $expense->CurrencyName ?></div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="modal-images-<?= $request->RequestId ?>" role="dialog">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><?= $lang['b_additional_images'] ?>
<!--                                                --><?php //= $request->UserDisplayName ?>
                                            </h5>
                                            <button type="button" class="btn-close shadow-none"
                                                    data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="swiper" id="swiper-<?= $request->RequestId ?>" data-swiper
                                                 dir="ltr">
                                                <div class="swiper-wrapper">
                                                    <?php
                                                    if (!empty($request->RequestReceipt)) {
                                                        ?>
                                                        <div class="swiper-slide mj-d-cargo-gallery-img-box">
                                                            <img src="<?= Utils::fileExist($request->RequestReceipt, BOX_EMPTY) ?>"
                                                                 class="mj-d-cargo-gallery-img"
                                                                 alt="<?= $request->RequestReceipt ?>"/>
                                                        </div>
                                                        <?php
                                                    }

                                                    if (!empty($request->RequestImages)) {
                                                        foreach ($request->RequestImages as $image) {
                                                            ?>
                                                            <div class="swiper-slide mj-d-cargo-gallery-img-box">
                                                                <img src="<?= Utils::fileExist($image, BOX_EMPTY) ?>"
                                                                     class="mj-d-cargo-gallery-img"
                                                                     alt="<?= $image ?>"/>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <div class="swiper-pagination"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }


                        foreach ($requests->response as $request) {
                            if (empty($request->RequestRate)) {
                                ?>
                                <div class="mj-b-cargo-card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="/dist/images/icons/photo-film.svg" class="mj-d-icon-box me-2"
                                                 alt="">
                                            <div>
                                                <span class="mj-d-icon-title"><?= $lang['b_rating'] ?>
<!--                                                    --><?php //= $request->UserDisplayName ?>
                                                </span>
                                                <p class="mj-d-cargo-item-desc mb-0">
                                                    <?= $lang['b_rating_driver'] ?>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mj-rating" data-request="<?= $request->RequestId ?>"
                                             data-cargo="<?= $cargo->CargoId ?>">
                                            <input type="radio" id="star-5-<?= $request->RequestId ?>"
                                                   name="rating-<?= $request->RequestId ?>" value="5" data-rate>
                                            <label for="star-5-<?= $request->RequestId ?>"
                                                   title="<?= $lang['five_star'] ?>"><?= $lang['five_star'] ?></label>
                                            <input type="radio" id="star-4-<?= $request->RequestId ?>"
                                                   name="rating-<?= $request->RequestId ?>" value="4" data-rate>
                                            <label for="star-4-<?= $request->RequestId ?>"
                                                   title="<?= $lang['four_star'] ?>"><?= $lang['four_star'] ?></label>
                                            <input type="radio" id="star-3-<?= $request->RequestId ?>"
                                                   name="rating-<?= $request->RequestId ?>" value="3" data-rate>
                                            <label for="star-3-<?= $request->RequestId ?>"
                                                   title="<?= $lang['three_star'] ?>"><?= $lang['three_star'] ?></label>
                                            <input type="radio" id="star-2-<?= $request->RequestId ?>"
                                                   name="rating-<?= $request->RequestId ?>" value="2" data-rate>
                                            <label for="star-2-<?= $request->RequestId ?>"
                                                   title="<?= $lang['two_star'] ?>"><?= $lang['two_star'] ?></label>
                                            <input type="radio" id="star-1-<?= $request->RequestId ?>"
                                                   name="rating-<?= $request->RequestId ?>" value="1" data-rate>
                                            <label for="star-1-<?= $request->RequestId ?>"
                                                   title="<?= $lang['one_star'] ?>"><?= $lang['one_star'] ?></label>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
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

                                    <a href="whatsapp://send?text=<?= $cargo->$slugname . $Settings['share_text']; ?>%0A<?= SITE_URL; ?>/cargo-ads/<?= $_REQUEST['id'] ?>"
                                       class="mj-btn mj-d-btn-whatsapp me-2"
                                       target="_blank"
                                       style="flex: 0 0 auto; min-height: 34px;">
                                        <img src="/dist/images/icons/whatsapp.svg" class="me-1" alt="whatsapp">
                                        <?= $lang['share_cargo_whatsapp'] ?></a>

                                    <a href="https://t.me/share/url?url=<?= SITE_URL; ?>/cargo-ads/<?= $_REQUEST['id'] ?>&text=<?= $cargo->$slugname . $Settings['share_text']; ?>"
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
            </div>
        </main>
        <a id="share-data" href="#" class="d-none" data-link="<?= SITE_URL; ?>/cargo-ads/<?= $_REQUEST['id'] ?>"
           data-title="<?= $cargo->$slugname ?>" data-share-setting="<?= $Settings['share_text'] ?>"
           data-source="<?= $sourceOutput ?>" data-dest="<?= $destOutput ?>"
           data-weight="<?= $cargo->CargoWeight ?> <?= $lang['d_cargo_weight_unit'] ?>"></a>

        <?php
        getFooter('', false);

    } else {
        Router::trigger404();
    }
} else {
    header('location: /login');
}