<?php
$pageSlug = "settings_sms";
// permission_can_edit

use MJ\Utils\Utils;
use MJ\Security\Security;
use MJ\SMS\SMS;

global $lang;

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
                if ($item000->slug_name == $pageSlug && ($item000->permission_can_edit == "yes" || $item000->permission_can_show == "yes" || $item000->permission_can_delete == "yes" || $item000->permission_can_insert == "yes")) {
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

        $sms_panel = "ghasedak";

        $ghasedak_api = "";
        $ghasedak_sender_number = "";
        $ghasedak_price_low = "";
        $ghasedak_admins_mobile = "";
        $ghasedak_template_low_price = "";

        if (!empty($dataSettings)) {
            foreach ($dataSettings as $index => $loop) {

                if ($index == "sms_panel") {
                    $sms_panel = $loop;
                }
                if ($index == "ghasedak_api") {
                    $ghasedak_api = $loop;
                }
                if ($index == "ghasedak_sender_number") {
                    $ghasedak_sender_number = $loop;
                }
                if ($index == "ghasedak_price_low") {
                    $ghasedak_price_low = $loop;
                }
                if ($index == "ghasedak_admins_mobile") {
                    $ghasedak_admins_mobile = $loop;
                }
                if ($index == "ghasedak_template_low_price") {
                    $ghasedak_template_low_price = $loop;
                }
            }

        }


        $amount_ghasedak = 0;
        $result_ghasedak = SMS::getCredit();
        if ($result_ghasedak->status == 200) {
            $amount_ghasedak = $result_ghasedak->response;
        }

        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('settings-sms', '/dist/js/admin/settings/settings-sms.init.js');

        getHeader($lang['settings_sms_panel'], [
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

                <div class="col-lg-12 col-xl-12">
                    <div class="row">
                        <div class="col-sm-12 col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title"><?= $lang['settings_sms_panel']; ?></h4>
                                    <p class="sub-header"><?= $lang['settings_sms_panel_desc']; ?></p>


                                    <ul class="nav nav-pills nav-fill navtab-bg flex-column">
                                        <li class="nav-item">
                                            <a href="#ghasedakTab" data-bs-toggle="tab" aria-expanded="true"
                                               class="nav-link active">
                                                <?= $lang['settings_ghasedak']; ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <div class="card">
                                <div class="card-body">

                                    <h5 class="text-uppercase text-center mt-0 mb-3 bg-light p-2"><?= $lang["select_sms_panel"]; ?></h5>
                                    <div class="px-3 progress-demo">
                                        <div class="float-end">
                                            <a target="_self" href="https://ghasedaksms.com/">
                                                <img src="/uploads/sms/ghasedak.png" width="25px">
                                            </a>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" id="ghasedak" name="sms_panel"
                                                <?= ($sms_panel == "ghasedak") ? ' checked ' : ''; ?>
                                                   class="form-check-input">
                                            <label class="form-check-label font-16 fw-bold"
                                                   for="sms_ghasedak"><?= $lang["ghasedak"]; ?></label>
                                            <p class="mb-2"><span class="fw-medium me-2"><?= $lang["amount"]; ?>:</span>
                                                <?= number_format($amount_ghasedak) . " " . $lang["rle"]; ?>
                                            </p>
                                        </div>

                                    </div>


                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-4">
                            <div class="card">
                                <div class="card-body">

                                    <h5 class="text-uppercase text-center mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                                    <div class="text-center progress-demo mb-3">
                                        <button id="btnActive" type="button"
                                                class="btn w-sm btn-soft-primary waves-effect shadow-none waves-light"
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
                        </div>
                    </div>


                    <div class="card">
                        <div class="card-body">

                            <div class="tab-content">

                                <div class="tab-pane show active" id="ghasedakTab">
                                    <div class="row">

                                        <h5 class="text-uppercase mt-0 mb-3  bg-light p-2"><?= $lang['settings_ghasedak']; ?></h5>

                                        <div class="col-lg-6">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="ghasedak_api"
                                                       value="<?= $ghasedak_api; ?>"
                                                       placeholder="<?= $lang['apikey']; ?>:">
                                                <label for="ghasedak_api"><?= $lang['apikey']; ?>:</label>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="ghasedak_sender_number"
                                                       value="<?= $ghasedak_sender_number; ?>"
                                                       placeholder="<?= $lang['number_sender']; ?>:">
                                                <label for="ghasedak_sender_number">
                                                    <?= $lang['number_sender']; ?>:
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="ghasedak_template_low_price"
                                                       value="<?= $ghasedak_template_low_price; ?>"
                                                       placeholder="<?= $lang['a_sms_template']; ?>:">
                                                <label for="ghasedak_template_low_price"><?= $lang['a_sms_template']; ?>:</label>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="form-floating mb-3">
                                                <input type="number" class="form-control" id="ghasedak_price_low"
                                                       value="<?= $ghasedak_price_low; ?>"
                                                       placeholder="<?= $lang['a_sms_price_low']; ?>:">
                                                <label for="ghasedak_price_low"><?= $lang['a_sms_price_low']; ?>:</label>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="ghasedak_admins_mobile"
                                                       value="<?= $ghasedak_admins_mobile; ?>"
                                                       placeholder="<?= $lang['a_sms_price_low_admins_number']; ?>:">
                                                <label for="ghasedak_admins_mobile"><?= $lang['a_sms_price_low_admins_number']; ?>:</label>
                                            </div>
                                        </div>

                                    </div>

                                </div> <!-- end tab-pane -->
                                <!-- end about me section content -->

                            </div> <!-- end tab-content -->
                        </div>
                    </div> <!-- end card-->

                </div> <!-- end col -->
            </div>
            <!-- end row-->

            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF2() ?>">
            <!-- end row -->
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'successful' => $lang['successful'],
                        'error' => $lang['error'],
                        'error_mag' => $lang['error_mag'],
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
                $lang['help_setting_sms_alert_amount'],
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