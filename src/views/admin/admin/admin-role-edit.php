<?php
$pageSlug = "admins";
// permission_can_edit

global $lang, $antiXSS;

use MJ\Security\Security;
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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_edit == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1

        $id = (int)$antiXSS->xss_clean($_REQUEST['id']);

        /**
         * Get Admin Role Info Permission Show (Role ID)
         */
        $resultRoleInfoPermissionFromShow = Admin::getRoleInfoPermissionFromShow($id);
        $dataRoleInfoPermissionFromShow = [];
        if ($resultRoleInfoPermissionFromShow->status == 200 && !empty($resultRoleInfoPermissionFromShow->response)) {
            $dataRoleInfoPermissionFromShow = $resultRoleInfoPermissionFromShow->response;
            $dataAdminRoleById = $resultRoleInfoPermissionFromShow->response[0];
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


        $resultLanguages = Utils::getFileValue("languages.json", "", false);
        $dataLanguages = [];
        if (!empty($resultLanguages)) {
            $dataLanguages = json_decode($resultLanguages);
        }

        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('admin-role-edit', '/dist/js/admin/admin/admin-role-edit.init.js');

        getHeader($lang["admin_role_edit"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_edit',
        ]);


// start roles 2
        if ($flagSlug) {
            // end roles 2);
            ?>


            <div class="row">


                <div class="col-sm-12 col-md-12 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["admin_role_edit"]; ?></h5>
                            <?php
                            if (!empty($dataLanguages)) {
                                foreach ($dataLanguages as $dataLanguagesTEMP) {
                                    ?>
                                    <div class="col-lg-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control nameRole"
                                                   value="<?php
                                                   if (!empty($dataAdminRoleById)) {
                                                       foreach (json_decode($dataAdminRoleById->role_name) as $dataAdminRoleByIdITEM) {
                                                           if ($dataAdminRoleByIdITEM->slug == $dataLanguagesTEMP->slug) {
                                                               echo $dataAdminRoleByIdITEM->value;
                                                           }
                                                       }
                                                   }
                                                   ?>"
                                                   data-slug="<?= $dataLanguagesTEMP->slug; ?>"
                                                   placeholder="<?= $lang["role_title"]; ?>(<?= $lang[$dataLanguagesTEMP->name]; ?>)">
                                            <label for="nameCAteGory"><?= $lang["role_title"];
                                                if ($dataLanguagesTEMP->status == "inactive") {
                                                    echo '<span class="text-danger font-11"> (' . $lang[$dataLanguagesTEMP->name] . ')</span>';
                                                } else {
                                                    echo '<span class="text-success font-11"> (' . $lang[$dataLanguagesTEMP->name] . ')</span>';
                                                } ?>
                                            </label>
                                            <small class="form-text text-muted">
                                                <?= $lang['length_text']; ?> : <span
                                                        class="text-danger"
                                                        data-id-length="<?= $dataLanguagesTEMP->slug; ?>">0</span>
                                            </small>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>


                        </div>
                    </div>


                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["Permission"]; ?></h5>

                            <div class="table-responsive">
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
                                            <tr class="adppppppp">
                                                <th scope="row"><?= $j++; ?></th>
                                                <td colspan="2" class="text-start"><?php
                                                    $slug_id = 0;
                                                    if (!empty($dataAllAdminSlugs)) {
                                                        foreach ($dataAllAdminSlugs as $dataAllAdminSlugsITEM) {
                                                            if ($dataAllAdminSlugsITEM->slug_id == $dataRoleInfoPermissionFromShowITEM->slug_id) {
                                                                $slug_id = $dataRoleInfoPermissionFromShowITEM->slug_id;
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
                                                            class="form-check-input adminPermission"
                                                            data-permission="show"
                                                            data-slug-id="<?= $dataRoleInfoPermissionFromShowITEM->permission_id; ?>"
                                                            id="insertAdmin<?= $slug_id; ?>"
                                                            type="checkbox">
                                                </td>
                                                <td>
                                                    <input
                                                        <?php if ($dataRoleInfoPermissionFromShowITEM->permission_can_insert == "yes") {
                                                            echo "checked";
                                                        } ?>
                                                            class="form-check-input adminPermission"
                                                            data-permission="insert"
                                                            data-slug-id="<?= $dataRoleInfoPermissionFromShowITEM->permission_id; ?>"
                                                            id="insertAdmin<?= $slug_id; ?>"
                                                            type="checkbox">
                                                </td>
                                                <td>
                                                    <input
                                                        <?php if ($dataRoleInfoPermissionFromShowITEM->permission_can_edit == "yes") {
                                                            echo "checked";
                                                        } ?>
                                                            class="form-check-input adminPermission"
                                                            data-permission="edit"
                                                            data-slug-id="<?= $dataRoleInfoPermissionFromShowITEM->permission_id; ?>"
                                                            id="insertAdmin<?= $slug_id; ?>"
                                                            type="checkbox">
                                                </td>
                                                <td>
                                                    <input
                                                        <?php if ($dataRoleInfoPermissionFromShowITEM->permission_can_delete == "yes") {
                                                            echo "checked";
                                                        } ?>
                                                            class="form-check-input adminPermission"
                                                            data-permission="delete"
                                                            data-slug-id="<?= $dataRoleInfoPermissionFromShowITEM->permission_id; ?>"
                                                            id="insertAdmin<?= $slug_id; ?>"
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
                                <button id="btnActive" type="button"
                                        data-id="<?= $dataAdminRoleById->role_id; ?>"
                                        class="<?= ($dataAdminRoleById->role_status == "active") ? "active" : ""; ?>
                                        setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light mt-1"
                                        data-style="zoom-in">
                                    <?= $lang["active_submit"]; ?>
                                </button>
                                <button id="btnInactive" type="button"
                                        data-id="<?= $dataAdminRoleById->role_id; ?>"
                                        class="<?= ($dataAdminRoleById->role_status == "inactive") ? "active" : ""; ?>
                                         setSubmitBtn btn w-sm btn-soft-warning waves-effect shadow-none waves-light mt-1"
                                        data-style="zoom-in">
                                    <?= $lang["inactive_submit"]; ?>
                                </button>
                                <a href="/admin/admin"
                                   class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["btn_back"]; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["all_info"]; ?></h5>
                            <div class="table-responsive">
                                <table class="table mb-0 table-sm">
                                    <tbody>
                                    <tr>
                                        <td><?= $lang["status"]; ?> :</td>
                                        <td>
                                            <?php
                                            if ($dataAdminRoleById->role_status == "active") {
                                                echo "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
                                            } elseif ($dataAdminRoleById->role_status == "inactive") {
                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['inactive'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-danger font-12'>" . $dataAdminRoleById->role_status . "</span>";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF2() ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'error' => $lang['error'],
                        'successful' => $lang['successful'],
                        'warning' => $lang['warning'],
                        'successful_submit_mag' => $lang['successful_submit_mag'],
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
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
                $lang['help_admin_role_4'],
                $lang['help_admin_role_1'],
                $lang['help_admin_role_2'],
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