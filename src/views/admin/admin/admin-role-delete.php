<?php
$pageSlug = "admins";

// permission_can_delete

use MJ\Security\Security;

global $lang, $antiXSS;

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_delete == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1

        $id = (int)$antiXSS->xss_clean($_REQUEST['id']);

        /**
         * Get All Admin Have This Role
         */
        $resultAllAdminHaveThisRole = Admin::getAllAdminHaveThisRole($id);
        $dataAllAdminHaveThisRole = [];
        if ($resultAllAdminHaveThisRole->status == 200 && !empty($resultAllAdminHaveThisRole->response)) {
            $dataAllAdminHaveThisRole = $resultAllAdminHaveThisRole->response;
        }


        /**
         * Get Admin Role Info Permission Show (Role ID)
         */
        $resultRoleInfoPermissionFromShow = Admin::getRoleInfoPermissionFromShow($id);
        $dataRoleInfoPermissionFromShow = [];
        if ($resultRoleInfoPermissionFromShow->status == 200 && !empty($resultRoleInfoPermissionFromShow->response)) {
            $dataRoleInfoPermissionFromShow = $resultRoleInfoPermissionFromShow->response;
            $dataRoleInfo = $resultRoleInfoPermissionFromShow->response[0];
        } else {
            header('Location: /admin/admin');
        }


        /**
         * Get All Admin Slug (Page ID)
         */
        $resultAllAdminSlugs = Admin::getAllAdminSlugs();
        $dataAllAdminSlugs = [];
        if ($resultAllAdminSlugs->status == 200 && !empty($resultAllAdminSlugs->response)) {
            $dataAllAdminSlugs = $resultAllAdminSlugs->response;
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
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');
        enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');

        // Load Script In Footer
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('admin-role-delete', '/dist/js/admin/admin/admin-role-delete.init.js');

        getHeader($lang["admin_role_delete"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_delete',
        ]);


// start roles 2
        if ($flagSlug) {
            // end roles 2
            ?>

            <div class="row">

                <div class="col-sm-12 col-md-12 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["admin_role_delete"]; ?></h5>
                            <div class="col-lg-12">
                                <div class="form-floating mb-3">
                                    <p class="">
                                        <?php
                                        echo $lang['delete_role_1'];
                                        echo (!empty(array_column(json_decode($dataRoleInfo->role_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                            '<strong class="text-danger">' . array_column(json_decode($dataRoleInfo->role_name, true), 'value', 'slug')[$_COOKIE['language']] . '</strong>'
                                            : '<strong class="text-danger">' . $lang['no_value'] . '</strong>';

                                        //                                        foreach (json_decode($dataRoleInfo->role_name) as $tempp) {
                                        //                                            if ($tempp->slug == $language) {
                                        //                                                echo '<strong class="text-danger">' . $tempp->value . '</strong>';
                                        //                                            }
                                        //                                        };
                                        echo $lang['delete_role_2'];
                                        ?>
                                    </p>
                                    <p class="">
                                        <?php
                                        echo $lang['delete_role_3'];
                                        $count = count($dataAllAdminHaveThisRole);
                                        if ($count == 0) {
                                            echo '<strong class="text-success">' . $count . '</strong>';
                                        } else {
                                            echo '<strong class="text-danger">' . $count . '</strong>';
                                        }
                                        ?>
                                    </p>
                                    <p>
                                        <?= $lang['delete_role_5']; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg collapsed" data-bs-toggle="collapse" href="#cardCollpase1"
                                   role="button"
                                   aria-expanded="true" aria-controls="cardCollpase1">
                                    <i class="mdi mdi-minus"></i>
                                </a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["Permission"]; ?></h5>
                            <div class="table-responsive collapse" id="cardCollpase1">
                                <table class="table table-striped mb-0 text-center">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="text-start" colspan="2"><?= $lang["pages"]; ?></th>
                                        <th><?= $lang["visitor"]; ?></th>
                                        <th><?= $lang["creator"]; ?></th>
                                        <th><?= $lang["editor"]; ?></th>
                                        <th><?= $lang["removal"]; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($dataRoleInfoPermissionFromShow)) {
                                        $j = 1;
                                        foreach ($dataRoleInfoPermissionFromShow as $dataRoleInfoPermissionFromShowITEM) {
                                            ?>
                                            <tr>
                                                <th scope="row"><?= $j++; ?></th>
                                                <td colspan="2" class="text-start"><?php
                                                    if (!empty($dataAllAdminSlugs)) {
                                                        foreach ($dataAllAdminSlugs as $dataAllAdminSlugsITEM) {
                                                            if ($dataAllAdminSlugsITEM->slug_id == $dataRoleInfoPermissionFromShowITEM->slug_id) {
                                                                echo $lang[$dataAllAdminSlugsITEM->slug_name];
                                                            }
                                                        }
                                                    }
                                                    ?></td>
                                                <td>
                                                    <input
                                                        <?php if ($dataRoleInfoPermissionFromShowITEM->permission_can_show == "yes") {
                                                            echo "checked";
                                                        } ?>
                                                            disabled class="form-check-input adminPermission"
                                                            type="checkbox">
                                                </td>
                                                <td>
                                                    <input
                                                        <?php if ($dataRoleInfoPermissionFromShowITEM->permission_can_insert == "yes") {
                                                            echo "checked";
                                                        } ?>
                                                            disabled class="form-check-input adminPermission"
                                                            type="checkbox">
                                                </td>
                                                <td>
                                                    <input
                                                        <?php if ($dataRoleInfoPermissionFromShowITEM->permission_can_edit == "yes") {
                                                            echo "checked";
                                                        } ?>
                                                            disabled class="form-check-input adminPermission"
                                                            type="checkbox">
                                                </td>
                                                <td>
                                                    <input
                                                        <?php if ($dataRoleInfoPermissionFromShowITEM->permission_can_delete == "yes") {
                                                            echo "checked";
                                                        } ?>
                                                            disabled class="form-check-input adminPermission"
                                                            type="checkbox">
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th class="text-start" colspan="2"><?= $lang["pages"]; ?></th>
                                        <th><?= $lang["visitor"]; ?></th>
                                        <th><?= $lang["creator"]; ?></th>
                                        <th><?= $lang["editor"]; ?></th>
                                        <th><?= $lang["removal"]; ?></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- end table-responsive-->
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-12 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                            <div class="text-center progress-demo">

                                <button type="button" id="btnDelete" data-id="<?= $id; ?>"
                                        class="btn w-sm btn-soft-danger waves-effect shadow-none waves-light mt-1"
                                        data-style="zoom-in">
                                    <?= $lang["delete"]; ?>
                                </button>

                                <a href="/admin/admin"
                                   class="btn btn w-sm btn-soft-secondary waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["btn_back"]; ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["select_replacement_role"]; ?></h5>
                            <select class="form-control" id="replacementRole" data-toggle="select2"
                                    data-width="100%">
                                <?php
                                if (!empty($dataAllRoles)) {
                                    foreach ($dataAllRoles as $dataAllRolesITEM) {
                                        if ($dataAllRolesITEM->role_id != $dataRoleInfo->role_id) {
                                            ?>
                                            <option
                                                    value="<?= $dataAllRolesITEM->role_id; ?>">
                                                <?= (!empty(array_column(json_decode($dataAllRolesITEM->role_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                    array_column(json_decode($dataAllRolesITEM->role_name, true), 'value', 'slug')[$_COOKIE['language']] : $lang['no_value'];
                                                ?>
                                            </option>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                </div>

            </div>

            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF2() ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'warning' => $lang['warning'],
                        'error' => $lang['error'],
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
                        'role_exist' => $lang['role_exist'],
                        'successful' => $lang['successful'],
                        'successful_submit_mag' => $lang['successful_submit_mag'],
                        'delete_but_error' => $lang['delete_but_error'],
                        'replacement_role_error' => $lang['replacement_role_error'],
                        'token_error' => $lang['token_error'],
                    ];
                    print_r(json_encode($var_lang));  ?>';
            </script>
            <?php

            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter(
            [
                $lang['delete_role_6'],
                $lang['delete_role_5'],
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
?>