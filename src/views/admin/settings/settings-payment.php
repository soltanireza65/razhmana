<?php
$pageSlug = "settings_payment";
// permission_can_edit

use MJ\Utils\Utils;
use MJ\Security\Security;

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


        $jibit_merchantid = "";
        $jibit_status = false;

        $jibit_Balance = 0;
        $jibit_BalanceR = InquiryJibit::getBalance();

        if (@json_decode($jibit_BalanceR) && isset(json_decode($jibit_BalanceR)->balances[0])) {

            $temp = json_decode($jibit_BalanceR, true)['balances'];
            if (!empty($temp)) {
                foreach ($temp as $tempLoop) {
                    if ($tempLoop['currency'] == "IRR") {
                        $jibit_Balance = $tempLoop['amount'];
                    }
                }
            }
        }

        if (!empty($dataSettings)) {
            foreach ($dataSettings as $index => $loop) {

                if ($index == "jibit_merchantid") {
                    $jibit_merchantid = $loop;
                }
                if ($index == "jibit_status") {
                    $jibit_status = $loop;
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
        enqueueScript('settings-payment', '/dist/js/admin/settings/settings-payment.init.js');

        getHeader($lang['settings_payment'], [
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

                            <h5 class="text-uppercase mt-0 mb-3  bg-light p-2"><?= $lang['settings_payment']; ?></h5>
                            <p class="sub-header"><?= $lang['settings_payment_desc']; ?></p>

                            <div class="nav nav-pills flex-column navtab-bg nav-pills-tab text-center"
                                 id="tabs-tab" role="tablist" aria-orientation="vertical">

                                <a class="nav-link mt-2 py-2 active" id="custom-jibit-tab"
                                   data-bs-toggle="pill" href="#custom-jibit" role="tab"
                                   aria-controls="custom-jibit"
                                   aria-selected="true">
                                    <?= $lang['settings_jibit']; ?>
                                </a>
                            </div>

                            <div class="border mt-4 rounded">
                                <h5 class="text-uppercase text-center mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                                <div class="text-center progress-demo mb-3">
                                    <button id="btnSubmit"
                                            type="button"
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
                <!-- end col-->


                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content p-3">


                                <div class="tab-pane active" id="custom-jibit" role="tabpanel"
                                     aria-labelledby="custom-jibit-tab">

                                    <h5 class="text-uppercase mt-0 mb-3  bg-light p-2"><?= $lang['settings_jibit_panel']; ?></h5>

                                    <div class="row mb-4 align-items-center">
                                        <label for="a_payment_balance"
                                               class="col-sm-3 col-form-label"><?= $lang['a_payment_balance']; ?></label>
                                        <div class="col-sm-9">
                                            <?= number_format($jibit_Balance) . " " . $lang['reyal']; ?>
                                        </div>
                                    </div>

                                    <div class="row mb-4 align-items-center">
                                        <label for="jibit_status"
                                               class="col-sm-3 col-form-label"><?= $lang['status_show_active_show_user']; ?></label>
                                        <div class="col-sm-9">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input"
                                                    <?= ($jibit_status) ? "checked" : ""; ?>
                                                       id="jibit_status">

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <label for="jibit_merchantid"
                                               class="col-sm-3 col-form-label"><?= $lang['merchantid']; ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="jibit_merchantid"
                                                   placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                                                   value="<?= $jibit_merchantid ?>">
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <label for="horizontal-password-input"
                                               class="col-sm-3 col-form-label mb-3">
                                            <?= $lang['icon']; ?>
                                        </label>
                                        <div class="col-sm-9">
                                            <form action="/" method="post" class="dropzone" id="jibitIcon"
                                                  data-plugin="dropzone">
                                                <div class="fallback">
                                                    <input name="file" type="file">
                                                </div>
                                                <div class="dz-message needsclick">
                                                    <img class="img-fluid rounded"
                                                         src="<?= PAYMENT_ADDRESS; ?>/jibit.webp">
                                                </div>
                                            </form>
                                            <div class="mt-1">
                                                <small><?= $lang['format_support']; ?> :
                                                    <bdi>.image/*</bdi>
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>
                </div>
                <!-- end col-->
            </div> <!-- end row-->

            <!-- end row -->

            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF2() ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'successful' => $lang['successful'],
                        'error' => $lang['error'],
                        'error_mag' => $lang['error_mag'],
                        'delete' => $lang['delete'],
                        'dictMaxFilesExceeded' => $lang['dictMaxFilesExceeded'],
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

        getFooter(
            [
                $lang['help_setting_payment_alert_merchantid']
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
