<?php
$pageSlug = "settings_site";
// permission_can_edit

global $lang;

use  MJ\Security\Security;

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
//                if ($item000->slug_name == $pageSlug && ($item000->permission_can_edit == "yes" || $item000->permission_can_show == "yes" || $item000->permission_can_delete == "yes" || $item000->permission_can_insert == "yes")) {
                if ($item000->slug_name == $pageSlug && ($item000->permission_can_edit == "yes")) {
                    $flagSlug = true;
                }
            }
        }
        // end roles 1


        $file_fa_IR = SITE_ROOT . "/settings/settings_fa_IR.php";
        $settings_fa_IR = file_get_contents($file_fa_IR);

        $file_en_US = SITE_ROOT . "/settings/settings_en_US.php";
        $settings_en_US = file_get_contents($file_en_US);

        $file_tr_Tr = SITE_ROOT . "/settings/settings_tr_Tr.php";
        $settings_tr_Tr = file_get_contents($file_tr_Tr);

        $file_ru_RU = SITE_ROOT . "/settings/settings_ru_RU.php";
        $settings_ru_RU = file_get_contents($file_ru_RU);


        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('settings-site', '/dist/js/admin/settings/settings-site.init.js');

        getHeader($lang['site_settings'], [
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
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">

                            <h4 class="header-title mb-3"><?= $lang['languages_settings']; ?></h4>

                            <form>
                                <div id="basicwizard">

                                    <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-4">
                                        <li class="nav-item">
                                            <a href="#basictab1" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2 active">
                                                <i class="mdi mdi-numeric-1-box-outline me-1"></i>
                                                <span class="d-none d-sm-inline"><?= $lang['Persian']; ?></span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#basictab2" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2">
                                                <i class="mdi mdi-numeric-2-box-outline me-1"></i>
                                                <span class="d-none d-sm-inline"><?= $lang['English']; ?></span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#basictab3" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2">
                                                <i class="mdi mdi-numeric-3-box-outline me-1"></i>
                                                <span class="d-none d-sm-inline"><?= $lang['Turkish']; ?></span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#basictab4" data-bs-toggle="tab" data-toggle="tab"
                                               class="nav-link rounded-0 pt-2 pb-2">
                                                <i class="mdi mdi-numeric-4-box-outline me-1"></i>
                                                <span class="d-none d-sm-inline"><?= $lang['Russia']; ?></span>
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content b-0 mb-0 pt-0">

                                        <!-- Start basictab1 fa_IR -->
                                        <div class="tab-pane active" id="basictab1">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="example-textarea"
                                                           class="form-label text-danger"><?= $lang['notic_desc_lang_setting']; ?></label>
                                                    <textarea class="form-control"
                                                              placeholder="<?= $lang['php_code']; ?>"
                                                              id="textarea_fa_IR"
                                                              style="height: 500px;direction: ltr"><?= $settings_fa_IR; ?></textarea>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <button data-style="zoom-in"
                                                            id="btn_fa_IR"
                                                            type="button"
                                                            class="btn btn-primary waves-effect waves-light">
                                                        <?= $lang['submit_change']; ?>
                                                    </button>
                                                </div>
                                                <!-- end col -->
                                            </div>
                                            <!-- end row -->
                                        </div>
                                        <!-- end basictab1 fa_IR -->

                                        <!-- Start basictab2 en_US -->
                                        <div class="tab-pane" id="basictab2">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="example-textarea"
                                                           class="form-label text-danger"><?= $lang['notic_desc_lang_setting']; ?></label>
                                                    <textarea class="form-control"
                                                              placeholder="<?= $lang['php_code']; ?>"
                                                              id="textarea_en_US"
                                                              style="height: 500px;direction: ltr"><?= $settings_en_US; ?></textarea>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <button data-style="zoom-in"
                                                            id="btn_en_US"
                                                            type="button"
                                                            class="btn btn-primary waves-effect waves-light">
                                                        <?= $lang['submit_change']; ?>
                                                    </button>
                                                </div>
                                                <!-- end col -->
                                            </div>
                                            <!-- end row -->
                                        </div>
                                        <!-- end basictab2 en_US -->


                                        <!-- Start basictab3 tr_Tr -->
                                        <div class="tab-pane" id="basictab3">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="example-textarea"
                                                           class="form-label text-danger"><?= $lang['notic_desc_lang_setting']; ?></label>
                                                    <textarea class="form-control"
                                                              placeholder="<?= $lang['php_code']; ?>"
                                                              id="textarea_tr_Tr"
                                                              style="height: 500px;direction: ltr"><?= $settings_tr_Tr; ?></textarea>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <button data-style="zoom-in"
                                                            id="btn_tr_Tr"
                                                            type="button"
                                                            class="btn btn-primary waves-effect waves-light">
                                                        <?= $lang['submit_change']; ?>
                                                    </button>
                                                </div>
                                                <!-- end col -->
                                            </div>
                                            <!-- end row -->
                                        </div>
                                        <!-- end basictab3 tr_Tr -->

                                        <!-- Start basictab4 ru_RU -->
                                        <div class="tab-pane" id="basictab4">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="example-textarea"
                                                           class="form-label text-danger"><?= $lang['notic_desc_lang_setting']; ?></label>
                                                    <textarea class="form-control"
                                                              placeholder="<?= $lang['php_code']; ?>"
                                                              id="textarea_ru_RU"
                                                              style="height: 500px;direction: ltr"><?= $settings_ru_RU; ?></textarea>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <button data-style="zoom-in"
                                                            id="btn_ru_RU"
                                                            type="button"
                                                            class="btn btn-primary waves-effect waves-light">
                                                        <?= $lang['submit_change']; ?>
                                                    </button>
                                                </div>
                                                <!-- end col -->
                                            </div>
                                            <!-- end row -->
                                        </div>
                                        <!-- end basictab4 ru_RU -->

                                    </div>
                                    <!-- tab-content -->
                                </div>
                                <!-- end #basicwizard-->
                            </form>

                        </div>
                        <!-- end card-body -->
                    </div>
                    <!-- end card-->
                </div>
            </div>
            <!-- end row -->
            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('admin-settings-site') ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'successful' => $lang['successful'],
                        'error_mag' => $lang['error_mag'],
                        'error' => $lang['error'],
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
                $lang['help_settings_lang_1'],
                $lang['help_settings_lang_2'],
                $lang['help_img_1']
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
