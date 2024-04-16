<?php
$pageSlug = "users";
// permission_can_show

global $lang, $antiXSS, $Settings;

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

        /**
         * Get User Log By Id
         */
        $resultUserLogsById = AUser::getUserLogsById($id);
        $dataUserLogsById = [];
        if ($resultUserLogsById->status == 200 && !empty($resultUserLogsById->response)) {
            $dataUserLogsById = $resultUserLogsById->response;
        }


        // Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');

        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');


        getHeader($lang["list_user_log"], [
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="row justify-content-between mb-3">
                                <div class="col-auto">
                                    <h4 class="page-title"><?= $lang['user_log']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">
                                        <a target="_self" href="/admin/users/info/<?= $id; ?>"
                                           class="btn btn-sm btn-outline-primary waves-effect waves-light "><i
                                                    class="mdi mdi-arrow-right me-1"></i>
                                            <?= $lang['back']; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="orders-table" data-page-length='25' data-order='[[ 0, "asc" ]]'
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['date']; ?></th>
                                        <th><?= $lang['message']; ?></th>
                                        <th><?= $lang['browser']; ?></th>
                                        <th><?= $lang['ip']; ?></th>
                                        <th><?= $lang['device']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($dataUserLogsById)) {
                                        $i = 1;
                                        $dataUserLogsById = array_reverse($dataUserLogsById);
                                        foreach ($dataUserLogsById as $dataUserLogsByIdITEM) {
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td>
                                                    <bdi><?= Utils::getTimeCountry($Settings['data_time_format'], json_decode($dataUserLogsByIdITEM->log_detail)->time); ?></bdi>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (array_key_exists(json_decode($dataUserLogsByIdITEM->log_detail)->message, $lang)) {
                                                        echo $lang[json_decode($dataUserLogsByIdITEM->log_detail)->message];
                                                    } else {
                                                        echo json_decode($dataUserLogsByIdITEM->log_detail)->message;
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (array_key_exists(json_decode($dataUserLogsByIdITEM->log_detail)->browser, $lang)) {
                                                        echo $lang[json_decode($dataUserLogsByIdITEM->log_detail)->browser];
                                                    } else {
                                                        echo json_decode($dataUserLogsByIdITEM->log_detail)->browser;
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?= json_decode($dataUserLogsByIdITEM->log_detail)->ip; ?>
                                                </td>
                                                <td>
                                                    <?= json_decode($dataUserLogsByIdITEM->log_detail)->device . " - " . json_decode($dataUserLogsByIdITEM->log_detail)->os; ?>
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
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter(
            [
                $lang['help_user_logs'],
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