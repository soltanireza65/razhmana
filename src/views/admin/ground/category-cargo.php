<?php
$pageSlug = "cargo_c";
// permission_can_show

global $lang;

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_show == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1


        /**
         * Get All Category Cars
         */
        $resultAllCarsTypes = Cargo::getAllCargoCategory();
        $dataAllCarsTypes = [];
        if ($resultAllCarsTypes->status == 200 && !empty($resultAllCarsTypes->response)) {
            $dataAllCarsTypes = $resultAllCarsTypes->response;
        }


        /**
         * Get All Cars
         */
        $resultAllCars = Cargo::getAllCargo();
        $dataAllCars = [];
        if ($resultAllCars->status == 200 && !empty($resultAllCars->response)) {
            $dataAllCars = $resultAllCars->response;
        }


        $tempp = [];
        if (!empty($dataAllCars) && !empty($dataAllCarsTypes)) {
            foreach ($dataAllCars as $index => $dataAllCarsLOOP) {
                foreach ($dataAllCarsTypes as $dataAllCarsTypesLOOP) {
                    if (!isset($tempp[$dataAllCarsTypesLOOP->category_id]['count'])) {
                        $tempp[$dataAllCarsTypesLOOP->category_id]['count'] = 0;
                        $tempp[$dataAllCarsTypesLOOP->category_id]['name'] = (!empty(array_column(json_decode($dataAllCarsTypesLOOP->category_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                            array_column(json_decode($dataAllCarsTypesLOOP->category_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                    }

                    if ($dataAllCarsTypesLOOP->category_id == $dataAllCarsLOOP->category_id) {
                        $tempp[$dataAllCarsTypesLOOP->category_id]['name'] = (!empty(array_column(json_decode($dataAllCarsTypesLOOP->category_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                            array_column(json_decode($dataAllCarsTypesLOOP->category_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                        $tempp[$dataAllCarsTypesLOOP->category_id]['count'] += 1;
                    }
                }
            }
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

        getHeader($lang["category_cargo"], [
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
                                    <h4 class="page-title"><?= $lang['category_cargo']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">
                                        <a target="_self" href="/admin/category/cargo/add"
                                           class="btn btn-sm btn-outline-primary waves-effect waves-light "><i
                                                    class="mdi mdi-plus-circle me-1"></i>
                                            <?= $lang['add_new']; ?>
                                        </a>
                                    </div>
                                </div><!-- end col-->
                            </div>

                            <div class="table-responsive">
                                <table id="orders-table" data-page-length='10' data-order='[[ 0, "desc" ]]'
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['title']; ?></th>
                                        <th><?= $lang['used']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $status_active = 0;
                                    $status_inactive = 0;
                                    if (!empty($dataAllCarsTypes)) {
                                        $i = 1;
                                        $dataAllCarsTypes = array_reverse($dataAllCarsTypes);
                                        foreach ($dataAllCarsTypes as $dataAllCarsTypesITEM) {
                                            $name = (!empty(array_column(json_decode($dataAllCarsTypesITEM->category_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                array_column(json_decode($dataAllCarsTypesITEM->category_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td class="table-user text-start">
                                                    <img src="<?= Utils::fileExist($dataAllCarsTypesITEM->category_image, BOX_EMPTY); ?>"
                                                         alt="<?= $lang['cargo']; ?>"
                                                         class="me-2 rounded-circle">
                                                    <div class="avatar-xs d-inline-block">
                                                    <span class="avatar-title rounded"
                                                          style="background-color: <?= $dataAllCarsTypesITEM->category_color; ?>">
                                                        &#10020;
                                                    </span>
                                                    </div>
                                                    <a href="/admin/category/cargo/edit/<?= $dataAllCarsTypesITEM->category_id; ?>"
                                                       class="text-body fw-normal">
                                                        <?= $name; ?>
                                                    </a>
                                                </td>
                                                <td><?php
                                                    if (!empty($tempp)) {
                                                        foreach ($tempp as $index => $loop) {
                                                            if ($index == $dataAllCarsTypesITEM->category_id) {
                                                                echo $loop['count'];
                                                            }
                                                        }
                                                    }
                                                    ?></td>
                                                <td>
                                                    <?php
                                                    if ($dataAllCarsTypesITEM->category_status == "active") {
                                                        echo "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
                                                        $status_active += 1;
                                                    } elseif ($dataAllCarsTypesITEM->category_status == "inactive") {
                                                        echo "<span class='badge badge-soft-warning font-12'>" . $lang['inactive'] . "</span>";
                                                        $status_inactive += 1;
                                                    } else {
                                                        echo "<span class='badge badge-soft-danger font-12'>" . $dataAllCarsTypesITEM->category_status . "</span>";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a target="_self"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['edit']; ?>"
                                                       href="/admin/category/cargo/edit/<?= $dataAllCarsTypesITEM->category_id; ?>"
                                                       class="action-icon">
                                                        <i class="mdi mdi-square-edit-outline"></i>
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
                                    <h4 class="page-title"><?= $lang['chart_category_cargo']; ?></h4>
                                </div>
                            </div>

                            <canvas id="myChart" style="width:100%" height="250"></canvas>

                            <div class="text-start mt-3">

                                <?php
                                if (!empty($tempp)) {
                                    foreach ($tempp as $loop) {
                                        ?>
                                        <p class="text-muted mb-2 font-13">
                                            <strong>
                                                <?= $loop['name']; ?> :
                                            </strong>
                                            <span data-plugin="counterup" class="ms-2"><?= $loop['count']; ?></span>
                                        </p>
                                        <?php
                                    }
                                }
                                ?>


                                <hr>
                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['all_category']; ?> :
                                    </strong>
                                    <span class="ms-2"
                                          data-plugin="counterup"><?= $status_inactive + $status_active; ?></span>
                                </p>

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['active']; ?> :
                                    </strong>
                                    <span class="ms-2" data-plugin="counterup"><?= $status_active; ?></span>
                                </p>

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['inactive']; ?> :
                                    </strong>
                                    <span class="ms-2" data-plugin="counterup"><?= $status_inactive; ?></span>
                                </p>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'tempp' => $tempp,
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
                $lang['help_cate_1'],
                $lang['help_cate_2'],
                $lang['help_cate_3'],
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