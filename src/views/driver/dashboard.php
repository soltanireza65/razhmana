<?php

global $lang, $Settings, $Tour;

use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    User::checkUserSlugAccess();
    $user = User::getUserInfo();

    include_once 'views/user/header-footer.php';

    enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');

    enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
    enqueueScript('slider-js', '/dist/js/user/slider.js');
    enqueueScript('dashboard-js', '/dist/js/driver/dashboard-driver.js');

/*    if (!isset($_COOKIE['t-d-dashboard']) || $_COOKIE['t-d-dashboard'] != 'shown') {
        enqueueStylesheet('shepherd-css', '/dist/libs/shepherd/shepherd.css');
        enqueueScript('shepherd-js', '/dist/libs/shepherd/shepherd.js');
        enqueueScript('toured-dashboard', '/dist/js/driver/toured-dashboard.init.js');
        */?><!--
        <script type="text/javascript">
            let tour_vars = <?php /*= json_encode($Tour) */?>;
        </script>
        --><?php
/*    }*/
    $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
    $car_types = Car::getAllCarsTypes('active');
    if ($car_types->status == 200) {
        $car_types = $car_types->response;
    } else {
        $car_types = [];
    }

    $cargoInfo = null;
    $cargoProgress = Driver::getMyCargoProgress($user->UserId);
    if ($cargoProgress->status == 200) {
        if (isset($cargoProgress->response[0]) && $cargoProgress->response[0]->Xtype == "out") {
            $cargoId = $cargoProgress->response[0]->cargo_id;
            $cargoType = "out";
            if(Driver::getCargoDetail($cargoProgress->response[0]->cargo_id)->status==200){
                $cargoInfo =  Driver::getCargoDetail($cargoProgress->response[0]->cargo_id)->response;
            }
        } elseif (isset($cargoProgress->response[0]) && $cargoProgress->response[0]->Xtype == "in") {
            $cargoId = $cargoProgress->response[0]->cargo_id;
            $cargoType = "in";
            if(Driver::getCargoInDetail($cargoProgress->response[0]->cargo_id)->status==200){
                $cargoInfo =  Driver::getCargoInDetail($cargoProgress->response[0]->cargo_id)->response;
            }
        }
    }

    $sliders = $Settings['d_poster_silders'];
    $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
    $slugname = 'CargoName_' . $language;
    getHeader($lang['d_dashboard_driver']);
    ?>
    <main class="container" style="padding-bottom: 180px;">
        <div class="row justify-content-center ">
            <div class="col-6 mj-b-dashboard-btns" style="padding-block: 10px !important     ">
                <div class="d-flex justify-content-around" id="tj-d-dashboard-1">
                    <a class="mj-driver-cargo" href="/cargo-ads">
                        <div class="mj-driver-dashboard-cargo-list-link">

                            <span><?= $lang['u_driver_out_available_cargo'] ?></span>
                        </div>
                    </a>
                </div>
                <div class="fa-boxes mj-driver-btn-icon"></div>
            </div>
            <div class="col-6 mj-b-dashboard-btns" style="padding-block: 10px !important    ">
                <div class="d-flex justify-content-around mj-driver-dashboard-btn" id="tj-d-dashboard-2">
                    <a class="mj-driver-cargo" href="/cargo-in-ads">
                        <div class="mj-driver-dashboard-cargo-list-link">

                            <span><?= $lang['u_driver_in_available_cargo'] ?></span>
                        </div>
                    </a>
                </div>
                <div class="fa-boxes mj-driver-btn-icon"></div>
            </div>
                <?php if (!is_null($cargoInfo)) { ?>
                <div class="col-12 mj-mycargo-col mt-2">
                    <div class="mj-mycargo-driver-content">
                        <a class="mj-driver-mycargo" href="<?php
                        if ($cargoType == "out") {
                            echo "/driver/cargo/" . $cargoId;
                        } elseif ($cargoType == "in") {
                            echo "/driver/cargo-in/" . $cargoId;
                        } else {
                            echo "javascript:void(0);";
                        }
                        ?>">
                            <div class="truck">
                                <div class="truck-container"></div>
                                <div class="glases"></div>
                                <div class="bonet"></div>

                                <div class="base"></div>

                                <div class="base-aux"></div>
                                <div class="wheel-back"></div>
                                <div class="wheel-front"></div>
                                <div class="smoke"></div>
                            </div>
                            <div class="mj-driver-mycargo-title d-flex">
                                <div class="fa-box"></div>
                                <span class="ms-1"><?=$cargoInfo->CargoName; ?></span>
                            </div>
                            <div class="mj-driver-mycargo-id" style="padding-right: 15px;text-align: start;">
                                <?=$cargoInfo->CargoId; ?>
                            </div>
                            <div class="mj-driver-mycargo-road d-flex">
                                <div></div>
                            </div>
                            <div class="mj-driver-mycargo-destination d-flex">
                                <div><?=$cargoInfo->CargoOriginCity.' - '.$cargoInfo->CargoOriginCountry->CountryName; ?></div>
                                <div><?=$cargoInfo->CargoDestinationCity.' - '.$cargoInfo->CargoDestinationCountry->CountryName;; ?></div>
                            </div>
                        </a>
                    </div>
                    <div class="mj-disable-btn-alert ">
                        <?= $lang['u_no_active_cargo']; ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <section class=" container-fluid mj-b-slider-section my-5" style="height: 200px">
            <div dir="rtl" class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php foreach ($sliders as $slider) { ?>
                        <div class="swiper-slide">
                            <a href="<?= $slider['url'] ?>">
                                <div class="mj-b-slide-card">
                                    <img src="<?= Utils::fileExist($slider['image'], POSTER_DEFAULT); ?>"
                                         alt="<?= $slider['alt'] ?>">
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
        <section class="mj-b-dasboard-reports mt-3">
            <div class="container">
                <div class="row">
                    <div class="col-6 mj-b-reports-cols" id="tj-d-dashboard-3">
                        <a href="/driver/my-requests">
                            <div id="report-1" class="mj-b-report-card">
                                <div class="mj-b-report-content">
                                <span>
                                    <?= $lang['a_my_request_and_cargos'] ?>
                                </span>
                                    <span>
                                    <?= Driver::getRequestCountByStatus($user->UserId) ?>
                                </span>
                                    <img src="/dist/images/icons/hand-wave.svg" alt="completed">
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 mj-b-reports-cols" id="tj-d-dashboard-44">
                        <a href="/driver/my-cars">
                            <div id="report-2" class="mj-b-report-card">
                                <div class="mj-b-report-content">
                                <span>
                                   <?= $lang['d_header_my_cars'] ?>
                                </span>
                                    <span>
                                    <?= Driver::getMyCarsCount($user->UserId) ?>
                                </span>
                                    <img src="/dist/images/icons/truck-container(white).svg" alt="progress">
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <div class="mj-dashboard-video-modal modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <div class="fa-close"></div>
                        </button>
                    </div>
                    <div class="modal-body">
                        <video controls>
                            <source src="<?= $Settings['d_learning']['link'] ?>" type="video/mp4">
                        </video>
                    </div>

                </div>
            </div>
        </div>
        <section class="mj-b-video-section" id="tj-b-dashboard-3">
            <div class="mj-b-video-card">
                <div class="mj-b-video-content">
                    <div class="mj-b-video-texts">
                        <h3><?= $Settings['d_learning']['title'] ?></h3>
                    </div>
                    <div class="mj-b-video-thumb">
                        <div class="mj-b-video-cover">
                            <a data-bs-toggle="modal" data-bs-target="#exampleModal" href="javascript:void(0)">
                                <img style="opacity: 0.5" src="/dist/images/icons/circle-play.svg" alt="">
                                <img class="mj-dashboard-video-cover" src="/dist/images/academy/logo.png" alt="">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php
    getFooter('', true, false);
} else {
    header('location: /login');
}