<?php

global $lang, $Settings, $Tour;

use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    User::checkUserSlugAccess();
    $user = User::getUserInfo();
//    User::checkBalanceIsGenerated($user->UserId);

    include_once 'header-footer.php';

    enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
    enqueueStylesheet('fontawesome-css', '/dist/libs/fontawesome/all.css');

    enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
    enqueueScript('fontawesome-js', '/dist/libs/fontawesome/all.min.js');
    enqueueScript('slider-js', '/dist/js/user/slider.js');
/*
    if (!isset($_COOKIE['t-b-dashboard']) || $_COOKIE['t-b-dashboard'] != 'shown') {
        enqueueStylesheet('shepherd-css', '/dist/libs/shepherd/shepherd.css');
        enqueueScript('shepherd-js', '/dist/libs/shepherd/shepherd.js');
        enqueueScript('toured-dashboard', '/dist/js/businessman/toured-dashboard.init.js');
        */?><!--
        <script type="text/javascript">
            let tour_vars = <?php /*= json_encode($Tour) */?>;
        </script>
        --><?php
/*    }*/
    getHeader($lang['b_dashboard_businessman']);

    $sliders = $Settings['b_home_silders'];
    ?>
    <main class="container">
        <section class="mj-b-dashboard-items-section">
            <div class="mj-b-dashboard-items-box">

            </div>
        </section>

        <section>
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-6 mj-b-dashboard-btns">
                        <div class="mj-b-add-cargo" id="tj-b-dashboard-2">
                            <a href="/businessman/add-cargo">
                                <img src="/dist/images/icons/circle-plus.svg" alt="">
                                <span><?= $lang['b_add_cargo_1'] ?></span>
                            </a>
                        </div>
                    </div>
                    <div class="col-6 mj-b-dashboard-btns">
                        <div class="mj-b-add-cargo" id="tj-b-dashboard-22">
                            <a href="/businessman/add-cargo-in">
                                <img src="/dist/images/icons/circle-plus.svg" alt="">
                                <span><?= $lang['b_add_cargo_in_1'] ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class=" container-fluid mj-b-slider-section my-5"
                 style="height: 200px;margin-bottom: 26px !important;">
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

        <section class="mj-b-dasboard-reports mt-3" id="tj-b-dashboard-4">
            <div class="container">
                <div class="row">
                    <div class="col-6 col-lg-6 mj-b-reports-cols">
                        <a href="/businessman/my-cargoes/out">
                            <div id="report-1" class="mj-b-report-card">
                                <div class="mj-b-report-content">
                                <span>
                                    <?= $lang['a_cargos_out'] ?>
                                </span>
                                    <span>
                                    <?= Businessman::getCargoOutCount($user->UserId) ?>
                                </span>
                                    <img src="/dist/images/intercargo.svg" alt="completed">
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-6 col-lg-6 mj-b-reports-cols">
                        <a href="/businessman/my-cargoes/in">
                            <div id="report-2" class="mj-b-report-card">
                                <div class="mj-b-report-content">
                                <span>
                                   <?= $lang['cargoes_in'] ?>
                                </span>
                                    <span>
                                    <?= Businessman::getCargoInCount($user->UserId) ?>
                                </span>
                                    <img src="/dist/images/innercargo.svg" alt="progress">
                                </div>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <div class="mj-dashboard-video-modal modal fade" id="exampleModal" tabindex="-1"
             aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <div class="fa-close"></div>
                        </button>
                    </div>
                    <div class="modal-body">
                        <video controls>
                            <source src="<?= $Settings['b_learning']['link'] ?>" type="video/mp4">
                        </video>
                    </div>

                </div>
            </div>
        </div>


        <section class="mj-b-video-section" id="tj-b-dashboard-3">
            <div class="mj-b-video-card">
                <div class="mj-b-video-content">
                    <div class="mj-b-video-texts">
                        <h3><?= $Settings['b_learning']['title'] ?></h3>
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
    getFooter('', false, true);
} else {
    header('location: /login');
}
?>

