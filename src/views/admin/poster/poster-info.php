<?php
$pageSlug = "a_poster";
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
        $result = Poster::getPosterInfoById($id);
        $data = [];
        if ($result->status == 200 && !empty($result->response)) {
            $data = $result->response;
        }

        if (empty($data)) {
            header('Location: /admin/poster');
        }


        /**
         * Get User Info By Id
         */
        $resultUser = AUser::getUserInfoById($data->user_id);
        $dataUser = [];
        if ($resultUser->status == 200 && !empty($resultUser->response)) {
            $dataUser = $resultUser->response[0];
        }
        if (empty($dataUser)) {
            header('Location: /admin');
        }

        $UserName = $lang['guest_user'];
        if (!empty($dataUser->user_firstname)) {
            $UserName = Security::decrypt($dataUser->user_firstname) . " " . Security::decrypt($dataUser->user_lastname);
        }

        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        $dataParent = [];
        if (!is_null($data->poster_parent_id)) {
            $resultParent = Poster::getPosterInfoById($data->poster_parent_id);
            if ($resultParent->status == 200 && !empty($resultParent->response)) {
                $dataParent = $resultParent->response;
            }
        }

        $dataChild = [];
        $resultChild = Poster::getPosterParentInfoById($id);
        if ($resultChild->status == 200 && !empty($resultChild->response)) {
            $dataChild = $resultChild->response;
        }


        /**
         * Get All Category Cars
         */
        $resultAllCarsTypes = Car::getAllCarsTypes();
        $dataAllCarsTypes = [];
        if ($resultAllCarsTypes->status == 200 && !empty($resultAllCarsTypes->response)) {
            $dataAllCarsTypes = $resultAllCarsTypes->response;
        }


        $BrandsModelsR = PosterC::getModelsPosterFromAdmin();
        $BrandsModels = [];
        if ($BrandsModelsR->status == 200) {
            $BrandsModels = $BrandsModelsR->response;
        }
        $dataB = [];
        foreach ($BrandsModels as $loop) {
            if ($loop->model_id == $data->model_id) {
//                echo (!empty(array_column(json_decode($loop->brand_name, true), 'value', 'slug')[$language])) ?
//                    array_column(json_decode($loop->brand_name, true), 'value', 'slug')[$language] : "";;
                $dataB = $loop;
            }
        }


        $GearboxsR = PosterC::getAllGearboxFromTabel();
        $Gearboxs = [];
        if ($GearboxsR->status == 200) {
            $Gearboxs = $GearboxsR->response;
        }


        $fuelsR = PosterC::getAllFuelFromTabel();
        $fuels = [];
        if ($fuelsR->status == 200) {
            $fuels = $fuelsR->response;
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
         * Get All Admins
         */
        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
        }


        $resultAllProperties = Poster::getAllProperties($id);
        $dataAllProperties = [];
        if ($resultAllProperties->status == 200 && !empty($resultAllProperties->response)) {
            $dataAllProperties = $resultAllProperties->response;
        }

        $resultProperties = PosterC::getAllPropertyFromTabel();
        $dataProperties = [];
        if ($resultProperties->status == 200 && !empty($resultProperties->response)) {
            $dataProperties = $resultProperties->response;
        }


        /**
         * Get Country And City Name
         */
        $resultLocation = Location::getMultiCityAndCountryByCityId($data->city_id);
        $dataLocation = [];
        if ($resultLocation->status == 200 && !empty($resultLocation->response)) {
            $dataLocation = $resultLocation->response[0];
        }


        $resultPosterTransactions = Poster::getAllPosterTransactions();
        $dataPosterTransactions = [];
        if ($resultPosterTransactions->status == 200 && !empty($resultPosterTransactions->response)) {
            $dataPosterTransactions = $resultPosterTransactions->response;
        }


        $getPosterReports = Poster::getPosterReports($id);

        $a_gearboxs = [];
        $a_fuels = [];
        $a_trailers = [];

        // Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
        enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
//        enqueueStylesheet('editable-css', '/dist/libs/x-editable/bootstrap-editable/css/bootstrap-editable.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');;
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');


        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
//        enqueueScript('editable-js', '/dist/libs/x-editable/bootstrap-editable/js/bootstrap-editable.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('poster-info', '/dist/js/admin/poster/poster-info.init.js');

        getHeader($lang["a_poster_info"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_edit',
        ]);


        // start roles 2
        if ($flagSlug) {
            // end roles 2
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


            </style>
            <div class="row">
                <?php
                $flagBrandAlertModal = false;
                $flagModelAlertModal = false;

                if (!empty($dataB)) {
                    if ($dataB->brand_status == "user") {
                        $flagBrandAlertModal = true;
                    }
                }
                if (!empty($dataB)) {
                    if ($dataB->model_status == "user") {
                        $flagModelAlertModal = true;
                    }
                }
                if ($flagBrandAlertModal || $flagModelAlertModal) {
                    ?>
                    <div id="myModal" class="modal show" tabindex="-1" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title"></h4>
                                    <button type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <?php if ($flagBrandAlertModal) { ?>
                                        <h5>
                                            <i class="mdi mdi-check-box-outline text-warning"></i> <?= $lang['a_new_brand']; ?>
                                        </h5>
                                    <?php }
                                    if ($flagModelAlertModal) { ?>
                                        <h5>
                                            <i class="mdi mdi-check-box-outline text-warning"></i> <?= $lang['a_new_model']; ?>
                                        </h5>
                                    <?php } ?>
                                </div>
                                <div class="modal-footer d-flex justify-content-center">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                        <?= $lang['closes']; ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="col-12">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["b-ads-title"]; ?></h5>
                                    <div class="row">

                                        <div class="col-lg-9">
                                            <div class="form-floating mb-3">
                                                <input id="poster-fa_IR" type="text" class="form-control nameAds"
                                                       value="<?= $data->poster_title_fa_IR ?>">
                                                <label for="nameAds">
                                                    <?= $lang['poster_title_fa_IR'] ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <button class="btn btn-primary submit-poster-title" data-language="fa_IR"
                                                    data-poster-id="<?= $_REQUEST['id'] ?>"><?= $lang['change_title'] ?></button>
                                        </div>


                                        <div class="col-lg-9">
                                            <div class="form-floating mb-3">
                                                <input id="poster-en_US" type="text" class="form-control nameAds"
                                                       value="<?= $data->poster_title_en_US ?>">
                                                <label for="nameAds">
                                                    <?= $lang['poster_title_en_US'] ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <button class="btn btn-primary submit-poster-title" data-language="en_US"
                                                    data-poster-id="<?= $_REQUEST['id'] ?>"><?= $lang['change_title'] ?></button>
                                        </div>


                                        <div class="col-lg-9">
                                            <div class="form-floating mb-3">
                                                <input id="poster-tr_Tr" type="text" class="form-control nameAds"
                                                       value="<?= $data->poster_title_tr_Tr ?>">
                                                <label for="nameAds">
                                                    <?= $lang['poster_title_tr_Tr'] ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <button class="btn btn-primary submit-poster-title" data-language="tr_Tr"
                                                    data-poster-id="<?= $_REQUEST['id'] ?>"><?= $lang['change_title'] ?></button>
                                        </div>


                                        <div class="col-lg-9">
                                            <div class="form-floating mb-3">
                                                <input id="poster-ru_RU" type="text" class="form-control nameAds"
                                                       value="<?= $data->poster_title_ru_RU ?>">
                                                <label for="nameAds">
                                                    <?= $lang['poster_title_ru_RU'] ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <button class="btn btn-primary submit-poster-title" data-language="ru_RU"
                                                    data-poster-id="<?= $_REQUEST['id'] ?>"><?= $lang['change_title'] ?></button>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-7">
                    <div class="row">

                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <!-- Start Poster Info -->
                            <div class="card">
                                <div class="card-body">

                                    <h5 id="poster-id" data-mj-id="<?= $id; ?>"
                                        class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_poster_info"]; ?>
                                        <?php if ($data->poster_immediate_time - time() > 0) { ?>
                                            <div class="d-inline-block float-end">
                                                <span data-tj-time="<?= $data->poster_immediate_time; ?>"></span>
                                                <i class="mdi mdi-alarm-light text-danger on-off"></i>
                                            </div>
                                        <?php } ?>
                                    </h5>

                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                            <tr>
                                                <td><?= $lang["a_user_name"]; ?> :</td>
                                                <td>
                                                    <div class="d-flex align-items-start">
                                                        <img class="me-2 avatar-sm rounded-circle"
                                                             src="<?= Utils::fileExist($dataUser->user_avatar, USER_AVATAR); ?>"
                                                             alt="<?= $UserName; ?>">
                                                        <div class="w-100">
                                                            <h5>
                                                                <a href="/admin/users/info/<?= $data->user_id; ?>">
                                                                    <?= $UserName; ?>
                                                                </a>
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_poster_type"]; ?> :</td>
                                                <td>
                                                    <?= (is_null($data->poster_parent_id)) ? $lang['a_new_2'] . ' <i class="mdi mdi-new-box text-success font-16"></i>' : $lang['a_update'] . ' <i class="mdi mdi-update text-warning font-16"></i>'; ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_type_2"]; ?> :</td>
                                                <td>
                                                   <span id="change_poster_type"
                                                         data-type="select"
                                                         data-mj-type="poster_type">
                                                       <?= (in_array($data->poster_type, ['trailer', 'truck'])) ? $lang["a_" . $data->poster_type] : $data->poster_type; ?>
                                                   </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["country"]; ?> :</td>
                                                <td>
                                                   <span id="change_poster_type"
                                                         data-type="select"
                                                         data-mj-type="poster_type">
                                                            <?php
                                                            if (!empty($dataLocation)) {
                                                                echo (!empty(array_column(json_decode($dataLocation->country_name, true), 'value', 'slug')[$language])) ?
                                                                    array_column(json_decode($dataLocation->country_name, true), 'value', 'slug')[$language] : $data->country_id;
                                                            }
                                                            ?>
                                                   </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["city"]; ?> :</td>
                                                <td>
                                                   <span id="change_poster_type"
                                                         data-type="select"
                                                         data-mj-type="poster_type">
                                                            <?php
                                                            if (!empty($dataLocation)) {
                                                                echo (!empty(array_column(json_decode($dataLocation->city_name, true), 'value', 'slug')[$language])) ?
                                                                    array_column(json_decode($dataLocation->city_name, true), 'value', 'slug')[$language] : $data->city_id;

                                                            }
                                                            ?>
                                                   </span>
                                                </td>
                                            </tr>


                                            <tr>
                                                <td><?= $lang["a_brand"]; ?> :</td>
                                                <td>
                                                    <?php
                                                    if (!empty($dataB)) {
                                                        if ($dataB->brand_status == "user") {
                                                            echo '<a href="/admin/category/brand/edit/' . $loop->brand_id . '" class="text-warning">' . array_column(json_decode($dataB->brand_name, true), 'value', 'slug')[$language] . '</a>';
                                                        } else {
                                                            echo '<a href="/admin/category/brand/edit/' . $loop->brand_id . '" class="text-primary">' . array_column(json_decode($dataB->brand_name, true), 'value', 'slug')[$language] . '</a>';
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_model"]; ?> :</td>
                                                <td>
                                                    <?php
                                                    if (!empty($dataB)) {
                                                        if (!empty(array_column(json_decode($dataB->model_name, true), 'value', 'slug')[$language])) {
                                                            if ($dataB->model_status == "user") {
                                                                echo '<a href="/admin/category/model/edit/' . $loop->model_id . '" class="text-warning">' . array_column(json_decode($dataB->model_name, true), 'value', 'slug')[$language] . '</a>';
                                                            } else {
                                                                echo '<a href="/admin/category/model/edit/' . $loop->model_id . '" class="text-primary">' . array_column(json_decode($dataB->model_name, true), 'value', 'slug')[$language] . '</a>';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                            </tr>

                                            <?php if ($data->poster_type == "truck") { ?>

                                                <tr>
                                                    <td><?= $lang["a_truck_status_poster"]; ?> :</td>
                                                    <td>
                                                   <span id="change_poster_type_status"
                                                         data-type="select"
                                                         data-bs-toggle="tooltip"
                                                         data-bs-placement="top"

                                                         data-mj-type="poster_type_status">
                                                       <?= (in_array($data->poster_type_status, ['new', 'stock', 'order'])) ? $lang["a_" . $data->poster_type_status] : $data->poster_type_status; ?>
                                                   </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?= $lang["a_gearboxs"]; ?> :</td>
                                                    <td>
                                                       <span id="change_gearbox_id"
                                                             data-type="select"
                                                             data-bs-toggle="tooltip"
                                                             data-bs-placement="top"
                                                             data-mj-type="gearbox_id">
                                                           <?php
                                                           foreach ($Gearboxs as $loop) {
                                                               $name = (!empty(array_column(json_decode($loop->gearbox_name, true), 'value', 'slug')[$language])) ?
                                                                   array_column(json_decode($loop->gearbox_name, true), 'value', 'slug')[$language] : "";

                                                               if ($loop->gearbox_id == $data->gearbox_id) {
                                                                   echo $name;
                                                               }
                                                               if ($loop->gearbox_status == "active") {
                                                                   array_push($a_gearboxs, ["text" => $name, 'value' => $loop->gearbox_id]);
                                                               }
                                                           }
                                                           ?>
                                                       </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?= $lang["a_fuel"]; ?> :</td>
                                                    <td>
                                                       <span id="change_fuel_id"
                                                             data-type="select"
                                                             data-bs-toggle="tooltip"
                                                             data-bs-placement="top"
                                                             data-mj-type="fuel_id">
                                                           <?php
                                                           foreach ($fuels as $loop) {
                                                               $name = (!empty(array_column(json_decode($loop->fuel_name, true), 'value', 'slug')[$language])) ?
                                                                   array_column(json_decode($loop->fuel_name, true), 'value', 'slug')[$language] : "";

                                                               if ($loop->fuel_id == $data->fuel_id) {
                                                                   echo $name;
                                                               }
                                                               if ($loop->fuel_status == "active") {
                                                                   array_push($a_fuels, ["text" => $name, 'value' => $loop->fuel_id]);
                                                               }
                                                           }
                                                           ?>
                                                       </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?= $lang["a_out_color"]; ?> :</td>
                                                    <td>
                                                        <button id="change_poster_color_out"
                                                                data-mj-type="poster_color_out"
                                                                class="btn"
                                                                style="background-color:<?= $data->poster_color_out; ?> ">
                                                            <i class="mdi mdi-copyright font-18"></i>
                                                        </button>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?= $lang["a_built_year"]; ?> :</td>
                                                    <td>
                                                    <span id="change_poster_built"
                                                          data-type="number"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"

                                                          data-mj-type="poster_built">
                                                        <?= ($data->poster_built) ? $data->poster_built : 0; ?>
                                                     </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?= $lang["a_run_worked"]; ?> :</td>
                                                    <td>
                                                    <span id="change_poster_used"
                                                          data-type="number"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          data-mj-type="poster_used">
                                                        <?= ($data->poster_used) ? number_format($data->poster_used) : 0; ?>
                                                     </span>
                                                    </td>
                                                </tr>

                                            <?php } elseif ($data->poster_type == 'trailer') { ?>

                                                <tr>
                                                    <td><?= $lang["a_type_trailer"]; ?> :</td>
                                                    <td>
                                                       <span id="change_trailer_id"
                                                             data-type="select"
                                                             data-bs-toggle="tooltip"
                                                             data-bs-placement="top"

                                                             data-mj-type="trailer_id">
                                                           <?php

                                                           foreach ($dataAllCarsTypes as $loop) {
                                                               $Car_name = (!empty(array_column(json_decode($loop->type_name, true), 'value', 'slug')[$language])) ?
                                                                   array_column(json_decode($loop->type_name, true), 'value', 'slug')[$language] : "";

                                                               if ($loop->type_id == $data->trailer_id) {
                                                                   echo $Car_name;
                                                               }

                                                               array_push($a_trailers, ["text" => $Car_name, 'value' => $loop->type_id]);
                                                           }
                                                           ?>
                                                       </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><?= $lang["a_axis_count"]; ?> :</td>
                                                    <td>
                                                   <span id="change_poster_axis"
                                                         data-type="select"
                                                         data-bs-toggle="tooltip"
                                                         data-bs-placement="top"
                                                         data-mj-type="poster_axis">
                                                       <?= $data->poster_axis; ?>
                                                   </span>
                                                    </td>
                                                </tr>

                                            <?php } ?>

                                            <tr>
                                                <td><?= $lang["a_cash_2"]; ?> :</td>
                                                <td>
                                                   <span id="change_poster_cash"
                                                         data-type="select"
                                                         data-bs-toggle="tooltip"
                                                         data-bs-placement="top"

                                                         data-mj-type="poster_cash">
                                                       <?= (in_array($data->poster_cash, ['yes', 'no'])) ? $lang["a_" . $data->poster_cash] : $data->poster_cash; ?>
                                                   </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_leasing"]; ?> :</td>
                                                <td>
                                                   <span id="change_poster_leasing"
                                                         data-type="select"
                                                         data-bs-toggle="tooltip"
                                                         data-bs-placement="top"
                                                         data-mj-type="poster_leasing">
                                                       <?= (in_array($data->poster_leasing, ['yes', 'no'])) ? $lang["a_" . $data->poster_leasing] : $data->poster_leasing; ?>
                                                   </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_installment"]; ?> :</td>
                                                <td>
                                                   <span id="change_poster_installments"
                                                         data-type="select"
                                                         data-bs-toggle="tooltip"
                                                         data-bs-placement="top"
                                                         data-mj-type="poster_installments">
                                                       <?= (in_array($data->poster_installments, ['yes', 'no'])) ? $lang["a_" . $data->poster_installments] : $data->poster_installments; ?>
                                                   </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["recommended_price"]; ?> :</td>
                                                <td>
                                                    <span id="change_poster_price"
                                                          data-type="number"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"

                                                          data-mj-type="poster_price">
                                                        <?= ($data->poster_price) ? number_format($data->poster_price) : $lang['a_agreement']; ?>
                                                     </span>&nbsp;&nbsp;
                                                    <span id="change_currency_id"
                                                          data-type="select"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          data-mj-type="currency_id"><?php
                                                        $array_currency = [];
                                                        foreach ($dataAllCurrencies as $loop) {
                                                            $currency_name0 = (!empty(array_column(json_decode($loop->currency_name, true), 'value', 'slug')[$language])) ?
                                                                array_column(json_decode($loop->currency_name, true), 'value', 'slug')[$language] : $loop->currency_id;

                                                            if ($loop->currency_id == $data->currency_id) {
                                                                echo $currency_name0;
                                                            }
                                                            array_push($array_currency, ["text" => $currency_name0, 'value' => $loop->currency_id]);
                                                        }
                                                        ?>
                                                    </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["date_submit"]; ?> :</td>
                                                <td>
                                                    <bdi><?= Utils::getTimeCountry($Settings['date_format'], $data->poster_submit_date); ?></bdi>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_number_of_days_left"]; ?> :</td>
                                                <td><?= $data->poster_expire; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["u_number_call"]; ?> :</td>
                                                <td>
                                                   <span id="change_poster_phone"
                                                         data-type="number"
                                                         data-bs-toggle="tooltip"
                                                         data-bs-placement="top"

                                                         data-mj-type="poster_phone">
                                                       <?= $data->poster_phone; ?>
                                                   </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["support_whatsapp"]; ?> :</td>
                                                <td>
                                                   <span id="change_poster_whatsapp"
                                                         data-type="number"
                                                         data-bs-toggle="tooltip"
                                                         data-bs-placement="top"
                                                         data-mj-type="poster_whatsapp">
                                                       <?= $data->poster_whatsapp; ?>
                                                   </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["u_time_tel"]; ?> :</td>
                                                <td>
                                                    <?= $lang['u_from'] . " " . $data->poster_time_from . " " . $lang['u_to'] . " " . $data->poster_time_to; ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["description"]; ?> :</td>
                                                <td>
                                                    <span id="change_cargo_desc"
                                                          data-type="textarea"
                                                          style="white-space:unset"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          data-mj-type="poster_desc">
                                                        <?= $data->poster_desc; ?>
                                                    </span>
                                                </td>
                                            </tr>

                                            </tbody>
                                        </table>

                                    </div>
                                    <!-- end table-responsive -->


                                </div>
                            </div>
                            <!-- End Poster Info -->

                            <!-- Start Log -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-widgets">
                                        <a class="pe-1 lh-lg collapsed" data-bs-toggle="collapse" href="#cardCollpase3"
                                           role="button"
                                           aria-expanded="false" aria-controls="cardCollpase3"><i
                                                class="mdi mdi-minus"></i></a>
                                    </div>
                                    <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["all_info"]; ?></h5>

                                    <div class="table-responsive collapse" id="cardCollpase3"
                                         style="max-height: 176px;overflow: auto;">
                                        <table class="table mb-0 table-sm">
                                            <tbody>

                                            <?php
                                            if (!empty($data->poster_options)) {
                                                $temp = json_decode($data->poster_options);
                                                foreach ($temp as $loop) {
                                                    ?>
                                                    <tr>
                                                        <td><?= (isset($loop->admin)) ? $lang['admin'] : $lang['user']; ?></td>
                                                        <td>
                                                            <?php
                                                            if ($loop->status_old == "accepted") {
                                                                echo "<span class='badge badge-soft-success font-12'>" . $lang['a_user_inquiry_accepted'] . "</span>";
                                                            } elseif ($loop->status_old == "pending") {
                                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['u_inquiry_air_pending'] . "</span>";
                                                            } elseif ($loop->status_old == "rejected") {
                                                                echo "<span class='badge badge-soft-danger font-12'>" . $lang['reject'] . "</span>";
                                                            } elseif ($loop->status_old == "deleted") {
                                                                echo "<span class='badge badge-soft-secondary font-12'>" . $lang['deleted'] . "</span>";
                                                            } elseif ($loop->status_old == "expired") {
                                                                echo "<span class='badge badge-soft-secondary font-12'>" . $lang['expire'] . "</span>";
                                                            } elseif ($loop->status_old == "needed") {
                                                                echo "<span class='badge badge-soft-info font-12'>" . $lang['needed'] . "</span>";
                                                            } else {
                                                                echo "<span class='badge badge-soft-pink font-12'>" . $loop->status_old . "</span>";
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            if ($loop->status_new == "accepted") {
                                                                echo "<span class='badge badge-soft-success font-12'>" . $lang['a_user_inquiry_accepted'] . "</span>";
                                                            } elseif ($loop->status_new == "pending") {
                                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['u_inquiry_air_pending'] . "</span>";
                                                            } elseif ($loop->status_new == "rejected") {
                                                                echo "<span class='badge badge-soft-danger font-12'>" . $lang['reject'] . "</span>";
                                                            } elseif ($loop->status_new == "deleted") {
                                                                echo "<span class='badge badge-soft-secondary font-12'>" . $lang['deleted'] . "</span>";
                                                            } elseif ($loop->status_new == "expired") {
                                                                echo "<span class='badge badge-soft-secondary font-12'>" . $lang['expire'] . "</span>";
                                                            } elseif ($loop->status_new == "needed") {
                                                                echo "<span class='badge badge-soft-info font-12'>" . $lang['needed'] . "</span>";
                                                            } else {
                                                                echo "<span class='badge badge-soft-pink font-12'>" . $loop->status_new . "</span>";
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="d-none"><?= $loop->poster_id; ?></td>
                                                        <td class="d-none"><?= $loop->reason; ?></td>
                                                        <td class="text-end">
                                                            <bdi><?= Utils::getTimeCountry($Settings['date_format'], $loop->date); ?></bdi>
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
                            </div>
                            <!-- End Log -->

                            <?php
                            if (!empty($getPosterReports)) {
                                ?>
                                <!-- Start Reports -->
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-widgets">
                                            <a class="pe-1 lh-lg collapsed" data-bs-toggle="collapse"
                                               href="#cardCollpase4"
                                               role="button"
                                               aria-expanded="true" aria-controls="cardCollpase4"><i
                                                    class="mdi mdi-minus"></i></a>
                                        </div>
                                        <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_list_poster_reports"]; ?></h5>
                                        <div class="table-responsive " id="cardCollpase4"
                                             style="max-height: 176px;overflow: auto;">
                                            <table class="table mb-0 table-sm">
                                                <tbody>
                                                <?php foreach ($getPosterReports as $loop) {
                                                    if (is_null($loop->report_id)) {
                                                        $desc = $loop->rp_desc;
                                                    } else {
                                                        $desc = (!empty(array_column(json_decode($loop->report_title, true), 'value', 'slug')[$language])) ?
                                                            array_column(json_decode($loop->report_title, true), 'value', 'slug')[$language] : $loop->report_id;
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><?= $desc; ?></td>
                                                        <td>
                                                            <?php
                                                            if ($loop->rp_status == "pending") {
                                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['a_new_report'] . "</span>";
                                                            } elseif ($loop->rp_status == "reviewed") {
                                                                echo "<span class='badge badge-soft-primary font-12'>" . $lang['a_reviewed'] . "</span>";
                                                            } else {
                                                                echo "<span class='badge badge-soft-pink font-12'>" . $loop->rp_status . "</span>";
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <bdi> <?= Utils::getTimeCountry($Settings['date_format'], $loop->rp_submit_date); ?></bdi>
                                                        </td>
                                                        <td>
                                                            <a href="/admin/users/info/<?= $loop->user_id; ?>"
                                                               target="_self"
                                                               data-bs-toggle="tooltip"
                                                               data-bs-placement="top"
                                                               title="<?= $lang['user_info']; ?>"
                                                               class="action-icon">
                                                                <i class="mdi mdi-18px mdi-account"></i>
                                                            </a>
                                                            <?php if ($loop->rp_status == "pending") { ?>
                                                                <a href="javascript:void(0);"
                                                                   data-bs-toggle="tooltip"
                                                                   data-bs-placement="top"
                                                                   title="<?= $lang['a_change_status']; ?>"
                                                                   data-tj-pr="<?= $loop->rp_id; ?>"
                                                                   data-tj-poster-id="<?= $loop->poster_id; ?>"
                                                                   class="action-icon btn-report">
                                                                    <i class="mdi mdi-alert-circle-check-outline text-danger"></i>
                                                                </a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>


                                    </div>
                                </div>
                                <!-- End Reports -->
                            <?php } ?>
                        </div>


                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-5">
                    <div class="row">


                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <!--Change Status-->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">
                                        <?= $lang["status"]; ?>
                                    </h5>
                                    <table class="table mb-3">
                                        <tbody>
                                        <tr>
                                            <td><?= $lang["a_poster_type"]; ?> :</td>
                                            <td>
                                                <?= (is_null($data->poster_parent_id)) ? $lang['a_new_2'] . ' <i class="mdi mdi-new-box text-success font-16"></i>' : $lang['a_update'] . ' <i class="mdi mdi-update text-warning font-16"></i>'; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> <?= $lang["status"]; ?></td>
                                            <td>
                                                <?php
                                                if ($data->poster_status == "accepted") {
                                                    echo "<span class='badge badge-soft-success font-12'>" . $lang['a_user_inquiry_accepted'] . "</span>";
                                                } elseif ($data->poster_status == "pending") {
                                                    echo "<span class='badge badge-soft-warning font-12'>" . $lang['u_inquiry_air_pending'] . "</span>";
                                                } elseif ($data->poster_status == "rejected") {
                                                    echo "<span class='badge badge-soft-danger font-12'>" . $lang['reject'] . "</span>";
                                                } elseif ($data->poster_status == "deleted") {
                                                    echo "<span class='badge badge-soft-secondary font-12'>" . $lang['deleted'] . "</span>";
                                                } elseif ($data->poster_status == "expired") {
                                                    echo "<span class='badge badge-soft-secondary font-12'>" . $lang['expire'] . "</span>";
                                                } elseif ($data->poster_status == "needed") {
                                                    echo "<span class='badge badge-soft-info font-12'>" . $lang['needed'] . "</span>";
                                                } else {
                                                    echo "<span class='badge badge-soft-pink font-12'>" . $data->poster_status . "</span>";
                                                }
                                                ?>
                                            </td>
                                        </tr>

                                        <?php if (!is_null($data->poster_parent_id)) { ?>
                                            <tr>
                                                <td><?= $lang["a_status_parent"]; ?> :</td>
                                                <td>
                                                    <?php
                                                    if (empty($dataParent)) {
                                                        echo $lang['a_not_has'];
                                                    } else {
                                                        echo '<a class="btn btn-outline-info rounded-pill waves-effect waves-light" data-tj-replace="' . $dataParent->poster_id . '" href="/admin/poster/info/' . $dataParent->poster_id . '">' . $lang['a_has'] . '</a>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } else { ?>
                                            <tr>
                                                <td><?= $lang["a_child_poster"]; ?> :</td>
                                                <td>
                                                    <?php
                                                    if (empty($dataChild)) {
                                                        echo $lang['a_not_has'];
                                                    } else {
                                                        echo '<a class="btn btn-outline-info rounded-pill waves-effect waves-light" href="/admin/poster/info/' . $dataChild->poster_id . '">' . $lang['a_has'] . '</a>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                    <div class="button-list d-flex justify-content-center">
                                        <?php if (is_null($data->poster_parent_id)) {
//                                            pending=>rejected,accepted,needed
//                                            needed=>rejected,accepted
//                                            rejected=>accepted,needed
//                                            accepted=>rejected,needed
                                            if ($data->poster_status == "pending") {
                                                ?>
                                                <button type="button"
                                                        id="acceptedBtn"
                                                        data-mj-status="accepted"
                                                        data-style="zoom-in"
                                                        class="btnSubmit btn btn-soft-success waves-effect waves-light">
                                                    <?= $lang["acceptedes"]; ?>
                                                </button>
                                                <button type="button"
                                                        id="rejectedBtn"
                                                        data-mj-status="rejected"
                                                        data-style="zoom-in"
                                                        class="btnSubmit btn btn-soft-danger waves-effect waves-light">
                                                    <?= $lang["rejecting"]; ?>
                                                </button>
                                                <button type="button"
                                                        id="neededBtn"
                                                        data-mj-status="needed"
                                                        data-style="zoom-in"
                                                        class="btnSubmit btn btn-soft-pink waves-effect waves-light">
                                                    <?= $lang["needed"]; ?>
                                                </button>
                                            <?php } elseif ($data->poster_status == "needed") { ?>
                                                <button type="button"
                                                        id="acceptedBtn"
                                                        data-mj-status="accepted"
                                                        data-style="zoom-in"
                                                        class="btnSubmit btn btn-soft-success waves-effect waves-light">
                                                    <?= $lang["acceptedes"]; ?>
                                                </button>
                                                <button type="button"
                                                        id="rejectedBtn"
                                                        data-mj-status="rejected"
                                                        data-style="zoom-in"
                                                        class="btnSubmit btn btn-soft-danger waves-effect waves-light">
                                                    <?= $lang["rejecting"]; ?>
                                                </button>
                                            <?php } elseif ($data->poster_status == "rejected") { ?>
                                                <button type="button"
                                                        id="acceptedBtn"
                                                        data-mj-status="accepted"
                                                        data-style="zoom-in"
                                                        class="btnSubmit btn btn-soft-success waves-effect waves-light">
                                                    <?= $lang["accepted"]; ?>
                                                </button>
                                                <button type="button"
                                                        id="neededBtn"
                                                        data-mj-status="needed"
                                                        data-style="zoom-in"
                                                        class="btnSubmit btn btn-soft-pink waves-effect waves-light">
                                                    <?= $lang["needed"]; ?>
                                                </button>
                                            <?php } elseif ($data->poster_status == "accepted") { ?>
                                                <button type="button"
                                                        id="rejectedBtn"
                                                        data-mj-status="rejected"
                                                        data-style="zoom-in"
                                                        class="btnSubmit btn btn-soft-danger waves-effect waves-light">
                                                    <?= $lang["rejecting"]; ?>
                                                </button>
                                                <button type="button"
                                                        id="neededBtn"
                                                        data-mj-status="needed"
                                                        data-style="zoom-in"
                                                        class="btnSubmit btn btn-soft-pink waves-effect waves-light">
                                                    <?= $lang["needed"]; ?>
                                                </button>
                                            <?php } ?>

                                        <?php } else {
//                                            pending=>accepted,needed
//                                            needed=>accepted

                                            if ($data->poster_status == "pending") {
                                                ?>
                                                <button type="button"
                                                        id="acceptedBtn"
                                                        data-mj-status="accepted"
                                                        data-style="zoom-in"
                                                        class="btnSubmit btn btn-soft-success waves-effect waves-light">
                                                    <?= $lang["acceptedes"]; ?>
                                                </button>
                                                <button type="button"
                                                        id="neededBtn"
                                                        data-mj-status="needed"
                                                        data-style="zoom-in"
                                                        class="btnSubmit btn btn-soft-pink waves-effect waves-light">
                                                    <?= $lang["needed"]; ?>
                                                </button>
                                            <?php } elseif ($data->poster_status == "needed") { ?>
                                                <button type="button"
                                                        id="acceptedBtn"
                                                        data-mj-status="accepted"
                                                        data-style="zoom-in"
                                                        class="btnSubmit btn btn-soft-success waves-effect waves-light">
                                                    <?= $lang["acceptedes"]; ?>
                                                </button>
                                            <?php } ?>
                                        <?php } ?>

                                    </div>

                                    <div class="mt-3">
                                        <div class="form-floating">
                                            <textarea class="form-control"
                                                      placeholder="<?= $lang['a_reason_poster']; ?>"
                                                      id="reason-poster"
                                                      style="height: 100px"></textarea>
                                            <label for="reason-poster"><?= $lang['a_reason_poster']; ?></label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!--/Change Status-->


                            <?php if ($data->poster_status == "deleted") { ?>
                                <!--Reason Delete-->
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_reason_delete"]; ?></h5>
                                        <p>
                                            <?php
                                            $delete_result = PosterC::getCategoryReasonDeletedPosterById($data->delete_id);
                                            $delete_data = [];
                                            if ($delete_result->status == 200 && !empty($delete_result->response)) {
                                                $delete_data = $delete_result->response[0];
                                            }
                                            echo (!empty(array_column(json_decode($delete_data->category_name, true), 'value', 'slug')[$language])) ?
                                                array_column(json_decode($delete_data->category_name, true), 'value', 'slug')[$language] : "";
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                <!--/Reason Delete-->
                            <?php } ?>


                            <!-- Start Images -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["cargo_thumbnail"]; ?></h5>
                                    <div class="row">

                                        <div class="col-lg-12">

                                            <div
                                                style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff"
                                                class="swiper mySwiper2">
                                                <div class="swiper-wrapper">
                                                    <?php
                                                    $imageFlag = true;
                                                    if (!empty($data->poster_images)) {
                                                        $images = json_decode($data->poster_images);
                                                        foreach ($images as $image) {
                                                            $imageFlag = false;
                                                            ?>
                                                            <div class="swiper-slide">
                                                                <img
                                                                    src="<?= Utils::fileExist($image, POSTER_DEFAULT); ?>"/>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    if ($imageFlag) {
                                                        ?>
                                                        <div class="swiper-slide">
                                                            <img src="<?= POSTER_DEFAULT; ?>"/>
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
                                                    if (!empty($data->poster_images)) {
                                                        $images = json_decode($data->poster_images);
                                                        foreach ($images as $image) {
                                                            ?>
                                                            <div class="swiper-slide">
                                                                <img
                                                                    src="<?= Utils::fileExist($image, POSTER_DEFAULT); ?>"/>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    if ($imageFlag) {
                                                        ?>
                                                        <div class="swiper-slide">
                                                            <img src="<?= POSTER_DEFAULT; ?>"/>
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
                            <!-- End Images -->


                            <!-- Start properties -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_properties"]; ?></h5>
                                    <div class="row">

                                        <div class="col-lg-12">
                                            <?php
                                            foreach ($dataAllProperties as $loop) {
                                                $name = '';
                                                $idd = '';
                                                $type = '';
                                                foreach ($dataProperties as $loop2) {
                                                    if ($loop->property_id == $loop2->property_id) {
                                                        $name = array_column(json_decode($loop2->property_name, true), 'value', 'slug')[$language];
                                                        $idd = $loop2->property_id;
                                                        $type = $loop2->property_type;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <div
                                                    class="form-check mb-2  <?= ($type == 'truck') ? 'form-check-success' : null; ?>">
                                                    <input class="form-check-input"
                                                           type="radio"
                                                           checked
                                                           id="property-<?= $idd; ?>">
                                                    <label class="form-check-label"
                                                           for="property-<?= $idd; ?>"><?= $name; ?></label>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>


                                    </div>
                                </div>
                            </div>
                            <!-- End properties -->


                            <?php
                            if (!empty($dataPosterTransactions)) {
                                ?>
                                <!--Reason Delete-->
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_poster_transactions"]; ?></h5>
                                        <div class="list-group">
                                          <!--  <?php /*foreach ($dataPosterTransactions as $index => $loop) { */?>
                                                <a href="/admin/transaction/info/<?php /*= $loop->transaction_id; */?>"
                                                   class="list-group-item list-group-item-action">
                                                    <?php /*= $lang['transaction'] . " " . ($index + 1); */?>
                                                </a>
                                            --><?php /*} */?>

                                        </div>
                                    </div>
                                </div>
                                <!--/Reason Delete-->
                            <?php } ?>


                        </div>

                    </div>
                </div>
            </div>


            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('admin-poster-info') ?>">

            <script>
                var var_lang = '<?php

                    $array_yes_no = [
                        ["text" => $lang['a_yes'], 'value' => 'yes'],
                        ["text" => $lang['a_no'], 'value' => 'no']
                    ];

                    $a_truck_status = [
                        ["text" => $lang['a_new'], 'value' => 'new'],
                        ["text" => $lang['a_stock'], 'value' => 'stock'],
                        ["text" => $lang['a_order'], 'value' => 'order']
                    ];

                    $array_type = [
                        ["text" => $lang['a_trailer'], 'value' => 'trailer'],
                        ["text" => $lang['a_truck'], 'value' => 'truck']
                    ];
                    $a_axis = [
                        ["text" => 4, 'value' => 4],
                        ["text" => 6, 'value' => 6],
                        ["text" => 8, 'value' => 8],
                    ];

                    $var_lang = [
                        'error' => $lang['error'],
                        'successful' => $lang['successful'],
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'successful_submit_mag' => $lang['successful_submit_mag'],
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
                        'token_error' => $lang['token_error'],
                        'a_desc_reason_poster' => $lang['a_desc_reason_poster'],

                        'rejected' => $lang['rejected'],
                        'pending' => $lang['pending'],
                        'accepted' => $lang['accepted'],
                        'canceled' => $lang['canceled'],
                        'a_select_country_2' => $lang['a_select_country_2'],
                        'a_report_status_change' => $lang['a_report_status_change'],

                        'array_status' => [],
                        'array_yes_no' => $array_yes_no,
                        'a_gearboxs' => $a_gearboxs,
                        'a_fuels' => $a_fuels,
                        'array_currency' => $array_currency,
                        'a_trailers' => $a_trailers,
                        'a_axis' => $a_axis,
                        'a_truck_status' => $a_truck_status,
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