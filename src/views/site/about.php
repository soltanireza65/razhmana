<?php

global $lang, $Settings;

include_once 'header-footer.php';

enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
enqueueScript('about-init', '/dist/js/site/about.init.js');

getHeader($lang['about']);
?>
    <style>
        .mj-about-us-list{
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .mj-inner-li{
            list-style: none;
        }
    </style>
    <main style="padding-bottom: 180px;">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="mj-card">
                        <div class="card-body">
                            <h4 class="mj-fw-600 mj-font-14 mt-0"><?= $Settings['introduction_of_company_title'] ?></h4>

                            <p class="mj-fw-400 mj-font-12 mb-0">
                                <?= $Settings['introduction_of_company_text'] ?>

                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="mj-card">
                        <div class="card-body">
                            <h4 class="mj-fw-600 mj-font-14 mt-0 mb-4"><?= $Settings['contact_us'] ?></h4>

                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <h6 class="mj-fw-600 mj-font-13 mt-0"><?= $Settings['tehran_office'] ?></h6>

                                        <div class="mb-2">
                                            <img src="/dist/images/icons/location-dot.svg" class="mj-header-tabs-icon"
                                                 alt="tehran-address-location">
                                            <span
                                                class="mj-fw-500 mj-font-12"><?= $Settings['tehran_office_address'] ?></span>
                                        </div>

                                        <div>
                                            <img src="/dist/images/icons/headset.svg" class="mj-header-tabs-icon"
                                                 alt="tehran-address-location">
                                            <span
                                                class="mj-fw-500 mj-font-12"><?= $Settings['tehran_office_tel'] ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <h6 class="mj-fw-600 mj-font-13 mt-0"><?= $Settings['tabriz_office'] ?></h6>

                                        <div class="mb-2">
                                            <img src="/dist/images/icons/location-dot.svg" class="mj-header-tabs-icon"
                                                 alt="tehran-address-location">
                                            <span
                                                class="mj-fw-500 mj-font-12"><?= $Settings['tabriz_office_address'] ?></span>
                                        </div>

                                        <div>
                                            <img src="/dist/images/icons/headset.svg" class="mj-header-tabs-icon"
                                                 alt="tehran-address-location">
                                            <span
                                                class="mj-fw-500 mj-font-12"><?= $Settings['tabriz_office_tel'] ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="mj-card">
                        <div class="card-body">
                            <h4 class="mj-fw-600 mj-font-14 mt-0"><?= $lang['u_permissions']; ?></h4>

                            <div class="swiper permissions-swiper" dir="ltr">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide ">
                                        <a target="_blank"
                                           href="https://trustseal.enamad.ir/?id=291458&amp;Code=0YG7IVvJtYTGMKI3AcY7">
                                            <img referrerpolicy="origin"
                                                 src="/dist/images/enamad.png" alt=""
                                                 style="cursor:pointer" id="0YG7IVvJtYTGMKI3AcY7">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php
getFooter('', false);