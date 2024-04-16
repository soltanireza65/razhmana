<?php
$pageSlug = "cargo";
// permission_can_show

global $lang, $antiXSS,$Settings;

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_show == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1

        $id = (int)$antiXSS->xss_clean($_REQUEST['id']);

        /**
         * Get User Info By Id
         */
        $resultUserInfoById = AUser::getUserInfoById($id);
        $dataUserInfoById = [];
        if ($resultUserInfoById->status == 200 && !empty($resultUserInfoById->response)) {
            $dataUserInfoById = $resultUserInfoById->response[0];
        }
        if (empty($dataUserInfoById)) {
            header('Location: /admin');
        }

        $Data = [];
        if ($dataUserInfoById->user_type == "driver") {
            $Result = Cargo::getDriverCargoByUserID($id);
            if ($Result->status == 200 && !empty($Result->response)) {
                $Data = $Result->response;
            }
        } else {
            $Result = Cargo::getAllCargosUserByUserId($id);
            if ($Result->status == 200 && !empty($Result->response)) {
                $Data = $Result->response;
            }
        }


        $UserNam = $lang['guest_user'];
        if (!empty($dataUserInfoById->user_firstname)) {
            $UserNam = Security::decrypt($dataUserInfoById->user_firstname) . " " . Security::decrypt($dataUserInfoById->user_lastname);
        }


        /**
         * Get All Cars
         */


        /**
         * Get All Category Cargo
         */
        $resultAllCargoCategory = Cargo::getAllCargoCategory();
        $dataAllCargoCategory = [];
        if ($resultAllCargoCategory->status == 200 && !empty($resultAllCargoCategory->response)) {
            $dataAllCargoCategory = $resultAllCargoCategory->response;
        }

        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }
        $cargoName="cargo_name_".$language;

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

        getHeader($lang["cargoes_out"], [
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
                                    <div class="d-flex align-items-start">
                                        <img class="d-flex me-3 rounded-circle avatar-lg" src="<?= USER_AVATAR; ?>"
                                             alt="<?= $UserNam; ?>">
                                        <div class="w-100">
                                            <h4 class="mt-0 mb-1">
                                                <a
                                                        href="/admin/users/info/<?= $id; ?>"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="<?= $lang['all_info']; ?>"
                                                        target="_self">
                                                    <i class="mdi mdi-account-circle-outline"></i>
                                                </a>
                                                <?= $UserNam; ?>

                                            </h4>
                                            <p class="text-muted">
                                                <?php
                                                if ($dataUserInfoById->user_type == "businessman") {
                                                    ?>
                                                    <i class="mdi mdi-office-building"></i>
                                                    <?= $lang['businessman']; ?>
                                                    <?php
                                                } elseif ($dataUserInfoById->user_type == "driver") {
                                                    ?>
                                                    <i class="mdi mdi-dump-truck"></i>
                                                    <?= $lang['driver']; ?>
                                                    <?php
                                                } elseif ($dataUserInfoById->user_type == "guest") {
                                                    ?>
                                                    <i class="mdi mdi-account-alert"></i>
                                                    <?= $lang['guest_user']; ?>
                                                <?php } else { ?>
                                                    <i class="mdi mdi-account-edit-outline"></i>
                                                    <?= $dataUserInfoById->user_type; ?>
                                                <?php } ?>
                                            </p>
                                            <p class="text-muted">
                                                <?= $lang['list_cargos']; ?>
                                            </p>
                                        </div>
                                    </div>
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
                                        <th><?= $lang['cargo_name']; ?></th>
                                        <th><?= $lang['cargo_submit_data']; ?></th>
                                        <th><?= $lang['category']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $status_accepted = 0;
                                    $status_pending = 0;
                                    $status_rejected = 0;
                                    $status_completed = 0;
                                    $status_canceled = 0;
                                    $status_progress = 0;
                                    if (!empty($Data)) {
                                        $i = 1;
                                        $Data = array_reverse($Data);
                                        foreach ($Data as $dataAllCargoITEM) {
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= mb_strimwidth($dataAllCargoITEM->$cargoName, 0, 30, '...'); ?></td>
                                                <td>
                                                    <bdi><?= Utils::getTimeCountry($Settings['date_format'], $dataAllCargoITEM->cargo_date); ?></bdi>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (!empty($dataAllCargoCategory)) {
                                                        foreach ($dataAllCargoCategory as $dataAllCargoCategoryITEM) {
                                                            if ($dataAllCargoCategoryITEM->category_id == $dataAllCargoITEM->category_id) {
                                                                echo (!empty(array_column(json_decode($dataAllCargoCategoryITEM->category_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                    array_column(json_decode($dataAllCargoCategoryITEM->category_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </td>

                                                <td>
                                                    <?php
                                                    if ($dataAllCargoITEM->cargo_status == "accepted") {
                                                        echo "<span class='badge badge-soft-success font-12'>" . $lang['accepted'] . "</span>";
                                                        $status_accepted += 1;
                                                    } elseif ($dataAllCargoITEM->cargo_status == "pending") {
                                                        echo "<span class='badge badge-soft-warning font-12'>" . $lang['pending'] . "</span>";
                                                        $status_pending += 1;
                                                    } elseif ($dataAllCargoITEM->cargo_status == "rejected") {
                                                        echo "<span class='badge badge-soft-danger font-12'>" . $lang['rejected'] . "</span>";
                                                        $status_rejected += 1;
                                                    } elseif ($dataAllCargoITEM->cargo_status == "progress") {
                                                        echo "<span class='badge badge-soft-info font-12'>" . $lang['progress'] . "</span>";
                                                        $status_progress += 1;
                                                    } elseif ($dataAllCargoITEM->cargo_status == "canceled") {
                                                        echo "<span class='badge badge-soft-secondary font-12'>" . $lang['canceled'] . "</span>";
                                                        $status_canceled += 1;
                                                    } elseif ($dataAllCargoITEM->cargo_status == "completed") {
                                                        echo "<span class='badge badge-soft-primary font-12'>" . $lang['completed'] . "</span>";
                                                        $status_completed += 1;
                                                    } else {
                                                        echo "<span class='badge badge-soft-pink font-12'>" . $dataAllCargoITEM->cargo_status . "</span>";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a target="_self"
                                                       href="/admin/cargo/<?= $dataAllCargoITEM->cargo_id; ?>"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['cargo_info']; ?>"
                                                       class="action-icon">
                                                        <i class="mdi mdi-dump-truck"></i>
                                                    </a>
                                                    <a href="/admin/users/info/<?= $dataAllCargoITEM->user_id; ?>"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['businessman_info']; ?>"
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
                                    <h4 class="page-title"><?= $lang['chart_cargoes']; ?></h4>
                                </div>
                            </div>

                            <canvas id="myChart" style="width:100%" height="250"></canvas>

                            <div class="text-start mt-3">

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['all_cargoes']; ?> :
                                    </strong>
                                    <span class="ms-2"
                                          data-plugin="counterup"><?= $status_accepted + $status_pending + $status_progress + $status_completed + $status_rejected + $status_canceled; ?></span>
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
                                        <?= $lang['progress']; ?> :
                                    </strong>
                                    <span class="ms-2" data-plugin="counterup"><?= $status_progress; ?></span>
                                </p>
                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['completed']; ?> :
                                    </strong>
                                    <span class="ms-2" data-plugin="counterup"><?= $status_completed; ?></span>
                                </p>
                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['canceled']; ?> :
                                    </strong>
                                    <span class="ms-2" data-plugin="counterup"><?= $status_canceled; ?></span>
                                </p>
                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['rejected']; ?> :
                                    </strong>
                                    <span class="ms-2" data-plugin="counterup"><?= $status_rejected; ?></span>
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
                            ['name' => $lang['completed'], 'count' => $status_completed],
                            ['name' => $lang['canceled'], 'count' => $status_canceled],
                            ['name' => $lang['progress'], 'count' => $status_progress],
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
                $lang['help_user_cargo_1'],
                $lang['help_user_cargo_2'],
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