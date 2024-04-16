<?php

global $lang, $Settings;

use MJ\Security\Security;
use MJ\Utils\Utils;

include_once getcwd() . '/views/user/header-footer.php';
enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');
enqueueStylesheet('select2', '/dist/libs/swiper/css/swiper-bundle.min.css');

enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');

enqueueScript('accounts1-js', '/dist/libs/lottie/lottie-player.js');
enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
enqueueScript('accounts-js', '/dist/js/user/drivers-list.js');
enqueueScript('select21-js', '/dist/js/user/drivers/drivers-list.js');
enqueueScript('custom-swiper-js', '/dist/js/user/slider.js');
getHeader($lang['u_driver_cv']);
if (isset($_COOKIE['user-login'])) {
    $user_id = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
    $my_cv = CV::getCvDetailByUserId($user_id);
    if ($my_cv->status == 200) {
        $my_cv = $my_cv->response[0];
    } else {
        $my_cv = [];
    }
}


//file_put_contents('./temp.json', json_encode($my_cv));
$language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
$user_type = 'other';
if (isset($_COOKIE['user-type']) && $_COOKIE['user-type'] == 'driver') {
    $user_type = 'driver';
}
$sliders = $Settings['u_driver_list_sliders'];

?>
    <style>
        .mj-ios-link-card {
            background: linear-gradient(275.49deg, #312D57 4.49%, #3E32AC 99.4%);
            border-radius: 30px;
            margin: auto;
            width: 90%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 5px;
            height: auto;
        }

        .mj-ios-download-title {
            display: flex;
            justify-content: space-evenly;
            align-items: center;

        }

        .mj-ios-download-title .mj-ios-link-text-box {
            background: #fff !important;
            color: #312D57;
            padding: 3px 30px;
            border-radius: 100px;
            font-size: 14px;
            font-weight: 500;
            margin-left: 5px;
            display: grid;
            gap: 5px;
        }
        .mj-ios-download-title .mj-ios-link-text-box span:nth-child(2) {

            font-size: 12px;
            font-weight: 500;

        }

        .mj-ios-link-text {
            width: 100%;
        }

        .mj-fa-arrow-down-ios {
            width: 33px;
            height: 33px;
            background: #F5B937;
            color: #fff !important;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: fontawesome;
        }

        #exampleModal .modal-body {
            text-align: center;
        }

        .select2-selection__choice {
            background: #0a7941 !important;
        }

        .select2-selection__choice__remove {
            color: #fff !important;
        }

        .mj-drivers-filter-btn {
            width: 100% !important;
        }
        .mj-b-slider-section {
            margin: auto !important;
            padding: 0 !important;
            width: 90%;
        }
        .mj-b-slide-card {
            max-width: 100%;
            height: auto !important;
            max-height: unset !important;
            display: flex;
            align-items: center;
            border-radius: 10px;
            overflow: hidden;
            justify-content: center;
        }
        .mj-b-slide-card img{
            height: 100%;
        }
    </style>


    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                        <?= $lang['cv-tutorial-title'] ?>
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mj-add-to-home-tut-head">
                        <img class="mb-2" src="/dist/images/ntirapp-qr.svg" alt="">
                    </div>
                    <div id="divider"></div>

                    <div class="mj-add-to-home-tut-video">
                        <video class="mj-turkey" width="80%" controls>
                            <source src="<?= $Settings['driver_service_video_section'] ?>" type="video/mp4">
                        </video>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="mj-filter-modal-content mj-filter-modal-content2">
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen ">
                <div id="filter-modal-body" class="modal-content">
                    <div class="modal-header">

                        <div style="text-align: right;font-size: 11px">

                            <h1 class="modal-title fs-5"
                                id="staticBackdropLabel"><?= $lang['u_drivers_filter_title'] ?></h1>

                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span class="fa-close"></span>
                        </button>
                    </div>
                    <div class="modal-body container">


                        <div class="row">

                            <button class="remove-filter mj-remove-cargo-filter">
                                <img src="/dist/images/poster/filter-refresh.svg" alt="refresh">
                                <span style="padding: 0 10px"><?= $lang['u_cargo_filter_remove_all'] ?></span>
                            </button>
                            <span
                                class="text-primary mb-3 mj-fw-300 mj-font-12"><?= $lang['cv-filter-desc']; ?>
                            </span>
                            <div class="col-12">
                                <div class="mj-trx-search mb-3">
                                    <form action="" class="mj-trx-serach-form searchopen">
                                        <input type="text" id="tx-search" placeholder="جستجو بین رانندگان ">
                                        <button type="button">
                                            <div class="fa-search"></div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="cargo-origin-country"
                                           class="text-dark mj-fw-500 mj-font-12 mb-1">
                                        <?= $lang['pb_fav_contries'] ?>
                                    </label>
                                    <div class="mj-custom-select cargo-origin-country2">
                                        <select class="form-select width-95 my-1 mb-3"
                                                id="cargo-origin-country2"
                                                name="cargo-origin-country"
                                                data-width="100%"
                                                multiple="multiple"
                                                data-placeholder="<?= $lang['u_select_by_country_filter'] ?>">
                                            <option value=""></option>


                                            <?php
                                            $countries = Location::getCountriesList();
                                            foreach ($countries->response as $item) { ?>
                                                <option value="<?= $item->CountryId ?>">
                                                    <?= $item->CountryName ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="cargo-origin-country"
                                           class="text-dark mj-fw-500 mj-font-12 mb-1">
                                        <?= $lang['cv-filter-active-inactive'] ?>
                                    </label>
                                    <div class="mj-cv-filter-radio-button">
                                        <div class="form-check">
                                            <input type="radio" id="active" name="filter-cv-status" checked>
                                            <label for="active"><?= $lang['pb_access'] ?></label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" id="inactive" name="filter-cv-status">
                                            <label for="inactive"><?= $lang['pb_not_access'] ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="cargo-origin-country"
                                           class="text-dark mj-fw-500 mj-font-12 mb-1">
                                        <?= $lang['cv_visa_location'] ?>
                                    </label>
                                    <div class="mj-custom-select driver-visa-location">
                                        <select class="form-select width-95 my-1 mb-3"
                                                id="driver-visa-location"
                                                name="driver-visa-location"
                                                data-width="100%"
                                                multiple="multiple"
                                                data-placeholder="<?= $lang['cv_visa_location'] ?>">
                                            <?php
                                            $visa_locations = VisaLocation::getAllVisaLocation();
                                            $slug = 'visa_name_' . $language;
                                            foreach ($visa_locations->response as $item) { ?>
                                                <option value="<?= $item->visa_id ?>">
                                                    <?= $item->$slug ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>


                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" id="submit-filter" name="submit-filter" class="mj-apply-filter">
                            <?= $lang['u_cargo_filter_submit']; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section style="padding-top: 110px">
        <div class="mj-wallet-head-blue <?= $user_type == 'other' ? 'isbusinessman' : ''; ?>">
            <div class="mj-wallet-blue">
                <?= $lang['u_driver_cv'] ?>

                <div>
                    <div class="mj-ios-link mb-1 mt-2">
                        <a href="https://ntirapp.com" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <div class="mj-ios-link-card">
                                <div class="mj-ios-link-text">
                                    <div class="mj-ios-download-title">
                                        <div class="mj-ios-link-text-box">
                                            <span> <?= $lang['cv-tutorial-title'] ?></span>
                                            <span> کلیک کنید</span>
                                        </div>

                                        <div class="fa-video mj-fa-arrow-down-ios fa-bounce"></div>
                                    </div>

                                </div>

                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <svg viewBox="0 0 1920 145" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1920 0C1920 77.8627 1490.19 141 960 141C429.81 141 0 77.8627 0 0H1920Z" fill="#3CA4F6"/>
            </svg>
            <?php if (isset($_COOKIE['user-login']) && CV::getUserCvCount($user_id) > 0) { ?>
                <a href="/user/drivers/detail/<?= $my_cv->cv_id ?>"
                   class="mj-driver-list-my-cv <?= $user_type == 'other' ? 'd-none' : ''; ?> ">
                    <div class="mj-my-cv-image">
                        <img src="<?= Utils::fileExist($my_cv->cv_user_avatar, BOX_EMPTY); ?>" alt="profile">
                    </div>
                    <div class="mj-driver-list-my-header">
                        <div class="mj-my-cv-name">
                            <?= $my_cv->cv_name . ' ' . $my_cv->cv_lname ?>
                        </div>
                        <div class="mj-my-cv-city">
                            <Span
                                id="mycv-city-name"><?= array_column(json_decode($my_cv->city_name, true), 'value', 'slug')[$language] ?></Span>
                            <div class="mx-1">|</div>
                            <span><?= Location::getCountryByCityId($my_cv->city_id)->CountryName ?></span>
                        </div>
                    </div>
                    <img id="mj-mycv-icon" src="/dist/images/drivers/truckcontainer.svg" alt="truck">
                </a>
            <?php } elseif (isset($_COOKIE['user-login'])) { ?>
                <a href="javascript:void(0)"
                   class="container mj-driver-list-my-cv empty  <?= $user_type == 'other' ? 'd-none' : ''; ?>">
                    <div class="mj-my-cv-image">
                        <img src="/dist/images/drivers/empty-profile.svg" alt="empty-profile">
                    </div>
                    <div class="mj-driver-list-my-header">
                        <div class="mj-my-cv-name">
                            *************
                        </div>
                        <div class="mj-my-cv-city">
                            <Span id="mycv-city-name">******</Span>
                            <div class="mx-1">|</div>
                            <span>********</span>
                        </div>
                    </div>
                    <img id="mj-mycv-icon" src="/dist/images/drivers/truckcontainer.svg" alt="truck">
                </a>
                <a href="/user/drivers/add"
                   class="mj-driver-list-add-cv-btn <?= $user_type == 'other' ? 'd-none' : ''; ?>"><?= $lang['u_driver_cv_add_cv'] ?></a>
            <?php } ?>

        </div>
    </section>
    <div class="container ">
        <div class="mj-filters-btn-driver mj-drivers-filter-btn ">
            <div class="mj-filter-button-modal " data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                <div class="fa-filter"></div>
                <?= $lang['u_advanced_search']; ?>
            </div>
            <div class="fa-close remove-filter d-none"></div>
        </div>

    </div>
    <!-- start slider-->
    <section class=" container-fluid mj-b-slider-section my-3">
        <div id="banners" dir="rtl" class="swiper DriverSwiper">
            <div class="swiper-wrapper">
                <?php foreach ($sliders as $slider) { ?>
                    <div class="swiper-slide">
                        <a href="<?= $slider['url'] ?>">
                            <div class="mj-b-slide-card">
                                <img src="<?= Utils::fileExist($slider['image'], POSTER_DEFAULT); ?>"
                                     data-src="<?= Utils::fileExist($slider['image'], POSTER_DEFAULT); ?>"
                                     alt="<?= $slider['alt'] ?>">
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <!-- end silder-->
    <section class="mj-drivers-list-section container" style="padding-bottom:70px ">
        <div>
            <div class="mj-trx-head px-2">
                <div class="mj-trx-list-title "><?= $lang['u_driver_cv_driver_list'] ?> :</div>
                <!--                <div class="mj-trx-operation-btns">-->
                <!--                    <div class="mj-search-btn me-2">-->
                <!--                        <div class="fa-search"></div>-->
                <!--                    </div>-->
                <!--                </div>-->
            </div>
            <!--            <div class="mj-trx-search">-->
            <!--                <form action="" class="mj-trx-serach-form">-->
            <!--                    <input type="text" id="tx-search"-->
            <!--                           placeholder="-->
            <?php //= $lang['u_driver_cv_driver_list_search_placeholder'] ?><!--">-->
            <!--                    <button type="button">-->
            <!--                        <div class="fa-search"></div>-->
            <!--                    </button>-->
            <!--                </form>-->
            <!--            </div>-->
        </div>

        <div class="mj-accounts-list">

        </div>

        <div class="mj-trx-list-load  ">
            <lottie-player src="/dist/lottie/wallet-load.json" background="transparent" speed="1" loop
                           autoplay></lottie-player>
        </div>
    </section>

<?php
getFooter('', false);