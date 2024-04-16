<?php
global $lang;

use MJ\Security\Security;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();

    $posterId = $_REQUEST['posterId'];
    $data = [];
    $data2 = [];
    $myType = '';
    $flagHref = true;

    $result = Poster::getPosterById($posterId);

    if ($result->status == 200) {
        $data = $result->response;
        if ($data->poster_type == "truck") {
            $myType = 'truck';
        } elseif ($data->poster_type == "trailer") {
            $myType = 'trailer';
        }

        $data2 = PosterC::getBrandsModalInfoByModelId($data->model_id);

        if (empty($data2)) {
            $flagHref = false;
            header('Location: /poster/my-list');
        }

    }


    if (empty($data) || empty($myType)) {
        $flagHref = false;
        header('Location: /poster/my-list');
    }


    $posterProperties = Poster::getPosterPropertiesByPosterId($data->poster_id);

    $Gearboxs = PosterC::getAllGearboxsFromUser();
    $fuels = PosterC::getAllFuelsFromUser();
    $currencies = Driver::getCurrencyList();
    $carTypes = Driver::getCarTypes();
    $countries = Location::getCountriesListByStatus('poster');
    $myCountry = Location::getCountryByCityId($data->city_id);

    if ($flagHref) {

        include_once getcwd() . '/views/user/header-footer.php';

        enqueueStylesheet('poster-css', '/dist/css/poster/poster.css');
        enqueueStylesheet('fontawesome-css', '/dist/libs/fontawesome/all.css');
        if ($user->UserAuthStatus == 'accepted') {
            enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');
            enqueueStylesheet('dropzone-css', '/dist/libs/dropzone/min/dropzone.min.css');
            enqueueStylesheet('pickr-master', '/dist/libs/pickr-master/nano.min.css');
        }
        enqueueStylesheet('add-css', '/dist/css/poster/add.css');

        // Load Script In Footer
        if ($user->UserAuthStatus == 'accepted') {
            enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
            enqueueScript('dropzone-js', '/dist/libs/dropzone/min/dropzone.min.js');
            enqueueScript('pickr-master', '/dist/libs/pickr-master/pickr.es5.min.js');
            enqueueScript('edit-js', '/dist/js/poster/edit.js');
        } else {
            enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
        }

        getHeader($lang['u_edit_poster'], false);


        $language  =  $_COOKIE['language'] ? $_COOKIE['language']   : 'fa_IR';
        $title_column_name = 'poster_title_'.$language;
        ?>
        <style>
            body {
                background-color: #FFFFFF !important;
            }

            .mj-a-radio-poster input[type='radio'] {
                display: none;
            }

            .mj-a-radio-poster label:before {
                content: " ";
                display: inline-block;
                margin: 0 5px 0 5px;
                width: 15px;
                height: 15px;
                border-radius: 11px;
                border: 1px solid #404040;
                background-color: transparent;
            }

            .mj-a-radio-poster label {
                position: relative;
                display: flex;
                align-items: center;
            }

            .mj-a-radio-poster input[type='radio']:checked + label:before {
                border-radius: 11px;
                background-color: var(--primary);
                border: 1px solid var(--primary);
            }
        </style>
        <header class="mj-a-back-poster-header">
            <div class="d-flex justify-content-start align-items-center ">
                <a href="javascript:void(0);" class="mj-app-header-btn mx-2" onclick="history.back();">
                    <img src="/dist/images/poster/arrow-right.svg" alt="back">
                </a>
                <span><?= $lang['u_edit_poster']; ?></span>
            </div>
        </header>
        <?php if ($user->UserAuthStatus == 'accepted') { ?>
            <main class="container" style="padding-top: 35px !important; ">

                <!-- Start General 1 -->
                <div class="row" id="first-item-one-poster">
                    <h3><?= $lang['u_select_type_poster']; ?></h3>
                    <p><?= $lang['u_select_type_desc_poster_edit']; ?></p>

                    <!-- poster type-->
                    <div class="col-12">
                        <div class="row g-2">

                            <div class="col-6 col-sm-6 mj-a-type-poster">
                                <input type="radio"
                                       id="type-truck"
                                       data-tj-id="truck"
                                       name="type-poster"
                                    <?= ($myType == "truck") ? "checked" : "disabled"; ?>>
                                <label for="type-truck">
                                    <img class="py-1" src="/dist/images/poster/truck.svg" alt="">
                                    <span class="py-1"><?= $lang['u_truck']; ?></span>
                                </label>
                            </div>

                            <div class="col-6 col-sm-6 mj-a-type-poster">
                                <input type="radio"
                                       id="type-trailer"
                                       data-tj-id="trailer"
                                       name="type-poster"
                                    <?= ($myType == "trailer") ? "checked" : "disabled"; ?>>
                                <label for="type-trailer">
                                    <img class="py-1" src="/dist/images/poster/trailer.svg" alt="">
                                    <span class="py-1"><?= $lang['u_trailer']; ?></span>
                                </label>
                            </div>

                        </div>
                    </div>
                    <!-- poster type-->
                    <div class="col-12 mt-4">
                        <div class="mj-a-price-poster d-flex mt-2">
                            <input type="text"
                                   class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                   id="ads-title"
                                   name="ads-title"
                                   placeholder="<?= $lang['b-ads-title'] ?>"
                                   value="<?= $data->$title_column_name ?>"
                                   style="min-height: 38px;">
                        </div>
                    </div>
                    <!-- poster status-->
                    <div class="col-12 mt-4" <?= ($myType != 'truck') ? 'style="display: none;"' : ''; ?>>
                        <div class="row mj-a-status-parent-poster mx-0">

                            <div class="col-4 col-sm-4 mj-a-status-poster text-center ">
                                <input type="radio"
                                       id="status-new"
                                       data-tj-id="new"
                                       name="status-poster"
                                    <?php
                                    if ($myType == "truck") {
                                        if ($data->poster_type_status == "new") {
                                            echo "checked";
                                        }
                                    } else {
                                        echo "checked";
                                    } ?>>
                                <label for="status-new">
                                    <?= $lang['u_zero']; ?>
                                </label>
                            </div>

                            <div class="col-4 col-sm-4 mj-a-status-poster text-center">
                                <input type="radio"
                                       id="status-stock"
                                       data-tj-id="stock"
                                       name="status-poster"
                                    <?php
                                    if ($myType == "truck") {
                                        if ($data->poster_type_status == "stock") {
                                            echo "checked";
                                        }
                                    } ?>>
                                <label for="status-stock">
                                    <?= $lang['u_worked']; ?>
                                </label>
                            </div>
                            <div class="col-4 col-sm-4 mj-a-status-poster text-center">
                                <input type="radio"
                                       id="status-order"
                                       data-tj-id="order"
                                       name="status-poster"
                                    <?php
                                    if ($myType == "truck") {
                                        if ($data->poster_type_status == "order") {
                                            echo "checked";
                                        }
                                    } ?>>
                                <label for="status-order">
                                    <?= $lang['u_remittance']; ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- poster status-->

                    <div class="col-12">
                        <div class="mj-a-back-poster">
                            <div class="mj-a-close-poster">
                                <a href="javascript:void(0);"
                                   onclick="history.back();">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            </div>
                            <div class="mj-a-check-poster">
                                <button class="" id="btn-first-next-item-one-poster">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End General 1-->


                <!--Start truck-->
                <div class="row mj-a-d-none" id="second-item-one-poster">

                    <div class="col-12">
                        <div class="input-group mj-a-input-group mb-2">
                            <div id="brand-truck"
                                 data-mj-value="<?= ($myType == "truck") ? $data2->brandId : ''; ?>"
                                 class="form-control">
                                <?php
                                if ($myType == "truck") {
                                    echo $data2->brandName;
                                } else {
                                    echo '<span class="mj-a-black">' . $lang['u_select_brand'] . '</span>';
                                } ?>
                            </div>
                            <div class="mj-a-caret-left fa-caret-left"></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="input-group mj-a-input-group mb-2">
                            <div id="model-truck"
                                 data-mj-value="<?= ($myType == "truck") ? $data2->modelId : ''; ?>"
                                 class="form-control">
                                <?php
                                if ($myType == "truck") {
                                    echo $data2->modelName;
                                } else {
                                    echo '<span class="mj-a-black">' . $lang['u_select_model'] . '</span>';
                                } ?>
                            </div>
                            <div class="mj-a-caret-left fa-caret-left"></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mj-a-select-parent mt-2">
                            <div class="mj-a-select-child-4">
                                <select class="form-select mj-a-select"
                                        id="gearbox-truck"
                                        data-width="100%"
                                        data-placeholder="<?= $lang['u_select_gearboxs']; ?>">

                                    <?php
                                    if ($myType != "truck") {
                                        echo '<option value=""></option>';
                                    }

                                    if (!empty($Gearboxs)) {
                                        foreach ($Gearboxs as $loop) {
                                            ?>
                                            <option <?= ($loop->id == $data->gearbox_id) ? 'selected' : ''; ?>
                                                    value="<?= $loop->id; ?>"><?= $loop->name; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mj-a-select-parent mt-2">
                            <div class="mj-a-select-child-5">
                                <select class="form-select mj-a-select"
                                        id="fuel-truck"
                                        data-width="100%"
                                        data-placeholder="<?= $lang['u_type_fuel']; ?>">

                                    <?php
                                    if ($myType != "truck") {
                                        echo '<option value=""></option>';
                                    }

                                    if (!empty($fuels)) {
                                        foreach ($fuels as $loop) {
                                            ?>
                                            <option <?= ($loop->id == $data->fuel_id) ? 'selected' : ''; ?>
                                                    value="<?= $loop->id; ?>"><?= $loop->name; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="row mt-2">
                            <div class="col-9 ">
                                <div class="input-group mj-a-input-group">
                                    <div id="color-input-truck"
                                         data-mj-value="<?php
                                         if ($myType == "truck") {
                                             echo $data->poster_color_out;
                                         } else {
                                             echo 'var(--primary)';
                                         }
                                         ?>"
                                         class="form-control"><span
                                                class="mj-a-black"><?= $lang['u_select_color']; ?></span>
                                    </div>
                                    <div class="mj-a-caret-left fa-caret-left"></div>
                                </div>
                            </div>
                            <div class="col-3 color-parent-truck">
                                <div id="color-truck"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-between mt-3">
                            <div>
                                <label for="built-truck" class="mj-a-label-range"> <?= $lang['u_built_year']; ?></label>
                                <div class="mj-add-year-select mt-2">
                                    <div class="mj-as-radio-poster ">
                                        <input id="jalali" type="radio"
                                               name="u-jalali-gregorian"
                                            <?php if ($myType == "truck") {
                                                if (intval($data->poster_built) < 1500) {
                                                    echo 'checked';
                                                }
                                            } else {
                                                echo 'checked';
                                            }
                                            ?>>
                                        <label for="jalali"><?= $lang['u_jalali']; ?></label>
                                    </div>
                                    <div class="mj-as-radio-poster ms-2">
                                        <input id="gregorian"
                                               type="radio"
                                               name="u-jalali-gregorian"
                                            <?php if ($myType == "truck") {
                                                if (intval($data->poster_built) > 1500) {
                                                    echo 'checked';
                                                }
                                            }
                                            ?>>
                                        <label for="gregorian"><?= $lang['u_gregorian']; ?></label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div style="font-size: 12px;margin-block: 10px ">
                            <?= $lang['u_slider_right_to_left']; ?>
                        </div>
                        <span id="built-text-truck"
                              class="mj-a-text-range"><?= ($myType == "truck") ? $data->poster_built : '1395'; ?></span>
                        <input dir="ltr"
                               class="form-range mt-3"
                               id="built-truck"
                               type="range"
                               value="<?= ($myType == "truck") ? ($data->poster_built > 1500) ? $data->poster_built - 621 : $data->poster_built : '1395'; ?>"
                               step="1"
                               name="built-truck"
                               min="1350"
                               max="1402">

                    </div>

                    <div class="col-12">
                        <div class="mj-a-back-poster">
                            <div class="mj-a-close-poster">
                                <button id="btn-second-back-item-one-poster">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="mj-a-check-poster">
                                <button id="btn-second-next-item-one-poster">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row mj-a-d-none" id="third-item-one-poster">


                    <div class="col-12">
                        <div class="mj-a-price-poster d-flex mt-2">
                            <input type="text"
                                   inputmode="numeric"
                                   class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                   id="run-truck"
                                   name="run-truck"
                                   lang="en"
                                   max="1000000"
                                   min="0"
                                   value="<?= ($myType == "truck") ? $data->poster_used : null; ?>"
                                   placeholder="<?= $lang['u_run_worked']; ?>"
                                   style="min-height: 38px;">
                            <span class="input-group-text mj-a-span-label"><?= $lang['u_km']; ?></span>
                        </div>
                    </div>


                    <div class="col-12">
                        <div class="mj-a-price-poster d-flex mt-2">
                            <input type="text"
                                   inputmode="numeric"
                                   class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                   id="price-truck"
                                   name="price-truck"
                                   value="<?= ($myType == "truck") ? $data->poster_price : null; ?>"
                                   placeholder="<?= $lang['b_cargo_recommended_price'] ?>"
                                   style="min-height: 38px;">
                            <select class="mj-a-select-currency-poster"
                                    id="currency-truck"
                                    name="currency-truck">
                                <option value="-1" selected><?= $lang['b_currency_type'] ?></option>
                                <?php
                                foreach ($currencies->response as $item) {
                                    ?>
                                    <option <?= ($myType == "truck" && $data->currency_id == $item->CurrencyId) ? "selected" : null; ?>
                                            value="<?= $item->CurrencyId ?>"><?= $item->CurrencyName ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <small class="d-block mj-font-11 mb-1 text-danger"
                               style="padding: 4px 16px 4px 4px"><?= $lang['b_price_zero_cargo_desc'] ?></small>
                    </div>


                    <div class="col-12">
                        <div class="d-flex align-items-center mt-3">
                            <label class="mj-a-label" for="cash-truck"><?= $lang['u_cash_2']; ?></label>
                            <input type="checkbox"
                                   class="mj-a-switch"
                                   id="cash-truck"
                                   data-tj-check-2="1"
                                <?= ($myType == "truck" && $data->poster_cash == "yes") ? "checked" : null; ?>>
                        </div>
                    </div>


                    <div class="col-12">
                        <div class="d-flex align-items-center mb-2 mt-3">
                            <label class="mj-a-label" for="leasing-truck"><?= $lang['u_leasing']; ?></label>
                            <input type="checkbox"
                                   class="mj-a-switch"
                                   id="leasing-truck"
                                   data-tj-check-2="2"
                                <?= ($myType == "truck" && $data->poster_leasing == "yes") ? "checked" : null; ?>>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex align-items-center mb-2 mt-3">
                            <label class="mj-a-label" for="installment-truck"><?= $lang['u_installment']; ?></label>
                            <input type="checkbox"
                                   class="mj-a-switch"
                                   id="installment-truck"
                                   data-tj-check-2="3"
                                <?= ($myType == "truck" && $data->poster_installments == "yes") ? "checked" : null; ?>>
                        </div>
                    </div>


                    <div class="col-12">
                        <div class="mj-a-back-poster">
                            <div class="mj-a-close-poster">
                                <button id="btn-third-back-item-one-poster">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="mj-a-check-poster">
                                <button id="btn-third-next-item-one-poster">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                <!--End truck-->


                <!--Start trailer-->
                <div class="row mj-a-d-none" id="second-item-two-poster">
<!--                    todo-->
                    <div class="col-12">
                        <div class="input-group mj-a-input-group mb-2">
                            <div id="brand-trailer"
                                 data-mj-value="<?= ($myType == "trailer") ? $data2->brandId : ''; ?>"
                                 class="form-control"><?php
                                if ($myType == "trailer") {
                                    echo $data2->brandName;
                                } else {
                                    echo '<span class="mj-a-black">' . $lang['u_select_brand'] . '</span>';
                                } ?></div>
                            <div class="mj-a-caret-left fa-caret-left"></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="input-group mj-a-input-group mb-2">
                            <div id="model-b-trailer"
                                 data-mj-value="<?= ($myType == "trailer") ? $data2->modelId : ''; ?>"
                                 class="form-control"><?php
                                if ($myType == "trailer") {
                                    echo $data2->modelName;
                                } else {
                                    echo '<span class="mj-a-black">' . $lang['u_select_model'] . '</span>';
                                } ?></div>
                            <div class="mj-a-caret-left fa-caret-left"></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mj-a-select-parent mt-2">
                            <div class="mj-a-select-child-7">
                                <select class="form-select mj-a-select"
                                        id="model-trailer"
                                        data-width="100%"
                                        data-placeholder="<?= $lang['u_select_model']; ?>">

                                    <?php
                                    if ($myType == "truck") {
                                        echo '<option value=""></option>';
                                    }

                                    foreach ($carTypes->response as $loop) {
                                        ?>
                                        <option <?= ($loop->TypeId == $data->trailer_id) ? 'selected' : ''; ?>
                                                value="<?= $loop->TypeId ?>"><?= $loop->TypeName ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="col-12">
                        <div class="mj-a-select-parent mt-2">
                            <div class="mj-a-select-child-1">
                                <select class="form-select mj-a-select"
                                        id="axis-trailer"
                                        data-width="100%"
                                        data-placeholder="<?= $lang['u_axis_count']; ?>">
                                    <?php
                                    if ($myType == "truck") {
                                        echo '<option value=""></option>';
                                    }
                                    ?>
                                    <option <?= ($data->poster_axis == 1) ? 'selected' : ''; ?> value="1">1</option>
                                    <option <?= ($data->poster_axis == 2) ? 'selected' : ''; ?> value="2">2</option>
                                    <option <?= ($data->poster_axis == 3) ? 'selected' : ''; ?> value="3">3</option>
                                    <option <?= ($data->poster_axis == 4) ? 'selected' : ''; ?> value="4">4</option>
                                    <option <?= ($data->poster_axis == 5) ? 'selected' : ''; ?> value="5">5</option>
                                    <option <?= ($data->poster_axis == 6) ? 'selected' : ''; ?> value="6">6</option>
                                    <option <?= ($data->poster_axis == 7) ? 'selected' : ''; ?> value="7">7</option>
                                    <option <?= ($data->poster_axis == 8) ? 'selected' : ''; ?> value="8">8</option>
                                    <option <?= ($data->poster_axis == 9) ? 'selected' : ''; ?> value="9">9</option>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mj-a-price-poster d-flex mt-2">
                            <input type="text"
                                   inputmode="numeric"
                                   class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                   id="price-trailer"
                                   name="price-trailer"
                                   value="<?= ($myType != "truck") ? $data->poster_price : null; ?>"
                                   placeholder="<?= $lang['b_cargo_recommended_price'] ?>"
                                   style="min-height: 38px;">
                            <select class="mj-a-select-currency-poster"
                                    id="currency-trailer"
                                    name="currency-trailer">
                                <option value="-1" selected><?= $lang['b_currency_type'] ?></option>
                                <?php
                                foreach ($currencies->response as $item) {
                                    ?>
                                    <option <?= ($myType != "truck" && $data->currency_id == $item->CurrencyId) ? "selected" : null; ?>
                                            value="<?= $item->CurrencyId ?>"><?= $item->CurrencyName ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <small class="d-block mj-font-11 mb-1 text-danger"
                               style="padding: 4px 16px 4px 4px"><?= $lang['b_price_zero_cargo_desc'] ?></small>
                    </div>

                    <div class="col-12">
                        <div class="d-flex align-items-center mb-2 mt-3">
                            <label class="mj-a-label" for="cash-trailer"><?= $lang['u_cash_2'] ?></label>
                            <input type="checkbox"
                                   class="mj-a-switch"
                                   id="cash-trailer"
                                   data-tj-check-1="1"
                                <?= ($myType != "truck" && $data->poster_cash == "yes") ? "checked" : null; ?>>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex align-items-center mb-2 mt-3">
                            <label class="mj-a-label" for="leasing-trailer"><?= $lang['u_leasing'] ?></label>
                            <input type="checkbox"
                                   class="mj-a-switch"
                                   id="leasing-trailer"
                                   data-tj-check-1="2"
                                <?= ($myType != "truck" && $data->poster_leasing == "yes") ? "checked" : null; ?>>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex align-items-center mb-2 mt-3">
                            <label class="mj-a-label" for="installment-trailer"><?= $lang['u_installment'] ?></label>
                            <input type="checkbox"
                                   class="mj-a-switch"
                                   id="installment-trailer"
                                   data-tj-check-1="3"
                                <?= ($myType != "truck" && $data->poster_installments == "yes") ? "checked" : null; ?>>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mj-a-back-poster">
                            <div class="mj-a-close-poster">
                                <button id="btn-second-back-item-two-poster">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="mj-a-check-poster">
                                <button id="btn-second-next-item-two-poster">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                <!--End trailer-->


                <!-- Start General 2-->
                <div class="row mj-a-d-none" id="fourth-item-one-poster">

                    <div class="col-12">
                        <div class="mj-filter-option-list" data-tj-property="<?= $posterProperties; ?>">
                            <!-- Add property html -->

                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mj-a-back-poster">

                            <div class="mj-a-close-poster">
                                <button id="btn-fourth-back-item-one-poster">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="mj-a-check-poster">
                                <button id="btn-fourth-next-item-one-poster">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row mj-a-d-none" id="second-item-general-poster">


                    <div class="col-12">
                        <div class="mj-a-select-parent">
                            <div class="mj-a-select-child-2">
                                <select class="form-select mj-a-select"
                                        id="countries"
                                        data-width="100%"
                                        data-placeholder="<?= $lang['select_country']; ?>">
                                    <?php
                                    foreach ($countries->response as $item) {
                                        ?>
                                        <option value="<?= $item->CountryId ?>"
                                            <?= ($myCountry->CountryId == $item->CountryId) ? 'selected' : null ?>><?= $item->CountryName ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mj-a-select-parent mt-2">
                            <div class="mj-a-select-child-3">
                                <select class="form-select mj-a-select"
                                        id="cities"
                                        data-tj-city="<?= $data->city_id; ?>"
                                        data-width="100%"
                                        data-placeholder="<?= $lang['select_city']; ?>">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="col-12">
                        <div class="input-group mj-a-input-group mt-2">
                            <input type="text"
                                   inputmode="tel"
                                   maxlength="14"
                                   lang="en"
                                   id="mobile"
                                   class="form-control"
                                   style="direction: rtl;color: #3F3F3F"
                                   value="<?= $data->poster_phone; ?>"
                                   placeholder="<?= $lang['u_number_call']; ?>">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="input-group mj-a-input-group mt-2">
                            <input type="text"
                                   inputmode="tel"
                                   maxlength="14"
                                   lang="en"
                                   id="phone"
                                   class="form-control"
                                   style="direction: rtl;color: #3F3F3F"
                                   value="<?= $data->poster_whatsapp; ?>"
                                   placeholder="<?= $lang['support_whatsapp']; ?>">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="row g-2 mt-2">
                            <div class="col-4 d-flex align-items-center mt-0">
                                <span><?= $lang['u_time_tel']; ?></span>
                            </div>
                            <div class="col-4 mt-0">
                                <div class="mj-a-select-time-poster">
                                    <span><?= $lang['u_from']; ?></span>
                                    <div class="mj-a-select-parent-2 d-inline-block">
                                        <div class="mj-a-select-child-6">
                                            <select class="form-select mj-a-select"
                                                    id="clock-from"
                                                    data-width="100%">
                                                <option <?= ($data->poster_time_from == "9") ? 'selected' : null; ?>
                                                        value="9">9:00
                                                </option>
                                                <option <?= ($data->poster_time_from == "10") ? 'selected' : null; ?>
                                                        value="10">10:00
                                                </option>
                                                <option <?= ($data->poster_time_from == "11") ? 'selected' : null; ?>
                                                        value="11">11:00
                                                </option>
                                                <option <?= ($data->poster_time_from == "12") ? 'selected' : null; ?>
                                                        value="12">12:00
                                                </option>
                                                <option <?= ($data->poster_time_from == "13") ? 'selected' : null; ?>
                                                        value="13">13:00
                                                </option>
                                                <option <?= ($data->poster_time_from == "14") ? 'selected' : null; ?>
                                                        value="14">14:00
                                                </option>
                                                <option <?= ($data->poster_time_from == "15") ? 'selected' : null; ?>
                                                        value="15">15:00
                                                </option>
                                                <option <?= ($data->poster_time_from == "16") ? 'selected' : null; ?>
                                                        value="16">16:00
                                                </option>
                                                <option <?= ($data->poster_time_from == "17") ? 'selected' : null; ?>
                                                        value="17">17:00
                                                </option>
                                                <option <?= ($data->poster_time_from == "18") ? 'selected' : null; ?>
                                                        value="18">18:00
                                                </option>
                                                <option <?= ($data->poster_time_from == "19") ? 'selected' : null; ?>
                                                        value="19">19:00
                                                </option>
                                                <option <?= ($data->poster_time_from == "20") ? 'selected' : null; ?>
                                                        value="20">20:00
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 mt-0">
                                <div class="  mj-a-select-time-poster">
                                    <span><?= $lang['u_to']; ?></span>
                                    <div class="mj-a-select-parent-2 d-inline-block">
                                        <div class="mj-a-select-child-8">
                                            <select class="form-select mj-a-select"
                                                    id="clock-to"
                                                    data-width="100%">
                                                <option <?= ($data->poster_time_to == "10") ? 'selected' : null; ?>
                                                        value="10">10:00
                                                </option>
                                                <option <?= ($data->poster_time_to == "11") ? 'selected' : null; ?>
                                                        value="11">11:00
                                                </option>
                                                <option <?= ($data->poster_time_to == "12") ? 'selected' : null; ?>
                                                        value="12">12:00
                                                </option>
                                                <option <?= ($data->poster_time_to == "13") ? 'selected' : null; ?>
                                                        value="13">13:00
                                                </option>
                                                <option <?= ($data->poster_time_to == "14") ? 'selected' : null; ?>
                                                        value="14">14:00
                                                </option>
                                                <option <?= ($data->poster_time_to == "15") ? 'selected' : null; ?>
                                                        value="15">15:00
                                                </option>
                                                <option <?= ($data->poster_time_to == "16") ? 'selected' : null; ?>
                                                        value="16">16:00
                                                </option>
                                                <option <?= ($data->poster_time_to == "17") ? 'selected' : null; ?>
                                                        value="17">17:00
                                                </option>
                                                <option <?= ($data->poster_time_to == "18") ? 'selected' : null; ?>
                                                        value="18">18:00
                                                </option>
                                                <option <?= ($data->poster_time_to == "19") ? 'selected' : null; ?>
                                                        value="19">19:00
                                                </option>
                                                <option <?= ($data->poster_time_to == "20") ? 'selected' : null; ?>
                                                        value="20">20:00
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-12">
           <textarea class="form-control mj-a-textarea-poster mt-2"
                     rows="3"
                     id="description"
                     placeholder="<?= $lang['u_description']; ?>"><?= $data->poster_desc; ?></textarea>
                    </div>


                    <div class="col-12">
                        <div class="mj-a-back-poster">

                            <div class="mj-a-close-poster">
                                <button id="btn-second-back-item-general-poster">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="mj-a-check-poster">
                                <button id="btn-second-next-item-general-poster">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row mj-a-d-none" id="third-item-general-poster">
                    <h4><?= $lang['u_image_poster']; ?></h4>
                    <p class="font-12"><?= $lang['u_image_poster_desc']; ?></p>
                    <div class="col-12">
                        <div class="mb-3">
                            <form action="#" method="post" class="dropzone mj-dropzone" id="trailerImages"
                                  data-plugin="dropzone" data-previews-container="#file-previews"
                                  data-upload-preview-template="#uploadPreviewTemplate">
                                <div class="fallback">
                                    <input type="file" name="file">
                                </div>

                                <div class="dz-message needsclick">
                                    <img src="/dist/images/icons/folder-plus.svg" class="mb-2" alt="">
                                    <h5><?= $lang['b_cargo_images_upload_desc'] ?></h5>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-12">
                        <script type="text/javascript">
                            let defaultImages = [<?=   json_decode($data->poster_images) ? '"' . implode('","', json_decode($data->poster_images)) . '"' : null ?>];
                        </script>
                        <div class="row dropzone-previews mt-3" id="file-previews">
                            <?php
                            foreach (json_decode($data->poster_images) as $image) {
                                ?>
                                <div class="col-12" data-remove="<?= $image ?>">
                                    <div class="card shadow-none border">
                                        <div class="p-2">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <img src="<?= $image ?>"
                                                         class="avatar-sm rounded" alt="<?= $image ?>">
                                                </div>
                                                <div class="col">
                                                    <strong class="text-muted"><?= basename($image) ?></strong>
                                                    <p class="mb-0"><?php
                                                        $size = filesize(getcwd() . $image);
                                                        if ($size >= 1000000000) {
                                                            $size = round($size / 1000000000, 1);
                                                            echo "{$size} GB";
                                                        } elseif ($size >= 1000000) {
                                                            $size = round($size / 1000000, 1);
                                                            echo "{$size} MB";
                                                        } elseif ($size < 1000000) {
                                                            $size = round($size / 1000, 1);
                                                            echo "{$size} KB";
                                                        }
                                                        ?></p>
                                                </div>
                                                <div class="col-auto">
                                                    <a href="javascript:void(0);"
                                                       onclick="removeImage(this)"
                                                       data-image="<?= $image ?>"
                                                       class="btn btn-lg btn-link text-danger shadow-none">
                                                        <i class="fe-x align-middle"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>

                        <div class="d-none" id="uploadPreviewTemplate">
                            <div class="col-12">
                                <div class="card shadow-none border">
                                    <div class="p-2">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <img data-dz-thumbnail src="#"
                                                     class="avatar-sm rounded" alt="">
                                            </div>
                                            <div class="col">
                                                <strong class="text-muted" data-dz-name></strong>
                                                <p class="mb-0" data-dz-size></p>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-striped"
                                                         role="progressbar" data-dz-progress
                                                         aria-valuemin="0"
                                                         aria-valuemax="100"></div>
                                                </div>
                                                <p class="text-center mj-dropzone-progress">
                                                    <?= $lang['dropzone_inprogress'] ?>
                                                </p>
                                            </div>
                                            <div class="col-auto">
                                                <a href="javascript:void(0);"
                                                   class="btn btn-lg btn-link text-danger shadow-none"
                                                   data-dz-remove>
                                                    <i class="fe-x align-middle"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="row dropzone-previews mt-3" id="file-previews"></div>

                        <div class="d-none" id="uploadPreviewTemplate">
                            <div class="col-12">
                                <div class="card shadow-none border">
                                    <div class="p-2">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <img data-dz-thumbnail src="#"
                                                     class="avatar-sm rounded" alt="">
                                            </div>
                                            <div class="col">
                                                <strong class="text-muted" data-dz-name></strong>
                                                <p class="mb-0" data-dz-size></p>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-striped"
                                                         role="progressbar" data-dz-progress
                                                         aria-valuemin="0"
                                                         aria-valuemax="100"></div>

                                                </div>
                                                <p class="text-center mj-dropzone-progress">
                                                    <?= $lang['dropzone_inprogress'] ?>
                                                </p>
                                            </div>
                                            <div class="col-auto">
                                                <a href="javascript:void(0);"
                                                   class="btn btn-lg btn-link text-danger shadow-none"
                                                   data-dz-remove>
                                                    <img src="/dist/images/icons/close-cargo.svg" alt="">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!--<div class="col-12">
                        <p class="font-12"> request ex request ex request ex request ex request ex</p>
                        <div class="btn-expert-poster">
                            <input type="checkbox" id="expert" name="expert">
                            <label for="expert">
                                <div class="fa-user-secret me-1"></div>
                                <span> request ex </span>
                            </label>
                        </div>
                    </div>-->

                    <div class="col-12">
                        <div class="d-flex justify-content-evenly mj-a-btn-poster mt-4">
                            <div class="mj-a-close-2-poster">
                                <button id="btn-third-back-item-general-poster">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="mj-a-check-2-poster">
                                <button id="btn-third-next-item-general-poster" data-tj-id="<?= $data->poster_id; ?>">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End General 2 -->


                <!--start brand modal -->
                <div class="modal fade" id="choose-brand-modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content">
                            <div class="mj-m-choose-brand-modal-header">
                                <div  class="mj-add-poster-modal-head">
                                    <h4 class="text-center pt-2">
                                        <?= $lang['u_select_brand']; ?>
                                    </h4>
                                    <div id="close-brand-modal" class="fa-close">

                                    </div>
                                </div>
                                <fieldset>
                                    <div class="mj-a-price-poster d-flex m-2">
                                        <input type="text"
                                               inputmode="text"
                                               class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                               id="search-brand"
                                               lang="en"
                                               placeholder="<?= $lang['u_search_brand']; ?>"
                                               style="min-height: 38px;">
                                        <div class="fa-search d-flex align-items-center me-2"></div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="mj-m-choose-model-modal-body">
                                <div class="row g-2 mx-1" id="brand-search-table">

                                    <!-- add Brands By Ajax -->

                                    <div class="col-4 mj-a-brand-item mj-a-another-brand">
                                        <input type="radio" name="brand-select" id="brand-cc-another">
                                        <label for="brand-cc-another" class="mj-a-brand-modal-div">
                                            <div class="fa-plus"></div>
                                            <span class="my-1"><?= $lang['u_other']; ?></span>
                                            <span class="d-none">no thing other</span>
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <div class="m-2 mj-a-height-0" id="add-new-brand-parent">
                                <span><?= $lang['u_enter_name_brand']; ?></span>
                                <div class="mj-a-price-poster d-flex">
                                    <input type="text"
                                           inputmode="text"
                                           class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                           id="add-new-brand"
                                           placeholder="<?= $lang['u_enter_name_brand_placeholder']; ?>"
                                           style="min-height: 38px;">
                                </div>
                            </div>

                            <div class="d-flex justify-content-center">
                                <button id="submit-modal-brand"
                                        class="submit-modal-brand-class w-75 py-1"
                                        disabled
                                        style="opacity: .5">
                                    <?= $lang['b_accept']; ?>
                                </button>
                            </div>


                        </div>
                    </div>
                </div>
                <!--end brand modal -->

                <!--start model modal -->
                <div class="modal fade" id="choose-model-modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content">
                            <div class="mj-m-choose-brand-modal-header">
                                <div  class="mj-add-poster-modal-head">
                                    <h4 class="text-center pt-2">
                                        <?= $lang['u_select_model_car']; ?>
                                    </h4>
                                    <div id="close-model-modal" class="fa-close">

                                    </div>
                                </div>
                                <fieldset>
                                    <div class="mj-a-price-poster d-flex m-2">
                                        <input type="text"
                                               inputmode="text"
                                               class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                               id="search-model"
                                               lang="en"
                                               placeholder="<?= $lang['u_search_model']; ?>"
                                               style="min-height: 38px;">
                                        <div class="fa-search d-flex align-items-center me-2"></div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="mj-m-choose-model-modal-body">
                                <div class="row g-2 mx-1" id="model-search-table">


                                    <div class="col-12 mj-a-model-item"><input type="radio" name="model-select"
                                                                               id="model-cc-another"></div>

                                </div>
                            </div>

                            <div class="m-2 mj-a-height-0" id="add-new-model-parent">
                                <span><?= $lang['u_enter_name_model']; ?></span>
                                <div class="mj-a-price-poster d-flex">
                                    <input type="text"
                                           inputmode="text"
                                           class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                           id="add-new-model"
                                           placeholder="<?= $lang['u_enter_name_model_placeholder']; ?>"
                                           style="min-height: 38px;">
                                </div>
                            </div>

                            <div class="d-flex justify-content-center">
                                <button id="submit-modal-model"
                                        disabled
                                        style="opacity: .5"
                                        class="submit-modal-brand-class w-75 py-1">
                                    <?= $lang['b_accept']; ?>
                                </button>
                            </div>


                        </div>
                    </div>
                </div>
                <!--end model modal -->

                <input id="token" name="token" type="hidden" value="<?= Security::initCSRF('add-edit-poster') ?>">
            </main>
            <?php
        } else {
            ?>
            <main class="container" style="padding-top: 0px !important; ">
                <div class="text-center">
                    <lottie-player src="/dist/lottie/auth-pending.json" class="mx-auto"
                                   style="max-width: 400px;" speed="1" loop
                                   autoplay></lottie-player>
                    <p class="text-center text-info font-17"><?= $lang['auth_imperfect']; ?></p>
                    <div class="mj-home-gt-blog-btn d-flex justify-content-center">
                        <a href="/user/auth"><?= $lang['return_to_auth_page']; ?></a>
                    </div>
                </div>
            </main>
            <div class="modal fade" id="modal-alert-auth" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="text-center my-3">
                                <i class="fas fa-fingerprint text-primary mb-4" style="font-size: 72px;"></i>

                                <h5 class="text-dark mj-fw-600 mj-font-14 mt-0 mb-4">
                                    <?= $lang['required_auth'] ?>
                                </h5>

                                <div class="d-flex align-items-center justify-content-center">
                                    <a href="/user/auth" class="mj-btn-more px-4 me-1">
                                        <?= $lang['d_auth_title'] ?>
                                    </a>

                                    <a href="javascript:void(0);" data-bs-dismiss="modal"
                                       class="mj-btn-more mj-btn-cancel px-4 ms-1">
                                        <?= $lang['d_btn_close'] ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        getFooter('', false, false);
    } else {
        header('Location: /poster/my-list');
    }
} else {
    header('location: /login');
}