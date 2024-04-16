<?php
$pageSlug = "cars";
// permission_can_edit

global $lang,$antiXSS;

use MJ\Utils\Utils;
use MJ\Security\Security;

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
         * Get Department By ID
         */
        $resultCarById = Car::getCarById($id);
        $dataCarById = [];
        if ($resultCarById->status == 200 && !empty($resultCarById->response)) {
            $dataCarById = $resultCarById->response[0];
        }
        if (empty($dataCarById)) {
            header('Location: /admin/car');
        }


        /**
         * Get All Languages
         */
        $resultLanguages = Utils::getFileValue("languages.json", "", false);
        $dataLanguages = [];
        if (!empty($resultLanguages)) {
            $dataLanguages = json_decode($resultLanguages);
        }


        /**
         * Get All Admins
         */
        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
        }


        /**
         * Get All Category Cars
         */
        $resultAllCarsTypes = Car::getAllCarsTypes();
        $dataAllCarsTypes = [];
        if ($resultAllCarsTypes->status == 200 && !empty($resultAllCarsTypes->response)) {
            $dataAllCarsTypes = $resultAllCarsTypes->response;
        }


        /**
         * Get User Info By Id
         */
        $resultUserInfoById = AUser::getUserInfoById($dataCarById->user_id);
        $dataUserInfoById = [];
        if ($resultUserInfoById->status == 200 && !empty($resultUserInfoById->response) && !empty($resultUserInfoById->response[0])) {
            $resultUserInfoById = $resultUserInfoById->response[0];
        }
        $name = $lang['guest_user'];
        if (!empty($resultUserInfoById)) {
            $name = Security::decrypt($resultUserInfoById->user_firstname) . " " . Security::decrypt($resultUserInfoById->user_lastname);
        }
        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');
        enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');

        // Load Script In Footer
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
        enqueueScript('car-edit', '/dist/js/admin/ground/car-edit.init.js');

        getHeader($lang["car_edit"], [
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
                .swiper-slide {
                    height: 400px;
                    max-height: 400px;
                    background-position: center;
                    background-size: cover;
                }

                .swiper-slide img {
                    width: 100%;
                    height: 100%;
                    object-fit: contain;
                    display: block;
                }
            </style>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">

                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["car_edit"]; ?></h5>
                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="swiper mySwiper">
                                        <div class="swiper-wrapper">
                                            <?php
                                            $carImageFlag=true;
                                            if (!empty($dataCarById->car_images)) {
                                                $tempsss = json_decode($dataCarById->car_images);
                                                foreach ($tempsss as $temp) {
                                                    $carImageFlag=false;
                                                    ?>
                                                    <div class="swiper-slide">
                                                        <img src="<?= Utils::fileExist($temp, BOX_EMPTY); ?>"
                                                             loading="lazy"
                                                             alt="<?= $lang['no_massages']; ?>">
                                                    </div>
                                                    <?php
                                                }
                                            }
                                              if($carImageFlag){
                                                ?>
                                                <div class="swiper-slide">
                                                    <img src="<?= BOX_EMPTY; ?>" alt="<?= $lang['no_massages']; ?>">
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="swiper-button-next"></div>
                                        <div class="swiper-button-prev"></div>
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                            <div class="text-center progress-demo">
                                <button id="accepted"
                                        type="button"
                                        data-id="<?= $dataCarById->car_id; ?>"
                                        data-style="zoom-in"
                                        class="<?php if ($dataCarById->car_status == "accepted") {
                                            echo "active";
                                        } ?> setSubmitBtn btn w-sm btn-success waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["accepted"]; ?>
                                </button>
                                <button id="rejected"
                                        type="button"
                                        data-id="<?= $dataCarById->car_id; ?>"
                                        data-style="zoom-in"
                                        class="<?php if ($dataCarById->car_status == "rejected") {
                                            echo "active";
                                        } ?> setSubmitBtn btn w-sm btn-danger waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["rejected"]; ?>
                                </button>
                                <button id="pending"
                                        type="button"
                                        data-id="<?= $dataCarById->car_id; ?>"
                                        data-style="zoom-in"
                                        class="<?php if ($dataCarById->car_status == "pending") {
                                            echo "active";
                                        } ?> setSubmitBtn btn w-sm btn-warning waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["pending"]; ?>
                                </button>
                                <button id="deleted"
                                        type="button"
                                        data-id="<?= $dataCarById->car_id; ?>"
                                        data-style="zoom-in"
                                        class="<?php if ($dataCarById->car_status == "deleted") {
                                            echo "active";
                                        } ?> setSubmitBtn btn w-sm btn-secondary waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["deleted"]; ?>
                                </button>
                                <a href="/admin/car"
                                   class="btn w-sm btn-dark  waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["cancel"]; ?>
                                </a>
                            </div>
                        </div>
                    </div>


                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase1" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase1">
                                    <i class="mdi mdi-minus"></i>
                                </a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["all_info"]; ?></h5>
                            <div class="table-responsive show" style="max-height: 176px;overflow: auto;"
                                 id="cardCollpase1">
                                <table class="table mb-0 table-sm">
                                    <tbody>
                                    <tr>
                                        <td colspan="2">
                                            <a target="_self" href="/admin/users/info/<?= $dataCarById->user_id; ?>">
                                                <i class="mdi mdi-account-circle" data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="<?= $lang["user_info"]; ?>"></i>
                                                <?= $lang["name_and_family"]; ?>
                                            </a>
                                            :
                                        </td>
                                        <td><?= $name; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><?= $lang["type_plaque"]; ?> :</td>
                                        <td><?php
                                            if($dataCarById->plaque_type =="iran"){
                                                echo $lang['iran_p'];
                                            }elseif ($dataCarById->plaque_type =="iran_international"){
                                                echo $lang['iran_international_p'];
                                            }elseif ($dataCarById->plaque_type =="turkey_international"){
                                                echo $lang['turkey_international_p'];
                                            }else{
                                                $dataCarById->plaque_type ;
                                            } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><?= $lang["plaque"]; ?> :</td>
                                        <td><?= $dataCarById->car_plaque; ?></td>
                                    </tr>

                                    <tr>
                                        <td colspan="2"><?= $lang["status"]; ?> :</td>
                                        <td><?php
                                            if ($dataCarById->car_status == "accepted") {
                                                echo "<span class='badge badge-soft-success font-12'>" . $lang['accepted'] . "</span>";
                                            } elseif ($dataCarById->car_status == "pending") {
                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['pending'] . "</span>";
                                            } elseif ($dataCarById->car_status == "rejected") {
                                                echo "<span class='badge badge-soft-danger font-12'>" . $lang['rejected'] . "</span>";
                                            } elseif ($dataCarById->car_status == "deleted") {
                                                echo "<span class='badge badge-soft-secondary font-12'>" . $lang['deleted'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-pink font-12'>" . $dataCarById->car_status . "</span>";
                                            }
                                            ?>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td colspan="2"><?= $lang["category"]; ?> :</td>
                                        <td>
                                            <?php
                                            if (!empty($dataAllCarsTypes)) {
                                                foreach ($dataAllCarsTypes as $dataAllCarsTypesITEM) {
                                                    if ($dataAllCarsTypesITEM->type_id == $dataCarById->type_id) {
                                                        echo (!empty(array_column(json_decode($dataAllCarsTypesITEM->type_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                            array_column(json_decode($dataAllCarsTypesITEM->type_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                                                    }
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>

                                    <?php
                                    if (!empty($dataCarById->car_options)) {

                                        $temp = json_decode($dataCarById->car_options);
                                        $name = "";

                                        if (!empty($temp->update)) {
                                            foreach ($temp->update as $loop) {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php
                                                        if (!empty($dataAllAdmins)) {
                                                            foreach ($dataAllAdmins as $dataAllAdminsLOOP) {
                                                                if ($dataAllAdminsLOOP->admin_id == $loop->admin) {
                                                                    $name = $dataAllAdminsLOOP->admin_nickname;
                                                                }
                                                            }
                                                        }
                                                        echo (!empty($name)) ? $name : $loop->admin;
                                                        ?>
                                                    </td>
                                                    <td><bdi><?= Utils::getTimeCountry('d F Y', $loop->time); ?></bdi></td>
                                                    <td>
                                                        <?php
                                                        if ($loop->status == "accepted") {
                                                            echo "<span class='badge badge-soft-success font-12'>" . $lang['accepted'] . "</span>";
                                                        } elseif ($loop->status == "pending") {
                                                            echo "<span class='badge badge-soft-warning font-12'>" . $lang['pending'] . "</span>";
                                                        } elseif ($loop->status == "rejected") {
                                                            echo "<span class='badge badge-soft-danger font-12'>" . $lang['rejected'] . "</span>";
                                                        } elseif ($loop->status == "deleted") {
                                                            echo "<span class='badge badge-soft-secondary font-12'>" . $lang['deleted'] . "</span>";
                                                        } else {
                                                            echo "<span class='badge badge-soft-pink font-12'>" . $loop->status . "</span>";
                                                        } ?>
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

                </div>
            </div>
            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('admin-car-edit') ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'error' => $lang['error'],
                        'successful' => $lang['successful'],
                        'warning' => $lang['warning'],
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
                        'token_error' => $lang['token_error'],
                    ];
                    print_r(json_encode($var_lang));  ?>';
            </script>
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter(
                [
                        $lang['help_car_8']
                ]
        );

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