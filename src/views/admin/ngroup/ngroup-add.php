<?php
$pageSlug = "ngroup";
// permission_can_insert

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_insert == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1

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
        enqueueScript('ngroup-add', '/dist/js/admin/ngroup/ngroup-add.init.js');

        getHeader($lang["notification_add"], [
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
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["notification_add"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="noticTitle"
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
                                <button id="btnPublish" type="button"
                                        class="setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["published_submit"]; ?>
                                </button>
                                <button id="btnDraft" type="button"
                                        class="setSubmitBtn btn w-sm btn-soft-warning waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["draft_submit"]; ?>
                                </button>
                                <a href="/admin/ngroup"
                                   class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light">
                                    <?= $lang["btn_back"]; ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["group_users"]; ?></h5>
                            <select class="form-control" multiple id="relation" data-toggle="select2"
                                    data-width="100%">
                                <option value="guest"
                                        data-type="driver"><?= $lang['driver'] . " - " . $lang['guest']; ?>
                                </option>
                                <option value="active"
                                        data-type="driver"><?= $lang['driver'] . " - " . $lang['active']; ?>
                                </option>
                                <option value="suspend"
                                        data-type="driver"><?= $lang['driver'] . " - " . $lang['suspend']; ?>
                                </option>
                                <option value="guest"
                                        data-type="businessman"><?= $lang['businessman'] . " - " . $lang['guest']; ?>
                                </option>
                                <option value="active"
                                        data-type="businessman"><?= $lang['businessman'] . " - " . $lang['active']; ?>
                                </option>
                                <option value="suspend"
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
                                        <option value="<?= $dataLanguagesITEM->slug; ?>">
                                            <?= $lang[$dataLanguagesITEM->name]; ?>
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
            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('admin-ngroup-add') ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'error' => $lang['error'],
                        'successful' => $lang['successful'],
                        'warning' => $lang['warning'],
                        'successful_submit_mag' => $lang['successful_submit_mag'],
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
                        'group_users_empty_error' => $lang['group_users_empty_error'],
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
