<?php
$pageSlug = "census";
// permission_can_show

global $lang, $antiXSS;

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_show == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1

        $start = $antiXSS->xss_clean($_REQUEST['start']);
        $end = $antiXSS->xss_clean($_REQUEST['end']);

        if (!is_numeric($start) || !is_numeric($end)) {
            header('Location: /admin/census/admins');
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
         * Get All Admin Slug (Page ID)
         */
        $resultAdminLogsById = Admin::getAdminsLogsByTime($start, $end);
        $dataAdminLogsById = [];
        if ($resultAdminLogsById->status == 200 && !empty($resultAdminLogsById->response)) {
            $dataAdminLogsById = $resultAdminLogsById->response;
        }

        // Load Script In Footer
        enqueueScript('chartJs', '/dist/libs/chart.js/Chart.bundle.min.js');
        enqueueScript('printThis', '/dist/libs/printThis/printThis.js');
        enqueueScript('census-admins', '/dist/js/admin/census/census-admins-show.init.js');

        getHeader($lang['census_admins'], [
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
            <style>
                @media print {
                    canvas {
                        display: flex;
                        justify-content: center;
                        min-height: 100%;
                        max-width: 100%;
                        max-height: 100%;
                        height: 200px !important;
                        width: 200px !important;
                    }

                    body {
                        direction: rtl;
                        text-align: right;
                    }
                }
            </style>
            <div class="row" id="printDIV">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2 text-center"><?= $lang["census_admins"]; ?></h5>
                            <div class="row d-flex justify-content-center">

                                <div class="col-sm-4 col-md-4 col-lg-4 printDIVs">
                                    <h4 class="font-13 text-muted text-uppercase mb-1">
                                        <?= $lang['date_start']; ?> :
                                    </h4>
                                    <p class=""> <?= Utils::getTimeCountry('Y/m/d', $start); ?></p>
                                </div>

                                <div class="col-sm-4 col-md-4 col-lg-4 printDIVs">
                                    <h4 class="font-13 text-muted text-uppercase mb-1"><?= $lang['date_end']; ?> :</h4>
                                    <p class=""> <?= Utils::getTimeCountry('Y/m/d', $end); ?></p>
                                </div>

                                <div class="col-sm-4 col-md-4 col-lg-4 ">
                                    <button type="button"
                                            onclick="printContent()"
                                            class="btn btn-lg width-lg btn-soft-info waves-effect waves-light">
                                        <i class="mdi mdi-printer me-1"></i>
                                        <?= $lang['print']; ?>
                                    </button>

                                    <a href="/admin/census/admins/"
                                       class="btn btn-lg width-lg btn-soft-primary waves-effect waves-light">
                                        <i class="mdi mdi-reload me-1"></i>
                                        <?= $lang['start_again']; ?>

                                    </a>


                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $array = [];
                if (!empty($dataAdminLogsById)) {
                    foreach ($dataAdminLogsById as $loop) {
                        if (isset($array[$loop->log_slug])) {
                            if (isset($array[$loop->log_slug][$loop->admin_id])) {
                                $array[$loop->log_slug][$loop->admin_id] += 1;
                            } else {
                                $array[$loop->log_slug][$loop->admin_id] = 1;
                            }
                        } else {
                            $array[$loop->log_slug][$loop->admin_id] = 1;
                        }
                    }
                }
                $temp = [];
                if (!empty($array)) {
                    foreach ($array as $index => $arrayLoop) {

                        ?>
                        <div class="col-sm-12 col-md-12 col-lg-12 printDIVs">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">

                                        <h5 class="text-uppercase mt-0 mb-3 bg-light p-2 text-center"><?= (empty($index)) ? $lang['other'] : $lang[$index]; ?></h5>
                                        <div class="col-sm-12 col-md-6 col-lg-4">
                                            <canvas id="chart<?= $index; ?>" style="width:100%" height="250"></canvas>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-8">
                                            <div class="table-responsive">
                                                <table class="table mb-0">
                                                    <thead class="table-light">
                                                    <tr>
                                                        <th><?= $lang['admin_name']; ?></th>
                                                        <th><?= $lang['count']; ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $ttt = [];
                                                    if (!empty($arrayLoop)) {
                                                        foreach ($arrayLoop as $indecc => $arrayLoopITEM) {
                                                            ?>
                                                            <tr>
                                                                <td><?php
                                                                    $name = Security::decrypt(array_column($dataAllAdmins, 'admin_name', 'admin_id')[$indecc]);
                                                                    echo $name;
                                                                    $ttt[] = ['adminName' => $name, 'count' => $arrayLoopITEM];
                                                                    ?></td>
                                                                <td>
                                                                    <span data-plugin="counterup"><?= $arrayLoopITEM; ?></span>
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
                            </div>
                        </div>
                        <?php
                        $temp[] = ['chart' => 'chart' . $index, 'chartName' => (empty($index)) ? $lang['other'] : $lang[$index], 'values' => $ttt];
                    }
                }

                ?>
            </div>

            <input id="startDefault" type="hidden">
            <input id="endDefault" type="hidden">
            <script>
                var var_lang = '<?php
                    $var_lang = $temp;
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