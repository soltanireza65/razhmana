<?php
$pageSlug = ['a_request_in_pending', 'a_request_in_accepted', 'a_request_in_rejected', 'a_request_in_progress', 'a_request_in_canceled', 'a_request_in_completed'];
// permission_can_show

global $lang;

include_once getcwd() . '/views/admin/header-footer.php';

$array_requests = [];
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

                if (in_array($item000->slug_name, $pageSlug) && $item000->permission_can_show == "yes") {
                    $flagSlug = true;
                }
                if (in_array($item000->slug_name, $pageSlug)) {
                    $array_requests[] = $item000->slug_name;
                }
            }
        }
// end roles 1

        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        // Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');

        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('data-table', '/dist/js/admin/ground/request.js');

        getHeader($lang["requests_list_in"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => 'request',
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
                                    <h4 class="page-title"><?= $lang['requests_list_in']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">

                                    </div>
                                </div><!-- end col-->
                            </div>

                            <div class="table-responsive">
                                <table id="orders-table"
                                       data-page-length='10'
                                       data-order='[[ 0, "desc" ]]'
                                       data-tj-col="request_id,cargo_name_<?=$language;?>,cargo_origin_id,cargo_destination_id,request_price,request_date,request_status,cargo_monetary_unit"
                                       data-tj-address="dt-request-in"
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['cargo_name']; ?></th>
                                        <th><?= $lang['b_cargo_source_city']; ?></th>
                                        <th><?= $lang['b_cargo_dest_city']; ?></th>
                                        <th><?= $lang['recommended_price']; ?></th>
                                        <th><?= $lang['date_create']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th class="all" data-orderable="false"><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>
                                            <input class="form-control" id="first-name1" type="text" data-tj-col="0"
                                                   placeholder="<?= $lang['search']; ?>"/>
                                        </th>
                                        <th>
                                            <input class="form-control" id="first-name2" type="text" data-tj-col="1"
                                                   placeholder="<?= $lang['search']; ?>"/>
                                        </th>
                                        <th>
                                            <input class="form-control" id="first-name3" type="text" data-tj-col="2"
                                                   placeholder="<?= $lang['search']; ?>"/>
                                        </th>
                                        <th>
                                            <input class="form-control" id="first-name4" type="text" data-tj-col="3"
                                                   placeholder="<?= $lang['search']; ?>"/>
                                        </th>
                                        <th><?= $lang['recommended_price']; ?></th>
                                        <th><?= $lang['date_create']; ?></th>
                                        <th>
                                            <?php
                                            $flagChecked = true;
                                            if (in_array('a_request_in_pending', $array_requests)) {
                                                ?>
                                                <div class="form-check">
                                                    <input type="radio" name="status" value="pending" id="pending"
                                                        <?php if ($flagChecked) {
                                                            echo "checked";
                                                            $flagChecked = false;
                                                        } ?>
                                                           data-tj-col="6" class="form-check-input">
                                                    <label class="form-check-label"
                                                           for="pending"><?= $lang['pending']; ?></label>
                                                </div>
                                            <?php }
                                            if (in_array('a_request_in_accepted', $array_requests)) {
                                                ?>
                                                <div class="form-check">
                                                    <input type="radio" name="status" value="accepted" id="accepted"
                                                        <?php if ($flagChecked) {
                                                            echo "checked";
                                                            $flagChecked = false;
                                                        } ?>
                                                           data-tj-col="6" class="form-check-input">
                                                    <label class="form-check-label"
                                                           for="accepted"><?= $lang['accepted']; ?></label>
                                                </div>
                                            <?php }
                                            if (in_array('a_request_in_rejected', $array_requests)) {
                                                ?>
                                                <div class="form-check">
                                                    <input type="radio" name="status" value="rejected" id="rejected"
                                                        <?php if ($flagChecked) {
                                                            echo "checked";
                                                            $flagChecked = false;
                                                        } ?>
                                                           data-tj-col="6" class="form-check-input">
                                                    <label class="form-check-label"
                                                           for="rejected"><?= $lang['rejected']; ?></label>
                                                </div>
                                            <?php }
                                            if (in_array('a_request_in_progress', $array_requests)) {
                                                ?>
                                                <div class="form-check">
                                                    <input type="radio" name="status" value="progress" id="progress"
                                                        <?php if ($flagChecked) {
                                                            echo "checked";
                                                            $flagChecked = false;
                                                        } ?>
                                                           data-tj-col="6" class="form-check-input">
                                                    <label class="form-check-label"
                                                           for="progress"><?= $lang['progress']; ?></label>
                                                </div>
                                            <?php }
                                            if (in_array('a_request_in_completed', $array_requests)) {
                                                ?>
                                                <div class="form-check">
                                                    <input type="radio" name="status" value="completed" id="completed"
                                                        <?php if ($flagChecked) {
                                                            echo "checked";
                                                            $flagChecked = false;
                                                        } ?>
                                                           data-tj-col="6" class="form-check-input">
                                                    <label class="form-check-label"
                                                           for="completed"><?= $lang['completed']; ?></label>
                                                </div>
                                            <?php }
                                            if (in_array('a_request_in_canceled', $array_requests)) {
                                                ?>
                                                <div class="form-check">
                                                    <input type="radio" name="status" value="canceled" id="canceled"
                                                        <?php if ($flagChecked) {
                                                            echo "checked";
                                                            $flagChecked = false;
                                                        } ?> data-tj-col="6" class="form-check-input">
                                                    <label class="form-check-label"
                                                           for="canceled"><?= $lang['canceled']; ?></label>
                                                </div>
                                            <?php } ?>
                                        </th>
                                        <th><?= $lang['status']; ?></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="token" name="token"
                       value="<?= $_SESSION['dt-request-in'] = "dt-request-in-44"; ?>">
            </div>

            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter(
            [
                $lang['help_request_1'],
                $lang['help_request_2'],
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