<?php
$pageSlug = "a_poster";
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

        // Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');

        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('data-table', '/dist/js/admin/users/users.init.js');

        getHeader($lang["a_list_poster_reports"], [
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
                #orders-table_filter {
                    display: none;
                }
            </style>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row justify-content-between mb-3">
                                <div class="col-auto">
                                    <h4 class="page-title"><?= $lang['a_list_poster_reports']; ?></h4>
                                </div>
                            </div>
                            <div class="table-responsive">

                                <table id="orders-table"
                                       data-page-length='10'
                                       data-order='[[ 0, "desc" ]]'
                                       data-tj-col="rp_id,user_firstname,user_lastname,rp_desc,rp_status,rp_submit_date,poster_id"
                                       data-tj-address="dt-posters-reports"
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['a_name']; ?></th>
                                        <th><?= $lang['a_lastname']; ?></th>
                                        <th><?= $lang['title']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th><?= $lang['date']; ?></th>
                                        <th data-orderable="false"><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th><input class="form-control" id="first-name" type="text" data-tj-col="1"
                                                   placeholder="<?= $lang['search']; ?>"/></th>
                                        <th><input class="form-control" id="last-name" type="text" data-tj-col="2"
                                                   placeholder="<?= $lang['search']; ?>"/></th>
                                        <th><?= $lang['title']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th><?= $lang['date']; ?></th>
                                        <th><?= $lang['action']; ?></th>
                                    </tr>
                                    </tfoot>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="token" name="token"
                   value="<?= $_SESSION['dt-posters-reports'] = "dt-posters-reports-44"; ?>">
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