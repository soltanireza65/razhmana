<?php
$pageSlug = "settings_poster";
// permission_can_edit

global $lang;

use MJ\Utils\Utils;
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

        $resultAllCurrencies = Currency::getAllCurrencies();
        $dataAllCurrencies = [];
        if ($resultAllCurrencies->status == 200 && !empty($resultAllCurrencies->response)) {
            $dataAllCurrencies = $resultAllCurrencies->response;
        }


        /**
         * Get All Settings
         */
        $resultSettings = Utils::getFileValue("settings.txt");
        $dataSettings = [];
        if (!empty($resultSettings)) {
            $dataSettings = json_decode($resultSettings, true);
        }

        $poster_expire_time = 0;
        $poster_immediate_time = 0;

        $poster_immediate_price_toman = 0;
        $poster_immediate_price_dollar = 0;
        $poster_immediate_price_euro = 0;

        $poster_ladder_price_toman = 0;
        $poster_ladder_price_dollar = 0;
        $poster_ladder_price_euro = 0;

        $poster_expert_time = 0;
        $poster_expert_price_toman = 0;
        $poster_expert_price_dollar = 0;
        $poster_expert_price_euro = 0;

        if (!empty($dataSettings)) {
            foreach ($dataSettings as $index => $loop) {

                if ($index == "poster_expire_time") {
                    $poster_expire_time = $loop;
                }
                if ($index == "poster_immediate_time") {
                    $poster_immediate_time = $loop;
                }

                if ($index == "poster_immediate_price_toman") {
                    $poster_immediate_price_toman = $loop;
                }
                if ($index == "poster_immediate_price_dollar") {
                    $poster_immediate_price_dollar = $loop;
                }
                if ($index == "poster_immediate_price_euro") {
                    $poster_immediate_price_euro = $loop;
                }


                if ($index == "poster_ladder_price_toman") {
                    $poster_ladder_price_toman = $loop;
                }
                if ($index == "poster_ladder_price_dollar") {
                    $poster_ladder_price_dollar = $loop;
                }
                if ($index == "poster_ladder_price_euro") {
                    $poster_ladder_price_euro = $loop;
                }

                if ($index == "poster_expert_time") {
                    $poster_expert_time = $loop;
                }
                if ($index == "poster_expert_price_toman") {
                    $poster_expert_price_toman = $loop;
                }
                if ($index == "poster_expert_price_dollar") {
                    $poster_expert_price_dollar = $loop;
                }
                if ($index == "poster_expert_price_euro") {
                    $poster_expert_price_euro = $loop;
                }
            }
        }


        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');
        enqueueStylesheet('dropzone', '/dist/libs/dropzone/min/dropzone.min.css');

        // Load Script In Footer
        enqueueScript('dropzone', '/dist/libs/dropzone/min/dropzone.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('settings-poster', '/dist/js/admin/settings/settings-poster.init.js');

        getHeader($lang['settings_poster'], [
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
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title"><?= $lang['settings_poster']; ?></h4>
                            <div class="nav nav-pills flex-column navtab-bg nav-pills-tab text-center" id="v-pills-tab"
                                 role="tablist" aria-orientation="vertical">

                                <a class="nav-link active show mt-2 py-2" id="custom-v-all-tab"
                                   data-bs-toggle="pill"
                                   href="#custom-v-all" role="tab" aria-controls="custom-v-all"
                                   aria-selected="true">
                                    <?= $lang['a_settings_overall']; ?>
                                </a>
                                <a class="nav-link mt-2 py-2" id="custom-v-price_time-tab" data-bs-toggle="pill"
                                   href="#custom-v-price_time" role="tab" aria-controls="custom-v-price_time"
                                   aria-selected="false">
                                    <?= $lang['a_settings_price_time']; ?>
                                </a>

                            </div>
                        </div>
                    </div> <!-- end col-->
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content p-3">


                                <!-- start settings all-->
                                <div class="tab-pane fade active show" id="custom-v-all" role="tabpanel"
                                     aria-labelledby="custom-v-all-tab">
                                    <div>
                                        <h5 class="text-uppercase mt-0 bg-light p-2"><?= $lang['a_settings_overall']; ?></h5>

                                        <div class="row">

                                            <div class="col-lg-12">
                                                <div class="row mb-4">
                                                    <label for="horizontal-password-input"
                                                           class="col-sm-3 col-form-label mb-3">
                                                        <?= $lang['a_poster_logo_default']; ?>
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <form action="/" method="post" class="dropzone"
                                                              id="logoLightUser"
                                                              data-plugin="dropzone">
                                                            <div class="fallback">
                                                                <input name="file" type="file" accept="image/*">
                                                            </div>
                                                            <div class="dz-message needsclick">
                                                                <img class="img-fluid rounded"
                                                                     src="/uploads/site/poster-default.svg">
                                                            </div>
                                                        </form>
                                                        <div class="mt-1">
                                                            <small><?= $lang['format_support']; ?> :
                                                                <bdi>svg</bdi>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-center my-3">
                                                <button type="submit" class="btn btn-primary btn-block"
                                                        data-style="zoom-in"
                                                        id="submit_poster_all"><?= $lang['submit_change']; ?></button>
                                            </div>
                                        </div>
                                        <!-- end row-->

                                    </div>
                                </div>
                                <!-- end settings all-->

                                <!-- start settings all-->
                                <div class="tab-pane fade" id="custom-v-price_time" role="tabpanel"
                                     aria-labelledby="custom-v-price_time-tab">
                                    <div>
                                        <h5 class="text-uppercase mt-0 bg-light p-2"><?= $lang['a_settings_overall']; ?></h5>

                                        <div class="row mt-3 mb-4">
                                            <label for="poster_expire_time"
                                                   class="col-sm-3 col-form-label"><?= $lang['a_settings_poster_expire']; ?></label>
                                            <div class="col-sm-9">
                                                <input type="number"
                                                       class="form-control"
                                                       id="poster_expire_time"
                                                       placeholder="1"
                                                       value="<?= $poster_expire_time; ?>">
                                                <small>
                                                    <?= $lang['a_alert_settings_expire_day']; ?>
                                                </small>
                                            </div>
                                        </div>

                                        <div class="row mt-3 mb-4">
                                            <label for="poster_immediate_time"
                                                   class="col-sm-3 col-form-label"><?= $lang['a_settings_poster_immediate']; ?></label>
                                            <div class="col-sm-9">
                                                <input type="number"
                                                       class="form-control"
                                                       id="poster_immediate_time"
                                                       placeholder="1"
                                                       value="<?= $poster_immediate_time; ?>">
                                                <small>
                                                    <?= $lang['a_alert_settings_expire_day']; ?>
                                                </small>
                                            </div>
                                        </div>


                                        <div class="row mt-3 mb-4">
                                            <label for="poster_immediate_price_toman"
                                                   class="col-sm-3 col-form-label"><?= $lang['a_price_upgrade_immediate']; ?></label>
                                            <div class="col-sm-9">
                                                <br>
                                                <div class="input-group">
                                                    <input type="number"
                                                           class="form-control"
                                                           id="poster_immediate_price_toman"
                                                           placeholder="1"
                                                           value="<?= $poster_immediate_price_toman; ?>">
                                                    <button class="btn input-group-text btn-dark waves-effect waves-light"
                                                            type="button">
                                                        <?= $lang['a_toman'] ?>
                                                    </button>
                                                </div>
                                                <br>
                                                <div class="input-group">
                                                    <input type="number"
                                                           class="form-control"
                                                           id="poster_immediate_price_dollar"
                                                           placeholder="1"
                                                           value="<?= $poster_immediate_price_dollar; ?>">
                                                    <button class="btn input-group-text btn-dark waves-effect waves-light"
                                                            type="button">
                                                        <?= $lang['a_dollar'] ?>
                                                    </button>
                                                </div>
                                                <br>
                                                <div class="input-group">
                                                    <input type="number"
                                                           class="form-control"
                                                           id="poster_immediate_price_euro"
                                                           placeholder="1"
                                                           value="<?= $poster_immediate_price_euro; ?>">
                                                    <button class="btn input-group-text btn-dark waves-effect waves-light"
                                                            type="button">
                                                        <?= $lang['a_euro'] ?>
                                                    </button>
                                                </div>
                                            </div>

                                        </div>


                                        <div class="row mt-3 mb-4">
                                            <label for="poster_ladder_price_toman"
                                                   class="col-sm-3 col-form-label"><?= $lang['a_price_upgrade_ladder']; ?></label>
                                            <div class="col-sm-9">
                                                <br>
                                                <div class="input-group">
                                                    <input type="number"
                                                           class="form-control"
                                                           id="poster_ladder_price_toman"
                                                           placeholder="1"
                                                           value="<?= $poster_ladder_price_toman; ?>">
                                                    <button class="btn input-group-text btn-dark waves-effect waves-light"
                                                            type="button">
                                                        <?= $lang['a_toman'] ?>
                                                    </button>
                                                </div>
                                                <br>
                                                <div class="input-group">
                                                    <input type="number"
                                                           class="form-control"
                                                           id="poster_ladder_price_dollar"
                                                           placeholder="1"
                                                           value="<?= $poster_ladder_price_dollar; ?>">
                                                    <button class="btn input-group-text btn-dark waves-effect waves-light"
                                                            type="button">
                                                        <?= $lang['a_dollar'] ?>
                                                    </button>
                                                </div>
                                                <br>
                                                <div class="input-group">
                                                    <input type="number"
                                                           class="form-control"
                                                           id="poster_ladder_price_euro"
                                                           placeholder="1"
                                                           value="<?= $poster_ladder_price_euro; ?>">
                                                    <button class="btn input-group-text btn-dark waves-effect waves-light"
                                                            type="button">
                                                        <?= $lang['a_euro'] ?>
                                                    </button>
                                                </div>
                                            </div>

                                        </div>


                                        <div class="row mt-3 mb-4">
                                            <label for="poster_expert_time"
                                                   class="col-sm-3 col-form-label"><?= $lang['a_expert_officer_time']; ?></label>
                                            <div class="col-sm-9">
                                                <input type="number"
                                                       class="form-control"
                                                       id="poster_expert_time"
                                                       placeholder="1"
                                                       value="<?= $poster_expert_time; ?>">
                                                <small>
                                                    <?= $lang['a_alert_settings_expire_day']; ?>
                                                </small>
                                            </div>
                                        </div>


                                        <div class="row mt-3 mb-4">
                                            <label for="poster_expert_price_toman"
                                                   class="col-sm-3 col-form-label"><?= $lang['a_expert_officer_price']; ?></label>
                                            <div class="col-sm-9">
                                                <br>
                                                <div class="input-group">
                                                    <input type="number"
                                                           class="form-control"
                                                           id="poster_expert_price_toman"
                                                           placeholder="1"
                                                           value="<?= $poster_expert_price_toman; ?>">
                                                    <button class="btn input-group-text btn-dark waves-effect waves-light"
                                                            type="button">
                                                        <?= $lang['a_toman'] ?>
                                                    </button>
                                                </div>
                                                <br>
                                                <div class="input-group">
                                                    <input type="number"
                                                           class="form-control"
                                                           id="poster_expert_price_dollar"
                                                           placeholder="1"
                                                           value="<?= $poster_expert_price_dollar; ?>">
                                                    <button class="btn input-group-text btn-dark waves-effect waves-light"
                                                            type="button">
                                                        <?= $lang['a_dollar'] ?>
                                                    </button>
                                                </div>
                                                <br>
                                                <div class="input-group">
                                                    <input type="number"
                                                           class="form-control"
                                                           id="poster_expert_price_euro"
                                                           placeholder="1"
                                                           value="<?= $poster_expert_price_euro; ?>">
                                                    <button class="btn input-group-text btn-dark waves-effect waves-light"
                                                            type="button">
                                                        <?= $lang['a_euro'] ?>
                                                    </button>
                                                </div>
                                            </div>

                                        </div>


                                        <div class="text-center my-3">
                                            <button type="submit" class="btn btn-primary btn-block"
                                                    data-style="zoom-in"
                                                    id="submit_price_time"><?= $lang['submit_change']; ?></button>
                                        </div>

                                        <!-- end row-->

                                    </div>
                                </div>
                                <!-- end settings all-->

                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('admin-settings-poster') ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'successful' => $lang['successful'],
                        'warning' => $lang['warning'],
                        'error_mag' => $lang['error_mag'],
                        'dictMaxFilesExceeded' => $lang['dictMaxFilesExceeded'],
                        'delete' => $lang['delete'],
                        'cancel_upload' => $lang['cancel_upload'],
                        'empty_input' => $lang['empty_input'],
                        'info' => $lang['info'],
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
