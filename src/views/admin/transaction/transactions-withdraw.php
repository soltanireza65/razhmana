<?php
$pageSlug = "transaction";
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
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');

        getHeader($lang["list_withdraw"], [
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
                                    <h4 class="page-title"><?= $lang['list_withdraw']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">

                                    </div>
                                </div><!-- end col-->
                            </div>

                            <div class="table-responsive">
                                <table id="orders-table"
                                       data-page-length='25'
                                       data-order='[[ 0, "desc" ]]'
                                       data-tj-col="transaction_id,user_firstname,user_lastname,transaction_authority,transaction_tracking_code,transaction_status,transaction_date,transaction_amount,card_id"
                                       data-tj-address="dt-transactions-withdraw"
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['a_name']; ?></th>
                                        <th><?= $lang['a_lastname']; ?></th>
                                        <th><?= $lang['tx_authority']; ?></th>
                                        <th><?= $lang['tx_tacking_code']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th><?= $lang['transaction_list_date']; ?></th>
                                        <th><?= $lang['transaction_amount']; ?></th>
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
                                        <th><input class="form-control" id="authority" type="text" data-tj-col="3"
                                                   placeholder="<?= $lang['search']; ?>"/></th>
                                        <th><input class="form-control" id="tacking-code" type="text" data-tj-col="4"
                                                   placeholder="<?= $lang['search']; ?>"/></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th><?= $lang['transaction_list_date']; ?></th>
                                        <th><?= $lang['transaction_amount']; ?></th>
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
                   value="<?= $_SESSION['dt-transactions-withdraw'] = "dt-transactions-withdraw-44"; ?>">

            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter(
            [
                $lang['help_transaction_1'],
                $lang['help_transaction_2'],
                $lang['help_transaction_3'],
                $lang['help_transaction_4'],
                $lang['help_transaction_5_1'],
                $lang['help_transaction_6'],
                $lang['help_transaction_7'],
                $lang['help_transaction_8'],
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