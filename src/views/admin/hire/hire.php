<?php
$pageSlug = "a_employ";

// permission_can_show

use MJ\Utils\Utils;

global $lang, $Settings;

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

        $getAllEmploy = Hire::getAllEmploy()->response;

        // Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');


        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('data-table', '/dist/js/admin/hire/hire.js');

        getHeader($lang["a_employ_list"], [
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
                                    <h4 class="page-title"><?= $lang['a_employ_list']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">
                                        <input type="text" id="a-goto-page" placeholder="<?=$lang['a_goto_page']?>" >
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="orders-table"
                                       data-page-length='10'
                                       data-order='[[ 0, "desc" ]]'
                                       data-tj-col="employ_id,employ_first_name,employ_last_name,c_name,employ_status,employ_title,employ_submit_date,employ_employ"
                                       data-tj-address="dt-hire"
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['b_name']; ?></th>
                                        <th><?= $lang['b_lastname']; ?></th>
                                        <th><?= $lang['employ_city']; ?></th>

                                        <th><?= $lang['status']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th><?= $lang['date_create']; ?></th>
                                        <th class="all" data-orderable="false"><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th><input class="form-control" id="employ_first_name" type="text"
                                                   data-tj-col="1"
                                                   placeholder="<?= $lang['search']; ?>"/></th>
                                        <th><input class="form-control" id="employ_last_name" type="text"
                                                   data-tj-col="2"
                                                   placeholder="<?= $lang['search']; ?>"/></th>
                                        <th><input class="form-control" id="c_name" type="text"
                                                   data-tj-col="3"
                                                   placeholder="<?= $lang['search']; ?>"/></th>

                                        <th><select class="form-select" id="employ_status" data-tj-col="4">
                                                <option value=""><?= $lang['u_wallet_filter_all']; ?></option>
                                                <option value="pending"><?= $lang['u_inquiry_air_pending']; ?></option>
                                                <option value="process"><?= $lang['u_inquiry_air_process']; ?></option>
                                                <option value="completed"><?= $lang['u_inquiry_air_completed']; ?></option>

                                                <option value="reject"><?= $lang['reject']; ?></option>
                                            </select></th>
                                        <th>
                                            <select class="form-select" id="employ_title" data-tj-col="5">
                                                <option value=""><?= $lang['u_wallet_filter_all']; ?></option>
                                                <?php
                                                $titles = Hire::getHireTitleAll()->response;
                                                foreach ($titles as $title) {
                                                    ?>
                                                    <option value="<?= $title->category_id; ?>"><?= (!empty(array_column(json_decode($title->category_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                            array_column(json_decode($title->category_name, true), 'value', 'slug')[$_COOKIE['language']] : $title->category_id; ?></option>
                                                <?php } ?>
                                            </select>
                                        </th>

                                        <th><?= $lang['action']; ?></th>
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
                   value="<?= $_SESSION['dt-hire'] = "dt-hire-44"; ?>">
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter([]);

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