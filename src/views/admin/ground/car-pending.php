<?php
$pageSlug = "cars";
// permission_can_show

global $lang;

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_show == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1


        /**
         * Get All cars
         */
        $resultAllCarsInfoByType = Car::getAllCars("pending");
        $dataAllCarsInfoByType = [];
        if ($resultAllCarsInfoByType->status == 200 && !empty($resultAllCarsInfoByType->response)) {
            $dataAllCarsInfoByType = $resultAllCarsInfoByType->response;
        }

        /**
         * Get All Category Cars
         */
        $resultAllCarsTypes = Car::getAllCarsTypes();
        $dataAllCarsTypes = [];
        if ($resultAllCarsTypes->status == 200 && !empty($resultAllCarsTypes->response)) {
            $dataAllCarsTypes = $resultAllCarsTypes->response;
        }

        getHeader($lang["cars_pending"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_show',
        ]);

        // start roles 2
        if ($flagSlug) {
            // end roles 2
            ?>
            <div class="row">
                <?php
                if (!empty($dataAllCarsInfoByType)) {
                    foreach ($dataAllCarsInfoByType as $dataAllCarsInfoByTypeITEM) {

                        /**
                         * Get User Info By Id
                         */
                        $resultUserInfoById = AUser::getUserInfoById($dataAllCarsInfoByTypeITEM->user_id);
                        $name = $lang['guest_user'];
                        if ($resultUserInfoById->status == 200 && !empty($resultUserInfoById->response) && !empty($resultUserInfoById->response[0])) {
                            $name = Security::decrypt($resultUserInfoById->response[0]->user_firstname) . " " . Security::decrypt($resultUserInfoById->response[0]->user_lastname);
                        }

                        ?>
                        <div class="col-lg-3">
                            <div class="text-center card">
                                <div class="card-body">
                                    <div class="pt-2 pb-2">
                                        <img src="<?= CAR_IMAGE; ?>"
                                             class="rounded-circle img-thumbnail avatar-xl" alt="<?= $name; ?>">

                                        <h4 class="mt-3">
                                            <a href="/admin/users/info/<?= $dataAllCarsInfoByTypeITEM->user_id; ?>"
                                               class="text-dark">
                                                <?= $name; ?>
                                            </a>
                                        </h4>
                                        <p class="text-muted">
                                            <?php
                                            if (!empty($dataAllCarsTypes)) {
                                                foreach ($dataAllCarsTypes as $dataAllCarsTypesITEM) {
                                                    if ($dataAllCarsTypesITEM->type_id == $dataAllCarsInfoByTypeITEM->type_id) {
                                                        echo (!empty(array_column(json_decode($dataAllCarsTypesITEM->type_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                            array_column(json_decode($dataAllCarsTypesITEM->type_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                                                    }
                                                }
                                            }
                                            ?>
                                        </p>
                                        <p class="text-dark">
                                            <?= $dataAllCarsInfoByTypeITEM->car_plaque; ?>
                                        </p>
                                        <a target="_self"
                                           href="/admin/car/<?= $dataAllCarsInfoByTypeITEM->car_id; ?>"
                                           class="btn btn-soft-primary btn-sm waves-effect waves-light">
                                            <?= $lang['show']; ?>
                                        </a>
                                        <a href="/admin/users/info/<?= $dataAllCarsInfoByTypeITEM->user_id; ?>"
                                           class="btn btn-soft-info btn-sm waves-effect waves-light">
                                            <?= $lang['user_info']; ?>
                                        </a>
                                    </div> <!-- end .padding -->
                                </div>
                            </div> <!-- end card-->
                        </div>
                        <!-- end col -->
                        <?php
                    }
                } else {
                    ?>
                    <div class="col-lg-12 ">
                        <p class="text-center">
                            <img src="<?= BOX_EMPTY; ?>"
                                 style="width: 50%;max-width: fit-content;"
                                 alt="<?= $lang['no_massages']; ?>">
                        </p>

                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter(
            [
                $lang['help_car_6'],
                $lang['help_car_7'],
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