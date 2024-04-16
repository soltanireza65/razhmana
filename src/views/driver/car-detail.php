<?php

global $lang;

use MJ\Router\Router;
use MJ\Security\Security;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    User::checkUserSlugAccess();
    $user = User::getUserInfo();
    $car = Driver::getCarDetail($_REQUEST['id'], $user->UserId);
    if ($car->status == 200) {
        $car = $car->response;
        include_once 'header-footer.php';

        enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');

        enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
        enqueueScript('car-detail-init', '/dist/js/driver/car-detail.init.js');

        getHeader($lang['d_car_detail_title']);

        ?>
        <main class="container" style="padding-bottom: 180px">
            <?php
            if ($car->CarStatus != 'rejected') {
                ?>
                <div class="row">
                    <div class="col-12 mb-3">
                        <button type="button" class="mj-btn-more w-100" data-bs-toggle="modal"
                                data-bs-target="#delete-car-modal" style="min-height: 42px">
                            <?= $lang['d_delete_car'] ?>
                        </button>
                        <div class="modal fade" id="delete-car-modal" data-bs-backdrop="static"
                             data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-sm" style="border-radius: 10px;">
                                    <div class="modal-body">
                                        <div class="text-center">
                                            <i class="fe-info d-block text-info mb-3" style="font-size: 72px;"></i>
                                            <h4 class="mj-fw-600 mj-font-14 mt-0 mb-4">
                                                <?= $lang['d_inactivate_car_message'] ?>
                                            </h4>

                                            <input type="hidden" id="token" name="token"
                                                   value="<?= Security::initCSRF('delete-car') ?>">
                                            <button class="mj-btn-primary py-1 px-3 rounded-3 mj-fw-400 mj-font-12"
                                                    id="delete-car" name="delete-car"
                                                    data-car="<?= $car->CarId ?>">
                                                <?= $lang['d_btn_yes'] ?>
                                            </button>
                                            <button class="mj-btn-outline-primary py-1 px-3 rounded-3 mj-fw-400 mj-font-12"
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
            ?>

            <div class="row">
                <div class="col-12">
                    <div class="mj-d-cargo-card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="car-detail col-12">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/arrow-up-left-from-circle.svg"
                                             class="mj-d-cargo-item-icon me-2" alt="origin"/>
                                        <div>
                                            <div class="mj-d-cargo-item-value"><?= $car->CarName ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="car-detail col-12">
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mj-input-filter-box"
                                             style="min-height: 42px; padding: 4px 12px;">
                                            <div class="d-flex align-items-center justify-content-around flex-row-reverse w-100"
                                                 id="format-plaque">
                                                    <div class="mj-fw-700 mj-font-13"><?= $car->CarPlaque ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="car-detail col-12">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="/dist/images/icons/house-signal.svg"
                                             class="mj-d-cargo-item-icon me-2" alt="customs-of-origin"/>
                                        <div>
                                            <div class="mj-d-cargo-item-value"><?= $car->CarType ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if (!empty($car->CarImages)) {
                                ?>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <img src="/dist/images/icons/folder-image.svg" class="mj-d-icon-box me-2"
                                             alt="gallery">
                                        <span class="mj-d-icon-title"><?= $lang['d_cargo_gallery'] ?></span>
                                    </div>

                                    <div class="d-flex flex-row-reverse">
                                        <?php
                                        if (!empty($car->CarImages)) {
                                            foreach ($car->CarImages as $key => $image) {
                                                if ($key == 3) {
                                                    break;
                                                }
                                                if ($key != 2) {
                                                    ?>
                                                    <img src="<?= Utils::fileExist($image, BOX_EMPTY) ?>"
                                                         class="mj-d-cargo-img ms-2"
                                                         alt="<?= $image ?>">
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
                                            echo $lang['no_image'];
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
                                                            foreach ($car->CarImages as $image) {
                                                                ?>
                                                                <div class="swiper-slide mj-d-cargo-gallery-img-box">
                                                                    <img src="<?= Utils::fileExist($image, BOX_EMPTY) ?>"
                                                                         class="mj-d-cargo-gallery-img"
                                                                         alt="<?= $image ?>"/>
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
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php
        getFooter('', false);
    } else {
        Router::trigger404();
    }
} else {
    header('location: /login');
}