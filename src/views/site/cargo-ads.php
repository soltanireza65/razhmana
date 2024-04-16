<?php

global $lang, $Tour;


//    $user = User::getUserInfo();
use MJ\Utils\Utils;

include_once 'views/user/header-footer.php';

enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');
enqueueStylesheet('select2', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');

enqueueScript('cargo-list-init-js', '/dist/libs/jquery/jquery.js');
enqueueScript('cargo-list-init-js', '/dist/libs/lottie/lottie-player.js');

enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
enqueueScript('cargo-list-init-js', '/dist/js/site/cargo-list.init.js');


$language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
$car_types = Car::getAllCarsTypes('active');
if ($car_types->status == 200) {
    $car_types = $car_types->response;
} else {
    $car_types = [];
}
getHeader($lang['d_dashboard_driver']);


$ring = Ring::getUserRingDetail();
if ($ring->status == 200) {
    $ring = $ring->response[0];
} else {
    $ring = [];
}
if (isset($ring->ring_origin_country_id)) {
    $cities = Location::getCitiesForMultiCountries(explode(',',$ring->ring_origin_country_id ));
}
if (isset($ring->ring_dest_country_id)) {
    $destcities = Location::getCitiesForMultiCountries(explode(',',$ring->ring_dest_country_id ));
}


?>
    <style>
        .select2-container--default .select2-search--dropdown .select2-search__field {
            background-image: url(/dist/images/search2.png) !important;
            background-repeat: no-repeat !important;
            background-size: 15px !important;
            background-position: left !important;
            border-radius: 5px;
            background: content-box;
        }

        .select2-container--default .select2-search--dropdown {
            padding: 10px 3px !important;

        }
        .select2-selection__choice{
            background: #0a7941 !important;
        }
        .select2-selection__choice__remove{
            color:  #fff !important;
        }
    </style>


    <!-- call owner modal start  -->
    <div class="mj-cargo-owner-modal-info modal " id="staticBackdrop1" data-bs-backdrop="static"
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
                        <span style="color: #303030;font-size: 16px;"
                              dir="ltr"><?= Utils::getFileValue("settings.txt", 'support_call'); ?></span>
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

    <!--bell filter modal start-->
    <div class="mj-filter-modal-content">
        <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">

                        <div style="text-align: right;font-size: 11px">

                            <h1 class="modal-title fs-5"
                                id="staticBackdropLabel"><?=$lang['ring']?></h1>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span class="fa-close"></span>
                        </button>
                    </div>
                    <div class="modal-body">


                        <div class="row">
                            <div class="mb-2">
                                <span style="color: red"><?= $lang['ring_notic'] ?>:</span>
                                <span style="font-size: 14px;color: #303030">
                                    <?= $lang['ring_desciption'] ?>
                                </span>
                            </div>
                            <button class=" mj-remove-cargo-filter" id="remove-bell">
                                <img src="/dist/images/poster/filter-refresh.svg" alt="refresh">
                                <span style="padding: 0 10px"><?= $lang['u_cargo_filter_remove_all'] ?></span>
                            </button>
                            <span
                                class="text-primary mb-3 mj-fw-300 mj-font-12"><?= $lang['u_select_city_after_country_enable']; ?></span>

                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="cargo-origin-country"
                                           class="text-dark mj-fw-500 mj-font-12 mb-1">
                                        <?= $lang['b_cargo_source_country'] ?>
                                    </label>
                                    <div class="mj-custom-select cargo-origin-country2">
                                        <select class="form-select width-95 my-1 mb-3"
                                                id="cargo-origin-country2"
                                                name="cargo-origin-country"
                                                data-width="100%"
                                                multiple="multiple"
                                                data-placeholder="<?= $lang['b_cargo_select_country'] ?>">
                                            <option value=""></option>
                                            <?php
                                            $countries = Location::getCountriesList();
                                            foreach ($countries->response as $item) {
                                                ?>
                                                <option
                                                    value="<?= $item->CountryId ?>" <?= in_array($item->CountryId, explode(',' ,$ring->ring_origin_country_id)) ? 'selected' : '' ?>><?= $item->CountryName ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="cargo-dest-country2"
                                           class="text-dark mj-fw-500 mj-font-12 mb-1">
                                        <?= $lang['b_cargo_dest_country'] ?>

                                    </label>
                                    <div class="mj-custom-select cargo-dest-country2">
                                        <select class="form-select width-95 my-1 mb-3"
                                                id="cargo-dest-country2"
                                                name="cargo-dest-country2"
                                                 multiple="multiple"
                                                data-width="100%"
                                                data-placeholder="<?= $lang['b_cargo_select_country'] ?>">
                                            <option value=""></option>
                                            <?php
                                            $countries = Location::getCountriesList();
                                            foreach ($countries->response as $item) {
                                                ?>
                                                <option
                                                    value="<?= $item->CountryId ?>" <?= in_array($item->CountryId, explode(',' ,$ring->ring_dest_country_id))  ? 'selected' : '' ?>><?= $item->CountryName ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="cargo-origin2"
                                           class="text-dark mj-fw-500 mj-font-12 mb-1">
                                        <?= $lang['b_cargo_source_city'] ?>
                                    </label>
                                    <div class="mj-custom-select cargo-origin2">
                                        <select class="form-select width-95 my-1 mb-3"
                                                id="cargo-origin2"
                                                name="cargo-origin2"
                                                data-width="100%"
                                                multiple="multiple"
                                                data-placeholder="<?= $lang['b_cargo_select_cities'] ?>">
                                             <?php


                                            foreach ($cities->response as $city) {
                                                ?>
                                                <option
                                                    value="<?= $city->CityId ?>" <?= in_array($city->CityId, explode(',' ,$ring->ring_origin_id))   ? 'selected' : '' ?>><?= $city->CityName . ' - ' . $city->CityNameEN ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="cargo-destination2"
                                           class="text-dark mj-fw-500 mj-font-12 mb-1">
                                        <?= $lang['b_cargo_dest_city'] ?>

                                    </label>
                                    <div class="mj-custom-select cargo-destination2">
                                        <select class="form-select width-95 my-1 mb-3"
                                                id="cargo-destination2"
                                                name="cargo-destination2"
                                                data-width="100%"
                                                multiple="multiple"
                                                data-placeholder="<?= $lang['b_cargo_select_cities'] ?>">
                                             <?php


                                            foreach ($destcities->response as $city) {
                                                ?>
                                                <option
                                                    value="<?= $city->CityId ?>" <?= in_array($city->CityId, explode(',' ,$ring->ring_dest_id))  ? 'selected' : '' ?>><?= $city->CityName . ' - ' . $city->CityNameEN ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>


                        </div>


                        <div class="mt-3 mb-2"> <?= $lang['b_car_type']; ?></div>

                        <div class="mj-filter-car-type" id="car_type_area2">
                            <?php
                            if (!empty($ring->ring_car_types_ids)) {
                                $car_type_ids = explode(",", $ring->ring_car_types_ids);
                            } else {
                                $car_type_ids = [];
                            }

                            foreach ($car_types as $type) {
                                $tmp = strval($type->type_id);

                                if (in_array($tmp, $car_type_ids)) {
                                    ?>
                                    <label>
                                        <input class="checkbox-type-car" type="checkbox" value="<?= $type->type_id ?>"
                                               checked>
                                        <div class="mj-car-type-filter">
                                            <?= array_column(json_decode($type->type_name), 'value', 'slug')[$language] ?>
                                        </div>

                                    </label>
                                    <?php
                                } else {
                                    ?>
                                    <label>
                                        <input class="checkbox-type-car" type="checkbox" value="<?= $type->type_id ?>">
                                        <div class="mj-car-type-filter">
                                            <?= array_column(json_decode($type->type_name), 'value', 'slug')[$language] ?>
                                        </div>

                                    </label>
                                    <?php
                                }
                            }
                            ?>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" id="submit-bell" name="submit-bell" class="mj-apply-filter">
                            <?= $lang['enable_ring'] ?>

                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--bell filter modal end-->
    <main class="container" style="padding-bottom: 180px;">
        <div class="mj-filter-modal-content mj-filter-modal-content2">
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                 aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-fullscreen ">
                    <div id="filter-modal-body" class="modal-content">
                        <div class="modal-header">

                            <div style="text-align: right;font-size: 11px">

                                <h1 class="modal-title fs-5"
                                    id="staticBackdropLabel"><?= $lang['u_cargo_filter'] ?></h1>
                                <span><?= $lang['u_cargo_filter_desc'] ?></span>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span class="fa-close"></span>
                            </button>
                        </div>
                        <div class="modal-body">


                            <div class="row">
                                <button class="remove-filter mj-remove-cargo-filter">
                                    <img src="/dist/images/poster/filter-refresh.svg" alt="refresh">
                                    <span style="padding: 0 10px"><?= $lang['u_cargo_filter_remove_all'] ?></span>
                                </button>
                                <span
                                    class="text-primary mb-3 mj-fw-300 mj-font-12"><?= $lang['u_select_city_after_country_enable']; ?></span>

                                <div id="cargofirstcountry" class="col-6 mj-first-country">
                                    <div class="mb-3">
                                        <label for="cargo-origin-country"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_source_country'] ?>
                                        </label>
                                        <div class="mj-custom-select cargo-origin-country">
                                            <select class="form-select width-95 my-1 mb-3"
                                                    id="cargo-origin-country"
                                                    name="cargo-origin-country"
                                                    data-width="100%"
                                                    data-placeholder="<?= $lang['b_cargo_select_country'] ?>">
                                                <option value="all-country"><?= $lang['b_filter_by_all'] ?></option>
                                                <?php
                                                $countries = Location::getCountriesList();
                                                foreach ($countries->response as $item) {
                                                    ?>
                                                    <option
                                                        value="<?= $item->CountryId ?>"><?= $item->CountryName ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="cargodestcountry" class="col-6">
                                    <div class="mb-3">
                                        <label for="cargo-dest-country"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_dest_country'] ?>

                                        </label>
                                        <div class="mj-custom-select cargo-dest-country">
                                            <select class="form-select width-95 my-1 mb-3"
                                                    id="cargo-dest-country"
                                                    name="cargo-dest-country"
                                                    data-width="100%"
                                                    data-placeholder="<?= $lang['b_cargo_select_country'] ?>">
                                                <option value="all-country"><?= $lang['b_filter_by_all'] ?></option>
                                                <?php
                                                foreach ($countries->response as $item) {
                                                    ?>
                                                    <option
                                                        value="<?= $item->CountryId ?>"><?= $item->CountryName ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="cargoOrigin" class="col-6">
                                    <div class="mb-3">
                                        <label for="cargo-origin"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_source_city'] ?>
                                        </label>
                                        <div class="mj-custom-select cargo-origin">
                                            <select class="form-select width-95 my-1 mb-3"
                                                    id="cargo-origin"
                                                    name="cargo-origin"
                                                    data-width="100%"
                                                    data-placeholder="<?= $lang['b_cargo_select_cities'] ?>">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="cargoDestination" class="col-6">
                                    <div class="mb-3">
                                        <label for="cargo-destination"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['b_cargo_dest_city'] ?>

                                        </label>
                                        <div class="mj-custom-select cargo-destination">
                                            <select class="form-select width-95 my-1 mb-3"
                                                    id="cargo-destination"
                                                    name="cargo-destination"
                                                    data-width="100%"
                                                    data-placeholder="<?= $lang['b_cargo_select_cities'] ?>">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                            </div>


                            <div class="mt-3 mb-2"> <?= $lang['b_car_type']; ?></div>

                            <div class="mj-filter-car-type" id="car_type_area">
                                <?php foreach ($car_types as $type) { ?>
                                    <label>
                                        <input class="checkbox-type-car" type="checkbox" value="<?= $type->type_id ?>">
                                        <div class="mj-car-type-filter">
                                            <?= array_column(json_decode($type->type_name), 'value', 'slug')[$language] ?>
                                        </div>

                                    </label>
                                <?php } ?>


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

        <div class=" mj-cargo-ads-header">

            <div class=" mj-cargo-search-id">
                <div class="mj-input-filter-box-list">
                    <input id="cargo-id" class="mj-input-filter mj-fw-500 mj-font-12 px-0" type="text"
                           inputmode="decimal" placeholder="<?= $lang['u_cargo_id_placeholder'] ?>">
                </div>
            </div>


            <div class="mj-filters-btn-driver">
                <div class="mj-filter-button-modal" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                    <div class="fa-filter"></div>
                    <?= $lang['u_advanced_search']; ?>
                </div>
                <div class="fa-close remove-filter d-none"></div>
            </div>

        </div>

        <?php if (isset($_COOKIE['user-login'])) {
            ?>
            <div class="row   ">
                <div class="col-12">
                    <div
                        class="mj-filter-bell-alert <?= ($ring != [] && $ring->ring_status == 'active') ? 'active' : '' ?>">
                        <a href="javascript:void(0)" class="mj-filter-bell-texts" data-bs-toggle="modal"
                           data-bs-target="#staticBackdrop2">
                            <div class="mj-filter-bell-title">
                                <span>گوش به زنگ</span>
                                <span>برای تنظیم روی گوش به زنگ کلیک کنید</span>
                            </div>
                        </a>
                        <div class="mj-filter-switcher">
                            <input type="checkbox" id="switch"
                                   name="filter-switch" <?= ($ring != [] && $ring->ring_status == 'active') ? 'checked' : '' ?>/><label
                                for="switch">Toggle</label>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } ?>




        <div class="row">
            <div class="col-12" id="cargo-list">

            </div>
            <div class="tj-a-loader d-none">
                <lottie-player style="width: 50%" src="/dist/lottie/wallet-load.json" background="transparent" speed="1"
                               loop
                               autoplay></lottie-player>
            </div>
            <div class="col-12 d-none">
                <button type="button" data-load-more data-page="0"
                        class="mj-btn-more mj-fw-400 mj-font-13 px-4 py-2 mx-auto"><?= $lang['d_button_load_more'] ?></button>
            </div>
            <?php
            ?>
        </div>
    </main>

    <div class="modal fade" id="exampleModaliframe" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">


                <iframe id="cargo-detail" style="height: 100%; width: 100%" src="" frameborder="0"></iframe>
                <a href="javascript:void(0)" onclick="window.history.back()">
                    <div class="mj-backbtn" style="z-index: 555555 !important;">
                        <div class="fa-caret-right"></div>
                    </div>
                </a>

            </div>
        </div>
    </div>
<?php

getFooter('', true, false);