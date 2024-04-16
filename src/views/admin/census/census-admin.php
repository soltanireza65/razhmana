<?php
$pageSlug = "census";
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


        /**
         * Get All Admins
         */
        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
        }

        // Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
        enqueueStylesheet('persian-datepicker', '/dist/libs/persian-calendar/persian-datepicker.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');
        enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');

        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');
        enqueueScript('persian-date-min-js', '/dist/libs/persian-calendar//persian-date.min.js');
        enqueueScript('persian-datepicker-min-js', '/dist/libs/persian-calendar/persian-datepicker.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('census-admin', '/dist/js/admin/census/census-admin.init.js');

        getHeader($lang['census_admin'], [
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
            <div class="row" id="printDIV">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2 text-center"><?= $lang["census_admin"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="dateStart"
                                                       placeholder="1374/04/04"
                                                       value="<?= date('Y/m/d', strtotime('-1 month')); ?>">
                                                <label for="dateStart"><?= $lang['date_start']; ?></label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="dateEnd"
                                                       placeholder="Password">
                                                <label for="dateEnd"><?= $lang['date_end']; ?></label>
                                            </div>
                                        </div>


                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <select class="form-select" id="selectAdmin" data-width="100%">

                                                <?php
                                                if (!empty($dataAllAdmins)) {
                                                    foreach ($dataAllAdmins as $dataAllAdminsITEM) {
                                                        ?>
                                                        <option
                                                                data-mj-nickname="<?= $dataAllAdminsITEM->admin_nickname; ?>"
                                                                data-mj-avatar="<?= $dataAllAdminsITEM->admin_avatar; ?>"
                                                                value="<?= $dataAllAdminsITEM->admin_id; ?>">
                                                            <?= Security::decrypt($dataAllAdminsITEM->admin_name); ?>
                                                        </option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-sm-6 col-md-6 col-lg-6 d-flex justify-content-between mt-3 mt-md-0 mt-sm-0">

                                            <button type="button"
                                                    id="btnSubmit"
                                                    data-style="zoom-in"
                                                    class="btn width-md btn-soft-primary waves-effect waves-light">
                                                <?= $lang['submit']; ?>
                                            </button>
                                            <button type="button"
                                                    onclick="printContent('printDIV')"
                                                    class="btn width-md btn-soft-info waves-effect waves-light">
                                                <?= $lang['print']; ?>
                                            </button>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 text-center">

                                    <img src="/dist/images/user.svg" id="adminAvatar"
                                         class="rounded-circle avatar-lg img-thumbnail"
                                         alt="profile-image">
                                    <h4 class="mb-0" id="adminName"><?= $lang['admin_name']; ?></h4>
                                    <p class="text-muted" id="adminNickname"><?= $lang['admin_nickname']; ?></p>

                                </div>
                            </div>


                            <hr class="mb-1">
                            <hr class="mt-1 ">
                            <div class="table-responsive">
                                <table id="orders-table" data-page-length='25' data-sort="false" data-searching="false"
                                       data-info="false" data-paging="false" data-order='[[ 0, "asc" ]]'
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th class="no-sort">#</th>
                                        <th class="text-center"><?= $lang['action']; ?></th>
                                        <th class="text-center"><?= $lang['count']; ?></th>

                                    </tr>
                                    </thead>
                                    <tbody id="AddDiv">
                                    <tr id="EmptyTR" class="text-center">
                                        <td colspan="3">
                                            <?= $lang['empty_table']; ?>
                                        </td>
                                        <td class="d-none"></td>
                                        <td class="d-none"></td>
                                    </tr>

                                    </tbody>

                                </table>
                            </div>


                        </div>
                    </div>
                </div>

            </div>

            <input id="startDefault" type="hidden">
            <input id="endDefault" type="hidden">
            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('admin-census-admin') ?>">

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