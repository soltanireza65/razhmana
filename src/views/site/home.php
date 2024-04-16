<?php
global $Settings, $lang, $Tour;

use MJ\Security\Security;
use MJ\Utils\Utils;

$showSlider = false;

$langCookie = 'fa_IR';
if (isset($_COOKIE['language'])) {
    $langCookie = $_COOKIE['language'];
} else {
    $langCookie = 'fa_IR';
    setcookie('language', 'fa_IR', time() + STABLE_COOKIE_TIMEOUT, "/");
    User::changeUserLanguageOnChangeLanguage('fa_IR');
}

include_once 'header-footer.php';

/*if (!isset($_COOKIE['t-home']) || $_COOKIE['t-home'] != 'shown') {
    enqueueStylesheet('shepherd-css', '/dist/libs/shepherd/shepherd.css');
    enqueueScript('shepherd-js', '/dist/libs/shepherd/shepherd.js');
    enqueueScript('toured-dashboard', '/dist/js/site/toured-home.init.js');
    */ ?><!--
    <script type="text/javascript">
        let tour_vars = <?php /*= json_encode($Tour) */ ?>;
    </script>
    --><?php
/*}*/

enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');

enqueueStylesheet('user-css', '/dist/css/user.css');
enqueueStylesheet('user-css', '/dist/css/driver/drivers.css');

enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
enqueueScript('slider-js', '/dist/js/user/slider.js');
enqueueScript('home-init', '/dist/js/site/home.js');
enqueueScript('lazyload-js', '/dist/libs/lazyload/lazyload.js');

getHeader($lang['home']);

$sliders = $Settings['u_home_sliders'];
$namad_sliders = $Settings['u_home_nemad_slider'];


/**
 * Get All Cargo
 */
$resultAllCargo = Cargo::getAllCargoByLimitAndStatus();
$dataAllCargo = [];
if ($resultAllCargo->status == 200 && !empty($resultAllCargo->response)) {
    $dataAllCargo = $resultAllCargo->response;
}


$resultAllCargoIn = Cargo::getAllCargoInByLimitAndStatus();
$dataAllCargoIn = [];
if ($resultAllCargoIn->status == 200 && !empty($resultAllCargoIn->response)) {
    $dataAllCargoIn = $resultAllCargoIn->response;
}


$count_ship_freight = 0;
$count_railroad_freight = 0;
$count_air_freight = 0;
$count_inventory_freight = 0;
$count_customs_freight = 0;
$count_minicargo_freight = 0;
if (isset($_COOKIE['user-login'])) {
    $user = User::getUserInfo();
    $user_type = User::getUserType(json_decode(Security::decrypt($_COOKIE['user-login']))->UserId);
    $count_ship_freight = Ship::getInquiryCountByStatus($user->UserId, 'Completed');
    $count_railroad_freight = Railroad::getInquiryCountByStatus($user->UserId, 'Completed');
    $count_air_freight = Air::getInquiryCountByStatus($user->UserId, 'Completed');
    $count_inventory_freight = Inventory::getInquiryCountByStatus($user->UserId, 'Completed');
    $count_minicargo_freight = MiniCargo::getInquiryCountByStatus($user->UserId, 'Completed');
} else {
    $user_type = 'not-login';
}

?>
<script type="application/ld+json">
        <?php print_r(Utils::getFileValue("settings.txt", "seo_home")) ?>













</script>

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
                <h5 class="mj-cargo-owner-info-welcome"><?= $lang['u_call_owner_modal_title'] ?></h5>
                <div class="mt-2 "
                     style="text-align: right;margin-bottom: 5px;width: 95%;display: block"><?= $lang['u_call_owner_modal_mobile'] ?></div>
                <div class="mj-cargo-owner-info-list">
                        <span style="color: #303030;font-size: 16px;" dir="ltr"><?= Utils::getFileValue("settings.txt", 'support_call'); ?></span>
                    <div>
                        <a href="tel:<?= Utils::getFileValue("settings.txt", 'support_call'); ?>">
                            <div class="fa-mobile-button"></div>
                            <span><?= $lang['u_call_owner_modal_calling'] ?></span>
                        </a>
                    </div>
                </div>
                <div class="mt-2"
                     style="    text-align: right;margin-bottom: 5px;width: 95%;display: block"><?= $lang['u_call_owner_modal_mobile'] ?></div>
                <div class="mj-cargo-owner-info-list">
                        <span style="color: #303030;font-size: 16px;"
                              dir="ltr"><?= Utils::getFileValue("settings.txt", 'support_call_2'); ?></span>
                    <div>
                        <a href="tel:<?= Utils::getFileValue("settings.txt", 'support_call_2'); ?>">
                            <div class="fa-phone"></div>
                            <span><?= $lang['u_call_owner_modal_calling'] ?></span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <?= $lang['u_close_call_owner_modal'] ?>!
                </button>
            </div>
        </div>
    </div>
</div>

<!-- call owner modal end  -->
<div class="mj-preloader">
    <div class="mj-preloader-content">
        <img id="preloader-logo" src="/dist/images/logo(preloader)blue.svg" alt="logo">
        <svg class="ip" viewBox="0 0 256 128" width="256px" height="128px" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="grad1" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0%" stop-color="#5ebd3e"/>
                    <stop offset="33%" stop-color="#ffb900"/>
                    <stop offset="67%" stop-color="#f78200"/>
                    <stop offset="100%" stop-color="#e23838"/>
                </linearGradient>
                <linearGradient id="grad2" x1="1" y1="0" x2="0" y2="0">
                    <stop offset="0%" stop-color="#e23838"/>
                    <stop offset="33%" stop-color="#973999"/>
                    <stop offset="67%" stop-color="#009cdf"/>
                    <stop offset="100%" stop-color="#5ebd3e"/>
                </linearGradient>
            </defs>
            <g fill="none" stroke-linecap="round" stroke-width="16">
                <g class="ip__track" stroke="#ddd">
                    <path d="M8,64s0-56,60-56,60,112,120,112,60-56,60-56"/>
                    <path d="M248,64s0-56-60-56-60,112-120,112S8,64,8,64"/>
                </g>
                <g stroke-dasharray="180 656">
                    <path class="ip__worm1" stroke="url(#grad1)" stroke-dashoffset="0"
                          d="M8,64s0-56,60-56,60,112,120,112,60-56,60-56"/>
                    <path class="ip__worm2" stroke="url(#grad2)" stroke-dashoffset="358"
                          d="M248,64s0-56-60-56-60,112-120,112S8,64,8,64"/>
                </g>
            </g>
        </svg>
    </div>
</div>
<main class="mj-container px-1 mj-home-main-section" style="padding-bottom: 60px !important;">
    <div class="container">
        <div class="row justify-content-center d-flex justify-content-center" style="margin-top: 5rem !important;">
            <div class="text-center col-6 col-lg-3">
                <div class="mj-home-icon-item ground">
                    <a href="javascript:void(0);" id="transportation" style="width: 100%;">
                        <div class="mj-icon-con3">
                            <div class="mj-icon-item-logo">
                                <img src="/uploads/site/poster-default.svg" data-src="/dist/images/home/ground-dou1.svg"
                                    alt="my-requests">
                            </div>
                        </div>
                        <div class="mj-icon-con">
    
                            <div class="mj-icon-item-title">
                                <?= $lang['h_inquiry_ground'] ?>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
    
            <div class="text-center col-6 col-lg-3">
                <div class="mj-home-icon-item driver">
                    <a href="/user/drivers" style="width: 100%;">
                        <div class="mj-icon-con3">
                            <div class="mj-icon-item-logo">
                                <img src="/dist/images/home/drivers-dou1.svg" alt="drounf">
                            </div>
                        </div>
                        <div class="mj-icon-con">
                            <div class="mj-icon-item-title">
                                <?= $lang['u_drivers_service'] ?>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
    
            <div class="text-center col-6 col-lg-3">
                <div class="mj-home-icon-item ads">
                    <a <?= isset($_COOKIE['user-login']) ? '' : 'onclick="setCookie(\'login-back-url\' ,\'/poster\' )"' ?> href="/poster" style="width: 100%;">
                        <div class="mj-icon-con3">
                            <div class="mj-icon-item-logo">
                                <img src="/uploads/site/poster-default.svg" data-src="/dist/images/home/sss-1.svg">
                            </div>
                        </div>
                        <div class="mj-icon-con">
        
                            <div class="mj-icon-item-title">
                                <?= $lang['menu_buy_car'] ?>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            
            <div class="text-center col-6 col-lg-3">
                <div class="mj-home-icon-item exchange">
                    <a <?= isset($_COOKIE['user-login']) ? '' : 'onclick="setCookie(\'login-back-url\' ,\'/exchange  \' )"' ?> href="/exchange" style="width: 100%;">
                        <div class="mj-icon-con3">
                            <div class="mj-icon-item-logo">
                                <img src="/uploads/site/poster-default.svg" data-src="/dist/images/home/exchange-dou1.svg">
                            </div>
                        </div>
                        <div class="mj-icon-con">
        
                            <div class="mj-icon-item-title">
                                <?= $lang['menu_exchange'] ?>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="row justify-content-center mt-5">
        <div class="text-center col-6 col-lg-3">
            <div class="mj-home-icon-item ground">
                <a href="javascript:void(0);" id="transportation">

                    <div class="mj-icon-con3">
                        <div class="mj-icon-item-logo">
                            <img src="/uploads/site/poster-default.svg" data-src="/dist/images/home/ground-dou1.svg"
                                alt="my-requests">
                        </div>
                    </div>
                    <div class="mj-icon-con">

                        <div class="mj-icon-item-title">
                            <?= $lang['h_inquiry_ground'] ?>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="text-center col-6 col-lg-3">
            <div class="mj-home-icon-item driver">
                <a href="/user/drivers">

                    <div class="mj-icon-con3">
                        <div class="mj-icon-item-logo">
                            <img src="/dist/images/home/drivers-dou1.svg" alt="drounf">
                        </div>
                    </div>
                    <div class="mj-icon-con">
                        <div class="mj-icon-item-title">
                            <?= $lang['u_drivers_service'] ?>
                        </div>
                    </div>

                </a>

            </div>
        </div>

        <div class="text-center col-6 col-lg-3">
            <div class="mj-home-icon-item ads">
                <a <?= isset($_COOKIE['user-login']) ? '' : 'onclick="setCookie(\'login-back-url\' ,\'/poster\' )"' ?>
                    href="/poster">
                    <div class="mj-icon-con3">
                        <div class="mj-icon-item-logo">
                            <img src="/uploads/site/poster-default.svg" data-src="/dist/images/home/sss-1.svg">
                        </div>
                    </div>
                    <div class="mj-icon-con">

                        <div class="mj-icon-item-title">
                            <?= $lang['menu_buy_car'] ?>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="text-center col-6 col-lg-3">
            <div class="mj-home-icon-item exchange ">
            <a <?= isset($_COOKIE['user-login']) ? '' : 'onclick="setCookie(\'login-back-url\' ,\'/exchange  \' )"' ?>
                href="/exchange">

                <div class="mj-icon-con3">
                    <div class="mj-icon-item-logo">
                        <img src="/uploads/site/poster-default.svg" data-src="/dist/images/home/exchange-dou1.svg">
                    </div>
                </div>
                <div class="mj-icon-con">

                    <div class="mj-icon-item-title">
                        <?= $lang['menu_exchange'] ?>
                    </div>
                </div>
            </a>
            </div>
        </div>
    </div> -->
    <!-- <div class="mj-home-icons mj-container mt-5">
        <div class="mj-home-icon-item ground">
            <a href="javascript:void(0);" id="transportation">

                <div class="mj-icon-con3">
                    <div class="mj-icon-item-logo">
                        <img src="/uploads/site/poster-default.svg" data-src="/dist/images/home/ground-dou1.svg"
                             alt="my-requests">
                    </div>
                </div>
                <div class="mj-icon-con">

                    <div class="mj-icon-item-title">
                        <?= $lang['h_inquiry_ground'] ?>
                    </div>
                </div>
            </a>
        </div>
        <div class="mj-home-icon-item driver">
            <a href="/user/drivers">

                <div class="mj-icon-con3">
                    <div class="mj-icon-item-logo">
                        <img src="/dist/images/home/drivers-dou1.svg" alt="drounf">
                    </div>
                </div>
                <div class="mj-icon-con">
                    <div class="mj-icon-item-title">
                        <?= $lang['u_drivers_service'] ?>
                    </div>
                </div>

            </a>

        </div>


        <div class="mj-home-icon-item ads">
            <a <?= isset($_COOKIE['user-login']) ? '' : 'onclick="setCookie(\'login-back-url\' ,\'/poster\' )"' ?>
                href="/poster">
                <div class="mj-icon-con3">
                    <div class="mj-icon-item-logo">
                        <img src="/uploads/site/poster-default.svg" data-src="/dist/images/home/sss-1.svg">
                    </div>
                </div>
                <div class="mj-icon-con">

                    <div class="mj-icon-item-title">
                        <?= $lang['menu_buy_car'] ?>
                    </div>
                </div>
            </a>
        </div>
        <div class="mj-home-icon-item exchange ">
            <a <?= isset($_COOKIE['user-login']) ? '' : 'onclick="setCookie(\'login-back-url\' ,\'/exchange  \' )"' ?>
                href="/exchange">

                <div class="mj-icon-con3">
                    <div class="mj-icon-item-logo">
                        <img src="/uploads/site/poster-default.svg" data-src="/dist/images/home/exchange-dou1.svg">
                    </div>
                </div>
                <div class="mj-icon-con">

                    <div class="mj-icon-item-title">
                        <?= $lang['menu_exchange'] ?>
                    </div>
                </div>
            </a>
        </div>
    </div> -->

    <!-- start slider-->
        
    <?php if ($showSlider == true): ?>
    <section class=" container-fluid mj-b-slider-section mj-container  my-3">
        <div id="banners" dir="rtl" class="swiper mySwiper">
            <div class="swiper-wrapper">
                <?php foreach ($sliders as $slider) { ?>
                    <div class="swiper-slide">
                        <a href="<?= $slider['url'] ?>">
                            <div class="mj-b-slide-card">
                                <img src="/uploads/site/poster-default.svg"
                                     data-src="<?= Utils::fileExist($slider['image'], POSTER_DEFAULT); ?>"
                                     alt="<?= $slider['alt'] ?>">
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- end silder-->
    <style>
        .mj-select-user-type-modal .modal-content {
            background: transparent !important;
            border: unset !important;
        }

        .btn-close {
            color: #fff !important;
            background-color: #fff !important;
            padding: 5px;
            transform: translateY(-15px);
        }

        .mj-home-btn-section {
            border-radius: 15px !important;
        }
    </style>

    <div class="modal fade mj-select-user-type-modal" id="select-user-type-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ">
                <div class="modal-header" style="   border-bottom: 0;">

                </div>
                <div class="modal-body" style="border-bottom:0 ;">

                    <div class="mj-home-btns-row d-flex justify-content-center row">
                        <?php if ($user_type == 'driver' || $user_type == 'businessman') {
                            ?>
                            <div class="mj-gt-dashboard-section" id="go-to-dashboard">
                                <div><?= $lang['go_to_dashboard'] ?></div>
                                <div><img src="/uploads/site/poster-default.svg"
                                          data-src="/dist/images/icons/user-tie(white).svg" alt=""></div>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="container-fluid mj-home-btn-section">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                        style="margin: 0;"></button>
                                <div class="mj-alert mj-alert-with-icon  mb-3 mj-home-alert-signup">
                                    <div class="mj-alert-icon">
                                        <img src="/dist/images/icons/circle-exclamation.svg"
                                             alt="exclamation">
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between w-100 pe-1">
                                        <?= $lang['change_user_type_role_permission'] ?>
                                    </div>
                                </div>

                                <div class="mj-gt-driver-btn mb-3" id="driver-login">
                                    <a href="javascript:void(0)">
                                        <span><?= $lang['log_in_as_a_driver']; ?></span>
                                        <img src="/dist/images/icons/truck-container(white).svg"
                                             data-src="/dist/images/icons/truck-container(white).svg"
                                             alt="<?= $lang['driver']; ?>">
                                    </a>
                                </div>
                                <div class="mj-gt-businessman-btn" id="businessman-login">
                                    <a href="javascript:void(0)">
                                        <span><?= $lang['log_in_as_a_businessman']; ?></span>
                                        <img src="/dist/images/icons/user-tie(white).svg"

                                             alt="<?= $lang['businessman']; ?>">

                                    </a>
                                </div>
                                <input type="hidden" id="token_change_user_type" name="token_change_user_type"
                                       value="<?= Security::initCSRF('token_change_user_type') ?>">
                                <div class="mj-circle"></div>
                                <div class="mj-aquare"></div>
                            </div>
                            <?php
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- start academy -->
    <section style="margin-block: 25px !important;" class="mt-5 mj-container">
        <a href="/academy">
            <div class="mj-academy-banner">
                <svg width="103" height="21" viewBox="0 0 103 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M20.5509 1.38322C21.481 0.495379 22.7174 0 24.0033 0H98C100.761 0 103 2.23858 103 5V21H0L20.5509 1.38322Z"
                        fill="var(--primary)"/>
                </svg>
                <div class="mj-academy-text">
                    <span><?= $lang['u_academyy']; ?></span>
                    <span><?= $lang['u_post_new_education']; ?></span>
                    <span><?= $lang['u_post_click_education']; ?></span>
                </div>
                <img src="/uploads/site/poster-default.svg" data-src="/dist/images/academy/academy-banner.png"
                     alt="academy">
            </div>
        </a>
    </section>
    <!-- end academy -->

    <!--start hire banner-->
    <!-- <div class="mj-hire-btn-home mj-container my-3  ">
        <a href="/employment">
            <div class="mj-hire-btn-link">
                <span><?= $lang['u_employ_invite']; ?></span>
                <img src="/uploads/site/poster-default.svg" data-src="/dist/images/hire.svg" alt="hire">
            </div>
        </a>
    </div> -->
    <!--end hire banner-->

    <!-- start cargo out slider -->
    <section class="mj-container my-2">
    <div class="mj-cargo-neweset-header mb-2">
        <div class="d-flex align-items-center">
            <div class="mj-white-card pe-1">
                <img src="/uploads/site/poster-default.svg" data-src="/dist/images/icons/boxes(blue).svg" alt="">
            </div>

            <span><?= $lang['site_new_cargos_out'] ?></span>
        </div>
        <a href="/cargo-ads"><?= $lang['see_all'] ?></a>
    </div>
    <div dir="rtl" class="swiper mySwiper mj-cargo-out-slider">
        <div class="swiper-wrapper" style="height: 317px;">
            <?php
            foreach ($dataAllCargo as $item) {
                if ($item->cargo_status == 'accepted' || $item->cargo_status == 'progress') {
                    $destOutput = '';
                    if (isset($item->cargo_destination_id)) {
                        $destination = $item->cargo_destination_id;
                        $destCountry = Driver::getCountryByCities($destination)->response;
                        $destCity = Location::getCityNameById($destination)->response;
                        $destOutput = $destCity . ' - ' . $destCountry;
                    }
                    $sourceOutput = '';
                    if (isset($item->cargo_origin_id)) {
                        $origin = $item->cargo_origin_id;
                        $sourceCountry = Driver::getCountryByCities($origin)->response;
                        $sourceCity = Location::getCityNameById($origin)->response;
                        $sourceOutput = $sourceCity . ' - ' . $sourceCountry;

                    }

                    $type_car_output = '';
                    if ($item->type_id == 18) {
                        $type_car_output = '<div class="mj-d-cargo-car-type-badge d-flex">
                        <div class="mj-ambulnace-light me-2">
                            <div id="light-lamp">
                                <div id="lamp-spinner"></div>
                            </div>

                            <div id="light-lamp-bottom"></div>
                        </div>
                        <span> ' . array_column(json_decode($item->type_name), 'value', 'slug')[$langCookie] . '</span>
                    </div>';
                    } else {
                        $type_car_output = '<div class="mj-d-cargo-car-type-badge d-flex">
                         <img src="/uploads/site/poster-default.svg" data-src="' . Utils::fileExist($item->type_icon, BOX_EMPTY) . '" alt="">
                        <span> ' . array_column(json_decode($item->type_name), 'value', 'slug')[$langCookie] . '</span>
                    </div>';
                    }
                    ?>
                    <div class="swiper-slide" dir="rtl">
                        <div class="mj-d-cargo-card">
                            <div class="mj-d-cargo-card-badge">
                                <?= $item->cargo_id ?>
                            </div>

                            <?= $type_car_output ?>
                            <div class="card-body">
                                <div class="d-flex align-items-center mt-2 mb-2">
                                    <div class="mj-d-cargo-item-category me-2"
                                         style="background: <?= $item->category_color ?>">
                                        <img src="/uploads/site/poster-default.svg"
                                             data-src="<?= Utils::fileExist($item->category_icon, BOX_EMPTY) ?>"
                                             alt="<?= $item->category_icon ?>">
                                        <span><?= array_column(json_decode($item->category_name), 'value', 'slug')[$langCookie] ?></span>
                                    </div>
                                    <div class="flex-fill">
                                        <h2 class="mj-d-cargo-item-header mt-0 mb-2"><?php
                                            $slug = 'cargo_name_' . $langCookie;
                                            echo $item->$slug ?></h2>
                                        <div
                                            class="mj-d-cargo-item-price-box d-flex align-items-center justify-content-between">
                                            <span><?= $lang['d_cargo_price'] ?>:</span>
                                            <span>
                                                    <?php if ($item->cargo_recommended_price == 0) {
                                                        echo $lang['u_agreement'];
                                                    } else {
                                                        echo number_format($item->cargo_recommended_price);
                                                        ?>
                                                        <small><?= array_column(json_decode($item->currency_name), 'value', 'slug')[$langCookie] ?></small>
                                                    <?php } ?>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="position-relative">
                                                <img src="/uploads/site/poster-default.svg"
                                                     data-src="/dist/images/icons/arrow-up-left-from-circle.svg"
                                                     class="mj-d-cargo-item-icon <?= ($item->cargo_green == "yes") ? "me-2" : ""; ?>"
                                                     alt="origin"/>

                                                <?= ($item->cargo_green == "yes") ? '<div class="mj-green-road-blob"></div>' : ""; ?>
                                            </div>

                                            <div>
                                                <div
                                                    class="mj-d-cargo-item-title <?= ($item->cargo_green == "yes") ? "" : "ps-2"; ?>">
                                                    <?= $lang['d_cargo_origin'] ?>:
                                                </div>
                                                <div class="mj-d-cargo-item-value mj-ellipse ps-2"><?= $sourceOutput ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-4">
                                            <img src="/uploads/site/poster-default.svg"
                                                 data-src="/dist/images/icons/calendar-star.svg"
                                                 class="mj-d-cargo-item-icon me-2" alt="loading-time"/>
                                            <div>
                                                <div class="mj-d-cargo-item-title"><?= $lang['d_cargo_loading_time'] ?>
                                                    :
                                                </div>
                                                <div class="mj-d-cargo-item-value mj-ellipse">
                                                    <?php
                                                    if ($item->cargo_status == 'accepted' && $item->cargo_start_date <= time() && ($item->cargo_start_date + CARGO_READY_TO_LOAD) >= time()) {
                                                        echo $lang['u_date_ready_to_load'];
                                                    } else {
                                                        echo ($langCookie == 'fa_IR') ? Utils::jDate('Y/m/d', $item->cargo_start_date) : date('Y-m-d', $item->cargo_start_date);
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-1">
                                            <img src="/uploads/site/poster-default.svg"
                                                 data-src="/dist/images/icons/arrow-down-left-from-circle.svg"
                                                 class="mj-d-cargo-item-icon me-2" alt="destination"/>
                                            <div>
                                                <div class="mj-d-cargo-item-title"><?= $lang['d_cargo_destination'] ?>
                                                    :
                                                </div>
                                                <div class="mj-d-cargo-item-value mj-ellipse"><?= $destOutput ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-1">
                                            <img src="/uploads/site/poster-default.svg"
                                                 data-src="/dist/images/icons/truck-container(blue).svg"
                                                 class="mj-d-cargo-item-icon me-2" alt="weight"/>
                                            <div>
                                                <div class="mj-d-cargo-item-title"><?= $lang['b_cargo_car_needed'] ?>
                                                    :
                                                </div>
                                                <div class="mj-d-cargo-item-value">
                                                    <?= $item->cargo_car_count; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php                                
                                
                                $phone = Cargo::getUserPhoneNumber($item->user_id);
                                $phoneDecrepted = Security::decrypt($phone->response[0]->user_mobile);
                                $phoneStr = str_replace("+", "", $phoneDecrepted);
                                ?>
                                <div class="btn-group w-100 mt-3" role="group" aria-label="Basic example">
                                    <a 
                                        href="/cargo-ads/<?= $item->cargo_id ?>" 
                                        class="btn btn-primary btn-sm w-50">
                                            <?= $lang['d_cargo_show'] ?>
                                    </a>

                                    <a
                                        href="tel:<?php echo $phoneDecrepted ?>>" 
                                        class="btn btn-outline-primary btn-sm w-50">
                                        <?php echo $phoneStr ?>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
    </section>
    <!-- end cargo out slider -->

    <!-- start cargo in slider -->
    <?php if (count($dataAllCargoIn) > 0) {
        ?>

        <section class="mj-container my-2">
        <div class="mj-cargo-neweset-header mb-2">
            <div class="d-flex align-items-center">
                <div class="mj-white-card pe-1">
                    <img src="/uploads/site/poster-default.svg" data-src="/dist/images/icons/boxes(blue).svg" alt="">
                </div>

                <span><?= $lang['site_new_cargos_in'] ?></span>
            </div>
            <a href="/cargo-in-ads"><?= $lang['see_all'] ?></a>
        </div>
        <div dir="rtl" class="swiper mySwiper">
            <div class="swiper-wrapper" style="height: 317px;">
                <?php
                foreach ($dataAllCargoIn as $item) {
                    if ($item->cargo_status == 'accepted' || $item->cargo_status == 'progress') {
                        $destOutput = '';
                        if (isset($item->cargo_destination_id)) {
                            $destination = $item->cargo_destination_id;
                            $destCountry = Driver::getCountryByCities($destination)->response;
                            $destCity = Location::getCityNameById($destination)->response;
                            $destOutput = $destCity;
                        }
                        $sourceOutput = '';
                        if (isset($item->cargo_origin_id)) {
                            $origin = $item->cargo_origin_id;
                            $sourceCountry = Driver::getCountryByCities($origin)->response;
                            $sourceCity = Location::getCityNameById($origin)->response;
                            $sourceOutput = $sourceCity . ' - ' . $sourceCountry;

                        }
                        $type_car_output = '';
                        if ($item->type_id == 18) {
                            $type_car_output = '<div class="mj-d-cargo-car-type-badge d-flex">
                        <div class="mj-ambulnace-light me-2">
                            <div id="light-lamp">
                                <div id="lamp-spinner"></div>
                            </div>

                            <div id="light-lamp-bottom"></div>
                        </div>
                        <span> ' . array_column(json_decode($item->type_name), 'value', 'slug')[$langCookie] . '</span>
                    </div>';
                        } else {
                            $type_car_output = '<div class="mj-d-cargo-car-type-badge d-flex">
                         <img src="/uploads/site/poster-default.svg" data-src="' . Utils::fileExist($item->type_icon, BOX_EMPTY) . '" alt="">
                        <span> ' . array_column(json_decode($item->type_name), 'value', 'slug')[$langCookie] . '</span>
                    </div>';
                        }
                        ?>
                        <div class="swiper-slide" dir="rtl">
                            <div class="mj-d-cargo-card">
                                <div class="mj-d-cargo-card-badge">
                                    <?= $item->cargo_id ?>
                                </div>
                                <?= $type_car_output ?>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mt-2 mb-2">
                                        <div class="mj-d-cargo-item-category me-2"
                                             style="background: <?= $item->category_color ?>">
                                            <img src="/uploads/site/poster-default.svg"
                                                 data-src="<?= Utils::fileExist($item->category_icon, BOX_EMPTY) ?>"
                                                 alt="<?= $item->category_icon ?>">
                                            <span><?= array_column(json_decode($item->category_name), 'value', 'slug')[$langCookie] ?></span>
                                        </div>
                                        <div class="flex-fill">
                                            <h2 class="mj-d-cargo-item-header mt-0 mb-2"><?php
                                                $slug = 'cargo_name_' . $langCookie;
                                                echo $item->$slug ?></h2>
                                            <div
                                                class="mj-d-cargo-item-price-box d-flex align-items-center justify-content-between">
                                                <span><?= $lang['d_cargo_price'] ?>:</span>
                                                <span>
                                                    <?php if ($item->cargo_recommended_price == 0) {
                                                        echo $lang['u_agreement'];
                                                    } else {
                                                        echo number_format($item->cargo_recommended_price);
                                                        ?>
                                                        <small><?= array_column(json_decode($item->currency_name), 'value', 'slug')[$langCookie] ?></small>
                                                    <?php } ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="cargo-detail col-6">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="position-relative">
                                                    <img src="/uploads/site/poster-default.svg"
                                                         data-src="/dist/images/icons/arrow-up-left-from-circle.svg"
                                                         class="mj-d-cargo-item-icon"
                                                         alt="origin"/>
                                                </div>

                                                <div>
                                                    <div class="mj-d-cargo-item-title ps-2">
                                                        <?= $lang['d_cargo_origin'] ?>:
                                                    </div>
                                                    <div
                                                        class="mj-d-cargo-item-value mj-ellipse ps-2"><?= $sourceOutput ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cargo-detail col-6">
                                            <div class="d-flex align-items-center mb-4">
                                                <img src="/uploads/site/poster-default.svg"
                                                     data-src="/dist/images/icons/calendar-star.svg"
                                                     class="mj-d-cargo-item-icon me-2" alt="loading-time"/>
                                                <div>
                                                    <div
                                                        class="mj-d-cargo-item-title"><?= $lang['d_cargo_loading_time'] ?>
                                                        :
                                                    </div>
                                                    <div class="mj-d-cargo-item-value mj-ellipse">
                                                        <?php
                                                        if ($item->cargo_status == 'accepted' && $item->cargo_start_date <= time() && ($item->cargo_start_date + CARGO_READY_TO_LOAD) >= time()) {
                                                            echo $lang['u_date_ready_to_load'];
                                                        } else {
                                                            echo ($langCookie == 'fa_IR') ? Utils::jDate('Y/m/d', $item->cargo_start_date) : date('Y-m-d', $item->cargo_start_date);
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cargo-detail col-6">
                                            <div class="d-flex align-items-center mb-1">
                                                <img src="/uploads/site/poster-default.svg"
                                                     data-src="/dist/images/icons/arrow-down-left-from-circle.svg"
                                                     class="mj-d-cargo-item-icon me-2" alt="destination"/>
                                                <div>
                                                    <div
                                                        class="mj-d-cargo-item-title"><?= $lang['d_cargo_destination'] ?>
                                                        :
                                                    </div>
                                                    <div
                                                        class="mj-d-cargo-item-value mj-ellipse"><?= $destOutput ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cargo-detail col-6">
                                            <div class="d-flex align-items-center mb-1">
                                                <img src="/uploads/site/poster-default.svg"
                                                     data-src="/dist/images/icons/truck-container(blue).svg"
                                                     class="mj-d-cargo-item-icon me-2" alt="weight"/>
                                                <div>
                                                    <div
                                                        class="mj-d-cargo-item-title"><?= $lang['b_cargo_car_needed'] ?>
                                                        :
                                                    </div>
                                                    <div class="mj-d-cargo-item-value">
                                                        <?= $item->cargo_car_count; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php                                
                                
                                    $phone = Cargo::getUserPhoneNumber($item->user_id);
                                    $phoneDecrepted = Security::decrypt($phone->response[0]->user_mobile);
                                    $phoneStr = str_replace("+", "", $phoneDecrepted);
                                    ?>
                                    <div class="btn-group w-100 mt-3" role="group" aria-label="Basic example">
                                        <a 
                                            href="/cargo-in-ads/<?= $item->cargo_id ?>"
                                            class="btn btn-primary btn-sm w-50">
                                                <?= $lang['d_cargo_show'] ?>
                                        </a>

                                        <a
                                            href="tel:<?php echo $phoneDecrepted ?>>" 
                                            class="btn btn-outline-primary btn-sm w-50">
                                            <?php echo $phoneStr ?>
                                        </a>
                                    </div>

                                    
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
         </section>
        <!-- end cargo in slider -->
    <?php } ?>


    



    <!-- start symbol -->
<div class="mj-footer-slider-card">
        <div class="swiper mj-footer-slider ">
            <div class="swiper-wrapper">
                <?php foreach ($namad_sliders as $slider) { ?>


                    <div class="swiper-slide">
                        <a href="javascript:void(0);">
                            <img referrerpolicy="origin"
                                 onclick='window.open("<?= $slider['url'] ?>", "Popup","toolbar=no, location=no, statusbar=no, menubar=no, scrollbars=1, resizable=0, width=580, height=600, top=30")'
                                 src="/uploads/site/poster-default.svg"
                                 data-src="<?= Utils::fileExist($slider['image'], POSTER_DEFAULT); ?>"
                                 style="cursor:pointer" id="0YG7IVvJtYTGMKI3AcY7">
                        </a>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
    <!-- end symbol -->


</main>

<!-- Modal -->
<div class="modal fade" id="language-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="language-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="height: 350px">

            <div class="modal-body">
                <div class=" " id="lang-modal-h">
                    <div class="mj-language-select2 p-2 ">
                        <div class="fa-language">

                        </div>

                        <div style="width: 100%; padding-top: 30px;">
                            <form class="mj-lang-items" action="/lang" method="post">
                                <div class="radio">
                                    <input id="lang-en" value="en_US" name="lang-radio" type="radio">
                                    <label for="lang-en" class="radio-label">
                                        <div class="mj-lang-home-item">
                                            <img src="/dist/images/language/en.svg" alt="EN">
                                            <span>EN</span>
                                        </div>
                                    </label>
                                </div>
                                <div class="radio">
                                    <input id="lang-ir" value="fa_IR" name="lang-radio" type="radio">
                                    <label for="lang-ir" class="radio-label">
                                        <div class="mj-lang-home-item">
                                            <img src="/dist/images/language/ir.svg" alt="IR">
                                            <span>IR</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="radio">
                                    <input id="lang-tr" value="tr_Tr" name="lang-radio" type="radio">
                                    <label for="lang-tr" class="radio-label">
                                        <div class="mj-lang-home-item">
                                            <img src="/dist/images/language/tr.svg" alt="TR">
                                            <span>TR</span>
                                        </div>
                                    </label>
                                </div>
                                <div class="radio">
                                    <input id="radio-ru" value="ru_RU" name="lang-radio" type="radio">
                                    <label for="radio-ru" class="radio-label">
                                        <div class="mj-lang-home-item">
                                            <img src="/dist/images/language/ru.svg" alt="RU">
                                            <span>RU</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="mj-lang-submit">
                                    <input id="lang-submit" type="submit" name="submit" value="OK">
                                </div>
                                <style>
                                    form {
                                        display: grid;
                                        gap: 7px;
                                        grid-template-columns: repeat(2, 1fr);
                                        width: 100%;
                                        position: relative;
                                        padding-bottom: 50px;
                                    }

                                    .mj-lang-submit {
                                        position: absolute;
                                        bottom: 0;
                                        transform: translateX(50%);
                                        right: 50%;
                                        width: 100%;
                                        text-align: center;
                                    }

                                    .mj-lang-submit #lang-submit {
                                        width: 50% !important;
                                        background: var(--primary);
                                        outline: unset;
                                        border: unset;
                                        border-radius: 10px;
                                        padding: 5px;
                                        color: #fff;
                                    }
                                </style>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php
getFooterHome();
?>
