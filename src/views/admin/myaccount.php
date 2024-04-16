<?php
$pageSlug = "myaccount";
// permission_can_edit

global $lang,$antiXSS,$Settings;

use  MJ\Security\Security;
use MJ\Utils\Utils;

include_once 'header-footer.php';

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
                //                if ($item000->slug_name == $pageSlug && ($item000->permission_can_edit == "yes" || $item000->permission_can_show == "yes" || $item000->permission_can_delete == "yes" || $item000->permission_can_insert == "yes")) {
                if ($item000->slug_name == $pageSlug && ($item000->permission_can_edit == "yes")) {
                    $flagSlug = true;
                }
            }
        }
// end roles 1


        /**
         * Get All Roles
         */
        $resultAllRoles = Admin::getAllRoles();
        $dataAllRoles = [];
        if ($resultAllRoles->status == 200 && !empty($resultAllRoles->response)) {
            $dataAllRoles = $resultAllRoles->response;
        }


        /**
         * Get Admin Info By Id
         */
        $resultAdminById = Admin::getAdminById($dataCheckAdminLogin->admin_id);
        $dataAdminById = [];
        if ($resultAdminById->status == 200 && !empty($resultAdminById->response)) {
            $dataAdminById = $resultAdminById->response[0];
        }

        // Load Stylesheets & Icons
        enqueueStylesheet('dropzone', '/dist/libs/dropzone/min/dropzone.min.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('dropzone', '/dist/libs/dropzone/min/dropzone.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('myaccount', '/dist/js/admin/myaccount.init.js');

        getHeader($lang["my_account"], [
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
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["my_account"]; ?></h5>
                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="nicknameAdmin"
                                               value="<?= $dataAdminById->admin_nickname; ?>"
                                               placeholder="<?= $lang["nickname"]; ?>">
                                        <label for="nicknameAdmin"><?= $lang["nickname"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span id="length_nicknameAdmin">0</span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-lg-12">
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
                                            <?= $lang['length_text']; ?> : <span id="length_passwordAdmin">0</span>
                                        </small>
                                    </div>
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
                                <button id="btnActive"
                                        type="button"
                                        class="setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["submit"]; ?>
                                </button>
                                <a href="/admin"
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
                                        <th scope="row"><?= $lang["name"]; ?> :</th>
                                        <td><?= Security::decrypt($dataAdminById->admin_name); ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?= $lang["phone_number"]; ?> :</th>
                                        <td><?= Security::decrypt($dataAdminById->admin_mobile); ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?= $lang["date_register"]; ?> :</th>
                                        <td>
                                            <bdi><?= Utils::getTimeCountry($Settings['date_format'], $dataAdminById->admin_register_date); ?></bdi>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?= $lang["status"]; ?> :</th>
                                        <td><?= $lang[$dataAdminById->admin_status]; ?></td>
                                    </tr>
                                    </tbody>
                                </table>
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
                        'successful_submit_mag' => $lang['successful_submit_mag'],
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
                        'dictMaxFilesExceeded' => $lang['dictMaxFilesExceeded'],
                        'mobile_invalid' => $lang['mobile_invalid'],
                        'delete' => $lang['delete'],
                        'cancel_upload' => $lang['cancel_upload'],
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
?>