<?php
$pageSlug = "transaction";
// permission_can_show

global $lang, $antiXSS;

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


        $UserNam = $lang['guest_user'];
        if (!empty($dataUserInfoById->user_firstname)) {
            $UserNam = Security::decrypt($dataUserInfoById->user_firstname) . " " . Security::decrypt($dataUserInfoById->user_lastname);
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

        getHeader($lang["list_transactions_user"], [
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
                                                <?= $lang['list_transactions_user']; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-0 my-lg-0">

                                    </div>
                                </div><!-- end col-->
                            </div>
                            <hr>
                            <div class="table-responsive">
                                <div class="table-responsive">
                                    <table id="orders-table" data-page-length='25' data-order='[[ 0, "desc" ]]'
                                           data-tj-col="transaction_id,transaction_authority,transaction_tracking_code,transaction_amount,currency_name,transaction_status,transaction_date,transaction_type,card_id"
                                           data-tj-address="dt-transactions-user"
                                           data-tj-where="<?= $id; ?>"
                                           class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= $lang['tx_authority']; ?></th>
                                            <th><?= $lang['tx_tacking_code']; ?></th>
                                            <th><?= $lang['transaction_amount']; ?></th>
                                            <th><?= $lang['a_currency_type']; ?></th>
                                            <th><?= $lang['status']; ?></th>
                                            <th><?= $lang['transaction_list_date']; ?></th>
                                            <th><?= $lang['transaction_type']; ?></th>
                                            <th data-orderable="false"><?= $lang['action']; ?></th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th><input class="form-control" id="authority" type="text" data-tj-col="3"
                                                       placeholder="<?= $lang['search']; ?>"/></th>
                                            <th><input class="form-control" id="tacking-code" type="text"
                                                       data-tj-col="3"
                                                       placeholder="<?= $lang['search']; ?>"/></th>
                                            <th><input class="form-control" id="transaction-amount" type="text"
                                                       data-tj-col="4"
                                                       placeholder="<?= $lang['search']; ?>"/></th>
                                            <th><?= $lang['a_currency_type']; ?></th>
                                            <th><?= $lang['status']; ?></th>
                                            <th><?= $lang['transaction_list_date']; ?></th>
                                            <th><?= $lang['transaction_type']; ?></th>
                                            <th><?= $lang['action']; ?></th>
                                        </tr>
                                        </tfoot>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <input type="hidden" id="token" name="token"
                   value="<?= $_SESSION['dt-transactions-user'] = "dt-transactions-user-44"; ?>">
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter(
            [
                $lang['help_user_transaction_1'],
                $lang['help_user_transaction_2'],
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