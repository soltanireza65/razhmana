<?php
$pageSlug = "inquiry_ship";
// permission_can_edit

global $lang,$antiXSS, $Settings;

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
         * Get Inquiry INFO BY ID
         */
        $result = Ship::getInquiryInfoById($id);
        $data = [];
        if ($result->status == 200) {
            $data = $result->response[0];
        }
        if (empty($data)) {
            header('Location: /admin/inquiry/ship');
        }
//print_r($data);
        /**
         * Get User Info By Id
         */
        $resultUserInfo = AUser::getUserInfoById($data->user_id);
        $userInfo = [];
        if ($resultUserInfo->status == 200 && !empty($resultUserInfo->response)) {
            $userInfo = $resultUserInfo->response[0];
        }

        $userName = $lang['guest_user'];
        if ($userInfo->user_firstname) {
            $userName = Security::decrypt($userInfo->user_firstname) . " " . Security::decrypt($userInfo->user_lastname);
        }
        $userAvatar = ($userInfo->user_avatar) ? $userInfo->user_avatar : USER_AVATAR;
        $userMobile = ($userInfo->user_mobile) ? Security::decrypt($userInfo->user_mobile) : '';
        $userType = ($userInfo->user_type) ? $userInfo->user_type : "businessman";


        /**
         * Get Country And City Name
         */
        $resultUsCityInfo = Location::getMultiCityAndCountryByCityId(strval($data->source_city_id . "," . $data->dest_city_id));
        $cityInfo = [];
        if ($resultUsCityInfo->status == 200 && !empty($resultUsCityInfo->response)) {
            $cityInfo = $resultUsCityInfo->response;
        }

        /**
         * Get port Name
         */
        $resultStation = Ship::getMultiPortsByIDs("$data->source_port_id ,$data->dest_port_id");
        $dataStation = [];
        if ($resultStation->status == 200 && !empty($resultStation->response)) {
            $dataStation = $resultStation->response;
        }

        /**
         * Get Cargo Category
         */
        $resultCargoCategory = Ship::getAllCategoryShipCargo('active');
        $cargoCategoryInfo = [];
        if ($resultCargoCategory->status == 200 && !empty($resultCargoCategory->response)) {
            $cargoCategoryInfo = $resultCargoCategory->response;
        }

        /**
         * Get All Container
         */
        $resultContainer = Ship::getAllContainer('active');
        $dataContainer = [];
        if ($resultContainer->status == 200 && !empty($resultContainer->response)) {
            $dataContainer = $resultContainer->response;
        }


        /**
         * Get All Packing
         */
        $resultPacking = Ship::getAllCategoryShipPackingActive();
        $dataPacking = [];
        if ($resultPacking->status == 200 && !empty($resultPacking->response)) {
            $dataPacking = $resultPacking->response;
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


        // Load Stylesheets & Icons
        enqueueStylesheet('editable-css', '/dist/libs/x-editable/bootstrap-editable/css/bootstrap-editable.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');
        enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');

        // Load Script In Footer
        enqueueScript('editable-js', '/dist/libs/x-editable/bootstrap-editable/js/bootstrap-editable.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('inquiry-ship-info', '/dist/js/admin/ship/inquiry-ship-info.init.js');

        getHeader($lang["inquiry_ship_title"], [
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
            <div class="row">

                <div class="col-sm-12 col-md-12 col-lg-7">

                    <!-- Start Inquiry Info -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">
                                <?= $lang["inquiry_ship_title"]; ?>
                            </h5>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <tbody>

                                    <tr id="inquiryId" data-tj-id="<?= $id; ?>">
                                        <td><?= $lang["title"]; ?> :</td>
                                        <td><?= $data->freight_name; ?></td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["businessman_name"]; ?> :</td>
                                        <td>
                                            <div class="d-flex align-items-start">
                                                <img class="me-2 avatar-sm rounded-circle"
                                                     src="<?= Utils::fileExist($userAvatar, USER_AVATAR); ?>"
                                                     alt="<?= $userName; ?>">
                                                <div class="w-100">
                                                    <h5 class="m-0">
                                                        <a target="_self"
                                                           href="/admin/users/info/<?= $data->user_id; ?>">
                                                            <?= $userName; ?>
                                                        </a>
                                                    </h5>
                                                    <div class=" my-1 font-11">
                                                        <!-- &#9733;&#9733;&#9733; -->
                                                        <?php
                                                        $user_rate = $userInfo->user_rate;
                                                        $user_rate_count = $userInfo->user_rate_count;
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
                                        <td><?= $lang["a_number_call"]; ?> :</td>
                                        <td>
                                            <a href="tel:<?= $userMobile; ?>">
                                                <bdi><?= $userMobile; ?></bdi>
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <?= $lang["cargo_category"]; ?> :
                                        </td>
                                        <td>
                                                    <span id="change_inquiry_category"
                                                          data-type="select"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          title="<?= $lang['click_for_edit']; ?>"
                                                          data-tj-type="category_id">
                                                                <?php
                                                                $array_category = [];
                                                                if (!empty($cargoCategoryInfo)) {
                                                                    foreach ($cargoCategoryInfo as $loop) {
                                                                        if ($loop->category_id == $data->category_id) {
                                                                            echo (!empty(array_column(json_decode($loop->category_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                                array_column(json_decode($loop->category_name, true), 'value', 'slug')[$_COOKIE['language']] : $data->category_id;
                                                                        }

                                                                        // from change and update to x-table
                                                                        $category_Cargo_name = (!empty(array_column(json_decode($loop->category_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                            array_column(json_decode($loop->category_name, true), 'value', 'slug')[$_COOKIE['language']] : $loop->category_id;
                                                                        array_push($array_category, ["text" => $category_Cargo_name, 'value' => $loop->category_id]);
                                                                    }
                                                                }
                                                                ?>
                                                    </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["a_type_packing"]; ?> :</td>
                                        <td>
                                                  <span id="change_inquiry_packing"
                                                        data-type="select"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="<?= $lang['click_for_edit']; ?>"
                                                        data-tj-type="packing_id">
                                                         <?php
                                                         $array_packing = [];
                                                         if (!empty($dataPacking)) {
                                                             foreach ($dataPacking as $loop) {
                                                                 if ($loop->packing_id == $data->packing_id) {
                                                                     echo (!empty(array_column(json_decode($loop->packing_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                         array_column(json_decode($loop->packing_name, true), 'value', 'slug')[$_COOKIE['language']] : $data->packing_id;
                                                                 }

                                                                 // from change and update to x-table
                                                                 $packing_name = (!empty(array_column(json_decode($loop->packing_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                     array_column(json_decode($loop->packing_name, true), 'value', 'slug')[$_COOKIE['language']] : $loop->packing_id;
                                                                 array_push($array_packing, ["text" => $packing_name, 'value' => $loop->packing_id]);
                                                             }
                                                         }
                                                         ?>
                                                  </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["a_requested_container"]; ?> :</td>
                                        <td>
                                                  <span id="change_inquiry_container"
                                                        data-type="select"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="<?= $lang['click_for_edit']; ?>"
                                                        data-tj-type="container_id">
                                                         <?php
                                                         $array_container = [];
                                                         if (!empty($dataContainer)) {
                                                             foreach ($dataContainer as $loop) {
                                                                 if ($loop->container_id == $data->container_id) {
                                                                     echo (!empty(array_column(json_decode($loop->container_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                         array_column(json_decode($loop->container_name, true), 'value', 'slug')[$_COOKIE['language']] : $data->container_id;
                                                                 }

                                                                 // from change and update to x-table
                                                                 $container_name = (!empty(array_column(json_decode($loop->container_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                     array_column(json_decode($loop->container_name, true), 'value', 'slug')[$_COOKIE['language']] : $loop->container_id;
                                                                 array_push($array_container, ["text" => $container_name, 'value' => $loop->container_id]);
                                                             }
                                                         }
                                                         ?>
                                                  </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["a_count_container"]; ?> :</td>
                                        <td>
                                                    <span id="change_inquiry_count_container"
                                                          data-type="number"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          title="<?= $lang['click_for_edit']; ?>"
                                                          data-tj-type="freight_count_container">
                                                          <?= $data->freight_count_container; ?>
                                                    </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["cargo_weight"]; ?> :</td>
                                        <td>
                                                    <span id="change_inquiry_weight"
                                                          data-type="number"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          title="<?= $lang['click_for_edit']; ?>"
                                                          data-tj-type="freight_wieght">
                                                          <?= $data->freight_wieght; ?>
                                                    </span>
                                            <?= $lang['ton']; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["cargo_volume"]; ?> :</td>
                                        <td>
                                                    <span id="change_inquiry_volume"
                                                          data-type="number"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          title="<?= $lang['click_for_edit']; ?>"
                                                          data-tj-type="freight_volume">
                                                         <?= $data->freight_volume; ?>
                                                    </span>
                                            <?= $lang['cubic_meter']; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["start_date"]; ?> :</td>
                                        <td>
                                            <bdi>
                                                <?= Utils::getTimeCountry($Settings['date_format'], $data->freight_start_date); ?>
                                            </bdi>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["description"]; ?> :</td>
                                        <td>
                                            <?= $data->freight_description; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="2">
                                            <i class="mdi mdi-square-edit-outline text-info changeLocation"
                                               data-tj-type="source"></i>
                                            <?= $lang['a_source_cargo']; ?>
                                            <ul class="list-group list-group-flush">
                                                <li class="align-items-center d-flex justify-content-around list-group-item">
                                                    <?= $lang['a_source_country']; ?>
                                                    <span>
                                                         <?php
                                                         if (!empty($cityInfo)) {
                                                             foreach ($cityInfo as $loop) {
                                                                 if ($loop->city_id == $data->source_city_id) {
                                                                     echo (!empty(array_column(json_decode($loop->country_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                         array_column(json_decode($loop->country_name, true), 'value', 'slug')[$_COOKIE['language']] : $data->source_city_id;
                                                                 }
                                                             }
                                                         }
                                                         ?>
                                                    </span>
                                                </li>
                                                <li class="align-items-center d-flex justify-content-around list-group-item">
                                                    <?= $lang['a_source_city']; ?>
                                                    <span>
                                                        <?php
                                                        if (!empty($cityInfo)) {
                                                            foreach ($cityInfo as $loop) {
                                                                if ($loop->city_id == $data->source_city_id) {
                                                                    echo (!empty(array_column(json_decode($loop->city_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                        array_column(json_decode($loop->city_name, true), 'value', 'slug')[$_COOKIE['language']] : $data->source_city_id;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </span>
                                                </li>
                                                <li class="align-items-center d-flex justify-content-around list-group-item">
                                                    <?= $lang['a_source_port']; ?>
                                                    <span>
                                                         <?php
                                                         if (!empty($dataStation)) {
                                                             foreach ($dataStation as $loop) {
                                                                 if ($loop->port_id == $data->source_port_id) {
                                                                     echo (!empty(array_column(json_decode($loop->port_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                         array_column(json_decode($loop->port_name, true), 'value', 'slug')[$_COOKIE['language']] : $data->source_port_id;
                                                                 }
                                                             }
                                                         }
                                                         ?>
                                                    </span>
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
                                                    <span>
                                                          <?php
                                                          if (!empty($cityInfo)) {
                                                              foreach ($cityInfo as $loop) {
                                                                  if ($loop->city_id == $data->dest_city_id) {
                                                                      echo (!empty(array_column(json_decode($loop->country_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                          array_column(json_decode($loop->country_name, true), 'value', 'slug')[$_COOKIE['language']] : $data->dest_city_id;
                                                                  }
                                                              }
                                                          }
                                                          ?>
                                                    </span>
                                                </li>
                                                <li class="align-items-center d-flex justify-content-around list-group-item">
                                                    <?= $lang['a_dest_city']; ?>
                                                    <span>
                                                            <?php
                                                            if (!empty($cityInfo)) {
                                                                foreach ($cityInfo as $loop) {
                                                                    if ($loop->city_id == $data->dest_city_id) {
                                                                        echo (!empty(array_column(json_decode($loop->city_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                            array_column(json_decode($loop->city_name, true), 'value', 'slug')[$_COOKIE['language']] : $data->dest_city_id;
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                    </span>
                                                </li>
                                                <li class="align-items-center d-flex justify-content-around list-group-item">
                                                    <?= $lang['a_dest_port']; ?>
                                                    <span>
                                                          <?php
                                                          if (!empty($dataStation)) {
                                                              foreach ($dataStation as $loop) {
                                                                  if ($loop->port_id == $data->dest_port_id) {
                                                                      echo (!empty(array_column(json_decode($loop->port_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                          array_column(json_decode($loop->port_name, true), 'value', 'slug')[$_COOKIE['language']] : $data->dest_port_id;
                                                                  }
                                                              }
                                                          }
                                                          ?>
                                                    </span>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                            <!-- end table-responsive -->

                        </div>
                    </div>
                    <!-- End Inquiry Info -->

                    <!-- Start Inquiry Log -->
                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg collapsed" data-bs-toggle="collapse" href="#cardCollpase1"
                                   role="button"
                                   aria-expanded="true" aria-controls="cardCollpase1">
                                    <i class="mdi mdi-minus"></i>
                                </a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["all_info"]; ?></h5>
                            <div class="table-responsive collapse" style="max-height: 250px;overflow: auto;"
                                 id="cardCollpase1">
                                <table class="table mb-0 table-sm">
                                    <tbody>
                                    <?php
                                    if (!empty($data->freight_options)) {

                                        $temp = json_decode($data->freight_options);
                                        if (!empty($temp->update)) {
                                            foreach ($temp->update as $loop) {
                                                ?>
                                                <tr>
                                                    <td><?php
                                                        if ($loop->type == "status") {
                                                            echo $lang['a_change_status'];
                                                        } elseif ($loop->type == "freight_admin_description") {
                                                            echo $lang['a_add_description'];
                                                        } elseif ($loop->type == "location") {
                                                            echo $lang['a_change_location'];
                                                        } else {
                                                            echo $loop->type;
                                                        }
                                                        ?></td>
                                                    <td class="table-user text-start">
                                                        <?php
                                                        $AdminAvatar = '';
                                                        $nameAdmin = '';
                                                        if (!empty($dataAllAdmins)) {
                                                            foreach ($dataAllAdmins as $dataAllAdminsLOOP) {
                                                                if ($dataAllAdminsLOOP->admin_id == $loop->admin) {
                                                                    $name = $dataAllAdminsLOOP->admin_nickname;
                                                                    $AdminAvatar = $dataAllAdminsLOOP->admin_avatar;
                                                                }
                                                            }
                                                        }
                                                        $nameAdmin = (!empty($name)) ? $name : $loop->admin;
                                                        ?>
                                                        <img class="me-2 avatar-sm rounded-circle"
                                                             src="<?= Utils::fileExist($AdminAvatar, USER_AVATAR); ?>"
                                                             alt="<?= $nameAdmin; ?>">
                                                        <?= $nameAdmin; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($loop->type == "status") {
                                                            if ($loop->value == "process") {
                                                                echo "<span class='badge badge-soft-info font-12'>" . $lang['a_freight_process'] . "</span>";
                                                            } elseif ($loop->value == "completed") {
                                                                echo "<span class='badge badge-soft-primary font-12'>" . $lang['a_freight_completed'] . "</span>";
                                                            } else {
                                                                echo "<span class='badge badge-soft-danger font-12'>" . $loop->value . "</span>";
                                                            }
                                                        } elseif ($loop->type == "location") {
                                                            if ($loop->value == "change_location_source") {
                                                                echo $lang['a_change_location_source'];
                                                            } elseif ($loop->value == "change_location_dest") {
                                                                echo $lang['a_change_location_dest'];
                                                            } else {
                                                                echo $loop->value;
                                                            }
                                                        } else {
                                                            echo $loop->value;
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <bdi><?= Utils::getTimeCountry($Settings['date_format'], $loop->date); ?></bdi>
                                                    </td>
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
                    <!-- End Inquiry Log -->
                </div>

                <div class="col-sm-12 col-md-12 col-lg-5">

                    <!--Change Inquiry Status-->
                    <?php
                    if (!in_array($data->freight_status, ['read','completed'])) {
                        ?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">
                                    <?= $lang["action"]; ?>
                                </h5>
                                <div class="button-list d-flex justify-content-center">
                                    <?php
                                    if ($data->freight_status == "pending") {
                                        ?>
                                        <button type="button"
                                                id="btnProcess"
                                                data-tj-status="process"
                                                data-tj-type="freight_status"
                                                data-style="zoom-in"
                                                class="btnSubmit btn btn-soft-success waves-effect waves-light">
                                            <?= $lang["me_start_survey"]; ?>
                                        </button>
                                        <?php
                                    } elseif ($data->freight_status == "process") {
                                        ?>
                                        <button type="button"
                                                id="btnCompleted"
                                                data-tj-status="completed"
                                                data-tj-type="freight_status"
                                                data-style="zoom-in"
                                                class="btnSubmit btn btn-soft-primary waves-effect waves-light">
                                            <?= $lang["me_survey_end"]; ?>
                                        </button>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <!--/Change Inquiry Status-->

                    <!--All info-->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">
                                <?= $lang["all_info"]; ?></h5>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <tbody>

                                    <tr>
                                        <td><?= $lang["inquiry_status"]; ?> :</td>
                                        <td>
                                            <?php
                                            if ($data->freight_status == "pending") {
                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['a_freight_pending'] . "</span>";
                                            } elseif ($data->freight_status == "process") {
                                                echo "<span class='badge badge-soft-info font-12'>" . $lang['a_freight_process'] . "</span>";
                                            } elseif ($data->freight_status == "completed") {
                                                echo "<span class='badge badge-soft-primary font-12'>" . $lang['a_freight_completed'] . "</span>";
                                            } elseif ($data->freight_status == "read") {
                                                echo "<span class='badge badge-soft-primary font-12'>" . $lang['a_freight_completed'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-danger font-12'>" . $data->freight_status . "</span>";
                                            }
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["a_request_price"]; ?> :</td>
                                        <td>
                                                    <span id="change_inquiry_price"
                                                          data-type="number"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          title="<?= $lang['click_for_edit']; ?>"
                                                          data-tj-type="freight_price">
                                                         <?= number_format($data->freight_price); ?>
                                                    </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["b_currency_type"]; ?> :</td>
                                        <td>
                                                    <span id="change_inquiry_currency"
                                                          data-type="select"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          title="<?= $lang['click_for_edit']; ?>"
                                                          data-tj-type="currency_id">
                                                         <?php
                                                         $array_currency = [];
                                                         if (!empty($dataAllCurrencies)) {
                                                             foreach ($dataAllCurrencies as $loop) {
                                                                 if ($loop->currency_id == $data->currency_id) {
                                                                     echo (!empty(array_column(json_decode($loop->currency_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                         array_column(json_decode($loop->currency_name, true), 'value', 'slug')[$_COOKIE['language']] : $data->currency_id;
                                                                 }

                                                                 // from change and update to x-table
                                                                 $category_currency_name = (!empty(array_column(json_decode($loop->currency_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                     array_column(json_decode($loop->currency_name, true), 'value', 'slug')[$_COOKIE['language']] : $loop->currency_id;
                                                                 array_push($array_currency, ["text" => $category_currency_name, 'value' => $loop->currency_id]);
                                                             }
                                                         }
                                                         ?>
                                                    </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["a_freight_free_time"]; ?> :</td>
                                        <td>
                                             <span id="change_inquiry_free_time"
                                                   data-type="number"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="<?= $lang['click_for_edit']; ?>"
                                                   data-tj-type="freight_free_time">
                                                  <?= $data->freight_free_time; ?>
                                             </span>
                                            <?= $lang['a_day']; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["a_freight_term_id"]; ?> :</td>
                                        <td>
                                            <span id="add_inquiry_term_id"
                                                  data-type="text"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-placement="top"
                                                  title="<?= $lang['click_for_edit']; ?>"
                                                  data-tj-type="freight_term_id">
                                            <?= $data->freight_term_id; ?>
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["a_freight_waybill"]; ?> :</td>
                                        <td>
                                            <span id="add_inquiry_waybill"
                                                  data-type="text"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-placement="top"
                                                  title="<?= $lang['click_for_edit']; ?>"
                                                  data-tj-type="freight_waybill">
                                            <?= $data->freight_waybill; ?>
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["a_add_admin_desc"]; ?> :</td>
                                        <td>
                                            <span id="add_inquiry_admin_description"
                                                  data-type="textarea"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-placement="top"
                                                  title="<?= $lang['click_for_edit']; ?>"
                                                  data-tj-type="freight_admin_description"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["request_submit_date"]; ?> :</td>
                                        <td>
                                            <bdi>
                                                <?= Utils::getTimeCountry($Settings['data_time_format'], $data->freight_submit_date); ?>
                                            </bdi>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--/All info-->

                </div>

            </div>


            <!-- Start MODAL Change Location -->
            <div class="modal fade" id="cityModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header py-3 px-4 border-bottom-0 d-block">
                            <button type="button" class="btn-close float-end" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            <h5 class="modal-title"><?= $lang['select_city']; ?></h5>
                        </div>
                        <div class="modal-body px-4 pb-4 pt-0">
                            <form class="needs-validation" novalidate>
                                <div class="row">

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label"></label>
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
                                            <label class="form-label"></label>
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
            <!-- End MODAL Change Location -->

            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF2() ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'error' => $lang['error'],
                        'successful' => $lang['successful'],
                        'warning' => $lang['warning'],
                        'successful_submit_mag' => $lang['successful_submit_mag'],
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
                        'token_error' => $lang['token_error'],
                        'a_empty' => $lang['a_empty'],
                        'array_category' => $array_category,
                        'array_container' => $array_container,
                        'array_packing' => $array_packing,
                        'array_currency' => $array_currency,
                        'a_add_new_description' => $lang['a_add_new_description'],
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