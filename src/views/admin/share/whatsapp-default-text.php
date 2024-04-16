<?php
$pageSlug = "a_share_whatsapp";
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
                if ($item000->slug_name == $pageSlug && ($item000->permission_can_edit == "yes" || $item000->permission_can_show == "yes" || $item000->permission_can_delete == "yes" || $item000->permission_can_insert == "yes")) {
                    $flagSlug = true;
                }
            }
        }
        // end roles 1


        /**
         * Get All Settings
         */
        $resultTextShareWhatsapp = Utils::getFileValue("settings.txt", "whatsapp_default_text");

        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('repeater', '/dist/libs/jquery.repeater/jquery.repeater.min.js');
        enqueueScript('whatsapp-default-text', '/dist/js/admin/share/whatsapp-default-text.init.js');

        getHeader($lang['a_whatsapp_default_text_2'], [
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

                            <h5 class="text-uppercase mt-0 mb-3  bg-light p-2"><?= $lang['action']; ?></h5>

                            <div class="text-center progress-demo mb-3">
                                <button id="btnSubmit"
                                        type="button"
                                        class="btn w-sm btn-soft-primary waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["submit"]; ?>
                                </button>

                                <a href="/admin"
                                   class="btn w-sm btn-soft-danger waves-effect shadow-none waves-light">
                                    <?= $lang["btn_back"]; ?>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end col-->


                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row px-2">
                                <h5 class="text-uppercase mt-0  bg-light p-2"><?= $lang['a_whatsapp_default_text_2']; ?></h5>

                                <div class="repeater">
                                    <div data-repeater-list="group-a">

                                        <?php
                                        if (!empty($resultTextShareWhatsapp) && $resultTextShareWhatsapp != '[]') {
                                            foreach (json_decode($resultTextShareWhatsapp) as $loop) {
                                                ?>
                                                <div class="row my-4 py-4 bg-light " data-repeater-item>
                                                    <label class="col-sm-3 col-form-label"><?= $lang['title']; ?></label>
                                                    <div class="col-sm-9 pb-3">
                                                        <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="mdi mdi-pencil text-primary"></i>
                                                        </span>
                                                            <input type="text"
                                                                   class="form-control"
                                                                   name="whatsAppDefaultTitle"
                                                                   value="<?= $loop->title; ?>"
                                                                   placeholder="<?= $lang['title']; ?>">

                                                        </div>
                                                        <small>
                                                            <?= $lang['name_let_inter']; ?>
                                                        </small>
                                                    </div>


                                                    <label class="col-sm-3 col-form-label"><?= $lang['text_massage']; ?></label>
                                                    <div class="col-sm-9  pb-2">
                                                        <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="mdi mdi-order-alphabetical-descending text-primary"></i>
                                                        </span>
                                                            <textarea class="form-control"
                                                                      name="whatsAppDefaultDesc"
                                                                      placeholder="<?= $lang['text_massage']; ?>"
                                                                      style="height: 100px"><?= $loop->desc; ?></textarea>
                                                        </div>
                                                    </div>


                                                    <div class="col-sm-12 col-md-12 ">
                                                        <button data-repeater-delete
                                                                class="btn btn-soft-danger btn-block ladda-button float-end">
                                                            <?= $lang['delete']; ?>
                                                        </button>
                                                    </div>
                                                </div>

                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <div class="row my-4 py-4 bg-light" data-repeater-item>
                                                <label class="col-sm-3 col-form-label"><?= $lang['title']; ?></label>
                                                <div class="col-sm-9 pb-3">
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="mdi mdi-pencil text-primary"></i>
                                                        </span>
                                                        <input type="text"
                                                               class="form-control"
                                                               name="whatsAppDefaultTitle"
                                                               placeholder="<?= $lang['title']; ?>">

                                                    </div>
                                                    <small>
                                                        <?= $lang['name_let_inter']; ?>
                                                    </small>
                                                </div>

                                                <label class="col-sm-3 col-form-label"><?= $lang['text_massage']; ?></label>
                                                <div class="col-sm-9  pb-2">
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="mdi mdi-order-alphabetical-descending text-primary"></i>
                                                        </span>
                                                        <textarea class="form-control"
                                                                  name="whatsAppDefaultDesc"
                                                                  placeholder="<?= $lang['text_massage']; ?>"
                                                                  style="height: 100px"></textarea>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12 col-md-12 ">
                                                    <button data-repeater-delete
                                                            class="btn btn-soft-danger btn-block ladda-button float-end">
                                                        <?= $lang['delete']; ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <button data-repeater-create
                                            class="btn btn-soft-primary btn-block ladda-button mt-2">
                                        <i class="mdi mdi-plus-circle-outline me-1"></i>
                                        <?= $lang['add']; ?>
                                    </button>
                                </div>


                                <div class="text-center my-3">
                                    <button type="submit" class="btn btn-primary btn-block"
                                            data-style="zoom-in"
                                            id="submit_whatsapp_default"><?= $lang['submit_change']; ?></button>
                                </div>

                            </div>
                            <!-- end row-->
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