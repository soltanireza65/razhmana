<?php
$pageSlug = "admins";
// permission_can_edit

global $lang;


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
                if ($item000->slug_name == $pageSlug && ($item000->permission_can_edit == "yes")) {
                    $flagSlug = true;
                }
            }
        }
        // end roles 1


        /**
         * Get All Settings
         */
        $resultSettings = Utils::getFileValue("settings.txt");
        $dataSettings = [];
        if (!empty($resultSettings)) {
            $dataSettings = json_decode($resultSettings, true);
        }

        $set_admin_cargo_out = '';
        $set_admin_cargo_in = '';



        if (!empty($dataSettings)) {
            foreach ($dataSettings as $index => $loop) {

                if ($index == "set_admin_cargo_out") {
                    $set_admin_cargo_out = $loop;
                }

                if ($index == "set_admin_cargo_in") {
                    $set_admin_cargo_in = $loop;
                }

            }
        }


        $resultAllRoles = Admin::getAllRoles();
        $dataAllRoles = [];
        if ($resultAllRoles->status == 200 && !empty($resultAllRoles->response)) {
            $dataAllRoles = $resultAllRoles->response;
        }


        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');
        enqueueStylesheet('dropzone', '/dist/libs/dropzone/min/dropzone.min.css');
        enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');

        // Load Script In Footer
        enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('dropzone', '/dist/libs/dropzone/min/dropzone.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('settings-security', '/dist/js/admin/settings/settings-security.init.js');

        getHeader($lang['settings_security'], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => 'settings_security',
            'pageSlugValue' => 'permission_can_edit',
        ]);

        // start roles 2
        if ($flagSlug) {
            // end roles 2
            ?>
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang['settings_security']; ?></h5>

                            <p class="sub-header"><?= $lang['security_desc_settings']; ?></p>
                            <div class="nav nav-pills flex-column navtab-bg nav-pills-tab text-center" id="v-pills-tab"
                                 role="tablist" aria-orientation="vertical">
                                <a class="nav-link active show py-2"
                                   id="v-all-tab"
                                   data-bs-toggle="pill"
                                   href="#v-all"
                                   role="tab"
                                   aria-controls="v-all"
                                   aria-selected="true">
                                    <?= $lang['settings_general']; ?>
                                </a>

                                <a class="nav-link mt-2 py-2 d-none"
                                   id="custom-v-export-tab"
                                   data-bs-toggle="pill"
                                   href="#custom-v-export"
                                   role="tab"
                                   aria-controls="custom-v-export"
                                   aria-selected="true">
                                    <?= $lang['settings_export_db']; ?>
                                </a>
                                <a class="nav-link mt-2 py-2 d-none" id="custom-v-settings-tab" data-bs-toggle="pill"
                                   href="#custom-v-settings" role="tab" aria-controls="custom-v-settings"
                                   aria-selected="false">
                                    <?= $lang['settings_export_settings']; ?>
                                </a>
                            </div>
                        </div>
                    </div> <!-- end col-->
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content p-3 pt-0">


                                <!-- start settings export-->
                                <div class="tab-pane fade active show" id="v-all" role="tabpanel"
                                     aria-labelledby="custom-v-export-tab">

                                    <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang['settings_general']; ?></h5>

                                    <div class="row mt-3 mb-4">
                                        <label for="set-admin-cargo-out"
                                               class="col-sm-3 col-form-label"><?= $lang['a_set_admin_from_cargo_out']; ?></label>
                                        <div class="col-sm-9">
                                            <select class="form-select my-1 mb-3"
                                                    multiple="multiple"
                                                    id="set-admin-cargo-out"
                                                    data-width="100%"
                                                    data-placeholder="<?= $lang['a_set_admin_from_cargo_out'] ?>">
                                                <?php
                                                $adminOutCargo=explode(",",$set_admin_cargo_out);
                                                if (!empty($dataAllRoles)) {
                                                    foreach ($dataAllRoles as $loop) {
                                                                    ?>
                                                                    <option  <?=(in_array($loop->role_id, $adminOutCargo))? "selected": null  ;?> value="<?= $loop->role_id ?>"><?=  (!empty(array_column(json_decode($loop->role_name, true), 'value', 'slug')[$language])) ?
                                                                            array_column(json_decode($loop->role_name, true), 'value', 'slug')[$language] :  $loop->category_id;?></option>
                                                <?php

                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mt-3 mb-4">
                                        <label for="set-admin-cargo-in"
                                               class="col-sm-3 col-form-label"><?= $lang['a_set_admin_from_cargo_in']; ?></label>
                                        <div class="col-sm-9">
                                            <select class="form-select my-1 mb-3"
                                                    multiple="multiple"
                                                    id="set-admin-cargo-in"
                                                    data-width="100%"
                                                    data-placeholder="<?= $lang['a_set_admin_from_cargo_in'] ?>">
                                                <?php
                                                $adminInCargo=explode(",",$set_admin_cargo_in);
                                                if (!empty($dataAllRoles)) {
                                                    foreach ($dataAllRoles as $loop) {
                                                        ?>
                                                        <option  <?=(in_array($loop->role_id, $adminInCargo))? "selected": null  ;?> value="<?= $loop->role_id ?>"><?=  (!empty(array_column(json_decode($loop->role_name, true), 'value', 'slug')[$language])) ?
                                                                array_column(json_decode($loop->role_name, true), 'value', 'slug')[$language] :  $loop->category_id;?></option>
                                                        <?php

                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="text-center my-3">
                                        <a href=javascript:void(0);"
                                           class="btn btn-primary btn-block"
                                           data-style="zoom-in"
                                           id="submit_all_settings"><?= $lang['submit_change']; ?></a>
                                    </div>



                                </div>
                                <!-- end settings export-->


                                <!-- start settings export-->
                                <div class="tab-pane fade" id="custom-v-export" role="tabpanel"
                                     aria-labelledby="custom-v-export-tab">

                                    <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang['settings_export_db']; ?></h5>

                                    <div class="alert alert-info" role="alert">
                                        <i class="mdi mdi-alert-circle-outline me-2"></i>
                                        <?= $lang['export_desc']; ?>
                                    </div>

                                    <div class="text-center my-3">
                                        <a target="_self" href="/admin/settings/export"
                                           class="btn btn-primary btn-block"
                                           data-style="zoom-in"
                                           id="submit_export"><?= $lang['submit_export']; ?></a>
                                    </div>



                                </div>
                                <!-- end settings export-->

                                <!-- start settings export-->
                                <div class="tab-pane fade" id="custom-v-settings" role="tabpanel"
                                     aria-labelledby="custom-v-settings-tab">
                                    <div>
                                        <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang['export_settings']; ?></h5>

                                        <div class="alert alert-info" role="alert">
                                            <i class="mdi mdi-alert-circle-outline me-2"></i>
                                            <?= $lang['settings_export_desc']; ?>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>فایل</th>
                                                    <th>انتخاب</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>تنظیمات زبان ها</td>
                                                    <td><input data-tj-name="settings" class="form-check-input" type="checkbox"></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>ترجمه زبان ها</td>
                                                    <td><input data-tj-name="languages" class="form-check-input" type="checkbox"></td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td>فایل های تنظیم</td>
                                                    <td><input data-tj-name="db" class="form-check-input" type="checkbox"></td>

                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td>فایل های آپلود</td>
                                                    <td><input data-tj-name="uploads" class="form-check-input" type="checkbox"></td>
                                                </tr>
                                                <tr>
                                                    <td>5</td>
                                                    <td>کل فایل های</td>
                                                    <td><input  data-tj-name="all" class="form-check-input" type="checkbox"></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="text-center my-3">
                                            <button
                                                    class="btn btn-primary btn-block"
                                                    data-style="zoom-in" disabled
                                                    id="submit_export_settings"><?= $lang['submit_export']; ?></button>
                                        </div>

                                    </div>
                                </div>
                                <!-- end settings export-->

                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('admin-settings-security') ?>">

            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'error_export' => $lang['error_export'],
                        'error' => $lang['error'],
                        'error_mag' => $lang['error_mag'],
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'successful' => $lang['successful'],
                        'token_error' => $lang['token_error'],
                        'warning' => $lang['warning'],
                        'dictMaxFilesExceeded' => $lang['dictMaxFilesExceeded'],
                        'delete' => $lang['delete'],
                        'cancel_upload' => $lang['cancel_upload'],
                        'empty_input' => $lang['empty_input'],
                        'info' => $lang['info'],

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
