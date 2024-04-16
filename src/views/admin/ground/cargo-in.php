<?php
$pageSlug = "cargo_in";
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
         * Get All Cargo Count
         */
        $resultAllCargoCount = Cargo::getCountCargoInFromChart();

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

        getHeader($lang["cargoes_in_list"], [
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
                                    <h4 class="page-title"><?= $lang['cargoes_in_list']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">

                                    </div>
                                </div><!-- end col-->
                            </div>

                            <div class="table-responsive">
                                <table id="orders-table" data-page-length='10' data-order='[[ 0, "desc" ]]'
                                       data-tj-col="cargo_id,user_firstname,cargo_name_fa_IR,cargo_date,category_name,cargo_status,user_lastname"
                                       data-tj-address="dt-cargo-in"
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['name_and_family']; ?></th>
                                        <th><?= $lang['cargo_name']; ?></th>
                                        <th><?= $lang['cargo_submit_data']; ?></th>
                                        <th><?= $lang['category']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th class="all" data-orderable="false"><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
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
                                          data-plugin="counterup"><?= $resultAllCargoCount['all']; ?></span>
                                </p>

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['accepted']; ?> :
                                    </strong>
                                    <span class="ms-2"
                                          data-plugin="counterup"><?= $resultAllCargoCount['accepted']; ?></span>
                                </p>

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['pending']; ?> :
                                    </strong>
                                    <span class="ms-2"
                                          data-plugin="counterup"><?= $resultAllCargoCount['pending']; ?></span>
                                </p>
                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['progress']; ?> :
                                    </strong>
                                    <span class="ms-2"
                                          data-plugin="counterup"><?= $resultAllCargoCount['progress']; ?></span>
                                </p>
                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['completed']; ?> :
                                    </strong>
                                    <span class="ms-2"
                                          data-plugin="counterup"><?= $resultAllCargoCount['completed']; ?></span>
                                </p>
                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['canceled']; ?> :
                                    </strong>
                                    <span class="ms-2"
                                          data-plugin="counterup"><?= $resultAllCargoCount['canceled']; ?></span>
                                </p>
                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['rejected']; ?> :
                                    </strong>
                                    <span class="ms-2"
                                          data-plugin="counterup"><?= $resultAllCargoCount['rejected']; ?></span>
                                </p>
                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['expired']; ?> :
                                    </strong>
                                    <span class="ms-2"
                                          data-plugin="counterup"><?= $resultAllCargoCount['expired']; ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="token" name="token"
                   value="<?= $_SESSION['dt-cargo-in'] = "dt-cargo-in-44"; ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'tempp' => [
                            ['name' => $lang['accepted'], 'count' => $resultAllCargoCount['accepted']],
                            ['name' => $lang['pending'], 'count' => $resultAllCargoCount['pending']],
                            ['name' => $lang['rejected'], 'count' => $resultAllCargoCount['rejected']],
                            ['name' => $lang['completed'], 'count' => $resultAllCargoCount['completed']],
                            ['name' => $lang['canceled'], 'count' => $resultAllCargoCount['canceled']],
                            ['name' => $lang['progress'], 'count' => $resultAllCargoCount['progress']],
                            ['name' => $lang['expired'], 'count' => $resultAllCargoCount['expired']],
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
                $lang['help_cargo_1'],
                $lang['help_cargo_2'],
                $lang['help_cargo_3'],
                $lang['help_cargo_4'],
                $lang['help_cargo_5'],
                $lang['help_cargo_6'],
                $lang['help_cargo_7'],
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