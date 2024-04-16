<?php
$pageSlug = "admins";
// permission_can_edit

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_edit == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1

        $id = (int)$antiXSS->xss_clean($_REQUEST['id']);
        $language = $antiXSS->xss_clean($_COOKIE['language']);

        /**
         * Get Admin Info By ID
         */

        $resultAdminById = Admin::getAdminById($id);
        $dataAdminById = [];
        if ($resultAdminById->status == 200 && !empty($resultAdminById->response)) {
            $dataAdminById = $resultAdminById->response[0];
        }
        if (empty($dataAdminById)) {
            header('Location: /admin/admin');
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
        enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');
        enqueueStylesheet('dropzone', '/dist/libs/dropzone/min/dropzone.min.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('dropzone', '/dist/libs/dropzone/min/dropzone.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('admin-edit', '/dist/js/admin/admin/admin-edit.init.js');

        getHeader($lang["admin_edit"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_edit',
        ]);

// start roles 2
        if ($flagSlug) {
            // end roles 2
            ?>

            <div class="row">

                <div class="col-lg-8">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["admin_edit"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="nameAdmin"
                                               data-id="<?= $dataAdminById->admin_id; ?>"
                                               value="<?= Security::decrypt($dataAdminById->admin_name); ?>"
                                               placeholder="<?= $lang["name_and_family"]; ?>">
                                        <label for="nameAdmin"><?= $lang["name_and_family"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                    class="text-danger"
                                                    id="length_nameAdmin">0</span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="nicknameAdmin"
                                               value="<?= $dataAdminById->admin_nickname; ?>"
                                               placeholder="<?= $lang["nickname"]; ?>">
                                        <label for="nicknameAdmin"><?= $lang["nickname"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                    class="text-danger"
                                                    id="length_nicknameAdmin">0</span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="mobileAdmin"
                                               minlength="11" maxlength="11"
                                               value="<?= Security::decrypt($dataAdminById->admin_mobile); ?>"
                                               placeholder="<?= $lang["phone_number"]; ?>">
                                        <label for="mobileAdmin"><?= $lang["phone_number"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['validation']; ?> : <span
                                                    class="text-danger"
                                                    id="length_mobileAdmin"><?= $lang['invalidate_phone']; ?></span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="emailAdmin"
                                               value="<?= Security::decrypt($dataAdminById->admin_email); ?>"
                                               placeholder="name@example.com">
                                        <label for="emailAdmin"><?= $lang["email"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['validation']; ?> : <span
                                                    class="text-danger"
                                                    id="length_emailAdmin"><?= $lang['invalidate_email']; ?></span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-lg-12">

                                    <?php if($dataAdminById->admin_id != 1){
                                     ?>
                                    <div class="mb-3">
                                        <label for="passwordAdmin" class="form-label"><?= $lang["password"]; ?></label>
                                        <div class="input-group input-group-merge">
                                            <input type="text" id="passwordAdmin" class="form-control"
                                                   value="<?= Security::decrypt($dataAdminById->admin_password); ?>"
                                                   placeholder="<?= $lang["password"]; ?>">
                                            <div class="input-group-text show-password" data-password="true">
                                                <span class="password-eye"></span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">
                                            <?= $lang['validation']; ?> : <span
                                                    class="text-danger"
                                                    id="length_passwordAdmin"><?= $lang['invalidate_password']; ?></span>
                                        </small>
                                    </div>
                                        <?php
                                    }?>

                                </div>

                                <div class="col-lg-12">

                                    <ol class="text-warning">
                                        <li>
                                            <?= $lang['pass_notic1']; ?>
                                        </li>
                                        <li>
                                            <?= $lang['pass_notic2']; ?>
                                        </li>
                                        <li>
                                            <?= $lang['pass_notic3']; ?>
                                        </li>
                                        <li>
                                            <?= $lang['pass_notic4']; ?>
                                        </li>
                                    </ol>

                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                            <div class="text-center progress-demo">
                                <button id="btnActive" type="button"
                                        class="<?= ($dataAdminById->admin_status == "active") ? "active" : ""; ?>
                                         setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["active_submit"]; ?>
                                </button>
                                <button id="btnInactive" type="button"
                                        class="<?= ($dataAdminById->admin_status == "inactive") ? "active" : ""; ?>
                                         setSubmitBtn btn w-sm btn-soft-warning waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["inactive_submit"]; ?>
                                </button>
                                <a href="/admin/admin"
                                   class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light">
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
                                        <th scope="row"><?= $lang["status"]; ?> :</th>
                                        <td>
                                            <?php
                                            if ($dataAdminById->admin_status == "active") {
                                                echo "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
                                            } elseif ($dataAdminById->admin_status == "inactive") {
                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['inactive'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-danger font-12'>" . $dataAdminById->admin_status . "</span>";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["role_admin"]; ?></h5>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <select class="form-control" id="rolesAdmin" data-toggle="select2"
                                            data-width="100%">

                                        <?php
                                        if (!empty($dataAllRoles)) {
                                            foreach ($dataAllRoles as $dataAllRolesITEM) {
                                                ?>
                                                <option
                                                    <?= ($dataAdminById->role_id == $dataAllRolesITEM->role_id) ? " selected " : ""; ?>
                                                        value="<?= $dataAllRolesITEM->role_id; ?>">
                                                    <?=
                                                    (!empty(array_column(json_decode($dataAllRolesITEM->role_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                        array_column(json_decode($dataAllRolesITEM->role_name, true), 'value', 'slug')[$_COOKIE['language']] : $lang['no_value'];
                                                    ?>
                                                </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["avatar"]; ?></h5>
                            <form action="/" method="post" class="dropzone" id="avatarAdmin"
                                  data-plugin="dropzone">
                                <div class="fallback">
                                    <input name="file" type="file">
                                </div>
                                <div class="dz-message needsclick">
                                    <img class="img-fluid rounded" src="<?= $dataAdminById->admin_avatar; ?>">
                                </div>
                            </form>

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
                        'email_error' => $lang['email_error'],
                        'pass_error' => $lang['pass_error'],
                        'nickname_error' => $lang['nickname_error'],
                        'name_error' => $lang['name_error'],
                        'avatar_error' => $lang['avatar_error'],
                        'successful' => $lang['successful'],
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
                        'email_exist' => $lang['email_exist'],
                        'phone_exist' => $lang['phone_exist'],
                        'email_pass_error' => $lang['email_pass_error'],
                        'dictMaxFilesExceeded' => $lang['dictMaxFilesExceeded'],
                        'mobile_invalid' => $lang['mobile_invalid'],
                        'delete' => $lang['delete'],
                        'cancel_upload' => $lang['cancel_upload'],
                        'validate_phone' => $lang['validate_phone'],
                        'invalidate_phone' => $lang['invalidate_phone'],
                        'validate_email' => $lang['validate_email'],
                        'invalidate_email' => $lang['invalidate_email'],
                        'validate_password' => $lang['validate_password'],
                        'invalidate_password' => $lang['invalidate_password'],
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
                $lang['help_admin_1'],
                $lang['help_admin_2'],
                $lang['help_admin_3'],
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