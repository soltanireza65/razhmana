<?php
$pageSlug = "a_experts";

// permission_can_edit

use MJ\Security\Security;
use MJ\Utils\Utils;

global $lang, $antiXSS, $Settings;

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

        $result = Expert::getExpertById($id);
        $data = [];
        if ($result->status == 200 && !empty($result->response)) {
            $data = $result->response[0];
        }
        if (empty($data)) {
            header('Location: /admin/currency');
        }


        /**
         * Get All Admins
         */
        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
        }

        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('expert-edit', '/dist/js/admin/expert/expert-edit.init.js');

        getHeader($lang["a_experts_edit"], [
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
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase1" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase1"><i class="mdi mdi-minus"></i></a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_experts_edit"]; ?></h5>

                            <div class="row show" id="cardCollpase1">

                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"
                                               class="form-control"
                                               id="firstname"
                                               value="<?= $data->expert_firstname; ?>"
                                               placeholder="<?= $lang["a_name"]; ?>">
                                        <label for="firstname"><?= $lang["a_name"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                    class="text-danger"
                                                    id="length_firstname">0</span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"
                                               class="form-control"
                                               id="lastname"
                                               value="<?= $data->expert_lastname; ?>"
                                               placeholder="<?= $lang["a_lastname"]; ?>">
                                        <label for="lastname"><?= $lang["a_lastname"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                    class="text-danger"
                                                    id="length_lastname">0</span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"
                                               class="form-control"
                                               id="mobile"
                                               minlength="11"
                                               value="<?= $data->expert_mobile; ?>"
                                               dir="ltr"
                                               placeholder="+989143302964">
                                        <label for="mobile"><?= $lang["phone_number"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['validation']; ?> : <span
                                                    class="text-danger"
                                                    id="length_mobile">0</span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-3">
                                    <div class="form-floating">
                                        <textarea class="form-control" placeholder="<?= $lang["address"]; ?>"
                                                  id="address"
                                                  style="height: 100px"><?= $data->expert_address; ?></textarea>
                                        <label for="address"><?= $lang["address"]; ?></label>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-3">
                                    <div class="form-floating">
                                        <textarea class="form-control" placeholder="<?= $lang["description"]; ?>"
                                                  id="description"
                                                  style="height: 100px"><?= $data->expert_desc; ?></textarea>
                                        <label for="description"><?= $lang["description"]; ?></label>
                                    </div>
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
                                        data-tj-id="<?= $id; ?>"
                                        class="setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["active_submit"]; ?>
                                </button>
                                <button id="btnInactive"
                                        type="button"
                                        data-tj-id="<?= $id; ?>"
                                        class="setSubmitBtn btn w-sm btn-soft-warning waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["inactive_submit"]; ?>
                                </button>
                                <a href="/admin/expert"
                                   class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light">
                                    <?= $lang["btn_back"]; ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg collapsed" data-bs-toggle="collapse" href="#cardCollpase2"
                                   role="button"
                                   aria-expanded="true" aria-controls="cardCollpase2">
                                    <i class="mdi mdi-minus"></i>
                                </a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["all_info"]; ?></h5>
                            <div class="table-responsive collapse" style="max-height: 176px;overflow: auto;"
                                 id="cardCollpase2">
                                <table class="table mb-0 table-sm">
                                    <tbody>
                                    <tr>
                                        <td colspan="2"><?= $lang["status"]; ?> :</td>
                                        <td><?php
                                            if ($data->expert_status == "active") {
                                                echo "<span class='badge badge-soft-success font-13'>" . $lang['active'] . "</span>";
                                            } elseif ($data->expert_status == "inactive") {
                                                echo "<span class='badge badge-soft-warning font-13'>" . $lang['inactive'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-danger font-13'>" . $data->expert_status . "</span>";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    if (!empty($data->expert_options)) {
                                        $temp = json_decode($data->expert_options);
                                        $name = "";
                                        if (!empty($dataAllAdmins)) {
                                            foreach ($dataAllAdmins as $dataAllAdminsLOOP) {
                                                if ($dataAllAdminsLOOP->admin_id == $temp->admin) {
                                                    $name = $dataAllAdminsLOOP->admin_nickname;
                                                    break;
                                                }
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $lang['d_create']; ?></td>
                                            <td><?= (!empty($name)) ? $name : $temp->admin; ?></td>
                                            <td>
                                                <bdi><?= Utils::getTimeCountry($Settings['date_format'], $temp->date_create); ?></bdi>
                                            </td>
                                        </tr>
                                        <?php
                                        if (!empty($temp->update)) {
                                            foreach ($temp->update as $loop) {
                                                ?>
                                                <tr>
                                                    <td><?= $lang['d_update']; ?></td>
                                                    <td>
                                                        <?php
                                                        if (!empty($dataAllAdmins)) {
                                                            foreach ($dataAllAdmins as $dataAllAdminsLOOP) {
                                                                if ($dataAllAdminsLOOP->admin_id == $loop->create) {
                                                                    $name = $dataAllAdminsLOOP->admin_nickname;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        echo (!empty($name)) ? $name : $loop->create;
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <bdi><?= Utils::getTimeCountry($Settings['date_format'], $loop->date); ?></bdi>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('admin-expert-edit') ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'error' => $lang['error'],
                        'successful' => $lang['successful'],
                        'successful_submit_mag' => $lang['successful_submit_mag'],
                        'successful_update_mag' => $lang['successful_update_mag'],
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