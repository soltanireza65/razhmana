<?php
$pageSlug = "cargo";
// permission_can_edit

global $lang, $antiXSS, $Settings;

use MJ\Security\Security;
use MJ\Utils\Utils;

include_once getcwd() . '/views/admin/header-footer.php';

// start roles 1
$resultCheckAdminLogin = Admin::checkAdminLogin();
$dataCheckAdminLogin = [];
if ($resultCheckAdminLogin->status == 200 && !empty($resultCheckAdminLogin->response)) {
    $dataCheckAdminLogin = $resultCheckAdminLogin->response;

    if ($dataCheckAdminLogin->admin_status == "active") {


        $dataCheckAdminRoleForCheck = [];
        if (!empty($dataCheckAdminLogin->role_id)) {
            $resultCheckAdminRoleForCheck = Admin::checkAdminRoleForCheck($dataCheckAdminLogin->role_id);
            if ($resultCheckAdminRoleForCheck->status == 200) {
                $dataCheckAdminRoleForCheck = $resultCheckAdminRoleForCheck->response;
            }
        }


        $flagSlug = false;
        if (!empty($dataCheckAdminRoleForCheck) && json_decode($dataCheckAdminRoleForCheck)->role_status == "active") {
            foreach (json_decode($dataCheckAdminRoleForCheck)->permissons as $item000) {
                if ($item000->slug_name == $pageSlug && $item000->permission_can_edit == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1

        $id = (int)$antiXSS->xss_clean($_REQUEST['id']);

        /**
         * Get Cargo INFO BY ID
         */
        $resultCargoByID = Cargo::getCargoByID($id);
        $dataCargoByID = [];
        if ($resultCargoByID->status == 200 && !empty($resultCargoByID->response)) {
            $dataCargoByID = $resultCargoByID->response[0];
        }
        if (empty($dataCargoByID)) {
            header('Location: /admin/cargo');
        }

        /**
         * Get User Info By Id
         */
        $resultUserInfoById = AUser::getUserInfoById($dataCargoByID->user_id);
        $dataUserInfoById = [];
        if ($resultUserInfoById->status == 200 && !empty($resultUserInfoById->response)) {
            $dataUserInfoById = $resultUserInfoById->response[0];
        }
        if (empty($dataUserInfoById)) {
            header('Location: /admin');
        }

        $UserName = $lang['guest_user'];
        if (!empty($dataUserInfoById->user_firstname)) {
            $UserName = Security::decrypt($dataUserInfoById->user_firstname) . " " . Security::decrypt($dataUserInfoById->user_lastname);
        }

        /**
         * Get All Category Cars
         */
        $resultAllCarsTypes = Car::getAllCarsTypes();
        $dataAllCarsTypes = [];
        if ($resultAllCarsTypes->status == 200 && !empty($resultAllCarsTypes->response)) {
            $dataAllCarsTypes = $resultAllCarsTypes->response;
        }

        $car_name = '';
        if (!empty($dataAllCarsTypes)) {
            foreach ($dataAllCarsTypes as $dataAllCarsTypesITEM) {
                if ($dataAllCarsTypesITEM->type_id == $dataCargoByID->type_id) {
                    $car_name = (!empty(array_column(json_decode($dataAllCarsTypesITEM->type_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                        array_column(json_decode($dataAllCarsTypesITEM->type_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                }
            }
        }


        /**
         * Get All Category Cargo
         */
        $resultAllCargoCategory = Cargo::getAllCargoCategory();
        $dataAllCargoCategory = [];
        if ($resultAllCargoCategory->status == 200 && !empty($resultAllCargoCategory->response)) {
            $dataAllCargoCategory = $resultAllCargoCategory->response;
        }


        $category_Cargo_name = '';
        $category_Cargo_color = '#6B8E03';
        $category_Cargo_image = POSTER_DEFAULT;
        if (!empty($dataAllCargoCategory)) {
            foreach ($dataAllCargoCategory as $dataAllCargoCategoryITEM) {
                if ($dataAllCargoCategoryITEM->category_id == $dataCargoByID->category_id) {
                    $category_Cargo_name = (!empty(array_column(json_decode($dataAllCargoCategoryITEM->category_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                        array_column(json_decode($dataAllCargoCategoryITEM->category_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                    $category_Cargo_color = $dataAllCargoCategoryITEM->category_color;
                    //  $category_Cargo_image = Utils::fileExist($dataAllCargoCategoryITEM->category_image, POSTER_DEFAULT);
                }
            }
        }


        /**
         * Get All Currencies
         */
        $resultAllCurrencies = Currency::getAllCurrencies();
        $dataAllCurrencies = [];
        if ($resultAllCurrencies->status == 200 && !empty($resultAllCurrencies->response)) {
            $dataAllCurrencies = $resultAllCurrencies->response;
        }


        /**
         * Get All Currencies
         */
        $resultAllRequestCargoByID = Cargo::getAllRequestCargoByID($id);
        $dataAllRequestCargoByID = [];
        if ($resultAllRequestCargoByID->status == 200 && !empty($resultAllRequestCargoByID->response)) {
            $dataAllRequestCargoByID = $resultAllRequestCargoByID->response;
        }


        /**
         * Get All Currencies
         */
        $resultAllExtraExpensesByCargoId = Cargo::getAllExtraExpensesByCargoId($id);
        $dataAllExtraExpensesByCargoId = [];
        if ($resultAllExtraExpensesByCargoId->status == 200 && !empty($resultAllExtraExpensesByCargoId->response)) {
            $dataAllExtraExpensesByCargoId = $resultAllExtraExpensesByCargoId->response;
        }

        $getCargoAllRequest = Cargo::getCargoAllRequest($id)->response;


        $resultAllAdmins = Admin::getAllAdminWithRole();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
        }


        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $getAdminFromAssign = Admin::getAdminFromAssign($id, 'cargo-out')->response;


        // Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('admin-css', '/dist/css/admin/admin.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
        enqueueStylesheet('FA-css', '/dist/libs/fontawesome/all.min.css');
        enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
        enqueueStylesheet('editable-css', '/dist/libs/x-editable/bootstrap-editable/css/bootstrap-editable.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');
        enqueueStylesheet('openlayers-css', '/dist/libs/ol/ol.css');

        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // map
        enqueueScript('elm', '/dist/libs/ele-pep/elem.js');
        enqueueScript('FA-js', '/dist/libs/fontawesome/all.min.js');
        enqueueScript('map-js', '/map/assets/index.11536adb.js');
        enqueueStylesheet('map-css', '/map/assets/index.152c57f7.css');
        enqueueScript('unpkg-css', '/dist/libs/ol/ol-debug.js');
        enqueueStylesheet('persian-datepicker', '/dist/libs/persian-calendar/persian-datepicker.min.css');

        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
        enqueueScript('editable-js', '/dist/libs/x-editable/bootstrap-editable/js/bootstrap-editable.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('persian-date-min-js', '/dist/libs/persian-calendar//persian-date.min.js');
        enqueueScript('persian-datepicker-min-js', '/dist/libs/persian-calendar/persian-datepicker.min.js');
        enqueueScript('cargo-info', '/dist/js/admin/ground/cargo-info.init.js');

        getHeader($lang["cargo_out_info"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_edit',
        ]);

        // start roles 2
        if ($flagSlug) {
            // end roles 2


            /**
             * Get Ciries
             */

            $cargo_origin_long = null;
            $cargo_origin_lat = null;
            $cargo_origin_city = $dataCargoByID->cargo_origin_id;
            $resultCityById = Location::getMultiCityAndCountryByCityId($cargo_origin_city);
            $dataCityById = [];
            if ($resultCityById->status == 200 && !empty($resultCityById->response)) {
                $dataCityById = $resultCityById->response[0];
                $cargo_origin_address = (!empty(array_column(json_decode($dataCityById->city_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                    array_column(json_decode($dataCityById->city_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                $cargo_origin_country_address = (!empty(array_column(json_decode($dataCityById->country_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                    array_column(json_decode($dataCityById->country_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                $cargo_origin_long = $dataCityById->city_long;
                $cargo_origin_lat = $dataCityById->city_lat;
            }


            $cargo_customs_of_origin_city = $dataCargoByID->cargo_origin_customs_id;
            $resultCityById = Ground::getCustomsById($cargo_customs_of_origin_city);
            $dataCityById = [];
            if ($resultCityById->status == 200 && !empty($resultCityById->response)) {
                $dataCityById = $resultCityById->response[0];
                $cargo_customs_of_origin_address = (!empty(array_column(json_decode($dataCityById->customs_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                    array_column(json_decode($dataCityById->customs_name, true), 'value', 'slug')[$_COOKIE['language']] : "";

            }


            $cargo_destination_long = null;
            $cargo_destination_lat = null;
            $cargo_destinationn_city = $dataCargoByID->cargo_destination_id;
            $resultCityById = Location::getMultiCityAndCountryByCityId($cargo_destinationn_city);
            $dataCityById = [];
            if ($resultCityById->status == 200 && !empty($resultCityById->response)) {
                $dataCityById = $resultCityById->response[0];
                $cargo_destinationn_address = (!empty(array_column(json_decode($dataCityById->city_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                    array_column(json_decode($dataCityById->city_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                $cargo_destinationn_country_address = (!empty(array_column(json_decode($dataCityById->country_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                    array_column(json_decode($dataCityById->country_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                $cargo_destination_long = $dataCityById->city_long;
                $cargo_destination_lat = $dataCityById->city_lat;
            }


            $cargo_destination_customs_city = $dataCargoByID->cargo_destination_customs_id;
            $resultCityById = Ground::getCustomsById($cargo_destination_customs_city);
            $dataCityById = [];
            if ($resultCityById->status == 200 && !empty($resultCityById->response)) {
                $dataCityById = $resultCityById->response[0];
                $cargo_destination_customs_address = (!empty(array_column(json_decode($dataCityById->customs_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                    array_column(json_decode($dataCityById->customs_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
            }


            ?>
            <style>

                .swiper {
                    width: 100%;
                    height: 300px;
                    margin-left: auto;
                    margin-right: auto;
                }

                .swiper-slide {
                    text-align: center;
                    font-size: 18px;
                    background: #fff;
                    /* Center slide text vertically */
                    display: -webkit-box;
                    display: -ms-flexbox;
                    display: -webkit-flex;
                    display: flex;
                    -webkit-box-pack: center;
                    -ms-flex-pack: center;
                    -webkit-justify-content: center;
                    justify-content: center;
                    -webkit-box-align: center;
                    -ms-flex-align: center;
                    -webkit-align-items: center;
                    align-items: center;
                    background-size: cover;
                    background-position: center;
                }

                .swiper-slide img {
                    display: block;
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }


                .mySwiper2 {
                    height: 80%;
                    width: 100%;
                }

                .mySwiper {
                    height: 20%;
                    box-sizing: border-box;
                    padding: 10px 0;
                }

                .mySwiper .swiper-slide {
                    width: 25%;
                    height: 100%;
                    opacity: 0.4;
                }

                .mySwiper .swiper-slide-thumb-active {
                    opacity: 1;
                }

                /**************/
                .icon-rotates {
                    transform: scaleX(-1);
                }

                /******** map ******/

                .map {
                    position: absolute;
                    height: 100%;
                    width: 100%;
                    min-height: 360px;
                    max-height: 500px;
                }

            </style>
            <div id="checklist-btn" class="mj-admin-cargo-checklist-btn"><?= $lang['a_check_list']; ?></div>
            <div class="mj-admin-cargo-checklist">
                <div class="mj-close-checklist">
                    <div class="fa-close">
                        <span><?= $lang['closes']; ?></span>
                    </div>
                    <Span><?= $lang['a_check_list_cargo']; ?></Span>
                </div>


                <div class="mj-admin-checklist-content">
                    <ul>
                        <?php
                        $checkList = $Settings['cargo_out_check_list'];
                        foreach ($checkList as $loop) {
                            echo '<li>' . $loop['title'] . '</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class="row">

                <div class="col-sm-12 col-md-12 col-lg-7">
                    <div class="row">
                        <!-- Start cargo Info -->
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="card">
                                <div class="card-body">

                                    <h5 class="text-uppercase mt-0 mb-3 bg-light p-2" id="CargoID"
                                        data-mj-cargo-id="<?= $id; ?>"><?= (empty($cargo_origin_address) || empty($cargo_customs_of_origin_address)
                                            || empty($cargo_destinationn_address) || empty($cargo_destination_customs_address)) ? '<i class="mdi mdi-alert-circle-outline text-danger"   data-bs-toggle="tooltip"
                                      data-bs-placement="top"
                                      title="' . $lang['no_address'] . '"></i>  ' : "";
                                        ?><?= $lang["cargo_out_info"]; ?></h5>

                                    <h4 class="header-title mb-3">
                                        <span class="mdi mdi-circle me-2"
                                              style="color: <?= $category_Cargo_color; ?>">

                                        </span>
                                        <span class="float-end">
                                            <?php
                                            $rateCargoesA = 0;
                                            if (!empty($dataCargoByID->cargo_rate)) {

                                                $rateCargoes = json_decode($dataCargoByID->cargo_rate);

                                                $rateCargoesC = 0;
                                                $rateCargoesE = 0;

                                                foreach ($rateCargoes as $rateCargo) {
                                                    $rateCargoesE += $rateCargo;
                                                    $rateCargoesC += 1;
                                                }
                                                $rateCargoesC = ($rateCargoesC == 0) ? $rateCargoesC += 1 : $rateCargoesC;
                                                $rateCargoesA = $rateCargoesE / $rateCargoesC;
                                            }
                                            print_r(Utils::getStarsByRate($rateCargoesA));
                                            ?>
                                        </span>
                                    </h4>

                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <?= $lang["title_Persian"]; ?> :
                                                </td>
                                                <td>
                                            <span id="change_cargo_name_fa_IR"
                                                  data-type="text"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-placement="top"
                                                  title="<?= $lang['click_for_edit']; ?>"
                                                  data-mj-type="cargo_name_fa_IR">
                                             <?= $dataCargoByID->cargo_name_fa_IR; ?>
                                            </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <?= $lang["title_English"]; ?> :
                                                </td>
                                                <td>
                                            <span id="change_cargo_name_en_US"
                                                  data-type="text"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-placement="top"
                                                  title="<?= $lang['click_for_edit']; ?>"
                                                  data-mj-type="cargo_name_en_US">
                                             <?= $dataCargoByID->cargo_name_en_US; ?>
                                            </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <?= $lang["title_Turkish"]; ?> :
                                                </td>
                                                <td>
                                            <span id="change_cargo_name_tr_Tr"
                                                  data-type="text"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-placement="top"
                                                  title="<?= $lang['click_for_edit']; ?>"
                                                  data-mj-type="cargo_name_tr_Tr">
                                             <?= $dataCargoByID->cargo_name_tr_Tr; ?>
                                            </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <?= $lang["title_Russia"]; ?> :
                                                </td>
                                                <td>
                                            <span id="change_cargo_name_ru_RU"
                                                  data-type="text"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-placement="top"
                                                  title="<?= $lang['click_for_edit']; ?>"
                                                  data-mj-type="cargo_name_ru_RU">
                                             <?= $dataCargoByID->cargo_name_ru_RU; ?>
                                            </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["businessman_name"]; ?> :</td>
                                                <td>
                                                    <div class="d-flex align-items-start">
                                                        <img class="me-2 avatar-sm rounded-circle"
                                                             src="<?= USER_AVATAR; ?>"
                                                             alt="<?= $UserName; ?>">
                                                        <div class="w-100">
                                                            <h5 class="m-0">
                                                                <a href="/admin/users/info/<?= $dataCargoByID->user_id; ?>">
                                                                    <?= $UserName; ?>
                                                                </a>

                                                            </h5>
                                                            <div class=" my-1 font-11">
                                                                <!--  &#9733;&#9733;&#9733;-->
                                                                <?php
                                                                $user_rate = $dataUserInfoById->user_rate;
                                                                $user_rate_count = $dataUserInfoById->user_rate_count;
                                                                $user_rate_count = ($user_rate_count == 0) ? $user_rate_count += 1 : $user_rate_count;
                                                                $user_rate_average = $user_rate / $user_rate_count;

                                                                print_r(Utils::getStarsByRate($user_rate_average));
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= $lang["cargo_category"]; ?> :
                                                </td>
                                                <td>
                                            <span id="change_cargo_category" data-type="select"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-placement="top"
                                                  title="<?= $lang['click_for_edit']; ?>"
                                                  data-mj-type="category_id">
                                             <?= $category_Cargo_name; ?>
                                            </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= $lang["requested_cars"]; ?> :</td>
                                                <td>
                                           <span id="change_cargo_type" data-type="select"
                                                 data-bs-toggle="tooltip"
                                                 data-bs-placement="top"
                                                 title="<?= $lang['click_for_edit']; ?>"
                                                 data-mj-type="type_id">
                                               <?= $car_name; ?>
                                           </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= $lang["cargo_status"]; ?> :</td>
                                                <td>
                                                    <!-- <span id="change_cargo_status" data-type="select"-->
                                                    <!--       data-bs-toggle="tooltip"-->
                                                    <!--       data-bs-placement="top"-->
                                                    <!--       title="< ?= $lang['click_for_edit']; ?>"-->
                                                    <!--       data-mj-type="cargo_status">-->
                                                    <?php
                                                    if ($dataCargoByID->cargo_status == "accepted") {
                                                        echo $lang['accepted'];
                                                    } elseif ($dataCargoByID->cargo_status == "pending") {
                                                        echo $lang['pending'];
                                                    } elseif ($dataCargoByID->cargo_status == "rejected") {
                                                        echo $lang['rejected'];
                                                    } elseif ($dataCargoByID->cargo_status == "progress") {
                                                        echo $lang['progress'];
                                                    } elseif ($dataCargoByID->cargo_status == "canceled") {
                                                        echo $lang['canceled'];
                                                    } elseif ($dataCargoByID->cargo_status == "completed") {
                                                        echo $lang['completed'];
                                                    } elseif ($dataCargoByID->cargo_status == "expired") {
                                                        echo $lang['expired'];
                                                    } else {
                                                        echo $dataCargoByID->cargo_status;
                                                    }
                                                    ?>
                                                    <!-- </span>-->
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= $lang["cargo_weight"]; ?> :</td>
                                                <td>
                                            <span id="change_cargo_weight"
                                                  data-type="number"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-placement="top"
                                                  title="<?= $lang['click_for_edit']; ?>"
                                                  data-mj-type="cargo_weight">
                                                <?= $dataCargoByID->cargo_weight; ?>
                                            </span>
                                                    <?= $lang['ton']; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= $lang["cargo_volume"]; ?> :</td>
                                                <td>
                                              <span id="change_cargo_volume" data-type="number"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="<?= $lang['click_for_edit']; ?>"
                                                    data-mj-type="cargo_volume">
                                                <?= $dataCargoByID->cargo_volume; ?>
                                            </span>
                                                    <?= $lang['cubic_meter']; ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_green_street_customs"]; ?> :</td>
                                                <td>
                                                    <?= ($dataCargoByID->cargo_green == "yes") ? '<i class="text-success spinner-grow spinner-grow-sm"></i>' : null ?>

                                                    <span id="change_cargo_green"
                                                          data-type="select"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          title="<?= $lang['click_for_edit']; ?>"
                                                          data-mj-type="cargo_green">
                                                <?= ($dataCargoByID->cargo_green == "yes") ? $lang["a_yes"] : $lang["a_no"]; ?>
                                            </span>
                                                </td>
                                            </tr>


                                            <tr>
                                                <td><?= $lang["car_count_request"]; ?> :</td>
                                                <td>
                                                    <?= $dataCargoByID->cargo_car_count; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= $lang["recommended_price"]; ?> :</td>
                                                <td>
                                                    <?php
                                                    $array_currency = [];
                                                    $currency_name = '';
                                                    if (!empty($dataAllCurrencies)) {
                                                        foreach ($dataAllCurrencies as $dataAllCurrenciesITEM) {

                                                            $currency_name0 = (!empty(array_column(json_decode($dataAllCurrenciesITEM->currency_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                array_column(json_decode($dataAllCurrenciesITEM->currency_name, true), 'value', 'slug')[$_COOKIE['language']] : $dataAllCurrenciesITEM->currency_id;

                                                            if ($dataAllCurrenciesITEM->currency_id == $dataCargoByID->cargo_monetary_unit) {
                                                                $currency_name = $currency_name0;
                                                            }
                                                            array_push($array_currency, ["text" => $currency_name0, 'value' => $dataAllCurrenciesITEM->currency_id]);
                                                        }
                                                    } else {
                                                        array_push($array_currency, []);
                                                    }
                                                    ?>
                                                    <span id="change_cargo_recommended_price"
                                                          data-type="number"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          title="<?= $lang['click_for_edit']; ?>"
                                                          data-mj-type="cargo_recommended_price">
                                                <?= (number_format($dataCargoByID->cargo_recommended_price)) ? number_format($dataCargoByID->cargo_recommended_price) : null; ?>
                                                     </span>
                                                    <span id="change_cargo_monetary_unit"
                                                          data-type="select"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          title="<?= $lang['click_for_edit']; ?>"
                                                          data-mj-type="cargo_monetary_unit"><?= $currency_name; ?></span>
                                                    <!--                                                    echo (number_format($dataCargoByID->cargo_recommended_price)) ? number_format($dataCargoByID->cargo_recommended_price) . " " . $currency_name : null . " " . $currency_name; ?>-->
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= $lang["cargo_submit_data"]; ?> :</td>
                                                <td>
                                                    <bdi><?= Utils::getTimeCountry($Settings['date_format'], $dataCargoByID->cargo_date); ?></bdi>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= $lang["start_date"]; ?> :</td>
                                                <td>
                                                    <i class="mdi mdi-square-edit-outline text-info"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['click_for_edit']; ?>"
                                                       id="StartDate"></i>
                                                    <bdi><?= Utils::getTimeCountry($Settings['date_format'], $dataCargoByID->cargo_start_date); ?></bdi>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["description_Persian"]; ?> :</td>
                                                <td>
                                                     <span id="change_cargo_description_fa_IR"
                                                           data-type="textarea"
                                                           style="white-space:unset"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['click_for_edit']; ?>"
                                                           data-mj-type="cargo_description_fa_IR">
                                                <?= $dataCargoByID->cargo_description_fa_IR; ?>
                                            </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["description_English"]; ?> :</td>
                                                <td>
                                                     <span id="change_cargo_description_en_US"
                                                           data-type="textarea"
                                                           style="white-space:unset"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['click_for_edit']; ?>"
                                                           data-mj-type="cargo_description_en_US">
                                                <?= $dataCargoByID->cargo_description_en_US; ?>
                                            </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["description_Turkish"]; ?> :</td>
                                                <td>
                                                     <span id="change_cargo_description_tr_Tr"
                                                           data-type="textarea"
                                                           style="white-space:unset"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['click_for_edit']; ?>"
                                                           data-mj-type="cargo_description_tr_Tr">
                                                <?= $dataCargoByID->cargo_description_tr_Tr; ?>
                                            </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["description_Russia"]; ?> :</td>
                                                <td>
                                                     <span id="change_cargo_description_ru_RU"
                                                           data-type="textarea"
                                                           style="white-space:unset"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['click_for_edit']; ?>"
                                                           data-mj-type="cargo_description_ru_RU">
                                                <?= $dataCargoByID->cargo_description_ru_RU; ?>
                                            </span>
                                                </td>
                                            </tr>


                                            </tbody>
                                        </table>

                                    </div>
                                    <!-- end table-responsive -->


                                </div>
                            </div>
                        </div>
                        <!-- End cargo Info -->


                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang['a_add_admin_desc']; ?></h5>
                                    <div class="input-group mt-3">
                                    <textarea class="form-control form-control-light" id="admin-desc"
                                              placeholder="<?= $lang['a_add_admin_desc']; ?>"></textarea>
                                        <button data-style="zoom-in"
                                                class="btn input-group-text btn-light"
                                                id="btn-admin-desc"
                                                type="button"><?= $lang['submit']; ?></button>
                                    </div>
                                    <br>
                                    <div class="table-responsive" style="max-height: 176px;overflow: auto;">
                                        <table class="table mb-0">
                                            <thead class="table-light">
                                            <tr>
                                                <th><?= $lang['admin_name']; ?></th>
                                                <th><?= $lang['description']; ?></th>
                                                <th><?= $lang['date']; ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $cargo_admin_desc = json_decode($dataCargoByID->cargo_admin_desc);
                                            if (!empty($cargo_admin_desc)) {
                                                $cargo_admin_desc = array_reverse($cargo_admin_desc);
                                                foreach ($cargo_admin_desc as $loop) {
                                                    if ($loop->type == "desc") {
                                                        $name = '';
                                                        if (!empty($dataAllAdmins)) {
                                                            foreach ($dataAllAdmins as $dataAllAdminsLOOP) {
                                                                if ($dataAllAdminsLOOP->admin_id == $loop->admin) {
                                                                    $name = $dataAllAdminsLOOP->admin_nickname;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td><?= $name; ?></td>
                                                            <td><?= $loop->desc; ?></td>
                                                            <td><?= Utils::getTimeCountry($Settings['data_time_format'], $loop->date); ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>


                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-5">
                    <div class="row">

                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang['a_set_admin']; ?></h5>
                                    <?php
                                    if (in_array(@json_decode(Security::decrypt($_COOKIE['INF']))->role_id, explode(',', Utils::getFileValue("settings.txt", "set_admin_cargo_out")))) {
                                        ?>
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <select class="form-control"
                                                        id="referTask"
                                                        multiple="multiple"
                                                        data-toggle="select2"
                                                        data-width="100%">
                                                    <?php
                                                    if (!empty($dataAllAdmins)) {
                                                        foreach ($dataAllAdmins as $dataAllAdminsITEM) {
                                                            ?>
                                                            <option
                                                                    data-tj-category-status="<?= ($dataAllAdminsITEM->admin_status == "active") ? $lang['active'] : $lang['inactive']; ?>"
                                                                    data-tj-category-color="<?php
                                                                    if ($dataAllAdminsITEM->admin_status == "inactive") {
                                                                        echo "danger";
                                                                    } elseif ($dataAllAdminsITEM->role_status == "inactive") {
                                                                        echo "pink";
                                                                    } else {
                                                                        echo 'success';
                                                                    }
                                                                    ?>"
                                                                    data-tj-type="admin"
                                                                    value="<?= $dataAllAdminsITEM->admin_id; ?>">
                                                                <?= $dataAllAdminsITEM->admin_nickname; ?>
                                                            </option>
                                                            <?php

                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="button-list d-flex justify-content-center">
                                            <button type="button"
                                                    id="setAdmin"
                                                    data-style="zoom-in"
                                                    class="btn btn-soft-success waves-effect waves-light">
                                                <?= $lang["accepted"]; ?>
                                            </button>
                                        </div>
                                    <?php } ?>


                                    <div class="mt-2" id="tooltips-container">
                                        <?php foreach ($getAdminFromAssign as $loop) { ?>
                                            <a href="javascript:void(0);" class="d-inline-block">
                                                <img src="<?= Utils::fileExist($loop->admin_avatar, USER_AVATAR); ?>"
                                                     class="rounded-circle avatar-md" alt="friend"
                                                     data-bs-container="#tooltips-container" data-bs-toggle="tooltip"
                                                     data-bs-placement="top"
                                                     title="<?= $loop->admin_nickname; ?>">
                                            </a>
                                        <?php } ?>
                                    </div>


                                </div>
                            </div>
                        </div>

                        <!--Change Cargo Status-->
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">
                                        <i class="mdi mdi-alert-outline text-warning"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="<?= $lang['change_cargo_status_desc']; ?>"></i>
                                        <?= $lang["change_cargo_status"]; ?>
                                    </h5>
                                    <table class="table mb-3">
                                        <tbody>
                                        <tr>
                                            <td> <?= $lang["change_cargo_status"]; ?></td>
                                            <td>
                                                <?php
                                                if ($dataCargoByID->cargo_status == "accepted") {
                                                    echo "<span class='badge badge-soft-success font-12'>" . $lang['accepted'] . "</span>";
                                                } elseif ($dataCargoByID->cargo_status == "pending") {
                                                    echo "<span class='badge badge-soft-warning font-12'>" . $lang['pending'] . "</span>";
                                                } elseif ($dataCargoByID->cargo_status == "rejected") {
                                                    echo "<span class='badge badge-soft-danger font-12'>" . $lang['rejected'] . "</span>";
                                                } elseif ($dataCargoByID->cargo_status == "progress") {
                                                    echo "<span class='badge badge-soft-info font-12'>" . $lang['progress'] . "</span>";
                                                } elseif ($dataCargoByID->cargo_status == "canceled") {
                                                    echo "<span class='badge badge-soft-secondary font-12'>" . $lang['canceled'] . "</span>";
                                                } elseif ($dataCargoByID->cargo_status == "completed") {
                                                    echo "<span class='badge badge-soft-primary font-12'>" . $lang['completed'] . "</span>";
                                                } elseif ($dataCargoByID->cargo_status == "expired") {
                                                    echo "<span class='badge badge-soft-secondary font-12'>" . $lang['expired'] . "</span>";
                                                } else {
                                                    echo "<span class='badge badge-soft-pink font-12'>" . $dataCargoByID->cargo_status . "</span>";
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div class="button-list d-flex justify-content-center">

                                        <?php
                                        if ($dataCargoByID->cargo_status == "pending") {
                                            ?>
                                            <button type="button"
                                                    id="btNaccepted"
                                                    data-mj-status="accepted"
                                                    data-style="zoom-in"
                                                    class="btnSubmit btn btn-soft-success waves-effect waves-light">
                                                <?= $lang["accepted"]; ?>
                                            </button>
                                            <button type="button"
                                                    id="btNrejected"
                                                    data-mj-status="rejected"
                                                    data-style="zoom-in"
                                                    class="btnSubmit btn btn-soft-danger waves-effect waves-light">
                                                <?= $lang["rejected"]; ?>
                                            </button>
                                            <?php
                                        } elseif (count($getCargoAllRequest) == 0) {
                                            ?>
                                            <button type="button"
                                                    id="btNaccepted"
                                                    data-mj-status="accepted"
                                                    data-style="zoom-in"
                                                    class="btnSubmit btn btn-soft-success waves-effect waves-light">
                                                <?= $lang["accepted"]; ?>
                                            </button>
                                            <button type="button"
                                                    id="btNrejected"
                                                    data-mj-status="rejected"
                                                    data-style="zoom-in"
                                                    class="btnSubmit btn btn-soft-danger waves-effect waves-light">
                                                <?= $lang["rejected"]; ?>
                                            </button>
                                        <?php } ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!--/Change Cargo Status-->

                        <!-- Start cargo Images -->
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["cargo_thumbnail"]; ?></h5>
                                    <div class="row">

                                        <div class="col-lg-12">

                                            <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff"
                                                 class="swiper mySwiper2">
                                                <div class="swiper-wrapper">
                                                    <?php
                                                    $imageFlag = true;
                                                    if (!empty($dataCargoByID->cargo_images)) {
                                                        $cargo_images = json_decode($dataCargoByID->cargo_images);
                                                        foreach ($cargo_images as $cargo_imagesITEM) {
                                                            $imageFlag = false;
                                                            ?>
                                                            <div class="swiper-slide">
                                                                <img src="<?= Utils::fileExist($cargo_imagesITEM, POSTER_DEFAULT); ?>"/>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    if ($imageFlag) {
                                                        ?>
                                                        <div class="swiper-slide">
                                                            <img src="<?= $category_Cargo_image; ?>"/>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>

                                                </div>
                                                <div class="swiper-button-next"></div>
                                                <div class="swiper-button-prev"></div>
                                            </div>
                                            <div thumbsSlider="" class="swiper mySwiper">
                                                <div class="swiper-wrapper">
                                                    <?php
                                                    if (!empty($dataCargoByID->cargo_images)) {
                                                        $cargo_images = json_decode($dataCargoByID->cargo_images);
                                                        foreach ($cargo_images as $cargo_imagesITEM) {
                                                            ?>
                                                            <div class="swiper-slide">
                                                                <img src="<?= Utils::fileExist($cargo_imagesITEM, POSTER_DEFAULT); ?>"/>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    if ($imageFlag) {
                                                        ?>
                                                        <div class="swiper-slide">
                                                            <img src="<?= $category_Cargo_image; ?>"/>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>


                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End cargo Images -->

                        <!--Cargo Cancel Desc-->
                        <?php
                        if (!empty($dataCargoByID->cargo_cancel_desc)) {
                            ?>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">
                                            <i class="mdi mdi-alert-outline text-info"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['cargo_cancel_desc_alert']; ?>"></i>
                                            <?= $lang["cargo_cancel_desc"]; ?></h5>
                                        <p><?= $dataCargoByID->cargo_cancel_desc; ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <!--/Cargo Cancel Desc-->


                    </div>
                </div>


            </div>

            <div class="row">
                <!-- Start Tabs-->
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form>
                                <div id="basicwizard">

                                    <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-4">
                                        <li class="nav-item">
                                            <a href="#basictab1" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2 active">
                                                <i class="mdi mdi-map-marker-radius-outline me-1"></i>
                                                <span class="d-none d-sm-inline"><?= $lang['location']; ?></span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#basictab2" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2">
                                                <i class="mdi mdi-account-question me-1"></i>
                                                <span class="d-none d-sm-inline"><?= $lang['requests']; ?></span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#basictab3" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2">
                                                <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>
                                                <span class="d-none d-sm-inline"><?= $lang['extra_expenses']; ?></span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#basictab4" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2">
                                                <i class="mdi mdi-math-log me-1"></i>
                                                <span class="d-none d-sm-inline"><?= $lang['admins_log']; ?></span>
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content b-0 mb-0 pt-0">
                                        <div class="tab-pane active" id="basictab1">
                                            <div class="row">
                                                <div class="col-sm-12 col-md-12 col-lg-6">

                                                    <div class="table-responsive">
                                                        <table class="table mb-0">
                                                            <tbody>
                                                            <!--Start Location Cargo-->
                                                            <tr>
                                                                <td colspan="2">
                                                                    <i class="mdi mdi-square-edit-outline text-info changeLocation"
                                                                       data-tj-type="source"></i>
                                                                    <?= $lang['a_source_cargo']; ?>
                                                                    <ul class="list-group list-group-flush">
                                                                        <li class="align-items-center d-flex justify-content-around list-group-item">
                                                                            <?= $lang['a_source_country']; ?>
                                                                            <span><?= $cargo_origin_country_address; ?></span>
                                                                        </li>
                                                                        <li class="align-items-center d-flex justify-content-around list-group-item">
                                                                            <?= $lang['a_source_city']; ?>
                                                                            <span><?= $cargo_origin_address; ?></span>
                                                                        </li>
                                                                        <li class="align-items-center d-flex justify-content-around list-group-item">
                                                                            <?= $lang['a_source_customs']; ?>
                                                                            <span> <?= ($dataCargoByID->cargo_green == "yes") ? '<i class="text-success spinner-grow spinner-grow-sm"></i> <span class="badge-outline-success"> ' . $lang["a_green_street_customs"] . ' </span>' : $cargo_customs_of_origin_address; ?> </span>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td colspan="2">
                                                                    <i class="mdi mdi-square-edit-outline text-info changeLocation"
                                                                       data-tj-type="dest"></i>
                                                                    <?= $lang['a_dest_cargo']; ?>
                                                                    <ul class="list-group list-group-flush">
                                                                        <li class="align-items-center d-flex justify-content-around list-group-item">
                                                                            <?= $lang['a_dest_country']; ?>
                                                                            <span><?= $cargo_destinationn_country_address; ?> </span>
                                                                        </li>
                                                                        <li class="align-items-center d-flex justify-content-around list-group-item">
                                                                            <?= $lang['a_dest_city']; ?>
                                                                            <span><?= $cargo_destinationn_address; ?></span>
                                                                        </li>
                                                                        <li class="align-items-center d-flex justify-content-around list-group-item">
                                                                            <?= $lang['a_dest_customs']; ?>
                                                                            <span><?= $cargo_destination_customs_address; ?></span>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                            <!--End Location Cargo-->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!-- end table-responsive -->
                                                </div>
                                                <div class="col-sm-12 col-md-12 col-lg-6">
                                                    <div class="position-relative">
                                                        <div id="map" class="map">
                                                            <div id="popup"></div>
                                                        </div>
                                                        <?php
                                                        $arrayMJ = [];

                                                        if ((!$dataAllRequestCargoByID)) {
                                                            foreach ($dataAllRequestCargoByID as $dataAllRequestCargoByIDLOOP) {
                                                                if ($dataAllRequestCargoByIDLOOP->request_status == 'progress') {
                                                                    $output = [];
                                                                    $output['lat'] = $dataAllRequestCargoByIDLOOP->request_lat;
                                                                    $output['long'] = $dataAllRequestCargoByIDLOOP->request_long;
                                                                    $arrayMJ[] = $output;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <div id="info" data-lat="<?= $dataCargoByID->cargo_latitude; ?>"
                                                             data-long="<?= $dataCargoByID->cargo_longitude; ?>"
                                                             data-icon="" data-source-lat="" data-source-long=""
                                                             data-dest-lat="" data-dest-long=""></div>


                                                    </div>

                                                    <script>
                                                        // let sourceOrigin = [{"lat":"" , "long":""}];
                                                        // // let destOrigin=[{"lat":"" , "long":""}];
                                                        // let requestLocationIcon = 'https://codershool.ir/map/truck-top.png';
                                                        // let requestLocations  =[{"lat":"38.0570236","long":"46.3124278"},{"lat":"38.062278","long":"46.3074925"}];
                                                        <?php

                                                        $requestLocationIcon = '/dist/images/location.png';
                                                        if (empty($arrayMJ)) {
                                                            $arrayMJ[] = ['lat' => 38.0713709, 'long' => 46.2884338];
                                                            $requestLocationIcon = '/dist/images/truck-top.png';
                                                        }

                                                        ?>
                                                        let sourceOrigin = [{"lat": "", "long": ""}];
                                                        let destOrigin = [{"lat": "", "long": ""}];
                                                        let requestLocationIcon = '<?=$requestLocationIcon;?>';
                                                        let requestLocations = JSON.parse('<?=json_encode($arrayMJ) ?>');
                                                    </script>
                                                </div>

                                                <!-- end col -->
                                            </div>
                                            <!-- end row -->
                                        </div>

                                        <div class="tab-pane" id="basictab2">
                                            <div class="row">
                                                <div class="col-12">

                                                    <div class="table-responsive">
                                                        <table id="orders-table" data-page-length='25'
                                                               data-order='[[ 0, "desc" ]]'
                                                               class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                                            <thead>
                                                            <tr>
                                                                <th class="d-none">#</th>
                                                                <th><?= $lang['request_number']; ?></th>
                                                                <th><?= $lang['driver']; ?></th>
                                                                <th><?= $lang['recommended_price']; ?></th>
                                                                <th><?= $lang['request_submit_date']; ?></th>
                                                                <th><?= $lang['request_start_date']; ?></th>
                                                                <th><?= $lang['rate']; ?></th>
                                                                <th><?= $lang['status']; ?></th>
                                                                <th class="all"><?= $lang['action']; ?></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php

                                                            if (!empty($dataAllRequestCargoByID)) {

                                                                $dataAllRequestCargoByID = array_reverse($dataAllRequestCargoByID);
                                                                foreach ($dataAllRequestCargoByID as $dataAllRequestCargoByIDITEM) {
                                                                    ?>
                                                                    <tr>
                                                                        <td class="d-none"><?= $dataAllRequestCargoByIDITEM->request_date; ?></td>
                                                                        <td><?= $dataAllRequestCargoByIDITEM->request_id; ?></td>
                                                                        <td class="table-user text-start">
                                                                            <?php
                                                                            $resultDriver = AUser::getUserInfoById($dataAllRequestCargoByIDITEM->user_id);
                                                                            $nameDriver = $lang['guest_user'];
                                                                            if ($resultDriver->status == 200 && !empty($resultDriver->response) && isset($resultDriver->response[0]) && !empty($resultDriver->response[0])) {
                                                                                $dataDriver = $resultDriver->response[0];
                                                                                $nameDriver = Security::decrypt($dataDriver->user_firstname) . " " . Security::decrypt($dataDriver->user_lastname);
                                                                            }
                                                                            ?>
                                                                            <img src="<?= USER_AVATAR; ?>"
                                                                                 alt="<?= $nameDriver; ?>"
                                                                                 class="me-2 rounded-circle">
                                                                            <a href="/admin/users/info/<?= $dataAllRequestCargoByIDITEM->user_id; ?>"
                                                                               class="text-body fw-normal">
                                                                                <?= $nameDriver; ?>
                                                                            </a>
                                                                        </td>

                                                                        <td>
                                                                            <?= number_format($dataAllRequestCargoByIDITEM->request_price) . " " . $currency_name; ?>
                                                                        </td>
                                                                        <td>
                                                                            <bdi>
                                                                                <?= Utils::getTimeCountry($Settings['date_format'], $dataAllRequestCargoByIDITEM->request_date); ?>
                                                                            </bdi>
                                                                        </td>
                                                                        <td>
                                                                            <bdi>
                                                                                <?= (!empty($dataAllRequestCargoByIDITEM->request_start_date)) ? Utils::getTimeCountry($Settings['date_format'], $dataAllRequestCargoByIDITEM->request_start_date) : $lang['no_start']; ?>
                                                                            </bdi>
                                                                        </td>
                                                                        <td>
                                                                            <?= Utils::getStarsByRate((int)$dataAllRequestCargoByIDITEM->request_rate); ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            if ($dataAllRequestCargoByIDITEM->request_status == "accepted") {
                                                                                echo "<span class='badge badge-soft-success font-12'>" . $lang['accepted'] . "</span>";
                                                                            } elseif ($dataAllRequestCargoByIDITEM->request_status == "pending") {
                                                                                echo "<span class='badge badge-soft-warning font-12 badgeRequestStatus' data-mj-flag>" . $lang['pending'] . "</span>";
                                                                            } elseif ($dataAllRequestCargoByIDITEM->request_status == "rejected") {
                                                                                echo "<span class='badge badge-soft-danger font-12 badgeRequestStatus' data-mj-flag>" . $lang['rejected'] . "</span>";
                                                                            } elseif ($dataAllRequestCargoByIDITEM->request_status == "progress") {
                                                                                echo "<span class='badge badge-soft-info font-12'>" . $lang['progress'] . "</span>";
                                                                            } elseif ($dataAllRequestCargoByIDITEM->request_status == "canceled") {
                                                                                echo "<span class='badge badge-soft-secondary font-12'>" . $lang['canceled'] . "</span>";
                                                                            } elseif ($dataAllRequestCargoByIDITEM->request_status == "completed") {
                                                                                echo "<span class='badge badge-soft-primary font-12'>" . $lang['completed'] . "</span>";
                                                                            } else {
                                                                                echo "<span class='badge badge-soft-pink font-12'>" . $dataAllRequestCargoByIDITEM->request_status . "</span>";
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            $lists = ['pending', 'accepted', 'rejected'];
                                                                            if (in_array($dataAllRequestCargoByIDITEM->request_status, $lists)) {
                                                                                ?>
                                                                                <a href="javascript: void(0);"
                                                                                   data-mj-status="rejected"
                                                                                   data-bs-toggle="tooltip"
                                                                                   data-bs-placement="top"
                                                                                   title="<?= $lang['change_status_rejected']; ?>"
                                                                                   data-mj-reguest-id="<?= $dataAllRequestCargoByIDITEM->request_id; ?>"
                                                                                   class="action-icon changeRequestSatus">
                                                                                    <i class="mdi mdi-minus-circle-outline text-danger"></i>
                                                                                </a>
                                                                                <a href="javascript: void(0);"
                                                                                   data-mj-status="pending"
                                                                                   data-bs-toggle="tooltip"
                                                                                   data-bs-placement="top"
                                                                                   title="<?= $lang['change_status_pending']; ?>"
                                                                                   data-mj-reguest-id="<?= $dataAllRequestCargoByIDITEM->request_id; ?>"
                                                                                   class="action-icon changeRequestSatus">
                                                                                    <i class="mdi mdi-alert-circle-outline text-warning"></i>
                                                                                </a>
                                                                                <a href="javascript: void(0);"
                                                                                   data-mj-status="accepted"
                                                                                   data-bs-toggle="tooltip"
                                                                                   data-bs-placement="top"
                                                                                   title="<?= $lang['change_status_accepted']; ?>"
                                                                                   data-mj-reguest-id="<?= $dataAllRequestCargoByIDITEM->request_id; ?>"
                                                                                   class="action-icon changeRequestSatus">
                                                                                    <i class="mdi mdi-check-circle-outline text-success"></i>
                                                                                </a>
                                                                                <?php
                                                                            }
                                                                            ?>

                                                                            <a href="javascript: void(0);"
                                                                               data-bs-toggle="tooltip"
                                                                               data-bs-placement="top"
                                                                               title="<?= $lang['show_images']; ?>"
                                                                               data-mj-request-id="<?= $dataAllRequestCargoByIDITEM->request_id; ?>"
                                                                               class="action-icon showRequest">
                                                                                <i class="mdi mdi-image-filter-vintage"></i>
                                                                            </a>
                                                                            <a target="_self"
                                                                               href="<?= Utils::fileExist($dataAllRequestCargoByIDITEM->request_receipt, POSTER_DEFAULT); ?>"
                                                                               data-bs-toggle="tooltip"
                                                                               data-bs-placement="top"
                                                                               title="<?= $lang['receipt']; ?>"
                                                                               class="action-icon">
                                                                                <i class="mdi mdi-receipt"></i>
                                                                            </a>
                                                                            <a target="_self"
                                                                               href="/admin/car/<?= $dataAllRequestCargoByIDITEM->car_id; ?>"
                                                                               data-bs-toggle="tooltip"
                                                                               data-bs-placement="top"
                                                                               title="<?= $lang['car_info']; ?>"
                                                                               class="action-icon">
                                                                                <i class="mdi mdi-dump-truck"></i>
                                                                            </a>
                                                                            <a href="/admin/users/info/<?= $dataAllRequestCargoByIDITEM->user_id; ?>"
                                                                               data-bs-toggle="tooltip"
                                                                               data-bs-placement="top"
                                                                               title="<?= $lang['user_info']; ?>"
                                                                               target="_self" class="action-icon">
                                                                                <i class="mdi mdi-eye"></i>
                                                                            </a>
                                                                            <?php
                                                                            if ($dataAllRequestCargoByIDITEM->request_cancel_text) {
                                                                                ?>
                                                                                <a href="javascript: void(0);"
                                                                                   data-bs-toggle="tooltip"
                                                                                   data-bs-placement="top"
                                                                                   title="<?= $lang['cargo_cancel_desc']; ?>"
                                                                                   data-mj-request-id="reqqq-<?= $dataAllRequestCargoByIDITEM->request_id; ?>"
                                                                                   class="action-icon showCancelRequest">
                                                                                    <i class="mdi mdi-cancel text-info"></i>
                                                                                </a>
                                                                                <textarea class="d-none"
                                                                                          id="reqqq-<?= $dataAllRequestCargoByIDITEM->request_id; ?>"><?= $dataAllRequestCargoByIDITEM->request_cancel_text; ?></textarea>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>

                                                            </tbody>

                                                        </table>
                                                    </div>

                                                </div>
                                                <!-- end col -->
                                            </div>
                                            <!-- end row -->
                                        </div>

                                        <div class="tab-pane" id="basictab3">
                                            <div class="row">
                                                <div class="col-12">


                                                    <div class="table-responsive">
                                                        <table class="table table-striped mb-0">
                                                            <thead>

                                                            <tr>
                                                                <th><?= $lang['request_number']; ?></th>
                                                                <th><?= $lang['title']; ?></th>
                                                                <th><?= $lang['request_price']; ?></th>
                                                                <th><?= $lang['date']; ?></th>
                                                                <th><?= $lang['status']; ?></th>
                                                                <th><?= $lang['action']; ?></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            $flagExtraExpenses = true;
                                                            if (!empty($dataAllExtraExpensesByCargoId)) {
                                                                foreach ($dataAllExtraExpensesByCargoId as $dataAllExtraExpensesByCargoIdITEM) {
                                                                    $flagExtraExpenses = false;
                                                                    ?>
                                                                    <tr>
                                                                        <td><?= $dataAllExtraExpensesByCargoIdITEM->request_id; ?></td>
                                                                        <td><?= $dataAllExtraExpensesByCargoIdITEM->expense_name; ?></td>
                                                                        <td><?php
                                                                            $currencyName = '';
                                                                            if (!empty($dataAllCurrencies)) {
                                                                                foreach ($dataAllCurrencies as $dataAllCurrenciesITEM) {
                                                                                    if ($dataAllCurrenciesITEM->currency_id == $dataAllExtraExpensesByCargoIdITEM->expense_monetary_unit) {
                                                                                        $currencyName = (!empty(array_column(json_decode($dataAllCurrenciesITEM->currency_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                                            array_column(json_decode($dataAllCurrenciesITEM->currency_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                                                                                    }
                                                                                }
                                                                            }

                                                                            echo number_format($dataAllExtraExpensesByCargoIdITEM->expense_price) . " " . $currencyName; ?></td>
                                                                        <td>
                                                                            <bdi><?= Utils::getTimeCountry($Settings['date_format'], $dataAllExtraExpensesByCargoIdITEM->expense_date); ?></bdi>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            if ($dataAllExtraExpensesByCargoIdITEM->expense_status == "accepted") {
                                                                                echo "<span class='badge badge-soft-success font-12 badgeExpenseStatus' data-mj-flag>" . $lang['accepted'] . "</span>";
                                                                            } elseif ($dataAllExtraExpensesByCargoIdITEM->expense_status == "pending") {
                                                                                echo "<span class='badge badge-soft-warning font-12 badgeExpenseStatus' data-mj-flag>" . $lang['pending'] . "</span>";
                                                                            } elseif ($dataAllExtraExpensesByCargoIdITEM->expense_status == "rejected") {
                                                                                echo "<span class='badge badge-soft-danger font-12 badgeExpenseStatus' data-mj-flag>" . $lang['rejected'] . "</span>";
                                                                            } elseif ($dataAllExtraExpensesByCargoIdITEM->expense_status == "canceled") {
                                                                                echo "<span class='badge badge-soft-secondary font-12 badgeExpenseStatus' data-mj-flag>" . $lang['canceled'] . "</span>";
                                                                            } else {
                                                                                echo "<span class='badge badge-soft-pink font-12 badgeExpenseStatus' data-mj-flag>" . $dataAllExtraExpensesByCargoIdITEM->expense_status . "</span>";
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <a href="javascript: void(0);"
                                                                               data-mj-status="pending"
                                                                               data-bs-toggle="tooltip"
                                                                               data-bs-placement="top"
                                                                               title="<?= $lang['change_status_pending']; ?>"
                                                                               data-mj-extra-id="<?= $dataAllExtraExpensesByCargoIdITEM->expense_id; ?>"
                                                                               class="action-icon changeExtraExpensesStatus">
                                                                                <i class="mdi mdi-refresh-circle text-warning"></i>
                                                                            </a>
                                                                            <a href="javascript: void(0);"
                                                                               data-mj-status="accepted"
                                                                               data-bs-toggle="tooltip"
                                                                               data-bs-placement="top"
                                                                               title="<?= $lang['change_status_accepted']; ?>"
                                                                               data-mj-extra-id="<?= $dataAllExtraExpensesByCargoIdITEM->expense_id; ?>"
                                                                               class="action-icon changeExtraExpensesStatus">
                                                                                <i class="mdi mdi-refresh-circle text-success"></i>
                                                                            </a>
                                                                            <a href="javascript: void(0);"
                                                                               data-mj-status="rejected"
                                                                               data-bs-toggle="tooltip"
                                                                               data-bs-placement="top"
                                                                               title="<?= $lang['change_status_rejected']; ?>"
                                                                               data-mj-extra-id="<?= $dataAllExtraExpensesByCargoIdITEM->expense_id; ?>"
                                                                               class="action-icon changeExtraExpensesStatus">
                                                                                <i class="mdi mdi-refresh-circle text-danger"></i>
                                                                            </a>
                                                                            <a href="javascript: void(0);"
                                                                               data-mj-status="canceled"
                                                                               data-bs-toggle="tooltip"
                                                                               data-bs-placement="top"
                                                                               title="<?= $lang['change_status_canceled']; ?>"
                                                                               data-mj-extra-id="<?= $dataAllExtraExpensesByCargoIdITEM->expense_id; ?>"
                                                                               class="action-icon changeExtraExpensesStatus">
                                                                                <i class="mdi mdi-refresh-circle"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            }
                                                            if ($flagExtraExpenses) {
                                                                ?>
                                                                <tr>
                                                                    <td class="text-info text-center"
                                                                        colspan="6"><?= $lang['no_extra_expenses']; ?></td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            ?>
                                                            </tbody>
                                                        </table>
                                                    </div>


                                                </div>
                                                <!-- end col -->
                                            </div>
                                            <!-- end row -->
                                        </div>

                                        <div class="tab-pane" id="basictab4">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped mb-0">
                                                            <thead>
                                                            <tr>
                                                                <th><?= $lang['admin_name']; ?></th>
                                                                <th><?= $lang['action']; ?></th>
                                                                <th><?= $lang['info_added']; ?></th>
                                                                <th class="d-none"><?= $lang['a_value_old']; ?></th>
                                                                <th class="d-none"><?= $lang['a_value_new']; ?></th>
                                                                <th><?= $lang['date']; ?></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            $dataOptions = @json_decode($dataCargoByID->cargo_options);

                                                            if (!empty($dataOptions)) {
                                                                foreach ($dataOptions as $loop) {

                                                                    $name = '';
                                                                    if (!empty($dataAllAdmins)) {
                                                                        foreach ($dataAllAdmins as $dataAllAdminsLOOP) {
                                                                            if ($dataAllAdminsLOOP->admin_id == $loop->admin) {
                                                                                $name = $dataAllAdminsLOOP->admin_nickname;
                                                                                break;
                                                                            }
                                                                        }
                                                                    }

                                                                    ?>
                                                                    <tr>
                                                                        <td><?= $name; ?></td>
                                                                        <td><?php
                                                                            if ($loop->type == "expense_id") {
                                                                                echo $lang['change_extra_status'];
                                                                            } elseif ($loop->type == "request_id") {
                                                                                echo $lang['a_change_request_status'];
                                                                            } elseif ($loop->type == "cargo_origin_id") {
                                                                                echo $lang['location_cargo_origin'];
                                                                            } elseif ($loop->type == "cargo_origin_customs_id") {
                                                                                echo $lang['location_cargo_customs_of_origin'];
                                                                            } elseif ($loop->type == "cargo_destination_id") {
                                                                                echo $lang['location_cargo_destination'];
                                                                            } elseif ($loop->type == "cargo_destination_customs_id") {
                                                                                echo $lang['location_cargo_destination_customs'];
                                                                            } elseif ($loop->type == "cargo_recommended_price") {
                                                                                echo $lang['a_change_cargo_recommended_price'];
                                                                            } elseif ($loop->type == "cargo_monetary_unit") {
                                                                                echo $lang['a_change_cargo_monetary_unit'];
                                                                            } elseif ($loop->type == "cargo_status") {
                                                                                echo $lang['change_cargo_status'];
                                                                            } elseif ($loop->type == "cargo_name_fa_IR") {
                                                                                echo $lang['title_Persian'];
                                                                            } elseif ($loop->type == "cargo_name_en_US") {
                                                                                echo $lang['title_English'];
                                                                            } elseif ($loop->type == "cargo_name_tr_Tr") {
                                                                                echo $lang['title_Turkish'];
                                                                            } elseif ($loop->type == "cargo_name_ru_RU") {
                                                                                echo $lang['title_Russia'];
                                                                            } elseif ($loop->type == "category_id") {
                                                                                echo $lang['category_cargo'];
                                                                            } elseif ($loop->type == "type_id") {
                                                                                echo $lang['category_car'];
                                                                            } elseif ($loop->type == "cargo_weight") {
                                                                                echo $lang['cargo_weight'];
                                                                            } elseif ($loop->type == "cargo_volume") {
                                                                                echo $lang['cargo_volume'];
                                                                            } elseif ($loop->type == "cargo_description_fa_IR") {
                                                                                echo $lang['description_Persian'];
                                                                            } elseif ($loop->type == "cargo_description_en_US") {
                                                                                echo $lang['description_English'];
                                                                            } elseif ($loop->type == "cargo_description_tr_Tr") {
                                                                                echo $lang['description_Turkish'];
                                                                            } elseif ($loop->type == "cargo_description_ru_RU") {
                                                                                echo $lang['description_Russia'];
                                                                            } elseif ($loop->type == "cargo_start_date") {
                                                                                echo $lang['start_date'];
                                                                            } elseif ($loop->type == "cargo_green") {
                                                                                echo $lang['a_green_street_customs'];
                                                                            } else {
                                                                                echo $loop->type;
                                                                            }
                                                                            ?></td>
                                                                        <td><?= $loop->data; ?></td>
                                                                        <td class="d-none"><?= $loop->old; ?></td>
                                                                        <td class="d-none"><?= $loop->new; ?></td>
                                                                        <td>
                                                                            <bdi><?= Utils::getTimeCountry($Settings['data_time_format'], $loop->date); ?></bdi>
                                                                        </td>

                                                                    </tr>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!-- end col -->
                                            </div>
                                            <!-- end row -->
                                        </div>

                                    </div>
                                    <!-- tab-content -->
                                </div>
                                <!-- end #basicwizard-->
                            </form>


                        </div>
                    </div>
                </div>
                <!-- End Tabs -->
            </div>


            <!-- Start MODAL Change Location -->
            <div class="modal fade" id="cityModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header py-3 px-4 border-bottom-0 d-block">
                            <button type="button" class="btn-close float-end" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            <h5 class="modal-title"><?= $lang['a_change_location_2']; ?></h5>
                        </div>
                        <div class="modal-body px-4 pb-4 pt-0">
                            <form class="needs-validation" novalidate>
                                <div class="row">

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="selectCountry"
                                                   class="form-label"><?= $lang['a_select_country']; ?></label>
                                            <select class="form-select selectLocation"
                                                    name="selectCountry"
                                                    id="selectCountry"
                                                    data-toggle="select2"
                                                    data-width="100%"
                                                    data-tj-type="">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="selectCity"
                                                   class="form-label"><?= $lang['select_city']; ?></label>
                                            <select class="form-select selectLocation"
                                                    name="selectCity"
                                                    id="selectCity"
                                                    data-toggle="select2"
                                                    data-width="100%"
                                                    data-mj-type="">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="selectCustoms"
                                                   class="form-label"><?= $lang['a_select_customs']; ?></label>
                                            <select class="form-select selectLocation"
                                                    name="selectCustoms"
                                                    id="selectCustoms"
                                                    data-toggle="select2"
                                                    data-width="100%"
                                                    data-mj-type="">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6 col-4">
                                    </div>
                                    <div class="col-md-6 col-8 text-end">
                                        <button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">
                                            <?= $lang['closes']; ?>
                                        </button>
                                        <button type="button"
                                                class="btn btn-success"
                                                id="submitModal"
                                                data-tj-type=""
                                                data-style="zoom-in">
                                            <?= $lang['submit_change']; ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div> <!-- end modal-content-->
                </div> <!-- end modal dialog-->
            </div>
            <!-- End MODAL Change Location-->


            <!-- Start MODAL Show Request Image -->
            <div class="modal fade" id="requestImageModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header py-3 px-4 border-bottom-0 d-block">
                            <button type="button" class="btn-close float-end" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            <h5 class="modal-title"><?= $lang['show_images']; ?></h5>
                        </div>
                        <div class="modal-body px-4 pb-4 pt-0">

                            <div class="swiper showImageReq">
                                <div class="swiper-wrapper" id="showImageReqID">
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>

                        </div>
                    </div> <!-- end modal-content-->
                </div> <!-- end modal dialog-->
            </div>
            <!-- End MODAL Show Request Image-->


            <!-- Start Request Cancel Modal -->
            <div id="requestmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"
                                id="standard-modalLabel"> <?= $lang['reason_cancel_cargo_driver']; ?></h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p id="requestModalValue"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                <?= $lang['closes']; ?>
                            </button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
            <!-- Start Request Cancel Modal -->

            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('admin-cargo-info') ?>">
            <input id="startDefault" type="hidden">

            <script>
                var var_lang = '<?php
                    $array_green = [
                        ["text" => $lang['a_yes'], 'value' => 'yes'],
                        ["text" => $lang['a_no'], 'value' => 'no']
                    ];
                    $array_category = [];
                    if (!empty($dataAllCargoCategory)) {
                        foreach ($dataAllCargoCategory as $dataAllCargoCategoryITEM) {
                            $category_Cargo_name = (!empty(array_column(json_decode($dataAllCargoCategoryITEM->category_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                array_column(json_decode($dataAllCargoCategoryITEM->category_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                            array_push($array_category, ["text" => $category_Cargo_name, 'value' => $dataAllCargoCategoryITEM->category_id]);
                        }

                    } else {
                        array_push($array_category, []);
                    }

                    $array_type = [];
                    if (!empty($dataAllCarsTypes)) {
                        foreach ($dataAllCarsTypes as $dataAllCarsTypesITEM) {
                            $Car_name = (!empty(array_column(json_decode($dataAllCarsTypesITEM->type_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                array_column(json_decode($dataAllCarsTypesITEM->type_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                            array_push($array_type, ["text" => $Car_name, 'value' => $dataAllCarsTypesITEM->type_id]);
                        }

                    } else {
                        array_push($array_type, []);
                    }
                    $array_status = [
                        ["value" => "accepted", "text" => $lang['accepted']],
                        ["value" => "pending", "text" => $lang['pending']],
                        ["value" => "rejected", "text" => $lang['rejected']],
//                            ["value"=>"progress","text"=>$lang['progress']],
//                            ["value"=>"canceled","text"=>$lang['canceled']],
//                            ["value"=>"completed","text"=>$lang['completed']],

                    ];
                    $var_lang = [
                        'error' => $lang['error'],
                        'successful' => $lang['successful'],
                        'warning' => $lang['warning'],
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'successful_submit_mag' => $lang['successful_submit_mag'],
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
                        'token_error' => $lang['token_error'],
                        'change_status_pending_to_rejected' => $lang['change_status_pending_to_rejected'],
                        'change_status_rejected_to_pending' => $lang['change_status_rejected_to_pending'],
                        'rejected' => $lang['rejected'],
                        'pending' => $lang['pending'],
                        'accepted' => $lang['accepted'],
                        'canceled' => $lang['canceled'],
                        'a_select_country_2' => $lang['a_select_country_2'],
                        'a_set_admin_min_one' => $lang['a_set_admin_min_one'],
                        'a_empty' => $lang['a_empty'],
//                        'tempp' => '[{"value":1,"text":"3szd423424"},{"value":2,"text":"asa"}]',
                        'array_category' => $array_category,
                        'array_type' => $array_type,
                        'array_status' => $array_status,
                        'array_currency' => $array_currency,
                        'array_green' => $array_green,
                    ];
                    print_r(json_encode($var_lang));  ?>';
            </script>
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter();

        // start roles 4
    } else {
        setcookie('EID', null, -1, '/');
        setcookie('UID', null, -1, '/');
        setcookie('INF', null, -1, '/');
        unset($_COOKIE['EID']);
        unset($_COOKIE['UID']);
        unset($_COOKIE['INF']);

        header('Location: ' . ADMIN_HEADER_LOCATION);
    }
} else {

    setcookie('EID', null, -1, '/');
    setcookie('UID', null, -1, '/');
    setcookie('INF', null, -1, '/');
    unset($_COOKIE['EID']);
    unset($_COOKIE['UID']);
    unset($_COOKIE['INF']);

    header('Location: ' . ADMIN_HEADER_LOCATION);
}
// end roles 4