<?php
$pageSlug = "admins";
// permission_can_insert

global $lang, $antiXSS;

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_insert == "yes") {
                    $flagSlug = true;
                }
            }
        }
        // end roles 1


        /**
         * Get All Admin Slug (Page ID)
         */
        $resultAllAdminSlugs = Admin::getAllAdminSlugs();
        $dataAllAdminSlugs = [];
        if ($resultAllAdminSlugs->status == 200 && !empty($resultAllAdminSlugs->response)) {
            $dataAllAdminSlugs = $resultAllAdminSlugs->response;
        }


        /**
         * Get All Languages
         */
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
        enqueueScript('admin-role-add', '/dist/js/admin/admin/admin-role-add.init.js');

        getHeader($lang["admin_role_add"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_insert',
        ]);

        // start roles 2
        if ($flagSlug) {
            // end roles 2
            ?>
            <div class="row">

                <div class="col-sm-12 col-md-12 col-lg-8">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["role_title"]; ?></h5>
                                    <?php
                                    if (!empty($dataLanguages)) {
                                        foreach ($dataLanguages as $dataLanguagesTEMP) {
                                            ?>
                                            <div class="col-lg-12">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control nameRole"
                                                           data-slug="<?= $dataLanguagesTEMP->slug; ?>"
                                                           placeholder="<?= $lang["role_name"]; ?>(<?= $lang[$dataLanguagesTEMP->name]; ?>)">
                                                    <label for="nameCAteGory"><?= $lang["role_name"];
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
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="col-lg-12">
                                        <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["access"]; ?></h5>
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
                                                if (!empty($dataAllAdminSlugs)) {
                                                    $i = 1;
                                                    foreach ($dataAllAdminSlugs as $dataAllAdminSlugsITEM) {
                                                        ?>
                                                        <tr class="adppppppp">
                                                            <th scope="row"><?= $i++; ?></th>
                                                            <td colspan="2"
                                                                class="text-start"><?= $lang[$dataAllAdminSlugsITEM->slug_name]; ?></td>
                                                            <td>
                                                                <input class="form-check-input adminPermission"
                                                                       type="checkbox"
                                                                       data-permission="show"
                                                                       data-slug-id="<?= $dataAllAdminSlugsITEM->slug_id; ?>"
                                                                       id="showAdmin<?= $dataAllAdminSlugsITEM->slug_id; ?>">
                                                            </td>
                                                            <td>
                                                                <input class="form-check-input adminPermission"
                                                                       type="checkbox"
                                                                       data-permission="insert"
                                                                       data-slug-id="<?= $dataAllAdminSlugsITEM->slug_id; ?>"
                                                                       id="insertAdmin<?= $dataAllAdminSlugsITEM->slug_id; ?>">
                                                            </td>
                                                            <td>
                                                                <input class="form-check-input adminPermission"
                                                                       type="checkbox"
                                                                       data-permission="edit"
                                                                       data-slug-id="<?= $dataAllAdminSlugsITEM->slug_id; ?>"
                                                                       id="editAdmin<?= $dataAllAdminSlugsITEM->slug_id; ?>">
                                                            </td>
                                                            <td>
                                                                <input class="form-check-input adminPermission"
                                                                       type="checkbox"
                                                                       data-permission="delete"

                                                                       data-slug-id="<?= $dataAllAdminSlugsITEM->slug_id; ?>"
                                                                       id="deleteAdmin<?= $dataAllAdminSlugsITEM->slug_id; ?>">
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
                                        </div> <!-- end table-responsive-->
                                    </div> <!-- end col -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-12 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                            <div class="text-center progress-demo">
                                <button id="btnActive" type="button"
                                        class="setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light mt-1"
                                        data-style="zoom-in">
                                    <?= $lang["active_submit"]; ?>
                                </button>
                                <button id="btnInactive" type="button"
                                        class="setSubmitBtn btn w-sm btn-soft-warning waves-effect shadow-none waves-light mt-1"
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