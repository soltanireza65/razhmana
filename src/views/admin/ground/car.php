<?php
$pageSlug = "cars";
// permission_can_show

global $lang;

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
         * Get All Cars
         */
        $resultAllCars = Car::getAllCars();
        $dataAllCars = [];
        if ($resultAllCars->status == 200 && !empty($resultAllCars->response)) {
            $dataAllCars = $resultAllCars->response;
        }

        /**
         * Get All Category Cars
         */
        $resultAllCarsTypes = Car::getAllCarsTypes();
        $dataAllCarsTypes = [];
        if ($resultAllCarsTypes->status == 200 && !empty($resultAllCarsTypes->response)) {
            $dataAllCarsTypes = $resultAllCarsTypes->response;
        }


        // Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');

        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('chartJs', '/dist/libs/chart.js/Chart.bundle.min.js');
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');
        enqueueScript('charts', '/dist/js/admin/charts.init.js');

        getHeader($lang["cars_list"], [
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
                <div class="col-sm-12 col-md-12 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row justify-content-between mb-3">
                                <div class="col-auto">
                                    <h4 class="page-title"><?= $lang['cars_list']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">

                                    </div>
                                </div><!-- end col-->
                            </div>

                            <div class="table-responsive">
                                <table id="orders-table" data-page-length='10' data-order='[[ 0, "desc" ]]'
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['category']; ?></th>
                                        <th><?= $lang['type_plaque']; ?></th>
                                        <th><?= $lang['plaque']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $status_accepted = 0;
                                    $status_pending = 0;
                                    $status_rejected = 0;
                                    $status_deleted = 0;
                                    if (!empty($dataAllCars)) {
                                        $i = 1;
                                        $dataAllCars = array_reverse($dataAllCars);
                                        foreach ($dataAllCars as $dataAllCarsITEM) {
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td>
                                                    <?php
                                                    if (!empty($dataAllCarsTypes)) {
                                                        foreach ($dataAllCarsTypes as $dataAllCarsTypesITEM) {
                                                            if ($dataAllCarsTypesITEM->type_id == $dataAllCarsITEM->type_id) {
                                                                echo (!empty(array_column(json_decode($dataAllCarsTypesITEM->type_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                    array_column(json_decode($dataAllCarsTypesITEM->type_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($dataAllCarsITEM->plaque_type == "iran") {
                                                        echo $lang['iran_p'];
                                                    } elseif ($dataAllCarsITEM->plaque_type == "iran_international") {
                                                        echo $lang['iran_international_p'];
                                                    } elseif ($dataAllCarsITEM->plaque_type == "turkey_international") {
                                                        echo $lang['turkey_international_p'];
                                                    } else {
                                                        $dataAllCarsITEM->plaque_type;
                                                    }
                                                    ?>
                                                </td>
                                                <td><?= $dataAllCarsITEM->car_plaque; ?></td>
                                                <td>
                                                    <?php
                                                    if ($dataAllCarsITEM->car_status == "accepted") {
                                                        echo "<span class='badge badge-soft-success font-12'>" . $lang['accepted'] . "</span>";
                                                        $status_accepted += 1;
                                                    } elseif ($dataAllCarsITEM->car_status == "pending") {
                                                        echo "<span class='badge badge-soft-warning font-12'>" . $lang['pending'] . "</span>";
                                                        $status_pending += 1;
                                                    } elseif ($dataAllCarsITEM->car_status == "rejected") {
                                                        echo "<span class='badge badge-soft-danger font-12'>" . $lang['rejected'] . "</span>";
                                                        $status_rejected += 1;
                                                    } elseif ($dataAllCarsITEM->car_status == "deleted") {
                                                        echo "<span class='badge badge-soft-secondary font-12'>" . $lang['deleted'] . "</span>";
                                                        $status_deleted += 1;
                                                    } else {
                                                        echo "<span class='badge badge-soft-pink font-12'>" . $dataAllCarsITEM->car_status . "</span>";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a target="_self"
                                                       href="/admin/car/<?= $dataAllCarsITEM->car_id; ?>"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['car_detail']; ?>"
                                                       class="action-icon">
                                                        <i class="mdi mdi-square-edit-outline"></i>
                                                    </a>
                                                    <a href="/admin/users/info/<?= $dataAllCarsITEM->user_id; ?>"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['user_info']; ?>"
                                                       target="_self" class="action-icon">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
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
                </div>
                <div class="col-sm-12 col-md-12 col-lg-4">
                    <div class="card">
                        <div class="card-body">

                            <div class="row justify-content-between mb-3">
                                <div class="col-auto">
                                    <h4 class="page-title"><?= $lang['chart_cars']; ?></h4>
                                </div>
                            </div>

                            <canvas id="myChart" style="width:100%" height="250"></canvas>

                            <div class="text-start mt-3">

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['all_cars']; ?> :
                                    </strong>
                                    <span class="ms-2"
                                          data-plugin="counterup"><?= $status_accepted + $status_pending + $status_rejected + $status_deleted; ?></span>
                                </p>

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['accepted']; ?> :
                                    </strong>
                                    <span class="ms-2" data-plugin="counterup"><?= $status_accepted; ?></span>
                                </p>

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['pending']; ?> :
                                    </strong>
                                    <span class="ms-2" data-plugin="counterup"><?= $status_pending; ?></span>
                                </p>
                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['rejected']; ?> :
                                    </strong>
                                    <span class="ms-2" data-plugin="counterup"><?= $status_rejected; ?></span>
                                </p>
                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['deleted']; ?> :
                                    </strong>
                                    <span class="ms-2" data-plugin="counterup"><?= $status_deleted; ?></span>
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'tempp' => [
                            ['name' => $lang['accepted'], 'count' => $status_accepted],
                            ['name' => $lang['pending'], 'count' => $status_pending],
                            ['name' => $lang['rejected'], 'count' => $status_rejected],
                            ['name' => $lang['deleted'], 'count' => $status_deleted],
                        ],
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
                $lang['help_car_1'],
                $lang['help_car_2'],
                $lang['help_car_3'],
                $lang['help_car_4'],
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