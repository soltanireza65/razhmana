<?php
$pageSlug = "ngroup";
// permission_can_edit

use MJ\Security\Security;
use MJ\Utils\Utils;

global $lang,$antiXSS,$Settings;

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
                if ($item000->slug_name == $pageSlug && ($item000->permission_can_edit == "yes")) {
                    $flagSlug = true;
                }
            }
        }
// end roles 1

        $id = (int)$antiXSS->xss_clean($_REQUEST['id']);

        /**
         * Get Newsletter By ID
         */
        $resultGroupNotificationById = GNotification::getGroupNotificationById($id);
        $dataGroupNotificationById = [];
        if ($resultGroupNotificationById->status == 200 && !empty($resultGroupNotificationById->response)) {
            $dataGroupNotificationById = $resultGroupNotificationById->response[0];
        }
        if (empty($dataGroupNotificationById)) {
            header('Location: /admin/ngroup');
        }


        /**
         * Get All Admins
         */
        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
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
        enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('TinyMCE', '/dist/libs/TinyMCE/js/TinyMCE.js');
        enqueueScript('ngroup-edit', '/dist/js/admin/ngroup/ngroup-edit.init.js');

        getHeader($lang["notification_edit"], [
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
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["notification_edit"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="noticTitle"
                                               value="<?= $dataGroupNotificationById->ngroup_title; ?>"
                                               placeholder="<?= $lang["title"]; ?>">
                                        <label for="noticTitle"><?= $lang["title"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> :
                                            <span id="length_noticTitle" class="text-danger">0</span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="noticSender"
                                               value="<?= $dataGroupNotificationById->ngroup_sender; ?>"
                                               placeholder="<?= $lang["sender"]; ?>">
                                        <label for="noticSender"><?= $lang["sender"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> :
                                            <span id="length_noticSender" class="text-danger">0</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <select class="form-control"  id="notic-type" data-toggle="select2"
                                                data-width="100%">
                                            <option value="-1"></option>
                                            <option value="discount"
                                            ><?= $lang['discount'] ?>
                                            </option>
                                            <option value="notices">
                                                <?= $lang['notices'] ?>
                                            </option>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-12 mb-3">
                                    <div id="ngroupBody" style="height: 400px;">
                                        <?php echo $dataGroupNotificationById->ngroup_message; ?>
                                    </div>
                                    <!-- end Snow-editor-->
                                </div>
                                <!-- end col -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                            <div class="text-center progress-demo">
                                <button id="btnPublish"
                                        type="button"
                                        data-ngroup-id="<?= $id; ?>"
                                        class="<?= ($dataGroupNotificationById->ngroup_status == "active") ? "active" : ""; ?>
                                        setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light mt-1"
                                        data-style="zoom-in">
                                    <?= $lang["published_submit"]; ?>
                                </button>
                                <button id="btnDraft"
                                        type="button"
                                        data-ngroup-id="<?= $id; ?>"
                                        class="<?= ($dataGroupNotificationById->ngroup_status == "inactive") ? "active" : ""; ?>
                                        setSubmitBtn btn w-sm btn-soft-warning waves-effect shadow-none waves-light mt-1"
                                        data-style="zoom-in">
                                    <?= $lang["draft_submit"]; ?>
                                </button>
                                <button id="btnDelete"
                                        type="button"
                                        data-ngroup-id="<?= $id; ?>"
                                        class="btn w-sm btn-soft-danger waves-effect shadow-none waves-light mt-1"
                                        data-style="zoom-in">
                                    <?= $lang["delete"]; ?>
                                </button>
                                <a href="/admin/ngroup"
                                   class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["btn_back"]; ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["group_users"]; ?></h5>
                            <select class="form-control" id="relation" data-toggle="select2"
                                    data-width="100%">
                                <?php
                                $user_type = $dataGroupNotificationById->user_type;
                                $user_status = $dataGroupNotificationById->user_status;
                                ?>
                                <option value="guest"
                                    <?= ($user_type=="driver" && $user_status == "guest") ? "selected" : ""; ?>
                                        data-type="driver"><?= $lang['driver'] . " - " . $lang['guest']; ?>
                                </option>
                                <option value="active"
                                    <?= ($user_type=="driver" && $user_status == "active") ? "selected" : ""; ?>
                                        data-type="driver"><?= $lang['driver'] . " - " . $lang['active']; ?>
                                </option>
                                <option value="suspend"
                                    <?= ($user_type=="driver" && $user_status == "suspend") ? "selected" : ""; ?>
                                        data-type="driver"><?= $lang['driver'] . " - " . $lang['suspend']; ?>
                                </option>
                                <option value="guest"
                                    <?= ($user_type=="businessman" && $user_status == "guest") ? "selected" : ""; ?>
                                        data-type="businessman"><?= $lang['businessman'] . " - " . $lang['guest']; ?>
                                </option>
                                <option value="active"
                                    <?= ($user_type=="businessman" && $user_status == "active") ? "selected" : ""; ?>
                                        data-type="businessman"><?= $lang['businessman'] . " - " . $lang['active']; ?>
                                </option>
                                <option value="active"
                                    <?= ($user_type=="businessman" && $user_status == "suspend") ? "selected" : ""; ?>
                                        data-type="businessman"><?= $lang['businessman'] . " - " . $lang['suspend']; ?>
                                </option>
                            </select>
                        </div>
                    </div>



                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["language"]; ?></h5>
                            <select class="form-control" id="language" data-toggle="select2"
                                    data-width="100%">
                                <?php
                                if (!empty($dataLanguages)) {
                                    foreach ($dataLanguages as $dataLanguagesITEM) {
                                        ?>
                                        <option
                                            <?= ($dataLanguagesITEM->slug==$dataGroupNotificationById->ngroup_language) ? "selected" : ""; ?>
                                                value="<?= $dataLanguagesITEM->slug; ?>">
                                            <?= $lang[$dataLanguagesITEM->name]; ?>
                                        </option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
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
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["all_info"]; ?></h5>
                            <div class="table-responsive collapse" id="cardCollpase1" style="max-height:300px;overflow: auto;">
                                <table class="table mb-0 table-sm">
                                    <tbody>
                                    <tr>
                                        <td colspan="2"><?= $lang["status"]; ?> :</td>
                                        <td><?php
                                            if ($dataGroupNotificationById->ngroup_status == "active") {
                                                echo "<span class='badge badge-soft-success font-13'>" . $lang['active'] . "</span>";
                                            } elseif ($dataGroupNotificationById->ngroup_status == "inactive") {
                                                echo "<span class='badge badge-soft-warning font-13'>" . $lang['inactive'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-danger font-13'>" . $dataGroupNotificationById->ngroup_status . "</span>";
                                            }
                                            ?></td>

                                    </tr>

                                    <tr>
                                        <td colspan="2"><?= $lang["language"]; ?> :</td>
                                        <td>
                                            <?php
                                            if (!empty($dataLanguages)) {
                                                foreach ($dataLanguages as $dataLanguagesITEM) {
                                                    if ($dataLanguagesITEM->slug==$dataGroupNotificationById->ngroup_language) {
                                                        echo $lang[$dataLanguagesITEM->name];
                                                    }

                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>

                                    <?php
                                    if (!empty($dataGroupNotificationById->ngroup_options)) {
                                        $temp = json_decode($dataGroupNotificationById->ngroup_options);
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
                                            <td><?= $lang['creator']; ?></td>
                                            <td><?= (!empty($name)) ? $name : $temp->admin; ?></td>
                                            <td><bdi><?= Utils::getTimeCountry($Settings['date_format'], $temp->date_create); ?></bdi></td>
                                        </tr>
                                        <?php
                                        if (!empty($temp->update)) {
                                            foreach ($temp->update as $loop) {
                                                ?>
                                                <tr>
                                                    <td><?= $lang['editor']; ?></td>
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
                                                    <td><bdi><?= Utils::getTimeCountry($Settings['date_format'], $loop->date); ?></bdi></td>
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
            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF2() ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'error' => $lang['error'],
                        'successful' => $lang['successful'],
                        'warning' => $lang['warning'],
                        'successful_submit_mag' => $lang['successful_submit_mag'],
                        'successful_delete_mag' => $lang['successful_delete_mag'],
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
                        'token_error' => $lang['token_error'],
                        'notices_type_not_empty' => $lang['notices_type_not_empty'],
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
                $lang['help_ngroup_1'],
                $lang['help_ngroup_2'],
                $lang['help_ngroup_3'],
                $lang['help_img_1'],
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