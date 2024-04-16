<?php
$pageSlug = "request";
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
         * Get All Requests And Cargoes
         */
        $resultAllRequests = Cargo::getAllRequests();
        $dataAllRequests = [];
        if ($resultAllRequests->status == 200 && !empty($resultAllRequests->response)) {
            $dataAllRequests = $resultAllRequests->response;
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
         * Get All Cities
         */
        $resultAllCities = Ground::getAllActiveCustoms();
        $dataAllCities = [];
        if ($resultAllCities->status == 200 && !empty($resultAllCities->response)) {
            $dataAllCities = $resultAllCities->response;
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

        getHeader($lang["requests_list"], [
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
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row justify-content-between mb-3">
                                <div class="col-auto">
                                    <h4 class="page-title"><?= $lang['requests_list']; ?></h4>
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
                                        <th><?= $lang['customs_of_origin']; ?></th>
                                        <th><?= $lang['destination_customs']; ?></th>
                                        <th><?= $lang['cargo_name']; ?></th>
                                        <th><?= $lang['date_create']; ?></th>
                                        <th><?= $lang['recommended_price']; ?></th>
                                        <th><?= $lang['rate']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th class="all" data-orderable="false"><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $status_accepted = 0;
                                    $status_pending = 0;
                                    $status_rejected = 0;
                                    $status_progress = 0;
                                    $status_completed = 0;
                                    $status_canceled = 0;
                                    if (!empty($dataAllRequests)) {
                                        $i = 1;
                                        $dataAllRequests = array_reverse($dataAllRequests);
                                        foreach ($dataAllRequests as $dataAllRequestsITEM) {
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td>
                                                    <?php
                                                    $name = $lang['no_address'];
                                                    if (!empty($dataAllRequestsITEM->cargo_customs_of_origin)
                                                        && @json_decode($dataAllRequestsITEM->cargo_customs_of_origin)
                                                        && isset(json_decode($dataAllRequestsITEM->cargo_customs_of_origin)->id)
                                                        && !empty(json_decode($dataAllRequestsITEM->cargo_customs_of_origin)->id)) {

                                                        if (!empty($dataAllCities)) {
                                                            foreach ($dataAllCities as $dataAllCitiesITEM) {
                                                                if ($dataAllCitiesITEM->city_id == json_decode($dataAllRequestsITEM->cargo_customs_of_origin)->id) {
                                                                    $name = (!empty(array_column(json_decode($dataAllCitiesITEM->customs_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                        array_column(json_decode($dataAllCitiesITEM->getAllActiveCustoms, true), 'value', 'slug')[$_COOKIE['language']] : $lang['no_name'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if ($name == $lang['no_address']) {
                                                        echo "<span class='text-danger'>" . $name . "</span>";
                                                    } elseif ($name == $lang['no_name']) {
                                                        echo "<span class='text-warning'>" . $name . "</span>";
                                                    } else {
                                                        echo $name;
                                                    }

                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $name2 = $lang['no_address'];

                                                    if (!empty($dataAllRequestsITEM->cargo_destination_customs)
                                                        && @json_decode($dataAllRequestsITEM->cargo_destination_customs)
                                                        && isset(json_decode($dataAllRequestsITEM->cargo_destination_customs)->id)
                                                        && !empty(json_decode($dataAllRequestsITEM->cargo_destination_customs)->id)) {

                                                        if (!empty($dataAllCities)) {
                                                            foreach ($dataAllCities as $dataAllCitiesITEM) {
                                                                if ($dataAllCitiesITEM->city_id == json_decode($dataAllRequestsITEM->cargo_destination_customs)->id) {
                                                                    $name2 = (!empty(array_column(json_decode($dataAllCitiesITEM->city_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                        array_column(json_decode($dataAllCitiesITEM->city_name, true), 'value', 'slug')[$_COOKIE['language']] : $lang['no_name'];
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                    }
                                                    if ($name2 == $lang['no_address']) {
                                                        echo "<span class='text-danger'>" . $lang['no_address'] . "</span>";
                                                    } elseif ($name2 == $lang['no_name']) {
                                                        echo "<span class='text-warning'>" . $lang['no_name'] . "</span>";
                                                    } else {
                                                        echo $name2;
                                                    }

                                                    ?>
                                                </td>
                                                <td><?= mb_strimwidth($dataAllRequestsITEM->cargo_name, 0, 50, '...'); ?></td>
                                                <td>
                                                    <bdi>
                                                        <?= Utils::getTimeCountry('d F Y', $dataAllRequestsITEM->request_date); ?>
                                                    </bdi>
                                                </td>
                                                <td><?= number_format($dataAllRequestsITEM->cargo_recommended_price) . " ";
                                                    if (!empty($dataAllCurrencies)) {
                                                        foreach ($dataAllCurrencies as $dataAllCurrency) {
                                                            if ($dataAllCurrency->currency_id == $dataAllRequestsITEM->cargo_monetary_unit) {
                                                                echo (!empty(array_column(json_decode($dataAllCurrency->currency_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                    array_column(json_decode($dataAllCurrency->currency_name, true), 'value', 'slug')[$_COOKIE['language']] : $lang['no_name'];
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?= Utils::getStarsByRate((int)$dataAllRequestsITEM->request_rate); ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($dataAllRequestsITEM->request_status == "accepted") {
                                                        $status_accepted += 1;
                                                        echo "<span class='badge badge-soft-success font-12'>" . $lang['accepted'] . "</span>";
                                                    } elseif ($dataAllRequestsITEM->request_status == "pending") {
                                                        $status_pending += 1;
                                                        echo "<span class='badge badge-soft-warning font-12'>" . $lang['pending'] . "</span>";
                                                    } elseif ($dataAllRequestsITEM->request_status == "rejected") {
                                                        $status_rejected += 1;
                                                        echo "<span class='badge badge-soft-danger font-12'>" . $lang['rejected'] . "</span>";
                                                    } elseif ($dataAllRequestsITEM->request_status == "progress") {
                                                        $status_progress += 1;
                                                        echo "<span class='badge badge-soft-info font-12'>" . $lang['progress'] . "</span>";
                                                    } elseif ($dataAllRequestsITEM->request_status == "canceled") {
                                                        $status_canceled += 1;
                                                        echo "<span class='badge badge-soft-secondary font-12'>" . $lang['canceled'] . "</span>";
                                                    } elseif ($dataAllRequestsITEM->request_status == "completed") {
                                                        $status_completed += 1;
                                                        echo "<span class='badge badge-soft-primary font-12'>" . $lang['completed'] . "</span>";
                                                    } else {
                                                        echo "<span class='badge badge-soft-pink font-12'>" . $dataAllRequestsITEM->request_status . "</span>";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a target="_self"
                                                       href="/admin/car/<?= $dataAllRequestsITEM->car_id; ?>"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['car_info']; ?>"
                                                       class="action-icon">
                                                        <i class="mdi mdi-dump-truck"></i>
                                                    </a>
                                                    <a href="/admin/users/info/<?= $dataAllRequestsITEM->user_id; ?>"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['user_info']; ?>"
                                                       target="_self" class="action-icon">
                                                        <i class="mdi mdi-account-circle-outline"></i>
                                                    </a>
                                                    <a href="/admin/cargo/<?= $dataAllRequestsITEM->cargo_id; ?>"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['cargo_info']; ?>"
                                                       target="_self" class="action-icon">
                                                        <i class="mdi mdi-truck-trailer"></i>
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
                <div class="col-sm-12 col-md-12 col-lg-6">
                    <div class="card">
                        <div class="card-body">

                            <div class="row justify-content-between mb-3">
                                <div class="col-auto">
                                    <h4 class="page-title"><?= $lang['chart_requests']; ?></h4>
                                </div>
                            </div>

                            <canvas id="myChart" style="width:100%" height="250"></canvas>

                            <div class="text-start mt-3">

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['all_requests']; ?> :
                                    </strong>
                                    <span class="ms-2"
                                          data-plugin="counterup">
                                        <?= $status_accepted + $status_pending + $status_rejected + $status_progress + $status_completed + $status_canceled; ?></span>
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
                            ['name' => $lang['progress'], 'count' => $status_progress],
                            ['name' => $lang['completed'], 'count' => $status_completed],
                            ['name' => $lang['canceled'], 'count' => $status_canceled],
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
                $lang['help_request_1'],
                $lang['help_request_2'],
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