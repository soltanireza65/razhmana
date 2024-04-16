<?php
$pageSlug = "admins";
// permission_can_show

global $lang, $Settings, $antiXSS;

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


        $language = $antiXSS->xss_clean($_COOKIE['language']);

        /**
         * Get All Admins
         */
        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
        }


        /**
         * Get All Roles
         */
        $resultAllRoles = Admin::getAllRoles();
        $dataAllRoles = [];
        if ($resultAllRoles->status == 200 && !empty($resultAllRoles->response)) {
            $dataAllRoles = $resultAllRoles->response;
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

        getHeader($lang["admins"], [
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
                            <div class="row justify-content-between">

                                <div class="col-auto">
                                    <h4 class="page-title"><?= $lang["admins"]; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">
                                        <a target="_self" href="/admin/admin/add"
                                           class="btn btn-sm btn-outline-primary waves-effect waves-light "><i
                                                    class="mdi mdi-plus-circle me-1"></i><?= $lang['add_new']; ?></a>
                                    </div>
                                </div><!-- end col-->
                            </div> <!-- end row -->
                        </div>
                    </div> <!-- end card -->
                </div> <!-- end col-->
            </div>

            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-8">
                    <div class="card">
                        <div class="card-body">

                            <div class="table-responsive">
                                <table id="orders-table" data-page-length='10' data-order='[[ 0, "asc" ]]'
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['name_and_family']; ?></th>
                                        <th><?= $lang['nickname']; ?></th>
                                        <th><?= $lang['role']; ?></th>
                                        <th><?= $lang['email']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th><?= $lang['data_register']; ?></th>
                                        <th class="all" data-orderable="false"><?= $lang['action']; ?></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                    $i = 1;
                                    if (!empty($dataAllAdmins)) {
                                        foreach ($dataAllAdmins as $dataAllAdminsITEM) {
                                            ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td class="table-user text-start">
                                                    <img src="<?= Utils::fileExist($dataAllAdminsITEM->admin_avatar, USER_AVATAR); ?>"
                                                         alt="<?= Security::decrypt($dataAllAdminsITEM->admin_name);; ?>"
                                                         class="me-2 rounded-circle">
                                                    <a href="/admin/admin/edit/<?= $dataAllAdminsITEM->admin_id; ?>"
                                                       class="text-body fw-normal">
                                                        <?= Security::decrypt($dataAllAdminsITEM->admin_name);; ?>
                                                    </a>
                                                </td>
                                                <td><?= $dataAllAdminsITEM->admin_nickname; ?></td>
                                                <td>
                                                    <i class="mdi mdi-shield-star-outline text-warning"></i>&nbsp;
                                                    <?php
                                                    if (!empty($dataAllRoles)) {
                                                        foreach ($dataAllRoles as $dataAllRolesITEM) {
                                                            if ($dataAllRolesITEM->role_id == $dataAllAdminsITEM->role_id) {
                                                                foreach (json_decode($dataAllRolesITEM->role_name) as $item) {
                                                                    if ($item->slug == $language) {
                                                                        echo $item->value;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </td>

                                                <td><?= Security::decrypt($dataAllAdminsITEM->admin_email); ?></td>
                                                <td>
                                                    <?php
                                                    if ($dataAllAdminsITEM->admin_status == "active") {
                                                        echo "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
                                                    } elseif ($dataAllAdminsITEM->admin_status == "inactive") {
                                                        echo "<span class='badge badge-soft-warning font-12'>" . $lang['inactive'] . "</span>";
                                                    } else {
                                                        echo "<span class='badge badge-soft-danger font-12'>" . $dataAllAdminsITEM->admin_status . "</span>";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <bdi>
                                                        <?= Utils::getTimeCountry($Settings['date_format'], $dataAllAdminsITEM->admin_register_date); ?>
                                                    </bdi>
                                                </td>
                                                <td>
                                                    <a href="/admin/admin/log/<?= $dataAllAdminsITEM->admin_id; ?>"
                                                       target="_self"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['show_logs']; ?>"
                                                       class="action-icon">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    <a href="/admin/admin/edit/<?= $dataAllAdminsITEM->admin_id; ?>"
                                                       target="_self"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['edit_2']; ?>"
                                                       class="action-icon">
                                                        <i class="mdi mdi-square-edit-outline"></i>
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
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-6"><h4
                                            class="page-title"><?= $lang["roles"]; ?></h4>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-6" style="text-align: left;">
                                    <a target="_self"
                                       href="/admin/admin/role/add"
                                       class="btn btn-sm btn-outline-primary waves-effect waves-light">
                                        <i class="mdi mdi-plus-circle me-1"></i>
                                        <?= $lang['add_new']; ?>
                                    </a>
                                </div>
                            </div>

                            <div class="col-auto">
                                <div class="text-lg-end my-1 my-lg-0">

                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="table-list-table-roles"
                                       class="table table-hover toggle-arrow-tiny text-dark mb-0 text-center"
                                       data-page-size="10" data-sort="false">
                                    <thead>
                                    <tr>
                                        <th class="text-start"><?= $lang["title"]; ?></th>
                                        <th><?= $lang["status"]; ?></th>
                                        <th><?= $lang["action"]; ?></th>
                                    </tr>
                                    </thead>

                                    <tbody>

                                    <?php
                                    if (!empty($dataAllRoles)) {
                                        foreach ($dataAllRoles as $dataAllRolesITEM) {
                                            ?>
                                            <tr>
                                                <td class=" text-start">
                                                    <i class="mdi mdi-star text-warning"></i>
                                                    <a href="javascript:void(0);" class="text-body fw-normal">
                                                        <?php
                                                        foreach (json_decode($dataAllRolesITEM->role_name) as $item) {
                                                            if ($item->slug == $language) {
                                                                echo $item->value;
                                                            }
                                                        }
                                                        ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($dataAllRolesITEM->role_status == "active") {
                                                        echo "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
                                                    } elseif ($dataAllRolesITEM->role_status == "inactive") {
                                                        echo "<span class='badge badge-soft-warning font-12'>" . $lang['inactive'] . "</span>";
                                                    } else {
                                                        echo "<span class='badge badge-soft-danger font-12'>" . $dataAllRolesITEM->role_status . "</span>";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="/admin/admin/role/edit/<?= $dataAllRolesITEM->role_id; ?>"
                                                       target="_self"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['edit']; ?>"
                                                       class="action-icon">
                                                        <i class="mdi mdi-pencil"></i></a>
                                                    <a href="/admin/admin/role/delete/<?= $dataAllRolesITEM->role_id; ?>"
                                                       target="_self"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="<?= $lang['delete']; ?>"
                                                       class="action-icon">
                                                        <i class="mdi mdi-delete"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>

                                    </tbody>

                                    <tfoot>
                                    <tr class="active">
                                        <td class="visually-hidden"></td>
                                        <td class="border-0 px-0" colspan="6">
                                            <div class="float-sm-end">
                                                <ul class="pagination pagination-rounded sd-pagination justify-content-end footable-pagination d-block mb-0"></ul>
                                            </div>
                                        </td>
                                    </tr>
                                    </tfoot>
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