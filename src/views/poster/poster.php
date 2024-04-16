<?php
global $Settings, $lang;


use MJ\Utils\Utils;

include_once getcwd() . '/views/user/header-footer.php';


enqueueStylesheet('poster-css', '/dist/css/poster/poster.css');
enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
enqueueStylesheet('filter-css', '/dist/css/poster/filter.css');
enqueueStylesheet('fontawesome-css', '/dist/libs/fontawesome/all.css');
enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');

enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
enqueueScript('fontawesome-js', '/dist/libs/fontawesome/all.min.js');
enqueueScript('range-js', '/dist/js/poster/range.js');
enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
enqueueScript('select2-js', '/dist/libs/lottie/lottie-player.js');
enqueueScript('filter-js', '/dist/js/poster/filter.js');
enqueueScript('lazyload-js', '/dist/libs/lazyload/lazyload.js');
enqueueScript('custom-swiper-js', '/dist/js/user/slider.js');

$langCookie = 'fa_IR';
$langCookie = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';

getHeader($lang['home']);
$sliders = $Settings['u_posters_sliders'];
$gear_boxs = PosterC::getAllGearboxsFromUser();
$fuels = PosterC::getAllFuelsFromUser();
$trailer_types = Car::getAllCarsTypes('active');
if ($trailer_types->status == 200) {
    $trailer_types = $trailer_types->response;
} else {
    $trailer_types = [];
}

$puller_brands = PosterC::getAllBrandsFromTabel('active');
if ($puller_brands->status == 200) {
    $puller_brands = $puller_brands->response;
} else {
    $puller_brands = [];
}
?>
<!--  iframe-modal start -->
<style>
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

    .mj-b-slide-card img {
        height: 100%;
        width: 100%;
    }
</style>
<div class="modal fade" id="exampleModaliframe" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">


            <iframe id="poster-detail" style="height: 100%; width: 100%" src="" frameborder="0"></iframe>
            <a href="javascript:void(0)" onclick="window.history.back()">
                <div class="mj-backbtn" style="z-index: 555555 !important;">
                    <div class="fa-caret-right"></div>
                </div>
            </a>

        </div>
    </div>
</div>

<!-- current-filter-modal start -->
<div class="mj-current-filter-modal">
    <div class="modal fade" id="exampleModalcurrentfilter" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                        <?= $lang['u_details_of_the_current_filter']; ?>
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="my-filter-detail-container">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="close-current-modal-detail">
                        <?= $lang['back']; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- city Modal start -->
<div class="mj-city-select-modal-content">
    <!-- Modal -->
    <div class="modal modal fade" id="exampleModal4" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                        <?= $lang['u_poster_filter_select_city']; ?>
                    </h1>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <button class="remove-filter mj-remove-cargo-filter">
                            <img src="/dist/images/poster/filter-refresh.svg" alt="refresh">
                            <span style="padding: 0 10px">
                                <?= $lang['u_delete_city_country']; ?>
                            </span>
                        </button>
                        <span class="text-primary mb-3 mj-fw-300 mj-font-12">
                            <?= $lang['u_select_city_after_country_enable']; ?>
                        </span>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="cargo-origin-country" class="text-dark mj-fw-500 mj-font-12 mb-1">
                                    <?= $lang['b_cargo_source_country'] ?>
                                </label>
                                <div class="mj-custom-select cargo-origin-country">
                                    <select class="form-select width-95 my-1 mb-3" id="cargo-origin-country"
                                        name="cargo-origin-country" data-width="100%"
                                        data-placeholder="<?= $lang['b_cargo_select_country'] ?>">
                                        <option value="all-country">
                                            <?= $lang['b_filter_by_all'] ?>
                                        </option>
                                        <?php
                                        $countries = Location::getCountriesList();
                                        foreach ($countries->response as $item) {
                                            ?>
                                            <option value="<?= $item->CountryId ?>">
                                                <?= $item->CountryName ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="col-12">
                            <div class="mb-3">
                                <label for="cargo-origin" class="text-dark mj-fw-500 mj-font-12 mb-1">
                                    <?= $lang['p-city-filter-modal'] ?>
                                </label>
                                <div class="mj-custom-select cargo-origin">
                                    <select class="form-select width-95 my-1 mb-3" id="cargo-origin" name="cargo-origin"
                                        data-width="100%" data-placeholder="<?= $lang['b_cargo_select_cities'] ?>">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-primary accept-city-modal">
                        <?= $lang['b_accept']; ?>
                    </button>
                    <button type="button" class="btn btn-secondary close-city-modal">
                        <?= $lang['back']; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- city Modal end -->

<div class="mj-save-filter-modal">
    <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">

                    <div class="mj-save-filter-modal-header">
                        <div class="mj-title-sf">
                            <img src="/dist/images/poster/save-filter-icon.svg" alt="">
                            <span>
                                <?= $lang['u_my_filter']; ?>
                            </span>
                        </div>
                        <div class="mj-delete-save-filter">
                            <img src="/dist/images/poster/trash(filter).svg" alt="trash">
                        </div>
                    </div>

                </div>
                <div class="modal-body">
                    <div class="mj-saved-filters"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="mj-saved-filters-close" data-bs-dismiss="modal" aria-label="Close">
                        <span class="fa-arrow-right"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="mj-saved-filter-second-modal">
        <div class="modal fade" id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img src="/dist/images/poster/trash(back).svg" alt="trash">
                        <span>
                            <?= $lang['u_do_you_sure_filter']; ?>
                        </span>
                    </div>
                    <div class="modal-footer">
                        <button class="mj-back-to-first-modal" data-bs-target="#exampleModalToggle"
                            data-bs-toggle="modal">
                            <?= $lang['back']; ?>
                        </button>
                        <button class="mj-second-modal-yes" data-bs-target="#exampleModalToggle" data-bs-toggle="modal">
                            <?= $lang['a_yes']; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mj-filter-modal-main">
    <div class="modal  fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <section style=" padding-bottom: 110px">
                    <!-- filter header start-->

                    <div class="mj-filter-header pt-2">
                        <a data-bs-dismiss="modal" aria-label="Close">
                            <div class="mj-refreshh-filter-btn me-2">
                                <img src="/dist/images/poster/arrow-right.svg" alt="close filter">
                            </div>
                        </a>
                        <div class="mj-filter-save-refresh">
                            <a data-bs-toggle="modal" href="#exampleModalToggle" id="my-filters">
                                <div class="mj-save-filtr-btn">
                                    <img src="/dist/images/poster/save-filter-icon.svg" alt="filtr-icon">
                                    <span>
                                        <?= $lang['u_my_filter']; ?>
                                    </span>
                                </div>
                            </a>
                            <div class="mj-refreshh-filter-btn">
                                <img src="/dist/images/poster/filter-refresh.svg" alt="filter-refresh">
                            </div>
                        </div>
                    </div>

                    <!-- filter header end-->

                    <!-- filter tabs start-->
                    <div class="mj-filter-tabs">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-tab-id="1" id="pills-home-tab"
                                    data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab"
                                    aria-controls="pills-home" aria-selected="true">
                                    <div class="mj-tab-title">
                                        <div class="fa-circle-info"></div>
                                        <span>
                                            <?= $lang['u_info']; ?>
                                        </span>
                                    </div>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-tab-id="2" id="pills-profile-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-profile" type="button" role="tab"
                                    aria-controls="pills-profile" aria-selected="false">
                                    <div class="mj-tab-title">
                                        <div class="fa-star"></div>
                                        <span>
                                            <?= $lang['brands']; ?>
                                        </span>
                                    </div>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-tab-id="3" id="pills-contact-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-contact" type="button" role="tab"
                                    aria-controls="pills-contact" aria-selected="false">
                                    <div class="mj-tab-title">
                                        <div class="fa-sliders"></div>
                                        <span>
                                            <?= $lang['u_price']; ?>
                                        </span>
                                    </div>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-tab-id="4" id="pills-last-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-4" type="button" role="tab" aria-controls="pills-contact"
                                    aria-selected="false">
                                    <div class="mj-tab-title">
                                        <div class="fa-circle-up"></div>
                                        <span>
                                            <?= $lang['u_option']; ?>
                                        </span>
                                    </div>
                                </button>
                            </li>

                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                aria-labelledby="pills-home-tab" tabindex="0">
                                <div class="mt-2 mb-2 d-none" id="current-filter-container">
                                    <span class="mj-current-filter-title">
                                        <span>
                                            <?= $lang['u_my_filter_2']; ?>
                                        </span>
                                        <span>
                                            <?= $lang['u_currently_the_search_is_based_on_the_filter_you_selected']; ?>
                                        </span>

                                    </span>
                                    <div class="mj-city-select active">
                                        <a href="javascript:void(0);" id="detail-modal">
                                            <span id="current-filter">
                                                <?= $lang['u_my_filter_2']; ?>
                                            </span>
                                        </a>
                                        <div id="remove-cuurent-filter" class="fa-close"></div>
                                    </div>
                                </div>
                                <div class="mt-2 mb-2">
                                    <div class="mj-city-select ">
                                        <a data-bs-toggle="modal" data-bs-target="#exampleModal4">
                                            <span id="select-country-city-title">
                                                <?= $lang['u_poster_select_location_default'] ?>
                                            </span>
                                        </a>
                                        <div id="selectcityicon" class="fa-caret-left"></div>
                                    </div>
                                </div>

                                <div class="mj-filter-divider"></div>
                                <div class="mj-filter-title mb-2">
                                    <?= $lang['categories']; ?> :
                                </div>

                                <div class="mj-filter-cat">
                                    <form>

                                        <input type="radio" id="truck" name="contact" checked />
                                        <label class="me-2" for="truck">
                                            <div class="mj-filter-Cat-item mj-filter-truck">
                                                <img src="/dist/images/poster/truck(filter).svg" alt="">
                                                <span>
                                                    <?= $lang['u_truck']; ?>
                                                </span>
                                            </div>
                                        </label>

                                        <input type="radio" id="trailer" name="contact" />
                                        <label for="trailer">
                                            <div class="mj-filter-Cat-item mj-filter-trailer">
                                                <img src="/dist/images/poster/trailer(filter).svg" alt="">
                                                <span>
                                                    <?= $lang['u_trailer']; ?>
                                                </span>
                                            </div>
                                        </label>
                                    </form>


                                </div>

                                <div class="mj-filter-divider"></div>
                                <div id="mjgear" class="mj-filter-title mb-2">
                                    <?= $lang['a_gearboxs']; ?> :
                                </div>
                                <div class="mj-filter-gear">

                                    <?php foreach ($gear_boxs as $item) {
                                        ?>
                                        <div class="mj-filter-gear-item mj-hand-gear" data-gear-box-id="<?= $item->id ?>"
                                            data-gear-box-name="<?= $item->name ?>">
                                            <img src="<?= Utils::fileExist($item->image, BOX_EMPTY) ?>" alt="">
                                            <span>
                                                <?= $item->name ?>
                                            </span>
                                        </div>
                                        <?php
                                    } ?>
                                </div>
                                <div id="devider1" class="mj-filter-divider"></div>
                                <div id="mjgas" class="mj-filter-title mb-2">
                                    <?= $lang['u_type_fuel']; ?> :
                                </div>
                                <div class="mj-filter-gas">
                                    <?php foreach ($fuels as $item) {
                                        ?>
                                        <div class="mj-filter-gas-item mj-gas-disel" data-fuel-id="<?= $item->id ?>"
                                            data-fuel-name="<?= $item->name ?>">
                                            <img src="<?= Utils::fileExist($item->image, BOX_EMPTY) ?>" alt="">
                                            <span>
                                                <?= $item->name ?>
                                            </span>
                                        </div>
                                        <?php
                                    } ?>
                                </div>
                                <div id="filter-trailer-type" class="mj-filter-title mb-2 d-none ">
                                    <?= $lang['a_type_trailer']; ?> :
                                </div>
                                <div id="selected-trailer-type"></div>
                                <div id="selected-trailer-subtype"></div>

                                <div class="mj-trailer-type d-none">
                                    <?php
                                    foreach ($trailer_types as $item) {
                                        ?>
                                        <div class="mj-trailer-item" data-type-id="<?= $item->type_id ?>"
                                            data-name="<?= array_column(json_decode($item->type_name), 'value', 'slug')[$langCookie] ?>">
                                            <img src="<?= Utils::fileExist($item->type_icon, BOX_EMPTY) ?>" alt="snow">
                                            <span>
                                                <?= array_column(json_decode($item->type_name), 'value', 'slug')[$langCookie] ?>
                                            </span>
                                        </div>
                                    <?php } ?>

                                </div>
                                <div class="mj-trailer-subtype">
                                </div>

                            </div>
                            <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                aria-labelledby="pills-profile-tab" tabindex="0">
                                <div class="mj-filter-brands">
                                    <div class="mj-brand-search">
                                        <input type="text" id="filter-brand-search"
                                            placeholder="<?= $lang['u_search_brand_2']; ?>">
                                        <div class="fa-search"></div>
                                    </div>
                                    <div id="mj-selected-brand-list" class="mj-selected-brand-list"></div>
                                    <div class="mj-brands-filter-list">
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-contact" role="tabpanel"
                                aria-labelledby="pills-contact-tab" tabindex="0">

                                <div class="mj-filter-ranges">
                                    <div class="mj-price-filter-range">
                                        <div class="mj-filter-title mb-2">
                                            <?= $lang['u_price_range']; ?> :
                                        </div>
                                        <div class="range-result mb-3">
                                            <div class="range-from">
                                                <span>
                                                    <?= $lang['u_from']; ?>
                                                </span>
                                                <span id="rangefrom">0</span>
                                            </div>
                                            <div class="range-to">
                                                <span>
                                                    <?= $lang['u_to']; ?>
                                                </span>
                                                <span id="rangeto">100,000,000,000</span>
                                            </div>
                                        </div>
                                        <div class="slider">
                                            <div class="progress"></div>
                                        </div>
                                        <div dir="ltr" class="range-input">
                                            <input type="range" class="range-min" step="25000000" min="0"
                                                max="100000000000" value="0">
                                            <input type="range" class="range-max" step="25000000" min="0"
                                                max="100000000000" value="100000000000">
                                        </div>
                                    </div>
                                    <div class="mj-filter-divider"></div>
                                    <div class="mj-payment-type-switch">
                                        <div class="text-center">
                                            <span class="d-block mb-1">
                                                <?= $lang['a_cash_2']; ?>
                                            </span>
                                            <label class="switch">
                                                <input type="checkbox" id="cash" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <div class="text-center">
                                            <span class="d-block mb-1">
                                                <?= $lang['a_installment']; ?>
                                            </span>
                                            <label class="switch">
                                                <input type="checkbox" id="installments" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <div class="text-center">
                                            <span class="d-block mb-1">
                                                <?= $lang['a_leasing']; ?>
                                            </span>
                                            <label class="switch">
                                                <input type="checkbox" id="leasing" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mj-filter-divider"></div>
                                    <div class="mj-use-filter-range">
                                        <div class="mj-filter-title mb-2">
                                            <?= $lang['u_run_worked']; ?> :
                                            <span style="font-size: 11px;font-weight: 300">
                                                (
                                                <?= $lang['u_based_on_km']; ?>)
                                            </span>
                                        </div>
                                        <div class="range-result mb-3">
                                            <div class="range-from">
                                                <span>
                                                    <?= $lang['u_from']; ?>
                                                </span>
                                                <span id="rangefrom">0</span>
                                            </div>
                                            <div class="range-to">
                                                <span>
                                                    <?= $lang['u_to']; ?>
                                                </span>
                                                <span id="rangeto">1,000,000</span>
                                            </div>
                                        </div>
                                        <div class="slider">
                                            <div class="progress"></div>
                                        </div>
                                        <div dir="ltr" class="range-input" data-gap="100">
                                            <input type="range" class="range-min" step="1000" min="0" max="1000000"
                                                value="0">
                                            <input type="range" class="range-max" step="1000" min="0" max="1000000"
                                                value="1000000">
                                        </div>
                                    </div>
                                    <div class="mj-filter-divider"></div>
                                    <div class="mj-filter-year-type mb-3">
                                        <form>

                                            <input type="radio" id="shamsi" name="shamsi" checked />
                                            <label for="shamsi" class="me-2">
                                                <span>
                                                    <?= $lang['u_jalali']; ?>
                                                </span>
                                            </label>

                                            <input type="radio" id="miladi" name="shamsi" />
                                            <label for="miladi">
                                                <span>
                                                    <?= $lang['u_gregorian']; ?>
                                                </span>
                                            </label>

                                        </form>
                                    </div>
                                    <div class="mj-year-filter-range">
                                        <div class="mj-filter-title mb-2">
                                            <?= $lang['a_built_year']; ?> : <span
                                                style="font-size: 11px;font-weight: 300">(
                                                <?= $lang['u_jalali']; ?>)
                                            </span>
                                        </div>
                                        <div class="range-result mb-3">
                                            <div class="range-from">
                                                <span>
                                                    <?= $lang['u_from']; ?>
                                                </span>
                                                <span id="rangefrom">1375</span>
                                            </div>
                                            <div class="range-to">
                                                <span>
                                                    <?= $lang['u_to']; ?>
                                                </span>
                                                <span id="rangeto">1402</span>
                                            </div>
                                        </div>
                                        <div class="slider">
                                            <div class="progress"></div>
                                        </div>
                                        <div dir="ltr" class="range-input">
                                            <input type="range" class="range-min" min="1375" max="1402" value="1375">
                                            <input type="range" class="range-max" min="1375" max="1402" value="1402">
                                        </div>
                                    </div>
                                    <div class="mj-filter-divider"></div>
                                </div>

                            </div>
                            <div class="tab-pane fade" id="pills-4" role="tabpanel" aria-labelledby="pills-4-tab"
                                tabindex="0">
                                <div class="mj-filter-options">
                                    <div class="mj-option-search">
                                        <input type="text" id="filter-option-search"
                                            placeholder="<?= $lang['u_search_options']; ?>">
                                        <div class="fa-search"></div>
                                    </div>
                                    <div class="mj-filter-option-list"></div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- filter tabs end-->
                    <div class="mj-filter-footer ">
                        <div class="mj-filter-footer-btns-card">
                            <a href="javascript:void(0);" class="mj-back-tab d-none">
                                <div class="mj-filter-search-btn-item2">
                                    <span>
                                        <div class="fa-caret-right"></div>
                                    </span>
                                </div>
                            </a>

                            <a href="javascript:void(0);" class="mj-next-tab">
                                <div class="mj-filter-search-btn-item">
                                    <span>
                                        <?= $lang['next'] ?>
                                    </span>
                                </div>
                            </a>

                            <a href="javascript:void(0)" class="mj-view-filter-btn d-none">
                                <div class="mj-filter-search-btn-item">
                                    <span>
                                        <?= $lang['view']; ?>
                                    </span>
                                </div>
                            </a>

                            <a href="javascript:void(0)" id="save-filter">
                                <div class="mj-filter-search-btn-item3">
                                    <span>
                                        <?= $lang['u_save_filter']; ?>
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>


<main class="container" style="padding-bottom: 100px !important; padding-top: 117px !important;">

    <!-- filter and city btns-->
    <div class="container mt-3 mb-2">
        <div class="row">
            <div class="mj-p-first-page-btns">
                <a href="javascript:void(0)" id="filter-btn">
                    <div class="mj-p-filter-btn2" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <div id="super-search-filter" class="fa-search"></div>
                        <div style="font-size: 12px;margin-right: 5px;">
                            <?= $lang['u_advanced_search']; ?>
                        </div>


                    </div>
                </a>
                <a href="/poster/dashboard" id="dashboard-btn">
                    <div class="mj-p-filter-btn">
                        <img style="width: 15px; margin-left: 10px; margin-right: unset"
                            src="/dist/images/poster/user.svg" alt="">

                        <div style="font-size: 12px">
                            <?= $lang['u_my_posters']; ?>
                        </div>

                    </div>
                </a>


            </div>
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
    <!-- end silder-->
    <!-- filter and city btns-->
    <div class="mj-poster-home-items">

    </div>


    <!-- filter and city item-->

</main>
<?php
getFooter('', false);