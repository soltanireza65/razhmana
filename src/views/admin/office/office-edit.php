<?php
$pageSlug = "ship-offices";

// permission_can_insert

use MJ\Security\Security;
use MJ\Utils\Utils;

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_insert == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1

        /**
         * Get All Post Categories
         */
        $resultAllCategories = Post::getAllPostCategories('active');
        $dataCategories = [];
        if ($resultAllCategories->status == 200 && !empty($resultAllCategories->response)) {
            $dataCategories = $resultAllCategories->response;
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
        enqueueStylesheet('dropzone', '/dist/libs/dropzone/min/dropzone.min.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');
        enqueueStylesheet('switchery-css', '/dist/libs/mohithg-switchery/switchery.min.css');

        // Load Script In Footer
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('dropzone', '/dist/libs/dropzone/min/dropzone.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('switchery', '/dist/libs/mohithg-switchery/switchery.min.js');
        enqueueScript('office-edit', '/dist/js/admin/office/office-edit.init.js');

        getHeader($lang["office_add"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_insert',
        ]);

        if (!isset($_REQUEST['id'])) {
            header('Location: /admin/office');
        }
        $office_id = $_REQUEST['id'];
        $office_detail = Office::getOfficeDetail($office_id);
        $office_detail = $office_detail->status == 200 ? $office_detail->response : [];

// start roles 2
        if ($flagSlug) {
            // end roles 2
            ?>
            <script>
                let office_detail = <?=json_encode($office_detail, JSON_PRETTY_PRINT)?>
            </script>
            <style>
                .switch {
                    position: relative;
                    display: inline-block;
                    width: 60px;
                    height: 34px;
                }

                .switch input {
                    opacity: 0;
                    width: 0;
                    height: 0;
                }

                .slider {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #ccc;
                    -webkit-transition: .4s;
                    transition: .4s;
                }

                .slider:before {
                    position: absolute;
                    content: "";
                    height: 26px;
                    width: 26px;
                    left: 4px;
                    bottom: 4px;
                    background-color: white;
                    -webkit-transition: .4s;
                    transition: .4s;
                }

                input:checked + .slider {
                    background-color: #2196F3;
                }

                input:focus + .slider {
                    box-shadow: 0 0 1px #2196F3;
                }

                input:checked + .slider:before {
                    -webkit-transform: translateX(26px);
                    -ms-transform: translateX(26px);
                    transform: translateX(26px);
                }

                /* Rounded sliders */
                .slider.round {
                    border-radius: 34px;
                }

                .slider.round:before {
                    border-radius: 50%;
                }
            </style>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase1" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase1"><i class="mdi mdi-minus"></i></a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["office_add"]; ?></h5>

                            <div class="row show" id="cardCollpase1">

                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="office-mobile"
                                               placeholder="<?= $lang["mobile"]; ?>"
                                               value="<?= $office_detail->office_mobile ?>" readonly>
                                        <label for="xTitle"><?= $lang["mobile"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                class="text-danger"
                                                id="length_xTitle">0</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="office-email"
                                               placeholder="<?= $lang["email"]; ?>"
                                               value="<?= $office_detail->office_email ?>" readonly>
                                        <label for="xTitle"><?= $lang["email"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                class="text-danger"
                                                id="length_xTitle">0</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="office-email"
                                               placeholder="<?= $lang["password"]; ?>"
                                               value="<?= $office_detail->office_password ?>" readonly>
                                        <label for="xTitle"><?= $lang["password"]; ?></label>
                                        <small class="form-text text-muted">
                                            <?= $lang['length_text']; ?> : <span
                                                class="text-danger"
                                                id="length_xTitle">0</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <select class="form-control" data-toggle="select2" name="" id="country">

                                        </select>

                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <select class="form-control" data-toggle="select2" name="" id="city">

                                        </select>
                                    </div>
                                </div>
                               <div class="col-12 ">
                                   <label class="switch">
                                       <input type="checkbox"  id="office-status"  <?=$office_detail->office_status == 'active'  ? 'checked' : ''?>>
                                       <span class="slider round"></span>
                                   </label>
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
                                <button id="edit-office"
                                        type="button"
                                        class="setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light"
                                        data-style="zoom-in">
                                    <?= $lang["submit"]; ?>
                                </button>

                                <a href="/admin/office"
                                   class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light">
                                    <?= $lang["btn_back"]; ?>
                                </a>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF2() ?>">

            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter(
            [
                $lang['help_post_1'],
                $lang['help_post_2'],
                $lang['help_post_3'],
                $lang['help_post_4'],
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