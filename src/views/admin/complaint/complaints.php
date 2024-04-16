<?php
$pageSlug = "complaint";
// permission_can_show

global $lang, $Settings;

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


        /**
         * Get All Complaints Whit Cargo
         */
        $Result = Complaint::getAllComplaintsWhitCargo();
        $Data = [];
        if ($Result->status == 200 && !empty($Result->response)) {
            $Data = $Result->response;
        }

        /**
         * Get All Admins
         */
        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
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


        getHeader($lang["list_complaints"], [
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
                                    <h4 class="page-title"><?= $lang['list_complaints']; ?></h4>
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
                                        <th><?= $lang['b_cargo_type']; ?></th>
                                        <th><?= $lang['admin_answer']; ?></th>
                                        <th><?= $lang['date']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th class="all" data-orderable="false"><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                    $status_accepted = 0;
                                    $status_pending = 0;
                                    $status_closed = 0;
                                    if (!empty($Data)) {
                                        $i = 1;
                                        $Data = array_reverse($Data);
                                        foreach ($Data as $DataITEM) {
                                            ?>
                                            <tr class="<?= (is_null($DataITEM->admin_id)) ? 'bg-soft-danger' : ''; ?>">
                                                <td><?= $i++; ?></td>
                                                <td><?= $DataITEM->$cargoName; ?></td>
                                                <td><?= ($DataITEM->xtype == "out") ? $lang['a_cargo_out_2'] : $lang['cargo_in']; ?></td>
                                                <td class="table-user text-start">
                                                    <?php
                                                    if (!empty($dataAllAdmins) && !is_null($DataITEM->admin_id)) {
                                                        foreach ($dataAllAdmins as $dataAllAdminsITEM) {
                                                            if ($dataAllAdminsITEM->admin_id == $DataITEM->admin_id) {
                                                                ?>
                                                                <img src="<?= Utils::fileExist($dataAllAdminsITEM->admin_avatar, USER_AVATAR); ?>"
                                                                     alt="<?= Security::decrypt($dataAllAdminsITEM->admin_nickname);; ?>"
                                                                     class="me-2 rounded-circle">
                                                                <?= $dataAllAdminsITEM->admin_nickname; ?>
                                                                <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                        <img src="<?= USER_AVATAR; ?>"
                                                             alt="<?= $lang['admin']; ?>"
                                                             class="me-2 rounded-circle">
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <bdi><?= Utils::getTimeCountry($Settings['data_time_format'], $DataITEM->complaint_date); ?></bdi>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($DataITEM->complaint_status == "accepted") {
                                                        echo "<span class='badge badge-soft-warning font-12'>" . $lang['now_answer'] . "</span>";
                                                        $status_accepted += 1;
                                                    } elseif ($DataITEM->complaint_status == "pending") {
                                                        echo "<span class='badge badge-soft-danger font-12'>" . $lang['pending_answer'] . "</span>";
                                                        $status_pending += 1;
                                                    } elseif ($DataITEM->complaint_status == "closed") {
                                                        echo "<span class='badge badge-soft-primary font-12'>" . $lang['closed'] . "</span>";
                                                        $status_closed += 1;
                                                    } else {
                                                        echo "<span class='badge badge-soft-pink font-12'>" . $DataITEM->complaint_status . "</span>";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="/admin/complaint/<?php echo ($DataITEM->xtype == "out") ? null : "in/";
                                                    echo $DataITEM->complaint_id; ?>"
                                                       target="_self"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['all_info']; ?>"
                                                       class="action-icon">
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
                                    <h4 class="page-title"><?= $lang['chart_complaint']; ?></h4>
                                </div>
                            </div>

                            <canvas id="myChart" style="width:100%" height="250"></canvas>

                            <div class="text-start mt-3">

                                <p class="text-muted mb-2 font-13">
                                    <strong>
                                        <?= $lang['all_complaint']; ?> :
                                    </strong>
                                    <span class="ms-2"
                                          data-plugin="counterup"><?= $status_accepted + $status_pending + $status_closed; ?></span>
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
                                        <?= $lang['closed']; ?> :
                                    </strong>
                                    <span class="ms-2" data-plugin="counterup"><?= $status_closed; ?></span>
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
                            ['name' => $lang['closed'], 'count' => $status_closed],
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
                $lang['help_complaint_1'],
                $lang['help_complaint_2'],
                $lang['help_complaint_3'],
                $lang['help_complaint_4'],
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