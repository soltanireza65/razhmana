<?php

global $lang, $Settings, $Tour;

use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();


    include_once getcwd() . '/views/user/header-footer.php';

    enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
    enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
    enqueueScript('slider-js', '/dist/js/user/slider.js');
/*    if (!isset($_COOKIE['t-inquiry']) || $_COOKIE['t-inquiry'] != 'shown') {
        enqueueStylesheet('shepherd-css', '/dist/libs/shepherd/shepherd.css');
        enqueueScript('shepherd-js', '/dist/libs/shepherd/shepherd.js');
        enqueueScript('toured-dashboard', '/dist/js/user/toured-inquiry.init.js');
        */?><!--
        <script type="text/javascript">
            let tour_vars = <?php /*= json_encode($Tour) */?>;
        </script>
        --><?php
/*    }*/
    getHeader($lang['b_minicargo_services']);

    $sliders = $Settings['u_user_sliders_minicargo'];
    ?>
    <main class="container">
        <style>
            .mj-backbtn{
                display: none !important;
            }
        </style>
        <section>
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-6 mj-b-dashboard-btns">
                        <div class="mj-b-add-cargo" id="t-add">
                            <a href="/user/minicargo/inquiry">
                                <img src="/dist/images/icons/circle-plus.svg" alt="">
                                <span><?= $lang['b_call_for_price'] ?></span>
                            </a>
                        </div>
                    </div>
                    <div class="col-6 mj-b-dashboard-btns">
                        <div class="mj-b-add-cargo" id="t-list">
                            <a href="/user/minicargo/inquiry-list">
                                <img src="/dist/images/icons/list-timeline.svg" alt="">
                                <span><?= $lang['u_inquiry_list'] ?></span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section class=" container-fluid mj-b-slider-section my-3">
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

        <section class="mj-b-dasboard-reports mt-3" id="t-status">
            <div class="container">
                <div class="row">
                    <div class="col-6 col-lg-3 mj-b-reports-cols">
                        <a href="/user/minicargo/inquiry-list/completed">
                            <div id="report-1" class="mj-b-report-card">
                                <div class="mj-b-report-content">
                                <span>
                                    <?= $lang['u_complete_inquiry'] ?>
                                </span>
                                    <span>
                                    <?= MiniCargo::getInquiryCountByStatus($user->UserId, 'completed') ?>
                                </span>
                                    <img id="inventory-report" src="/dist/images/storage.svg" alt="completed">
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-lg-3 mj-b-reports-cols">
                        <a href="/user/minicargo/inquiry-list/completed">
                            <div id="report-4" class="mj-b-report-card">
                                <div class="mj-b-report-content">
                                <span>
                                    <?= $lang['u_read_inquiry'] ?>
                                </span>
                                    <span>
                                    <?= MiniCargo::getInquiryCountByStatus($user->UserId, 'read') ?>
                                </span>
                                    <img id="check-report" src="/dist/images/check.svg" alt="completed">
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-6 col-lg-3 mj-b-reports-cols">
                        <a href="/user/minicargo/inquiry-list/process">
                            <div id="report-2" class="mj-b-report-card">
                                <div class="mj-b-report-content">
                                <span>
                                   <?= $lang['u_process_inquiry'] ?>
                                </span>
                                    <span>
                                    <?= MiniCargo::getInquiryCountByStatus($user->UserId, 'process') ?>
                                </span>
                                    <img src="/dist/images/warehouse-question.svg" alt="progress">
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-6 col-lg-3 mj-b-reports-cols">
                        <a href="/user/minicargo/inquiry-list/pending">
                            <div id="report-3" class="mj-b-report-card">
                                <div class="mj-b-report-content">
                                <span>
                                    <?= $lang['u_pending_inquiry'] ?>
                                </span>
                                    <span>
                                    <?= MiniCargo::getInquiryCountByStatus($user->UserId, 'pending') ?>
                                </span>
                                    <img src="/dist/images/icons/zoom.svg" alt="pending">
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
                            <source src="<?= $Settings['u_learning_minicargo']['link'] ?>" type="video/mp4">
                        </video>
                    </div>

                </div>
            </div>
        </div>

        <section class="mj-b-video-section" id="tj-b-dashboard-3">
            <div class="mj-b-video-card">
                <div class="mj-b-video-content">
                    <div class="mj-b-video-texts">
                        <h3><?= $Settings['u_learning_minicargo']['title'] ?></h3>
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
    getFooter();
} else {
    header('location: /login');
}