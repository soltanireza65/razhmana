<?php

global $lang;

if (User::userIsLoggedIn()) {
    User::checkUserSlugAccess();
    include_once 'header-footer.php';

    enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');
    enqueueStylesheet('FA-css', '/dist/libs/fontawesome/all.css');
    enqueueScript('FA-js', '/dist/libs/fontawesome/all.min.js');
    enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
    enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    enqueueScript('freight-price-init-js', '/dist/js/businessman/freight-price.init.js');

    getHeader($lang['b_title_getprice']);

    ?>

    <main>
        <div class="container">



            <div class="row">
                <div class="col-12">
                    <div class="mj-card">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-justified mj-header-tabs text-nowrap" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a href="#ground-transportation" class="nav-link mj-header-tabs-link active"
                                       id="ground-transportation-tab" data-bs-toggle="tab" role="tab"
                                       aria-controls="ground-transportation" aria-selected="true">
                                        <img src="/dist/images/icons/ground-transportation.svg"
                                             class="mj-header-tabs-icon" alt="ground-transportation"/>
                                        <?= $lang['ground_transportation'] ?>
                                    </a>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <a href="#air-transportation" class="nav-link mj-header-tabs-link"
                                       id="air-transportation-tab" data-bs-toggle="tab" role="tab"
                                       aria-controls="air-transportation" aria-selected="false">
                                        <img src="/dist/images/icons/air-transportation.svg" class="mj-header-tabs-icon"
                                             alt="air-transportation"/>
                                        <?= $lang['air_transportation'] ?>
                                    </a>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <a href="#maritime-transportation" class="nav-link mj-header-tabs-link"
                                       id="maritime-transportation-tab" data-bs-toggle="tab" role="tab"
                                       aria-controls="maritime-transportation" aria-selected="false">
                                        <img src="/dist/images/icons/maritime-transportation.svg"
                                             class="mj-header-tabs-icon" alt="maritime-transportation"/>
                                        <?= $lang['maritime_transportation'] ?>
                                    </a>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <a href="#railroad-transportation" class="nav-link mj-header-tabs-link"
                                       id="railroad-transportation-tab" data-bs-toggle="tab" role="tab"
                                       aria-controls="railroad-transportation" aria-selected="false">
                                        <img src="/dist/images/icons/railroad-transportation.svg"
                                             class="mj-header-tabs-icon" alt="railroad-transportation"/>
                                        <?= $lang['railroad_transportation'] ?>
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="ground-transportation" role="tabpanel"
                                     aria-labelledby="ground-transportation-tab">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="origin-city"
                                                       class="text-dark mj-fw-500 mj-font-12 mb-1"><?= $lang['b_cargo_source_city'] ?></label>
                                                <div class="mj-custom-select origin-city">
                                                    <select id="origin-city" name="origin-city" data-width="100%">
                                                        <option value="-1"
                                                                selected><?= $lang['b_cargo_select_cities'] ?></option>
                                                        <?php
                                                        $cities = Businessman::getCities('city');
                                                        foreach ($cities->response as $item) {
                                                            ?>
                                                            <option value="<?= $item->CityId ?>"><?= $item->CityName.' - '  .$item->CityNameEN ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="destination-city"
                                                       class="text-dark mj-fw-500 mj-font-12 mb-1"><?= $lang['b_cargo_dest_city'] ?></label>
                                                <div class="mj-custom-select destination-city">
                                                    <select id="destination-city" name="destination-city"
                                                            data-width="100%">
                                                        <option value="-1"
                                                                selected><?= $lang['b_cargo_select_cities'] ?></option>
                                                        <?php
                                                        foreach ($cities->response as $item) {
                                                            ?>
                                                            <option value="<?= $item->CityId ?>"><?= $item->CityName .' - '  .$item->CityNameEN ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="category"
                                                       class="text-dark mj-fw-500 mj-font-12 mb-1"><?= $lang['b_cargo_category'] ?></label>
                                                <div class="mj-custom-select category">
                                                    <select id="category" name="category" data-width="100%">
                                                        <option value="-1"
                                                                selected><?= $lang['b_cargo_category_select'] ?></option>
                                                        <?php
                                                        $cargoTypes = Businessman::getCargoTypes();
                                                        foreach ($cargoTypes->response as $item) {
                                                            ?>
                                                            <option value="<?= $item->CategoryId ?>"><?= $item->CategoryName ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="currency"
                                                       class="text-dark mj-fw-500 mj-font-12 mb-1"><?= $lang['b_currency_type'] ?></label>
                                                <div class="mj-custom-select currency">
                                                    <select id="currency" name="currency" data-width="100%">
                                                        <option value="-1"
                                                                selected><?= $lang['b_currency_type_select'] ?></option>
                                                        <?php
                                                        $currencies = Driver::getCurrencyList();
                                                        foreach ($currencies->response as $item) {
                                                            ?>
                                                            <option value="<?= $item->CurrencyId ?>"><?= $item->CurrencyName ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div>
                                                <button type="button" id="ground-inquiry" name="ground-inquiry"
                                                        class="mj-btn-more w-100 py-2">
                                                    <?= $lang['b_call_for_price'] ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="air-transportation" role="tabpanel"
                                     aria-labelledby="air-transportation-tab">
                                    <div class="text-center">
                                        <lottie-player src="/dist/lottie/airplane.json" class="mx-auto"
                                                       style="max-width: 400px;" speed="1" loop
                                                       autoplay></lottie-player>

                                        <h4 class="mj-fw-500 mj-font-13"><?= $lang['coming_soon'] ?></h4>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="maritime-transportation" role="tabpanel"
                                     aria-labelledby="maritime-transportation-tab">
                                    <div class="text-center">
                                        <lottie-player src="/dist/lottie/ship.json" class="mx-auto"
                                                       style="max-width: 400px;" speed="1" loop
                                                       autoplay></lottie-player>

                                        <h4 class="mj-fw-500 mj-font-13"><?= $lang['coming_soon'] ?></h4>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="railroad-transportation" role="tabpanel"
                                     aria-labelledby="railroad-transportation-tab">
                                    <div class="text-center">
                                        <lottie-player src="/dist/lottie/train.json" class="mx-auto"
                                                       style="max-width: 400px;" speed="1" loop
                                                       autoplay></lottie-player>

                                        <h4 class="mj-fw-500 mj-font-13"><?= $lang['coming_soon'] ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <template id="template-result">
                <div class="modal fade" id="modal-result" role="dialog" tabindex="-1" data-bs-backdrop="static"
                     data-bs-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= $lang['freight_result'] ?></h5>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="my-3">
                                    #CONTENT#
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </main>

    <?php
    getFooter('', false);

} else {
    header('location: /login');
}