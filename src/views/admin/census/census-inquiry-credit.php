<?php
$pageSlug = "census";
// permission_can_show

global $lang;

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

        // Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');;

        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('jquery-mask-plugin', '/dist/libs/jquery-mask-plugin/jquery.mask.min.js');
        enqueueScript('mask', '/dist/libs/inputmask/inputmask.js');
        enqueueScript('census-inquiry-credit', '/dist/js/admin/census/census-inquiry-credit.init.js');

        getHeader($lang['a_cart_inquiry'], [
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
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2 text-center"><?= $lang["a_cart_inquiry"]; ?></h5>
                            <div class="row">
                                <div class="col-sm-6 col-md-6 col-lg-2">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="dateD"
                                               placeholder="04"
                                               value="<?= Utils::jDate('d', strtotime('now')); ?>">
                                        <label for="dateStart"><?= $lang['day']; ?></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-2">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="dateM"
                                               placeholder="04"
                                               value="<?= Utils::jDate('m', strtotime('now')); ?>">
                                        <label for="dateStart"><?= $lang['month']; ?></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-2">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="dateY"
                                               placeholder="74"
                                               value="<?= Utils::jDate('y', strtotime('now')); ?>">
                                        <label for="dateStart"><?= $lang['year']; ?></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-6" style="direction: ltr;">
                                    <button type="button"
                                            onclick="printContent('printDIV')"
                                            class="btn width-md btn-soft-info waves-effect waves-light">
                                        <?= $lang['print']; ?>
                                    </button>
                                    <button type="button"
                                            id="btnSubmit"
                                            data-style="zoom-in"
                                            class="btn width-md btn-soft-primary waves-effect waves-light">
                                        <?= $lang['submit']; ?>
                                    </button>
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
            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF2() ?>">
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